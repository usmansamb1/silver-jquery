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
        Schema::create('rfid_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('source_vehicle_id');
            $table->uuid('target_vehicle_id');
            $table->string('rfid_number');
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->json('transfer_details')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('source_vehicle_id')->references('id')->on('vehicles')->onDelete('no action');
            $table->foreign('target_vehicle_id')->references('id')->on('vehicles')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_transfers');
    }
}; 