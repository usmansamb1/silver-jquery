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
        // For MySQL, we don't need to worry about specific constraint names
        // MySQL handles this differently, so we'll just ensure the column is properly set
        
        Schema::table('payments', function (Blueprint $table) {
            // Ensure the payment_type column allows all required values
            $table->string('payment_type')->change();
        });
        
        // For MySQL 8.0+, we could add a check constraint like this:
        // DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_type_check 
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
