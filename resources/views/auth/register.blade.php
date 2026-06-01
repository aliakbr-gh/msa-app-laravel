@extends('layout.layout')

@section('title', 'Register')

@section('page-title', 'Register')

@section('content')
    <form onsubmit="registerUser(event)" class="mx-auto" style="max-width: 400px;">
        @csrf

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" id="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select id="role_id" class="form-select">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary w-100">Register</button>

        <div class="text-center mt-3">
            <a href="/">Already have account?</a>
        </div>
    </form>
    <script>
        function registerUser(e) {
            e.preventDefault();
            $.ajax({
                url: "/register",
                method: "POST",
                data: {
                    username: $('#username').val(),
                    phone: $('#phone').val(),
                    password: $('#password').val(),
                    role_id: $('#role_id').val(),
                },

                beforeSend: function() {
                    showLoader();
                },

                success: function(response) {
                    console.log(response);
                    showToast(response?.message);
                    redirect("/");
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
