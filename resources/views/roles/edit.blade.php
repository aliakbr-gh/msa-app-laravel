@extends('layout.layout')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
    <div class="container mt-4">
        <form onsubmit="updateRole(event, {{ $role->id }})">
            @csrf

            <div class="card p-4">
                <div class="mb-3">
                    <label class="form-label">name</label>
                    <input type="text" id="name" name="name" class="form-control"
                        value="{{ old('name', $role->name) }}">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Update Role
                    </button>

                    <a href="/roles" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>

            </div>

        </form>
        <script>
            function updateRole(e, id) {
                e.preventDefault();
                $.ajax({
                    url: `/roles/${id}`,
                    method: "PATCH",
                    data: {
                        name: $('#name').val(),
                    },

                    beforeSend: function() {
                        showLoader();
                    },

                    success: function(response) {
                        console.log(response);
                        showToast(response?.message);
                        redirect("/roles");
                    },

                    error: function(error) {
                        console.log(error?.responseJSON);
                        showToast(error?.responseJSON?.message, 'error');
                    },

                    complete: function() {
                        hideLoader();
                    }
                });
            }
        </script>
    </div>
@endsection
