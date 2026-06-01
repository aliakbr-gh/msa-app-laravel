<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = [
        'module',
        'entry_date',
        'qty',
        'rate',
        'description',
        'amount',
        'opening_balance',
        'remaining_balance',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'qty' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
        ];
    }

    public function isPaid(): bool
    {
        return strtolower(trim($this->description)) === 'paid';
    }
}
