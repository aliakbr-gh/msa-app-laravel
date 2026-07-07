<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const FIXED_ROLES = ['admin', 'cashier'];

    protected $fillable = [
        'name'
    ];

    public static function fixed()
    {
        foreach (self::FIXED_ROLES as $role) {
            self::firstOrCreate(['name' => $role]);
        }

        return self::whereIn('name', self::FIXED_ROLES)
            ->orderByRaw("CASE name WHEN 'admin' THEN 1 WHEN 'cashier' THEN 2 ELSE 3 END")
            ->get();
    }
}
