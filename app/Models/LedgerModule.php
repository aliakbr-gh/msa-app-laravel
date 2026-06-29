<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LedgerModule extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'has_qty',
        'qty_label',
        'rate_label',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'has_qty' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public static function slugFromTitle(string $title): string
    {
        return Str::slug($title);
    }
}
