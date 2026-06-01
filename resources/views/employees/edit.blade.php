@extends('layout.layout')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')

@section('content')
    <form onsubmit="updateEmployee(event, {{ $employee->id }})" class="mx-auto" style="max-width: 520px;" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="{{ $employee->full_name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Salary</label>
            <input type="number" step="0.01" name="salary" class="form-control" value="{{ $employee->salary }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">CNIC</label>
            <input type="text" name="cnic" class="form-control" value="{{ $employee->cnic }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Picture</label>
            <input type="file" name="picture" class="form-control" accept="image/*">
            @if ($employee->picture)
                <img src="{{ asset('storage/' . $employee->picture) }}" class="mt-2 rounded" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $employee->full_name }}">
            @endif
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Update Employee</button>
            <a href="/employees" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        function updateEmployee(e, id) {
            e.preventDefault();
            const data = new FormData(e.target);
            data.append('_method', 'PATCH');
            $.ajax({
                url: `/employees/${id}`,
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/employees');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
