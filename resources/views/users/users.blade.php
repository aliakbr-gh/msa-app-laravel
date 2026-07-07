@extends('layout.layout')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    @php($role = auth()->user()->role?->name)

    <div class="container mt-4">
        <div class="mb-3">
            @if ($role === 'admin')
                <a href="/users/create" class="btn btn-primary">Create User</a>
            @endif
        </div>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-2"><a href="/users" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
        @include('partials.per-page', ['paginator' => $users])
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>********</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ ucfirst($user->role->name ?? "No Role") }}
                                </span>
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($user->updated_at)->format('d-m-Y') }}
                            </td>
                            <td>
                                @if ($role === 'admin')
                                    <a href="/users/{{ $user->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                    <button onclick="confirmDelete('/users/{{ $user->id }}', '/users', 'Delete this user?')" class="btn btn-sm btn-danger">Delete</button>
                                @else
                                    <span class="text-muted">Read only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if ($users->hasPages())
            {{ $users->links() }}
        @endif

    </div>
@endsection
