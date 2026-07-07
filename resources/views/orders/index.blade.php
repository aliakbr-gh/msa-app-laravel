@extends('layout.layout')

@section('title', 'Restaurant ERP Orders')
@section('page-title', 'Restaurant ERP Order Records')

@section('content')
    @php($role = auth()->user()->role?->name)
    <div class="container mt-4">
        <div class="mb-3">
            @if ($role === 'admin')
                <a href="/orders/create" class="btn btn-primary">Create Order</a>
            @endif
        </div>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><input type="number" name="book_no" value="{{ request('book_no') }}" placeholder="Book No" class="form-control"></div>
            <div class="col-md-1"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-1"><a href="/orders" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
        @include('partials.per-page', ['paginator' => $orders])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Book No</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Deg Qty</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Total</th>
                        <th>Advance</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->book_no }}</td>
                            <td>{{ $order->order_date->format('d-m-Y') }}</td>
                            <td>{{ $order->order_delivery_date?->format('d-m-Y') ?? '-' }}</td>
                            <td>{{ $order->deg_qty }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->mobile_number ?? '-' }}</td>
                            <td>{{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ number_format($order->advance_received, 2) }}</td>
                            <td>{{ number_format($order->balance, 2) }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($order->status) }}</span></td>
                            <td>
                                <a href="/orders/{{ $order->id }}/invoice" class="btn btn-sm btn-success">Invoice</a>
                                @if (in_array($role, ['admin', 'cashier']) && (float) $order->balance > 0)
                                    <button class="btn btn-sm btn-warning" onclick="openPayModal({{ $order->id }}, '{{ $order->customer_name }}', {{ $order->balance }})">Pay</button>
                                @endif
                                @if ($role === 'admin')
                                    <a href="/orders/{{ $order->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                    <button onclick="confirmDelete('/orders/{{ $order->id }}', '/orders', 'Delete this order?')" class="btn btn-sm btn-danger">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            {{ $orders->links() }}
        @endif
    </div>

    <div class="modal fade" id="orderPayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" onsubmit="saveOrderPayment(event)">
                @csrf
                <input type="hidden" id="pay_order_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="payModalTitle">Order Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Paid On</label>
                        <input type="date" name="paid_on" value="{{ now()->toDateString() }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" id="pay_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Pay</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPayModal(orderId, customerName, balance) {
            $('#pay_order_id').val(orderId);
            $('#pay_amount').attr('max', balance).val(Number(balance).toFixed(2));
            $('#payModalTitle').text(`Payment for ${customerName}`);
            new bootstrap.Modal(document.getElementById('orderPayModal')).show();
        }

        function saveOrderPayment(e) {
            e.preventDefault();
            $.ajax({
                url: `/orders/${$('#pay_order_id').val()}/pay`,
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/orders');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message || 'Payment failed', 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
