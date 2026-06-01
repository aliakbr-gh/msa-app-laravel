@extends('layout.layout')

@section('title', 'Create Role')

@section('page-title', 'Create Role')

@section('content')
    <form onsubmit="createRole(event)" class="mx-auto" style="max-width: 400px;">
        @csrf

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" id="name" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Create Role</button>
    </form>
    <script>
        function createRole(e) {
            e.preventDefault();
            $.ajax({
                url: "/roles/create",
                method: "POST",
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
@endsection
