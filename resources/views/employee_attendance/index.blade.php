@extends('layout.layout')

@section('title', 'Employee Attendance')
@section('page-title', 'Employee Attendance')

@section('content')
    <div class="container mt-4">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="date" name="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Load Date</button>
            </div>
        </form>

        <form onsubmit="saveAttendance(event)">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $date }}">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Present</th>
                            <th>Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            @php($status = $attendances[$employee->id] ?? 'present')
                            <tr>
                                <td>{{ $employee->full_name }}</td>
                                <td><input type="radio" name="attendance[{{ $employee->id }}]" value="present" {{ $status === 'present' ? 'checked' : '' }}></td>
                                <td><input type="radio" name="attendance[{{ $employee->id }}]" value="absent" {{ $status === 'absent' ? 'checked' : '' }}></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button class="btn btn-success">Save Attendance</button>
        </form>

        <div class="mt-3">{{ $employees->links() }}</div>
    </div>

    <script>
        function saveAttendance(e) {
            e.preventDefault();
            $.ajax({
                url: '/employees/attendance',
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
