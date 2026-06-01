<?php

namespace App\Http\Controllers;

use App\Helpers\APIResponse;
use App\Models\OrderRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderRecordController extends Controller
{
    public function index()
    {
        $orders = OrderRecord::latest('order_date')->latest('id')->paginate(10);

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
        $data = $this->withCalculatedPayments($data);

        $order = OrderRecord::create($data);

        return APIResponse::success('Order created successfully', $order, 201);
    }

    public function edit(OrderRecord $order)
    {
        return response()->view('orders.edit', compact('order'));
    }

    public function update(Request $request, OrderRecord $order)
    {
        $data = $this->validatedData($request, $order->id);
        $order->update($this->withCalculatedPayments($data));

        return APIResponse::success('Order updated successfully', $order->fresh());
    }

    public function destroy(OrderRecord $order)
    {
        $order->delete();

        return APIResponse::success('Order deleted successfully');
    }

    public function invoice(OrderRecord $order)
    {
        return response()->view('orders.invoice', compact('order'));
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
            'pending_payment' => 'nullable|numeric|min:0',
            'comments' => 'nullable|string',
            'status' => 'required|string|max:50',
        ]);
    }

    private function withCalculatedPayments(array $data): array
    {
        $data['advance_received'] = $data['advance_received'] ?? 0;
        $data['balance'] = max(0, (float) $data['total_amount'] - (float) $data['advance_received']);
        $data['pending_payment'] = $data['pending_payment'] ?? $data['balance'];

        return $data;
    }
}
