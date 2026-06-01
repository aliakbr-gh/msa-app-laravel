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
        Schema::create('doctor_slips', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade')->comment('Reference to patient');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade')->comment('Assigned doctor');
            $table->foreignId('created_by')->constrained('users')->comment('Receptionist who created slip');
            $table->string('token_no')->nullable()->comment('Queue token number');
            $table->date('visit_date')->comment('Date of visit');
            $table->enum('payment_status', ['pending', 'paid', 'unpaid'])->default('pending')->comment('Payment status of consultation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_slips');
    }
};
