@extends('layout.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <div class="text-center">
        @auth
            <h5 class="mb-3">Welcome, {{ auth()->user()->username }}</h5>

            <form onsubmit="logoutUser(event)">
                @csrf
                <button class="btn btn-danger">Logout</button>
            </form>

            <p>{{ auth()->user()->role === "admin" ? "a" : "d" }}</p>

            <p>{{ auth()->user()->role === "pharmacy" ? "Phhhhaaaaaa" : "d" }}</p>
        @endauth
    </div>

    <script>
        function logoutUser(e) {
            e.preventDefault();
            $.ajax({
                url: "/logout",
                method: "POST",

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
