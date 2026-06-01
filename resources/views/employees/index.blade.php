@extends('layout.layout')

@section('title', 'Employees')
@section('page-title', 'Employees')

@section('content')
    <div class="container mt-4">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="/employees/create" class="btn btn-primary">Create Employee</a>
            <a href="/employees/attendance" class="btn btn-outline-primary">Mark Attendance</a>
            <a href="/employees/salaries" class="btn btn-outline-success">Salary Payments</a>
            <a href="/employees/report" class="btn btn-outline-dark">Monthly Report</a>
        </div>
        @include('partials.per-page', ['paginator' => $employees])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Picture</th>
                        <th>Full Name</th>
                        <th>Salary</th>
                        <th>CNIC</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>
                                @if ($employee->picture)
                                    <img src="{{ asset('storage/' . $employee->picture) }}" alt="{{ $employee->full_name }}" class="rounded" style="width: 54px; height: 54px; object-fit: cover;">
                                @else
                                    <span class="badge bg-secondary">No Image</span>
                                @endif
                            </td>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ number_format($employee->salary, 2) }}</td>
                            <td>{{ $employee->cnic ?? '-' }}</td>
                            <td>{{ $employee->created_at->format('d-m-Y') }}</td>
                            <td>{{ $employee->updated_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="/employees/{{ $employee->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                <button onclick="confirmDelete('/employees/{{ $employee->id }}', '/employees', 'Delete this employee?')" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            {{ $employees->links() }}
        @endif
    </div>
@endsection
