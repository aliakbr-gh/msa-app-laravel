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

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Monthly Salary</th>
                        <th>Present Days</th>
                        <th>Absent Days</th>
                        <th>Paid Amount</th>
                        <th>Unpaid Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        @php
                            $present = $employee->attendances->where('status', 'present')->count();
                            $absent = $employee->attendances->where('status', 'absent')->count();
                            $paid = $employee->salaryPayments->sum('amount');
                            $unpaid = max(0, (float) $employee->salary - (float) $paid);
                        @endphp
                        <tr>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ number_format($employee->salary, 2) }}</td>
                            <td><span class="badge bg-success">{{ $present }}</span></td>
                            <td><span class="badge bg-danger">{{ $absent }}</span></td>
                            <td>{{ number_format($paid, 2) }}</td>
                            <td>{{ number_format($unpaid, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $employees->appends(['month' => $month])->links() }}
    </div>
@endsection
