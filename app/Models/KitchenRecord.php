<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenRecord extends Model
{
    protected $fillable = [
        'details',
        'order_shop',
        'qty',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
        ];
    }
}
