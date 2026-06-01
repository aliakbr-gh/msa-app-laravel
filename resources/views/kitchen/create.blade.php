@extends('layout.layout')

@section('title', 'Create Kitchen Record')
@section('page-title', 'Create Kitchen Record')

@section('content')
    <form onsubmit="saveKitchenRecord(event)" class="mx-auto" style="max-width: 560px;">
        @csrf
        <div class="mb-3">
            <label class="form-label">Details</label>
            <textarea name="details" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Order/Shop</label>
            <input type="text" name="order_shop" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Qty</label>
            <input type="number" step="0.01" name="qty" class="form-control" required>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Create Record</button>
            <a href="/kitchen" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        function saveKitchenRecord(e) {
            e.preventDefault();
            $.ajax({
                url: '/kitchen/create',
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/kitchen');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
