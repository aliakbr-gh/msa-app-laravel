@extends('layout.layout')

@section('title', 'Employee Attendance')
@section('page-title', 'Employee Attendance')

@section('content')
    <div class="container mt-4">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <form method="GET" class="row g-2">
                <div class="col-auto">
                    <input type="date" name="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Load Date</button>
                </div>
            </form>

            <a href="/employees/attendance-report?month={{ \Carbon\Carbon::parse($date)->format('Y-m') }}" class="btn btn-outline-dark">
                Monthly Attendance Report
            </a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Employees</div>
                    <h4 class="mb-0">{{ $summary['total'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Marked</div>
                    <h4 class="mb-0">{{ $summary['marked'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Present</div>
                    <h4 class="mb-0 text-success">{{ $summary['present'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="text-muted">Absent</div>
                    <h4 class="mb-0 text-danger">{{ $summary['absent'] }}</h4>
                </div>
            </div>
        </div>

        <form onsubmit="saveAttendance(event)">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $date }}">

            <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-success" onclick="markAll('present')">Mark Page Present</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="markAll('absent')">Mark Page Absent</button>
                <button type="submit" class="btn btn-sm btn-primary">Save Attendance</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            @php($status = $attendances[$employee->id] ?? null)
                            <tr>
                                <td class="text-start">
                                    <strong>{{ $employee->full_name }}</strong>
                                    <div class="text-muted small">{{ $employee->cnic ?? 'No CNIC' }}</div>
                                </td>
                                <td>{{ number_format($employee->salary, 2) }}</td>
                                <td>
                                    <select name="attendance[{{ $employee->id }}]" class="form-select attendance-status {{ $status === 'absent' ? 'border-danger' : 'border-success' }}">
                                        <option value="present" {{ ($status ?? 'present') === 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                    </select>
                                </td>
                                <td>
                                    @if ($status)
                                        <span class="badge {{ $status === 'present' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">Not Saved Yet</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No employees found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-3">{{ $employees->links() }}</div>
    </div>

    <script>
        function markAll(status) {
            $('.attendance-status').val(status).trigger('change');
        }

        $(document).on('change', '.attendance-status', function() {
            $(this).toggleClass('border-danger', $(this).val() === 'absent');
            $(this).toggleClass('border-success', $(this).val() === 'present');
        });

        function saveAttendance(e) {
            e.preventDefault();
            $.ajax({
                url: '/employees/attendance',
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/employees/attendance?date={{ $date }}');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
