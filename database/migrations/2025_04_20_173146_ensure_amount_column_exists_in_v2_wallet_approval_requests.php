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
        if (!Schema::hasColumn('v2_wallet_approval_requests', 'amount')) {
            Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
                $table->decimal('amount', 10, 2)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't drop the column in down() to prevent data loss since this is a fix migration
    }
};
