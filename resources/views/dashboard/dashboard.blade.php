@extends('layout.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                @auth
                    <h5 class="mb-1">Welcome, {{ auth()->user()->username }}</h5>
                @endauth
                <div class="text-muted">{{ now('Asia/Karachi')->format('d M Y, h:i A') }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="/orders/create" class="btn btn-primary">New Order</a>
                <a href="/employees/attendance" class="btn btn-success">Mark Attendance</a>
                <a href="/reports" class="btn btn-outline-dark">Reports</a>
                <a href="/backup/download" class="btn btn-outline-secondary">Backup</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted">Today Orders</div>
                    <h3 class="mb-1">{{ $summary['today_orders'] }}</h3>
                    <div>{{ number_format($summary['today_sales'], 2) }} sales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted">Month Orders</div>
                    <h3 class="mb-1">{{ $summary['month_orders'] }}</h3>
                    <div>{{ number_format($summary['month_sales'], 2) }} sales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted">Pending Payment</div>
                    <h3 class="mb-1">{{ number_format($summary['pending_payment'], 2) }}</h3>
                    <div>All open order balance</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted">Employees</div>
                    <h3 class="mb-1">{{ $summary['employees'] }}</h3>
                    <div class="text-success">{{ $summary['today_present'] }} present</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Cash Overview</h5>
                        <a href="/reports" class="btn btn-sm btn-outline-primary">Open Reports</a>
                    </div>
                    <canvas id="cashChart" height="145"></canvas>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="border rounded p-3 h-100">
                    <h5>Today Attendance</h5>
                    <canvas id="attendanceChart" height="190"></canvas>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="border rounded p-3 h-100">
                    <h5>Order Sales</h5>
                    <canvas id="ordersChart" height="190"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-5">
                <div class="border rounded p-3 h-100">
                    <h5>Food Balances</h5>
                    <canvas id="foodBalanceChart" height="175"></canvas>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted">Salary Paid</div>
                            <h4>{{ number_format($summary['salary_paid'], 2) }}</h4>
                            <div>This month</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted">Food Purchases</div>
                            <h4>{{ number_format($summary['food_purchases'], 2) }}</h4>
                            <div>This month</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted">Kitchen Records</div>
                            <h4>{{ $summary['kitchen_records'] }}</h4>
                            <div>Today</div>
                        </div>
                    </div>
                    @foreach ($foodBalances as $foodBalance)
                        <div class="col-md-6">
                            <div class="border rounded p-3 d-flex justify-content-between align-items-center">
                                <span>{{ $foodBalance['title'] }}</span>
                                <strong>{{ number_format($foodBalance['balance'], 2) }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="table-responsive border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="/orders" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <table class="table table-sm table-bordered align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Book</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Pending</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->book_no }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ number_format($order->pending_payment, 2) }}</td>
                                    <td><span class="badge bg-info text-dark">{{ ucfirst($order->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No recent orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="table-responsive border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Recent Food Entries</h5>
                        <a href="/roti" class="btn btn-sm btn-outline-primary">Open Ledgers</a>
                    </div>
                    <table class="table table-sm table-bordered align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Module</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentLedgers as $entry)
                                <tr>
                                    <td>{{ ucwords(str_replace('-', ' ', $entry->module)) }}</td>
                                    <td>{{ $entry->entry_date->format('d-m-Y') }}</td>
                                    <td>{{ $entry->description }}</td>
                                    <td>{{ number_format($entry->amount, 2) }}</td>
                                    <td>{{ number_format($entry->remaining_balance, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No recent food entries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const dashboardColors = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1', '#20c997'];

        new Chart(document.getElementById('cashChart'), {
            type: 'bar',
            data: {
                labels: @json($charts['cash']['labels']),
                datasets: [{ label: 'Amount', data: @json($charts['cash']['data']), backgroundColor: dashboardColors }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('attendanceChart'), {
            type: 'doughnut',
            data: {
                labels: @json($charts['attendance']['labels']),
                datasets: [{ data: @json($charts['attendance']['data']), backgroundColor: ['#198754', '#dc3545'] }]
            }
        });

        new Chart(document.getElementById('ordersChart'), {
            type: 'doughnut',
            data: {
                labels: @json($charts['orders']['labels']),
                datasets: [{ data: @json($charts['orders']['data']), backgroundColor: ['#0d6efd', '#20c997'] }]
            }
        });

        new Chart(document.getElementById('foodBalanceChart'), {
            type: 'bar',
            data: {
                labels: @json($charts['foodBalances']['labels']),
                datasets: [{ label: 'Remaining Balance', data: @json($charts['foodBalances']['data']), backgroundColor: dashboardColors }]
            },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
        });
    </script>
@endsection
