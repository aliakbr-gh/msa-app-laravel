<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['name' => 'superadmin'],
            ['updated_at' => now(), 'created_at' => now()]
        );
        DB::table('roles')->updateOrInsert(
            ['name' => 'admin'],
            ['updated_at' => now(), 'created_at' => now()]
        );
        DB::table('roles')->updateOrInsert(
            ['name' => 'cashier'],
            ['updated_at' => now(), 'created_at' => now()]
        );

        $superadminRoleId = DB::table('roles')->where('name', 'superadmin')->value('id');

        DB::table('users')->updateOrInsert(
            ['username' => 'superadmin'],
            [
                'phone' => null,
                'role_id' => $superadminRoleId,
                'password' => Hash::make('superadmin'),
                'is_active' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        //
    }
};
