@extends('layout.layout')

@section('title', $config['title'])
@section('page-title', $config['title'])

@section('content')
    @php($role = auth()->user()->role?->name)
    <div class="container mt-4">
        <div class="mb-3">
            @if (in_array($role, ['superadmin', 'admin']))
                <a href="/{{ $module }}/create" class="btn btn-primary">Create Record</a>
            @endif
            @if (in_array($role, ['superadmin', 'cashier']))
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#payModal">Pay</button>
            @endif
        </div>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-2"><a href="/{{ $module }}" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
        @include('partials.per-page', ['paginator' => $entries])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Date</th>
                        @if ($config['has_qty'])
                            <th>{{ $config['qty_label'] }}</th>
                            <th>{{ $config['rate_label'] }}</th>
                        @endif
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Remaining Balance</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        <tr>
                            <td>{{ $entries->firstItem() + $loop->index }}</td>
                            <td>{{ $entry->entry_date->format('d-m-Y') }}</td>
                            @if ($config['has_qty'])
                                <td>{{ number_format($entry->qty, 2) }}</td>
                                <td>{{ number_format($entry->rate, 2) }}</td>
                            @endif
                            <td>
                                <span class="badge {{ strtolower($entry->description) === 'paid' ? 'bg-success' : 'bg-info text-dark' }}">
                                    {{ $entry->description }}
                                </span>
                            </td>
                            <td>{{ number_format($entry->amount, 2) }}</td>
                            <td>{{ number_format($entry->remaining_balance, 2) }}</td>
                            <td>{{ $entry->created_at->format('d-m-Y') }}</td>
                            <td>{{ $entry->updated_at->format('d-m-Y') }}</td>
                            <td>
                                @if ($role === 'superadmin')
                                    <a href="/{{ $module }}/{{ $entry->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                    <button onclick="confirmDelete('/{{ $module }}/{{ $entry->id }}', '/{{ $module }}', 'Delete this record?')" class="btn btn-sm btn-danger">Delete</button>
                                @else
                                    <span class="text-muted">Read only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $config['has_qty'] ? 10 : 8 }}">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            {{ $entries->links() }}
        @endif
    </div>

    @if (in_array($role, ['superadmin', 'cashier']))
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" onsubmit="savePayment(event)">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Pay {{ $config['title'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="entry_date" value="{{ now()->toDateString() }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Pay</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function savePayment(e) {
            e.preventDefault();
            $.ajax({
                url: '/{{ $module }}/pay',
                method: 'POST',
                data: $(e.target).serialize(),
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/{{ $module }}');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message || 'Payment failed', 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
