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
        // First, check if the column already exists to avoid SQL errors
        if (!Schema::hasColumn('v2_wallet_approval_requests', 'deleted_at')) {
            Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
