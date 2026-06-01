<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form method="GET" class="d-flex align-items-center gap-2">
        @foreach (request()->except(['per_page', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <label class="form-label mb-0">Show</label>
        <select name="per_page" class="form-select form-select-sm" style="width: 90px;" onchange="this.form.submit()">
            @foreach ([10, 25, 50, 100] as $size)
                <option value="{{ $size }}" {{ request('per_page', $defaultPerPage ?? 10) == $size ? 'selected' : '' }}>
                    {{ $size }}
                </option>
            @endforeach
        </select>
        <span class="text-muted">per page</span>
    </form>

    @isset($paginator)
        <div class="text-muted small">
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} records
        </div>
    @endisset
</div>
