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
        // For MySQL, we'll modify the column to use ENUM or just skip check constraints
        // MySQL 8.0+ supports check constraints but older versions don't
        // For compatibility, we'll just ensure the column exists and is properly typed
        
        Schema::table('payments', function (Blueprint $table) {
            // Modify the payment_type column to allow the new values
            // MySQL will handle validation at the application level
            $table->string('payment_type')->change();
        });
        
        // Note: Check constraints in MySQL 8.0+ can be added like this:
        // DB::statement("ALTER TABLE payments ADD CONSTRAINT check_payment_type 
        //     CHECK (payment_type IN ('credit_card', 'bank_transfer', 'bank_guarantee', 'bank_lc'))");
        // But for compatibility, we'll handle validation in the application
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For MySQL, we just revert the column change if needed
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_type')->change();
        });
    }
};
