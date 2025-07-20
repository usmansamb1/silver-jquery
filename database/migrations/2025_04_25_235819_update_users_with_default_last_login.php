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
        // First ensure the column exists
        if (!Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable()->after('otp_created_at');
            });
        }
        
        // Update all users that have NULL last_login_at to have their created_at as the last_login_at
        DB::statement('UPDATE users SET last_login_at = created_at WHERE last_login_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to remove data in down migrations
    }
};
