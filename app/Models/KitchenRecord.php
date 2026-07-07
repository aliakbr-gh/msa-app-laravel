<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenRecord extends Model
{
    protected $fillable = [
        'delivery_date',
        'details',
        'order_shop',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'qty' => 'decimal:2',
        ];
    }
}
