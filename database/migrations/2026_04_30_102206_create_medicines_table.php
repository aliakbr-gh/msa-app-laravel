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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->string('name')->comment('Medicine name');
            $table->string('batch_no')->nullable()->comment('Batch number');
            $table->integer('quantity')->default(0)->comment('Available stock quantity');
            $table->decimal('purchase_price', 10, 2)->default(0)->comment('Cost price');
            $table->decimal('sale_price', 10, 2)->default(0)->comment('Selling price');
            $table->date('expiry_date')->nullable()->comment('Expiry date of medicine');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
