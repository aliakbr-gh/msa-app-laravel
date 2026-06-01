<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->string('name')->comment('Doctor full name');
            $table->string('specialization')->nullable()->comment('Medical specialty');
            $table->string('phone')->nullable()->comment('Contact number');
            $table->time('timing_from')->nullable()->comment('Start availability time');
            $table->time('timing_to')->nullable()->comment('End availability time');
            $table->decimal('fee', 10, 2)->default(0)->comment('Consultation fee');
            $table->boolean('is_active')->default(true)->comment('Doctor active status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
