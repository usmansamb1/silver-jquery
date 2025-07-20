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
        Schema::table('rfid_transactions', function (Blueprint $table) {
            $table->string('hyperpay_transaction_id')->nullable()->after('transaction_reference');
            $table->index('hyperpay_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfid_transactions', function (Blueprint $table) {
            $table->dropIndex(['hyperpay_transaction_id']);
            $table->dropColumn('hyperpay_transaction_id');
        });
    }
};
