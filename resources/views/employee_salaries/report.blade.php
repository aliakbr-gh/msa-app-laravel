@extends('layout.layout')

@section('title', 'Employee Monthly Report')
@section('page-title', 'Employee Monthly Report')

@section('content')
    <div class="container mt-4">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="month" name="month" value="{{ $month }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Load Report</button>
            </div>
        </form>

        @include('partials.per-page', ['paginator' => $employees])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Monthly Salary</th>
                        <th>Present Days</th>
                        <th>Absent Days</th>
                        <th>Off Deduction</th>
                        <th>Net Salary</th>
                        <th>Paid Amount</th>
                        <th>Unpaid Amount</th>
                        <th>Slip</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        @php
                            $present = $employee->attendances->where('status', 'present')->count();
                            $absent = $employee->attendances->where('status', 'absent')->count();
                            $salary = (float) ($employee->salaryHistories->first()?->salary ?? $employee->salary);
                            $daysInMonth = \Carbon\Carbon::createFromFormat('Y-m', $month)->daysInMonth;
                            $deduction = ($salary / max(1, $daysInMonth)) * $absent;
                            $netSalary = max(0, $salary - $deduction);
                            $paid = $employee->salaryPayments->sum('amount');
                            $unpaid = max(0, $netSalary - (float) $paid);
                        @endphp
                        <tr>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ number_format($salary, 2) }}</td>
                            <td><span class="badge bg-success">{{ $present }}</span></td>
                            <td><span class="badge bg-danger">{{ $absent }}</span></td>
                            <td>{{ number_format($deduction, 2) }}</td>
                            <td>{{ number_format($netSalary, 2) }}</td>
                            <td>{{ number_format($paid, 2) }}</td>
                            <td>{{ number_format($unpaid, 2) }}</td>
                            <td><a class="btn btn-sm btn-outline-primary" href="/employees/{{ $employee->id }}/salary-slip?month={{ $month }}">Slip</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            {{ $employees->appends(['month' => $month])->links() }}
        @endif
    </div>
@endsection
