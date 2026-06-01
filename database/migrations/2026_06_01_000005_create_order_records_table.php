<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_records', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->date('order_delivery_date')->nullable();
            $table->unsignedBigInteger('book_no')->unique();
            $table->unsignedInteger('deg_qty')->default(0);
            $table->string('customer_name');
            $table->string('mobile_number')->nullable();
            $table->text('details')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('advance_received', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('pending_payment', 12, 2)->default(0);
            $table->text('comments')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_records');
    }
};
