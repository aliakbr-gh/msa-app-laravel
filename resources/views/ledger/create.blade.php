@extends('layout.layout')

@section('title', 'Create ' . $config['title'])
@section('page-title', 'Create ' . $config['title'])

@section('content')
    <form onsubmit="saveEntry(event)" class="mx-auto" style="max-width: 560px;">
        @csrf
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="entry_date" value="{{ now()->toDateString() }}" class="form-control" required>
        </div>

        @if ($config['has_qty'])
            <div class="mb-3">
                <label class="form-label">{{ $config['qty_label'] }}</label>
                <input type="number" step="0.01" name="qty" id="qty" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ $config['rate_label'] }}</label>
                <input type="number" step="0.01" name="rate" id="rate" class="form-control" required>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" placeholder="Paid or purchase detail" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
        </div>
        @if (! $lastEntry)
            <div class="mb-3">
                <label class="form-label">Opening Remaining Balance</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control" required>
            </div>
        @endif
        <div class="d-flex gap-2">
            <button class="btn btn-primary">Create Record</button>
            <a href="/{{ $module }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        $('#qty, #rate').on('input', function() {
            const qty = Number($('#qty').val() || 0);
            const rate = Number($('#rate').val() || 0);
            if (qty && rate) $('#amount').val((qty * rate).toFixed(2));
        });

        function saveEntry(e) {
            e.preventDefault();
            $.ajax({
                url: '/{{ $module }}/create',
                method: 'POST',
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
