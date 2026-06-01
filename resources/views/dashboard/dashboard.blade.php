@extends('layout.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <div class="text-center">
        @auth
            <h4 class="mb-3">Welcome, {{ auth()->user()->username }}</h4>
            {{-- <form onsubmit="logoutUser(event)">
                @csrf
                <button class="btn btn-danger">Logout</button>
            </form> --}}
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
