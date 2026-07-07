@extends('layout.layout')

@section('title', 'Create Order')
@section('page-title', 'Create Order')

@section('content')
    <form onsubmit="saveOrder(event)" class="mx-auto" style="max-width: 760px;">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Order Date</label>
                <input type="date" name="order_date" value="{{ now()->toDateString() }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Delivery Date</label>
                <input type="date" name="order_delivery_date" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Book No</label>
                <input type="number" name="book_no" value="{{ $nextBookNo }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Deg Qty</label>
                <input type="number" name="deg_qty" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Customer Name</label>
                <input type="text" name="customer_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile_number" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Details</label>
                <textarea name="details" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Advance Received</label>
                <input type="number" step="0.01" name="advance_received" id="advance_received" value="0" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Pending Payment</label>
                <input type="number" step="0.01" name="pending_payment" id="pending_payment" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Comments</label>
                <textarea name="comments" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary">Create Order</button>
            <a href="/orders" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        $('#total_amount, #advance_received').on('input', function() {
            const total = Number($('#total_amount').val() || 0);
            const advance = Number($('#advance_received').val() || 0);
            $('#pending_payment').val(Math.max(0, total - advance).toFixed(2));
        });

        function saveOrder(e) {
            e.preventDefault();
            $.ajax({
                url: '/orders/create',
                method: 'POST',
                data: $(e.target).serialize(),
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
