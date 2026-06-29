<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const FIXED_ROLES = ['superadmin', 'admin', 'cashier'];

    protected $fillable = [
        'name'
    ];

    public static function fixed()
    {
        foreach (self::FIXED_ROLES as $role) {
            self::firstOrCreate(['name' => $role]);
        }

        return self::whereIn('name', self::FIXED_ROLES)
            ->orderByRaw("CASE name WHEN 'superadmin' THEN 1 WHEN 'admin' THEN 2 WHEN 'cashier' THEN 3 ELSE 4 END")
            ->get();
    }
}
