@extends('layout.layout')

@section('title', 'MSA Kitchen')
@section('page-title', 'MSA Kitchen')

@section('content')
    <div class="container mt-4">
        <div class="mb-3">
            <a href="/kitchen/create" class="btn btn-primary">Create Kitchen Record</a>
        </div>
        @include('partials.per-page', ['paginator' => $records])

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Details</th>
                        <th>Order/Shop</th>
                        <th>Qty</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $records->firstItem() + $loop->index }}</td>
                            <td class="text-start">{{ $record->details }}</td>
                            <td>{{ $record->order_shop }}</td>
                            <td>{{ number_format($record->qty, 2) }}</td>
                            <td>{{ $record->created_at->format('d-m-Y') }}</td>
                            <td>{{ $record->updated_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="/kitchen/{{ $record->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                <button onclick="deleteKitchenRecord({{ $record->id }})" class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No kitchen records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            {{ $records->links() }}
        @endif
    </div>

    <script>
        function deleteKitchenRecord(id) {
            if (!confirm('Delete this kitchen record?')) return;
            $.ajax({
                url: `/kitchen/${id}`,
                method: 'DELETE',
                beforeSend: showLoader,
                success: function(response) {
                    showToast(response?.message);
                    redirect('/kitchen');
                },
                error: function(error) {
                    showToast(error?.responseJSON?.message, 'error');
                },
                complete: hideLoader
            });
        }
    </script>
@endsection
