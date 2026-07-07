@extends('layout.layout')

@section('title', 'Modules')
@section('page-title', 'Modules')

@section('content')
    @php($role = auth()->user()->role?->name)
    <div class="container mt-4">
        <div class="mb-3">
            @if ($role === 'admin')
                <a href="/modules/create" class="btn btn-primary">Create Module</a>
            @endif
        </div>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-2"><a href="/modules" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
        @include('partials.per-page', ['paginator' => $modules])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Has Qty</th>
                        <th>Qty Label</th>
                        <th>Rate Label</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modules as $module)
                        <tr>
                            <td>{{ $module->title }}</td>
                            <td><a href="/{{ $module->slug }}">/{{ $module->slug }}</a></td>
                            <td>{{ $module->has_qty ? 'Yes' : 'No' }}</td>
                            <td>{{ $module->qty_label ?? '-' }}</td>
                            <td>{{ $module->rate_label ?? '-' }}</td>
                            <td><span class="badge {{ $module->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $module->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                @if ($role === 'admin')
                                    <a href="/modules/{{ $module->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                    <button onclick="confirmDelete('/modules/{{ $module->id }}', '/modules', 'Delete this module? Existing records will remain hidden unless recreated.')" class="btn btn-sm btn-danger">Delete</button>
                                @else
                                    <span class="text-muted">Read only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No modules found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($modules->hasPages())
            {{ $modules->links() }}
        @endif
    </div>
@endsection
