<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_modules', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title')->unique();
            $table->boolean('has_qty')->default(false);
            $table->string('qty_label')->nullable();
            $table->string('rate_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        foreach ([
            ['title' => 'Roti', 'has_qty' => false, 'qty_label' => null, 'rate_label' => null],
            ['title' => 'Beef', 'has_qty' => true, 'qty_label' => 'Qty (Kg)', 'rate_label' => 'Beef Rate'],
            ['title' => 'Chicken 1', 'has_qty' => true, 'qty_label' => 'Weight (Kg)', 'rate_label' => 'Farm Rate'],
            ['title' => 'Chicken 2', 'has_qty' => true, 'qty_label' => 'Weight (Kg)', 'rate_label' => 'Farm Rate'],
        ] as $module) {
            DB::table('ledger_modules')->insert([
                'slug' => Str::slug($module['title']),
                'title' => $module['title'],
                'has_qty' => $module['has_qty'],
                'qty_label' => $module['qty_label'],
                'rate_label' => $module['rate_label'],
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (['superadmin', 'admin', 'cashier'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $roles = DB::table('roles')->whereIn('name', ['superadmin', 'admin', 'cashier'])->pluck('id', 'name');

        foreach ([
            ['username' => 'superadmin', 'password' => 'superadmin', 'role' => 'superadmin'],
            ['username' => 'user', 'password' => 'user', 'role' => 'admin'],
            ['username' => 'cashier', 'password' => 'cashier', 'role' => 'cashier'],
        ] as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                [
                    'phone' => null,
                    'role_id' => $roles[$user['role']],
                    'password' => Hash::make($user['password']),
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_modules');
    }
};
