<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(['name' => 'admin'], ['created_at' => now(), 'updated_at' => now()]);
        DB::table('roles')->updateOrInsert(['name' => 'cashier'], ['created_at' => now(), 'updated_at' => now()]);
        DB::table('roles')->whereNotIn('name', ['admin', 'cashier'])->delete();

        Schema::create('employee_salary_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('salary', 12, 2)->default(0);
            $table->date('effective_from');
            $table->timestamps();

            $table->index(['employee_id', 'effective_from']);
        });

        DB::table('employees')->orderBy('id')->chunk(100, function ($employees) {
            foreach ($employees as $employee) {
                DB::table('employee_salary_histories')->insert([
                    'employee_id' => $employee->id,
                    'salary' => $employee->salary,
                    'effective_from' => $employee->created_at ? substr($employee->created_at, 0, 10) : now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_record_id')->constrained('order_records')->cascadeOnDelete();
            $table->date('paid_on');
            $table->decimal('amount', 12, 2);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['order_record_id', 'paid_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('employee_salary_histories');
    }
};
