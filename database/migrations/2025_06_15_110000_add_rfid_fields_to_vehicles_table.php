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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('rfid_number')->nullable()->after('year');
            $table->decimal('rfid_balance', 10, 2)->default(0.00)->after('rfid_number');
            $table->enum('rfid_status', ['active', 'inactive', 'pending', 'suspended'])->nullable()->after('rfid_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['rfid_number', 'rfid_balance', 'rfid_status']);
        });
    }
}; 