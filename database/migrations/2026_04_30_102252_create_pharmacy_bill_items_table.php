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
        Schema::create('pharmacy_bill_items', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('bill_id')->constrained('pharmacy_bills')->onDelete('cascade')->comment('Related bill');
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade')->comment('Medicine reference');
            $table->integer('quantity')->comment('Quantity sold');
            $table->decimal('price', 10, 2)->comment('Price per unit at time of sale');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_bill_items');
    }
};
