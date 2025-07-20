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
            // Modify current_step to allow NULL values
            $table->integer('current_step')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('v2_wallet_approval_requests', function (Blueprint $table) {
            // Revert back to not allowing NULL
            $table->integer('current_step')->nullable(false)->change();
        });
    }
};
