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
            if (!Schema::hasColumn('v2_wallet_approval_requests', 'current_step')) {
                $table->integer('current_step')->default(1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            $table->dropColumn('current_step');
        });
    }
}; 