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
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id');
                $table->decimal('amount', 12, 2);
                $table->string('payment_type'); // credit_card, bank_transfer, bank_guarantee, bank_lc
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->text('notes')->nullable();
                $table->text('files')->nullable(); // Stored as JSON
                $table->string('transaction_id')->nullable(); // For external payment references
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the payments table in down() as it might be used by other migrations
        // Schema::dropIfExists('payments');
    }
}; 