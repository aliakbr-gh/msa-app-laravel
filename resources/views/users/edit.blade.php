@extends('layout.layout')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="container mt-4">
        <form onsubmit="updateUser(event, {{ $user->id }})">
            @csrf

            <div class="card p-4">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control"
                        value="{{ old('username', $user->username) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="text" id="password" name="password" class="form-control"
                        placeholder="Leave blank if you didn't want to change">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control"
                        value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>

                    <select name="role_id" id="role_id" class="form-select">
                        <option value="">Select Role</option>

                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" id="is_active" name="is_active" value={{ $user->is_active }}
                        class="form-check-input" {{ $user->is_active ? 'checked' : '' }}>

                    <label class="form-check-label">Is Active</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Update User
                    </button>

                    <a href="/users" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>

            </div>

        </form>
        <script>
            function updateUser(e, id) {
                e.preventDefault();
                $.ajax({
                    url: `/users/${id}`,
                    method: "PATCH",
                    data: {
                        username: $('#username').val(),
                        phone: $('#phone').val(),
                        password: $('#password').val(),
                        role_id: $('#role_id').val(),
                        is_active: $('#is_active').is(':checked') ? 1 : 0,
                    },

                    beforeSend: function() {
                        showLoader();
                    },

                    success: function(response) {
                        console.log(response);
                        showToast(response?.message);
                        redirect("/users");
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
