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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('reference_number')->unique();
            $table->string('pickup_location');
            $table->string('status')->default('pending');
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
