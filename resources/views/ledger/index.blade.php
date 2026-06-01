@extends('layout.layout')

@section('title', $config['title'])
@section('page-title', $config['title'])

@section('content')
    <div class="container mt-4">
        <div class="mb-3">
            <a href="/{{ $module }}/create" class="btn btn-primary">Create Record</a>
        </div>

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
                                <a href="/{{ $module }}/{{ $entry->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                <button onclick="deleteEntry({{ $entry->id }})" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $config['has_qty'] ? 10 : 8 }}">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $entries->links() }}
    </div>

    <script>
        function deleteEntry(id) {
            if (!confirm('Delete this record?')) return;
            $.ajax({
                url: `/{{ $module }}/${id}`,
                method: 'DELETE',
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/{{ $module }}');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
