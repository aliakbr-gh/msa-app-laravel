@extends('layout.layout')
@php
    $hideHeader = true;
@endphp
@section('title', 'Setup Project')
@section('page-title', 'Setup Project')
@section('content')
<div class="container mt-4">
    <form onsubmit="addSU(event)">
        @csrf

        <div class="card p-4">
            <div class="mb-3">
                <label class="form-label">Password: </label>
                <input type="text" id="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password: </label>
                <input type="text" id="confirm_password" name="confirm_password" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">S.Key: </label>
                <input type="text" id="s_key" name="s_key" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Add Super User
                </button>
            </div>

        </div>

    </form>
    <script>
        function addSU(e) {
            e.preventDefault();
            $.ajax({
                url: `/setup-project`,
                method: "POST",
                data: {
                    password: $('#password').val(),
                    confirm_password: $('#confirm_password').val(),
                    s_key: $('#s_key').val(),
                },

                beforeSend: function() {
                    showLoader();
                },

                success: function(response) {
                    console.log(response);
                    showToast(response?.message);
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
