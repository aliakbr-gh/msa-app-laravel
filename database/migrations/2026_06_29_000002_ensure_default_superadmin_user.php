<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['name' => 'admin'],
            ['updated_at' => now(), 'created_at' => now()]
        );
        DB::table('roles')->updateOrInsert(
            ['name' => 'cashier'],
            ['updated_at' => now(), 'created_at' => now()]
        );

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');

        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'phone' => null,
                'role_id' => $adminRoleId,
                'password' => Hash::make('admin'),
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
