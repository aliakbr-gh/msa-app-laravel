@extends('layout.layout')

@section('title', 'Create Employee')
@section('page-title', 'Create Employee')

@section('content')
    <form onsubmit="createEmployee(event)" class="mx-auto" style="max-width: 520px;" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Salary</label>
            <input type="number" step="0.01" name="salary" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">CNIC</label>
            <input type="text" name="cnic" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Picture</label>
            <input type="file" name="picture" class="form-control" accept="image/*">
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Create Employee</button>
            <a href="/employees" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        function createEmployee(e) {
            e.preventDefault();
            $.ajax({
                url: '/employees/create',
                method: 'POST',
                data: new FormData(e.target),
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
