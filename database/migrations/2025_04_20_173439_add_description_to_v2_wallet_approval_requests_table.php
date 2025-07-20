<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL compatible approach
        if (!Schema::hasColumn('v2_wallet_approval_requests', 'description')) {
            Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
                $table->text('description')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            if (Schema::hasColumn('v2_wallet_approval_requests', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
