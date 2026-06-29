@extends('layout.layout')

@section('title', 'Restaurant ERP Kitchen')
@section('page-title', 'Restaurant ERP Kitchen')

@section('content')
    @php($role = auth()->user()->role?->name)
    <div class="container mt-4">
        <div class="mb-3">
            @if (in_array($role, ['superadmin', 'admin']))
                <a href="/kitchen/create" class="btn btn-primary">Create Kitchen Record</a>
            @endif
        </div>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3"><input type="date" name="delivery_date" value="{{ $deliveryDate }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Search</button></div>
            <div class="col-md-1"><a href="/kitchen" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>

        <h5>Deliveries for {{ \Carbon\Carbon::parse($deliveryDate)->format('d-m-Y') }}</h5>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Book No</th>
                        <th>Customer</th>
                        <th>Deg Qty</th>
                        <th>Details</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todayOrders as $order)
                        <tr>
                            <td>{{ $order->book_no }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->deg_qty }}</td>
                            <td class="text-start">{!! nl2br(e($order->details ?? '-')) !!}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($order->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No deliveries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h5>Kitchen Records</h5>
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
                                @if ($role === 'superadmin')
                                    <a href="/kitchen/{{ $record->id }}/edit" class="btn btn-sm btn-primary">Update</a>
                                    <button onclick="confirmDelete('/kitchen/{{ $record->id }}', '/kitchen', 'Delete this kitchen record?')" class="btn btn-sm btn-danger">Delete</button>
                                @else
                                    <span class="text-muted">Read only</span>
                                @endif
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
@endsection
