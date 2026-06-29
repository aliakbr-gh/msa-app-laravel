@extends('layout.layout')

@section('title', 'Salary Slip')
@section('page-title', 'Salary Slip')

@section('content')
    <div class="container mt-4">
        <div class="d-flex gap-2 mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">Print</button>
            <a href="/employees/report?month={{ $month }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="bg-white text-dark border mx-auto p-4" style="max-width: 760px;">
            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                <div>
                    <h3 class="mb-0">Restaurant ERP</h3>
                    <div class="text-muted">Employee Salary Slip</div>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">{{ $employee->full_name }}</div>
                    <div>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</div>
                </div>
            </div>

            <table class="table table-bordered">
                <tr><th>Monthly Salary</th><td class="text-end">{{ number_format($salary, 2) }}</td></tr>
                <tr><th>Absent Days</th><td class="text-end">{{ $absent }}</td></tr>
                <tr><th>Off Deduction</th><td class="text-end">{{ number_format($deduction, 2) }}</td></tr>
                <tr><th>Net Salary</th><td class="text-end fw-bold">{{ number_format($netSalary, 2) }}</td></tr>
                <tr><th>Paid Amount</th><td class="text-end">{{ number_format($paid, 2) }}</td></tr>
                <tr><th>Remaining Balance</th><td class="text-end fw-bold">{{ number_format($balance, 2) }}</td></tr>
            </table>
        </div>
    </div>

    <style>
        @media print {
            .navbar, .no-print, #themeBtn, #toastContainer { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
@endsection
