@extends('layout.layout')

@section('title', 'Profile')

@section('page-title', 'Profile')

@section('content')

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th width="200">ID</th>
                <td>{{ $profile['id'] }}</td>
            </tr>
            <tr>
                <th>Username</th>
                <td>{{ $profile['username'] }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $profile['phone'] }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    <span class="badge bg-primary">
                        {{ $user->role->name }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($profile['is_active'])
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $profile['created_at'] }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="card mt-4 mx-auto" style="max-width: 520px;">
    <div class="card-header fw-semibold">Change Password</div>
    <form class="card-body" onsubmit="changePassword(event)">
        @csrf
        <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required minlength="4">
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="4">
        </div>
        <button class="btn btn-primary">Change Password</button>
    </form>
</div>

<script>
    function changePassword(e) {
        e.preventDefault();
        $.ajax({
            url: '/profile/password',
            method: 'PATCH',
            data: $(e.target).serialize(),
            beforeSend: showLoader,
            success: function(response) {
                showToast(response?.message);
                e.target.reset();
            },
            error: function(error) {
                showToast(error?.responseJSON?.message || 'Password change failed', 'error');
            },
            complete: hideLoader
        });
    }
</script>
@endsection
