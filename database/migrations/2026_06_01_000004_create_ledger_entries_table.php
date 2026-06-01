<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->date('entry_date');
            $table->decimal('qty', 10, 2)->nullable();
            $table->decimal('rate', 12, 2)->nullable();
            $table->string('description');
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('opening_balance', 12, 2)->nullable();
            $table->decimal('remaining_balance', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['module', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
