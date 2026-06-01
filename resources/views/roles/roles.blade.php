@extends('layout.layout')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('content')

    <div class="container mt-4">
        <div class="mb-3">
            <a href="/roles/create" class="btn btn-primary">
                Create Roles
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($role->created_at)->format('d-m-Y') }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($role->updated_at)->format('d-m-Y') }}
                            </td>
                            <td>
                                @if (!in_array($role->id, [1, 2, 3, 4]))
                                    <a href="/roles/{{ $role->id }}/edit" class="btn btn-sm btn-primary">
                                        Update
                                    </a>

                                    <button onclick="deleteRole({{ $role->id }})" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                @else
                                    <span class="badge bg-secondary">
                                        Protected
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{ $roles->links() }}

    </div>

    <script>
        function deleteRole(id) {
            $.ajax({
                url: `/roles/${id}`,
                method: "DELETE",

                beforeSend: function() {
                    showLoader();
                },

                success: function(response) {
                    console.log(response);
                    showToast(response?.message);
                    redirect("/roles");
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
