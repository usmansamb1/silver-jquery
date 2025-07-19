<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            // Check if the column already exists
            if (!Schema::hasColumn('v2_wallet_approval_requests', 'payment_id')) {
                $table->uuid('payment_id')->nullable()->after('user_id');
                $table->foreign('payment_id')
                      ->references('id')
                      ->on('payments')
                      ->onDelete('no action');
            }
        });
    }

    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });
    }
}; 