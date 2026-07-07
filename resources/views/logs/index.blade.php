@extends('layout.layout')

@section('title', 'Logs')
@section('page-title', 'Activity Logs')

@section('content')
    <div class="container mt-4">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-2"><a href="/logs" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>

        @include('partials.per-page', ['paginator' => $logs])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $log->username ?? '-' }}</td>
                            <td>{{ $log->role_name ?? '-' }}</td>
                            <td>{{ $log->module ?? '-' }}</td>
                            <td><span class="badge bg-info text-dark">{{ strtoupper($log->action) }}</span></td>
                            <td class="text-start">{{ $log->description ?? '-' }}</td>
                            <td>{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            {{ $logs->links() }}
        @endif
    </div>
@endsection
