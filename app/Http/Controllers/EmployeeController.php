<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeSalaryHistory;
use App\Models\EmployeeSalaryPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = $this->applyDateRange(Employee::latest(), $request)
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->view('employees.index', compact('employees'));
    }

    public function create()
    {
        return response()->view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'cnic' => 'nullable|string|max:30|unique:employees,cnic',
            'picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('employees', 'public');
        }

        $employee = Employee::create($data);
        $employee->salaryHistories()->create([
            'salary' => $employee->salary,
            'effective_from' => now()->toDateString(),
        ]);
        $this->logActivity('create', 'employees', "Created employee {$employee->full_name}");

        return APIResponse::success('Employee created successfully', $employee, 201);
    }

    public function edit(Employee $employee)
    {
        return response()->view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'cnic' => ['nullable', 'string', 'max:30', Rule::unique('employees', 'cnic')->ignore($employee->id)],
            'picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            if ($employee->picture) {
                Storage::disk('public')->delete($employee->picture);
            }

            $data['picture'] = $request->file('picture')->store('employees', 'public');
        }

        $salaryChanged = (float) $employee->salary !== (float) $data['salary'];
        $employee->update($data);

        if ($salaryChanged) {
            $employee->salaryHistories()->create([
                'salary' => $employee->salary,
                'effective_from' => now()->toDateString(),
            ]);
        }
        $this->logActivity('update', 'employees', "Updated employee {$employee->full_name}");

        return APIResponse::success('Employee updated successfully', $employee);
    }

    public function destroy(Employee $employee)
    {
        if ($employee->picture) {
            Storage::disk('public')->delete($employee->picture);
        }

        $employee->delete();
        $this->logActivity('delete', 'employees', "Deleted employee {$employee->full_name}");

        return APIResponse::success('Employee deleted successfully');
    }

    public function attendance(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $employees = Employee::orderBy('full_name')->paginate($this->perPage($request, 25))->withQueryString();
        $attendances = EmployeeAttendance::whereDate('attendance_date', $date)
            ->pluck('status', 'employee_id');
        $summary = [
            'total' => Employee::count(),
            'present' => EmployeeAttendance::whereDate('attendance_date', $date)->where('status', 'present')->count(),
            'absent' => EmployeeAttendance::whereDate('attendance_date', $date)->where('status', 'absent')->count(),
            'marked' => EmployeeAttendance::whereDate('attendance_date', $date)->count(),
        ];

        return response()->view('employee_attendance.index', compact('employees', 'attendances', 'date', 'summary'));
    }

    public function markAttendance(Request $request)
    {
        $data = $request->validate([
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent',
        ]);

        foreach ($data['attendance'] as $employeeId => $status) {
            EmployeeAttendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $data['attendance_date'],
                ],
                ['status' => $status]
            );
        }
        $this->logActivity('update', 'employee_attendance', 'Updated attendance records');

        return APIResponse::success('Attendance saved successfully');
    }

    public function salaries(Request $request)
    {
        $payments = $this->applyDateRange(EmployeeSalaryPayment::with('employee')->latest('paid_on'), $request, 'paid_on')
            ->paginate($this->perPage($request))
            ->withQueryString();
        $employees = Employee::orderBy('full_name')->get();

        return response()->view('employee_salaries.index', compact('payments', 'employees'));
    }

    public function storeSalaryPayment(Request $request)
    {
        $payment = EmployeeSalaryPayment::create($request->validate([
            'employee_id' => 'required|exists:employees,id',
            'paid_on' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]));
        $this->logActivity('pay', 'employee_salaries', "Salary payment recorded for employee #{$payment->employee_id}");

        return APIResponse::success('Salary payment saved successfully', $payment, 201);
    }

    public function report(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $employees = Employee::with([
            'attendances' => fn ($query) => $query->whereBetween('attendance_date', [$startDate, $endDate]),
            'salaryPayments' => fn ($query) => $query->whereBetween('paid_on', [$startDate, $endDate]),
            'salaryHistories' => fn ($query) => $query->where('effective_from', '<=', $endDate)->latest('effective_from'),
        ])->orderBy('full_name')->paginate($this->perPage($request))->withQueryString();

        return response()->view('employee_salaries.report', compact('employees', 'month'));
    }

    public function salarySlip(Request $request, Employee $employee)
    {
        $month = $request->query('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $employee->load([
            'attendances' => fn ($query) => $query->whereBetween('attendance_date', [$startDate, $endDate]),
            'salaryPayments' => fn ($query) => $query->whereBetween('paid_on', [$startDate, $endDate]),
            'salaryHistories' => fn ($query) => $query->where('effective_from', '<=', $endDate)->latest('effective_from'),
        ]);
        $salary = (float) ($employee->salaryHistories->first()?->salary ?? $employee->salary);
        $absent = $employee->attendances->where('status', 'absent')->count();
        $deduction = ($salary / max(1, $endDate->daysInMonth)) * $absent;
        $netSalary = max(0, $salary - $deduction);
        $paid = (float) $employee->salaryPayments->sum('amount');
        $balance = max(0, $netSalary - $paid);

        return response()->view('employee_salaries.slip', compact('employee', 'month', 'salary', 'absent', 'deduction', 'netSalary', 'paid', 'balance'));
    }

    public function attendanceReport(Request $request)
    {
        $month = $request->query('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $days = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $days->push($date->copy());
        }

        $employees = Employee::with([
            'attendances' => fn ($query) => $query->whereBetween('attendance_date', [$startDate, $endDate]),
        ])->orderBy('full_name')->paginate($this->perPage($request))->withQueryString();

        return response()->view('employee_attendance.report', compact('employees', 'days', 'month'));
    }
}
