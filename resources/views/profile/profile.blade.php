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
@endsection