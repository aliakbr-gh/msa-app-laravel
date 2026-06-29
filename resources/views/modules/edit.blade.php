@extends('layout.layout')

@section('title', 'Edit Module')
@section('page-title', 'Edit Module')

@section('content')
    <form onsubmit="updateModule(event, {{ $module->id }})" class="mx-auto" style="max-width: 560px;">
        @csrf
        <div class="mb-3">
            <label class="form-label">Module Title</label>
            <input type="text" name="title" value="{{ $module->title }}" class="form-control" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="has_qty" value="1" id="has_qty" class="form-check-input" {{ $module->has_qty ? 'checked' : '' }}>
            <label for="has_qty" class="form-check-label">Use quantity and rate fields</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity Label</label>
            <input type="text" name="qty_label" value="{{ $module->qty_label }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Rate Label</label>
            <input type="text" name="rate_label" value="{{ $module->rate_label }}" class="form-control">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" {{ $module->is_active ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Update Module</button>
            <a href="/modules" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        function updateModule(e, id) {
            e.preventDefault();
            $.ajax({
                url: `/modules/${id}`,
                method: 'PATCH',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/modules');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message || 'Module update failed', 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
