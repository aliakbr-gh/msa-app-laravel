<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_record_id',
        'paid_on',
        'amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(OrderRecord::class, 'order_record_id');
    }
}
