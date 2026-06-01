@extends('layout.layout')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
    <div class="container-fluid mt-4">
        <form method="GET" class="row g-2 align-items-end mb-4">
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Apply Filter</button>
            </div>
            <div class="col-md-2">
                <button type="button" onclick="window.print()" class="btn btn-outline-dark w-100">Print Report</button>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="text-muted">Orders</div><h4>{{ $summary['orders'] }}</h4><div>Total {{ number_format($summary['order_total'], 2) }}</div></div></div>
            <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="text-muted">Pending Payment</div><h4>{{ number_format($summary['pending_payment'], 2) }}</h4><div>Advance {{ number_format($summary['advance_received'], 2) }}</div></div></div>
            <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="text-muted">Salary Paid</div><h4>{{ number_format($summary['salary_paid'], 2) }}</h4><div>{{ $summary['employees'] }} employees</div></div></div>
            <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="text-muted">Kitchen</div><h4>{{ number_format($summary['kitchen_qty'], 2) }}</h4><div>{{ $summary['kitchen_records'] }} records</div></div></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                    <h5>Money Flow</h5>
                    <canvas id="moneyFlowChart" height="150"></canvas>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="border rounded p-3 h-100">
                    <h5>Orders by Status</h5>
                    <canvas id="orderStatusChart" height="190"></canvas>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="border rounded p-3 h-100">
                    <h5>Attendance</h5>
                    <canvas id="attendanceChart" height="190"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                    <h5>Food Ledger Balances</h5>
                    <canvas id="ledgerBalanceChart" height="155"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="table-responsive border rounded p-3 h-100">
                    <h5>Food Modules</h5>
                    <table class="table table-sm table-bordered align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Module</th>
                                <th>Entries</th>
                                <th>Purchases</th>
                                <th>Paid</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ledgerModules as $module)
                                <tr>
                                    <td>{{ $module['title'] }}</td>
                                    <td>{{ $module['entries'] }}</td>
                                    <td>{{ number_format($module['purchases'], 2) }}</td>
                                    <td>{{ number_format($module['paid'], 2) }}</td>
                                    <td>{{ number_format($module['balance'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="table-responsive border rounded p-3 h-100">
                    <h5>Top Customers</h5>
                    <table class="table table-sm table-bordered align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Customer</th>
                                <th>Orders</th>
                                <th>Total</th>
                                <th>Pending</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topCustomers as $customer)
                                <tr>
                                    <td>{{ $customer['name'] }}</td>
                                    <td>{{ $customer['orders'] }}</td>
                                    <td>{{ number_format($customer['total'], 2) }}</td>
                                    <td>{{ number_format($customer['pending'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No customer data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                    <h5>Attendance Summary</h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="display-6 text-success">{{ $summary['attendance_present'] }}</div>
                            <div class="text-muted">Present</div>
                        </div>
                        <div class="col-6">
                            <div class="display-6 text-danger">{{ $summary['attendance_absent'] }}</div>
                            <div class="text-muted">Absent</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .navbar, form, #themeBtn, #toastContainer {
                display: none !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartColors = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1', '#20c997'];

        new Chart(document.getElementById('moneyFlowChart'), {
            type: 'bar',
            data: {
                labels: @json($charts['moneyFlow']['labels']),
                datasets: [{ label: 'Amount', data: @json($charts['moneyFlow']['data']), backgroundColor: chartColors }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($charts['orderStatus']['labels']),
                datasets: [{ data: @json($charts['orderStatus']['data']), backgroundColor: chartColors }]
            }
        });

        new Chart(document.getElementById('attendanceChart'), {
            type: 'doughnut',
            data: {
                labels: @json($charts['attendance']['labels']),
                datasets: [{ data: @json($charts['attendance']['data']), backgroundColor: ['#198754', '#dc3545'] }]
            }
        });

        new Chart(document.getElementById('ledgerBalanceChart'), {
            type: 'bar',
            data: {
                labels: @json($charts['ledgerBalances']['labels']),
                datasets: [{ label: 'Remaining Balance', data: @json($charts['ledgerBalances']['data']), backgroundColor: chartColors }]
            },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
        });
    </script>
@endsection
