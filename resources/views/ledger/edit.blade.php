@extends('layout.layout')

@section('title', 'Edit ' . $config['title'])
@section('page-title', 'Edit ' . $config['title'])

@section('content')
    <form onsubmit="updateEntry(event, {{ $entry->id }})" class="mx-auto" style="max-width: 560px;">
        @csrf
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="entry_date" value="{{ $entry->entry_date->format('Y-m-d') }}" class="form-control" required>
        </div>

        @if ($config['has_qty'])
            <div class="mb-3">
                <label class="form-label">{{ $config['qty_label'] }}</label>
                <input type="number" step="0.01" name="qty" id="qty" value="{{ $entry->qty }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ $config['rate_label'] }}</label>
                <input type="number" step="0.01" name="rate" id="rate" value="{{ $entry->rate }}" class="form-control" required>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" value="{{ $entry->description }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" value="{{ $entry->amount }}" class="form-control" required>
        </div>
        @if ($entry->opening_balance !== null)
            <div class="mb-3">
                <label class="form-label">Opening Remaining Balance</label>
                <input type="number" step="0.01" name="opening_balance" value="{{ $entry->opening_balance }}" class="form-control">
            </div>
        @endif
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Update Record</button>
            <a href="/{{ $module }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        $('#qty, #rate').on('input', function() {
            const qty = Number($('#qty').val() || 0);
            const rate = Number($('#rate').val() || 0);
            if (qty && rate) $('#amount').val((qty * rate).toFixed(2));
        });

        function updateEntry(e, id) {
            e.preventDefault();
            $.ajax({
                url: `/{{ $module }}/${id}`,
                method: 'PATCH',
                data: $(e.target).serialize(),
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
