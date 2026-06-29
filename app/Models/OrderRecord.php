<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRecord extends Model
{
    protected $fillable = [
        'order_date',
        'order_delivery_date',
        'book_no',
        'deg_qty',
        'customer_name',
        'mobile_number',
        'details',
        'total_amount',
        'advance_received',
        'balance',
        'pending_payment',
        'comments',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'order_delivery_date' => 'date',
            'total_amount' => 'decimal:2',
            'advance_received' => 'decimal:2',
            'balance' => 'decimal:2',
            'pending_payment' => 'decimal:2',
        ];
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
}
