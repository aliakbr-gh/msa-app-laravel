@extends('layout.layout')

@section('title', 'Login')

@section('page-title', 'Login')

@section('content')
    <form onsubmit="loginUser(event)" class="mx-auto" style="max-width: 400px;">
        @csrf

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Login</button>
    </form>
    <script>
        function loginUser(e) {
            e.preventDefault();
            $.ajax({
                url: "/login",
                method: "POST",
                data: {
                    username: $('#username').val(),
                    password: $('#password').val(),
                },

                beforeSend: function() {
                    showLoader();
                },

                success: function(response) {
                    console.log(response);
                    showToast(response?.message);
                    redirect("/dashboard");
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
