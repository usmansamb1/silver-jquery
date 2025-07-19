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
        Schema::table('payments', function (Blueprint $table) {
            // Add hyperpay_transaction_id column with unique constraint
            $table->string('hyperpay_transaction_id')->nullable()->unique()->after('notes');
            $table->index(['user_id', 'hyperpay_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'hyperpay_transaction_id']);
            $table->dropColumn('hyperpay_transaction_id');
        });
    }
}; 