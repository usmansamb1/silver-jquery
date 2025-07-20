<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('v2_wallet_approval_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('current_step');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            if (Schema::hasColumn('v2_wallet_approval_requests', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
}; 