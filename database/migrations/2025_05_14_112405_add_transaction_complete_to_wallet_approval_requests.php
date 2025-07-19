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
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            // Add a boolean column to track if the transaction has been completed
            $table->boolean('transaction_complete')->default(false)->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            $table->dropColumn('transaction_complete');
        });
    }
};
