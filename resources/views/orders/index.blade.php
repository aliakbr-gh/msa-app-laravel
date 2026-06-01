@extends('layout.layout')

@section('title', 'MSA Foods Orders')
@section('page-title', 'MSA Foods Order Records')

@section('content')
    <div class="container mt-4">
        <div class="mb-3">
            <a href="/orders/create" class="btn btn-primary">Create Order</a>
        </div>

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
                                <a href="/orders/{{ $order->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                <button onclick="deleteOrder({{ $order->id }})" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11">No orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </div>

    <script>
        function deleteOrder(id) {
            if (!confirm('Delete this order?')) return;
            $.ajax({
                url: `/orders/${id}`,
                method: 'DELETE',
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/orders');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
