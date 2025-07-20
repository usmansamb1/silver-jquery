<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('v2_wallet_approval_requests', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            if (Schema::hasColumn('v2_wallet_approval_requests', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
