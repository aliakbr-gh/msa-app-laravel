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
        Schema::create('pharmacy_bills', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade')->comment('Patient reference');
            $table->foreignId('doctor_slip_id')->nullable()->constrained()->onDelete('cascade')->comment('Related doctor slip');
            $table->foreignId('created_by')->constrained('users')->comment('Pharmacist who created bill');
            $table->decimal('total_amount', 10, 2)->default(0)->comment('Total bill amount');
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid')->comment('Payment status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_bills');
    }
};
