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
        // First, backup any existing data just in case
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type VARCHAR(50)");
        
        // Now update to include all payment types
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('credit_card', 'bank_transfer', 'bank_guarantee', 'bank_lc') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values
        // First convert any bank_guarantee or bank_lc to bank_transfer
        DB::statement("UPDATE payments SET payment_type = 'bank_transfer' WHERE payment_type IN ('bank_guarantee', 'bank_lc')");
        
        // Then change the column back
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('credit_card', 'bank_transfer') NOT NULL");
    }
};