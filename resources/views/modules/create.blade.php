@extends('layout.layout')

@section('title', 'Create Module')
@section('page-title', 'Create Module')

@section('content')
    <form onsubmit="saveModule(event)" class="mx-auto" style="max-width: 560px;">
        @csrf
        <div class="mb-3">
            <label class="form-label">Module Title</label>
            <input type="text" name="title" class="form-control" placeholder="Example: Fish" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="has_qty" value="1" id="has_qty" class="form-check-input">
            <label for="has_qty" class="form-check-label">Use quantity and rate fields</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity Label</label>
            <input type="text" name="qty_label" class="form-control" placeholder="Qty (Kg)">
        </div>
        <div class="mb-3">
            <label class="form-label">Rate Label</label>
            <input type="text" name="rate_label" class="form-control" placeholder="Rate">
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Create Module</button>
            <a href="/modules" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        function saveModule(e) {
            e.preventDefault();
            $.ajax({
                url: '/modules/create',
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/modules');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message || 'Module creation failed', 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
