@extends('layout.layout')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')

    <div class="container mt-4">
        <div class="mb-3">
            <a href="/users/create" class="btn btn-primary">
                Create User
            </a>
        </div>
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
                                <a href="/users/{{ $user->id }}/edit" class="btn btn-sm btn-primary">
                                    Update
                                </a>


                                <button onclick="deleteUser({{ $user->id }})" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
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

        {{ $users->links() }}

    </div>

    <script>
        function deleteUser(id) {
            $.ajax({
                url: `/users/${id}`,
                method: "DELETE",

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

@endsection
