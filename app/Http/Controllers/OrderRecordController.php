<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\OrderRecord;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderRecordController extends Controller
{
    public function index(Request $request)
    {
        $orders = $this->applyDateRange(OrderRecord::with('payments')->latest('order_date')->latest('id'), $request, 'order_date')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->view('orders.index', compact('orders'));
    }

    public function create()
    {
        $nextBookNo = ((int) OrderRecord::max('book_no')) + 1;

        return response()->view('orders.create', compact('nextBookNo'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $advance = (float) ($data['advance_received'] ?? 0);
        $data = $this->withCalculatedPayments($data, $advance);

        $order = DB::transaction(function () use ($data, $advance) {
            $order = OrderRecord::create($data);
            if ($advance > 0) {
                $order->payments()->create([
                    'paid_on' => $order->order_date,
                    'amount' => $advance,
                    'notes' => 'Advance received',
                ]);
            }

            return $this->syncOrderBalance($order);
        });

        return APIResponse::success('Order created successfully', $order, 201);
    }

    public function edit(OrderRecord $order)
    {
        return response()->view('orders.edit', compact('order'));
    }

    public function update(Request $request, OrderRecord $order)
    {
        $data = $this->validatedData($request, $order->id);
        $order->update($this->withCalculatedPayments($data, (float) $order->payments()->sum('amount')));
        $this->syncOrderBalance($order);

        return APIResponse::success('Order updated successfully', $order->fresh());
    }

    public function destroy(OrderRecord $order)
    {
        $order->delete();

        return APIResponse::success('Order deleted successfully');
    }

    public function invoice(OrderRecord $order)
    {
        $order->load('payments');

        return response()->view('orders.invoice', compact('order'));
    }

    public function pay(Request $request, OrderRecord $order)
    {
        $data = $request->validate([
            'paid_on' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $paid = (float) $order->payments()->sum('amount');
        abort_if($paid + (float) $data['amount'] > (float) $order->total_amount, 422, 'Payment cannot exceed order balance.');

        $payment = DB::transaction(function () use ($order, $data) {
            $payment = $order->payments()->create($data);
            $this->syncOrderBalance($order);

            return $payment;
        });

        return APIResponse::success('Order payment recorded successfully', $payment, 201);
    }

    private function validatedData(Request $request, ?int $orderId = null): array
    {
        return $request->validate([
            'order_date' => 'required|date',
            'order_delivery_date' => 'nullable|date',
            'book_no' => ['required', 'integer', 'min:1', Rule::unique('order_records', 'book_no')->ignore($orderId)],
            'deg_qty' => 'required|integer|min:0',
            'customer_name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:30',
            'details' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'advance_received' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
            'status' => 'required|string|max:50',
        ]);
    }

    private function withCalculatedPayments(array $data, float $paidAmount): array
    {
        $data['advance_received'] = $paidAmount;
        $data['balance'] = max(0, (float) $data['total_amount'] - $paidAmount);
        $data['pending_payment'] = $data['balance'];

        return $data;
    }

    private function syncOrderBalance(OrderRecord $order): OrderRecord
    {
        $paid = (float) $order->payments()->sum('amount');
        $balance = max(0, (float) $order->total_amount - $paid);

        $order->update([
            'advance_received' => $paid,
            'balance' => $balance,
            'pending_payment' => $balance,
        ]);

        return $order->fresh('payments');
    }
}
