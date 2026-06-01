@extends('layout.layout')

@section('title', 'Invoice #' . $order->book_no)
@section('page-title', 'Invoice #' . $order->book_no)

@section('content')
    <div class="container mt-4">
        <div class="d-flex gap-2 mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">Print / Save PDF</button>
            <button onclick="copyInvoiceImage()" class="btn btn-success">Copy as Image</button>
            <a href="/orders" class="btn btn-secondary">Back</a>
        </div>

        <div id="invoiceCard" class="bg-white text-dark border mx-auto p-4" style="max-width: 820px;">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                <div>
                    @if (file_exists(public_path('images/msa-logo.png')))
                        <img src="{{ asset('images/msa-logo.png') }}" alt="MSA Logo" style="max-height: 80px;">
                    @elseif (file_exists(public_path('images/msa-logo.svg')))
                        <img src="{{ asset('images/msa-logo.svg') }}" alt="MSA Logo" style="max-height: 80px;">
                    @else
                        <h2 class="fw-bold mb-0">MSA Foods</h2>
                    @endif
                    <div class="text-muted">Order Invoice</div>
                </div>
                <div class="text-end">
                    <h4 class="mb-1">Book No: {{ $order->book_no }}</h4>
                    <div>Order: {{ $order->order_date->format('d-m-Y') }}</div>
                    <div>Delivery: {{ $order->order_delivery_date?->format('d-m-Y') ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Customer</strong>
                    <div>{{ $order->customer_name }}</div>
                    <div>{{ $order->mobile_number ?? '-' }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <strong>Status</strong>
                    <div>{{ ucfirst($order->status) }}</div>
                    <div>Deg Qty: {{ $order->deg_qty }}</div>
                </div>
            </div>

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th style="width: 28%;">Details</th>
                        <td>{!! nl2br(e($order->details ?? '-')) !!}</td>
                    </tr>
                    <tr>
                        <th>Comments</th>
                        <td>{!! nl2br(e($order->comments ?? '-')) !!}</td>
                    </tr>
                </tbody>
            </table>

            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Amount</th>
                            <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Advance Received</th>
                            <td class="text-end">{{ number_format($order->advance_received, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td class="text-end fw-bold">{{ number_format($order->balance, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Pending Payment</th>
                            <td class="text-end fw-bold">{{ number_format($order->pending_payment, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .navbar, .no-print, #themeBtn, #toastContainer {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            #invoiceCard {
                border: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        async function copyInvoiceImage() {
            try {
                const canvas = await html2canvas(document.getElementById('invoiceCard'), { scale: 2 });
                canvas.toBlob(async function(blob) {
                    await navigator.clipboard.write([new ClipboardItem({ 'image/png': blob })]);
                    showToast('Invoice image copied');
                });
            } catch (error) {
                showToast('Copy failed. Try screenshot or print instead.', 'error');
            }
        }
    </script>
@endsection
