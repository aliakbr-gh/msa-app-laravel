@extends('layout.layout')

@section('title', 'Salary Payments')
@section('page-title', 'Salary Payments')

@section('content')
    @php($role = auth()->user()->role?->name)
    <div class="container mt-4">
        @if (in_array($role, ['superadmin', 'admin']))
        <form onsubmit="saveSalaryPayment(event)" class="row g-2 align-items-end mb-4">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select" required>
                    <option value="">Select Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Paid On</label>
                <input type="date" name="paid_on" value="{{ now()->toDateString() }}" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Notes</label>
                <input type="text" name="notes" class="form-control">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100">Add Payment</button>
            </div>
        </form>
        @endif

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-2"><a href="/employees/salaries" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>

        @include('partials.per-page', ['paginator' => $payments])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_on->format('d-m-Y') }}</td>
                            <td>{{ $payment->employee->full_name ?? '-' }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->notes ?? '-' }}</td>
                            <td>{{ $payment->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No salary payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            {{ $payments->links() }}
        @endif
    </div>

    @if (in_array($role, ['superadmin', 'admin']))
    <script>
        function saveSalaryPayment(e) {
            e.preventDefault();
            $.ajax({
                url: '/employees/salaries',
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/employees/salaries');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
    @endif
@endsection
