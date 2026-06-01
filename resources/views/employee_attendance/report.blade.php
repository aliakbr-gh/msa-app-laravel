@extends('layout.layout')

@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@section('content')
    <div class="container-fluid mt-4">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="month" name="month" value="{{ $month }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Load Report</button>
            </div>
            <div class="col-md-2">
                <a href="/employees/attendance?date={{ $month }}-01" class="btn btn-secondary w-100">Back</a>
            </div>
        </form>

        @include('partials.per-page', ['paginator' => $employees])

        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th class="text-start">Employee</th>
                        @foreach ($days as $day)
                            <th>{{ $day->format('d') }}</th>
                        @endforeach
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        @php
                            $attendanceByDate = $employee->attendances->keyBy(fn ($attendance) => $attendance->attendance_date->format('Y-m-d'));
                            $presentCount = $employee->attendances->where('status', 'present')->count();
                            $absentCount = $employee->attendances->where('status', 'absent')->count();
                        @endphp
                        <tr>
                            <td class="text-start fw-semibold">{{ $employee->full_name }}</td>
                            @foreach ($days as $day)
                                @php($attendance = $attendanceByDate->get($day->format('Y-m-d')))
                                <td>
                                    @if (($attendance?->status) === 'present')
                                        <span class="badge bg-success">P</span>
                                    @elseif (($attendance?->status) === 'absent')
                                        <span class="badge bg-danger">A</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-success fw-semibold">{{ $presentCount }}</td>
                            <td class="text-danger fw-semibold">{{ $absentCount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            {{ $employees->links() }}
        @endif
    </div>
@endsection
