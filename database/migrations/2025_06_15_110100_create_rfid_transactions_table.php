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
        Schema::create('rfid_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->uuid('user_id');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('payment_method')->default('wallet');
            $table->string('transaction_reference')->nullable();
            $table->string('status')->default('completed');
            $table->string('payment_status')->default('paid');
            $table->json('transaction_details')->nullable();
            $table->timestamps();
            
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('no action');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_transactions');
    }
}; 