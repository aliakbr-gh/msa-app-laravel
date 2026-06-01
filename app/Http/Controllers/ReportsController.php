<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeSalaryPayment;
use App\Models\KitchenRecord;
use App\Models\LedgerEntry;
use App\Models\OrderRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = Carbon::parse($data['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($data['to'] ?? now()->toDateString())->endOfDay();

        $orders = OrderRecord::whereBetween('order_date', [$from->toDateString(), $to->toDateString()])->get();
        $ledgers = LedgerEntry::whereBetween('entry_date', [$from->toDateString(), $to->toDateString()])->get();
        $attendance = EmployeeAttendance::whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])->get();
        $salaryPayments = EmployeeSalaryPayment::whereBetween('paid_on', [$from->toDateString(), $to->toDateString()])->get();
        $kitchenRecords = KitchenRecord::whereBetween('created_at', [$from, $to])->get();

        $ledgerModules = collect(['roti' => 'Roti', 'beef' => 'Beef', 'chicken-1' => 'Chicken 1', 'chicken-2' => 'Chicken 2'])
            ->map(function ($title, $module) use ($ledgers) {
                $moduleLedgers = $ledgers->where('module', $module);
                $latestEntry = LedgerEntry::where('module', $module)->latest('entry_date')->latest('id')->first();

                return [
                    'module' => $module,
                    'title' => $title,
                    'entries' => $moduleLedgers->count(),
                    'paid' => $moduleLedgers->filter->isPaid()->sum('amount'),
                    'purchases' => $moduleLedgers->reject->isPaid()->sum('amount'),
                    'balance' => $latestEntry?->remaining_balance ?? 0,
                ];
            })->values();

        $summary = [
            'employees' => Employee::count(),
            'orders' => $orders->count(),
            'order_total' => $orders->sum('total_amount'),
            'advance_received' => $orders->sum('advance_received'),
            'pending_payment' => $orders->sum('pending_payment'),
            'salary_paid' => $salaryPayments->sum('amount'),
            'ledger_paid' => $ledgers->filter->isPaid()->sum('amount'),
            'ledger_purchases' => $ledgers->reject->isPaid()->sum('amount'),
            'attendance_present' => $attendance->where('status', 'present')->count(),
            'attendance_absent' => $attendance->where('status', 'absent')->count(),
            'kitchen_qty' => $kitchenRecords->sum('qty'),
            'kitchen_records' => $kitchenRecords->count(),
        ];

        $charts = [
            'orderStatus' => [
                'labels' => $orders->groupBy('status')->keys()->map(fn ($status) => ucfirst($status))->values(),
                'data' => $orders->groupBy('status')->map->count()->values(),
            ],
            'attendance' => [
                'labels' => ['Present', 'Absent'],
                'data' => [$summary['attendance_present'], $summary['attendance_absent']],
            ],
            'ledgerBalances' => [
                'labels' => $ledgerModules->pluck('title'),
                'data' => $ledgerModules->pluck('balance'),
            ],
            'moneyFlow' => [
                'labels' => ['Order Total', 'Advance', 'Pending', 'Salary Paid', 'Food Purchases', 'Food Paid'],
                'data' => [
                    $summary['order_total'],
                    $summary['advance_received'],
                    $summary['pending_payment'],
                    $summary['salary_paid'],
                    $summary['ledger_purchases'],
                    $summary['ledger_paid'],
                ],
            ],
        ];

        $topCustomers = $orders
            ->groupBy('customer_name')
            ->map(fn ($customerOrders, $customerName) => [
                'name' => $customerName,
                'orders' => $customerOrders->count(),
                'total' => $customerOrders->sum('total_amount'),
                'pending' => $customerOrders->sum('pending_payment'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        return response()->view('reports.index', compact(
            'from',
            'to',
            'summary',
            'ledgerModules',
            'charts',
            'topCustomers'
        ));
    }
}
