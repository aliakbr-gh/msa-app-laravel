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
        Schema::create('patients', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->string('name')->comment('Patient full name');
            $table->integer('age')->comment('Patient age in years');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Gender of patient');
            $table->string('phone')->nullable()->comment('Contact number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
