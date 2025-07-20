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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained('wallets')->onDelete('no action');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->uuidMorphs('reference'); // Polymorphic relationship (Payment, ServiceBooking, etc.)
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('type'); // deposit, withdrawal, service_payment, refund, adjustment
            $table->string('status')->default('completed'); // completed, pending, failed, reversed
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('wallet_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
}; 