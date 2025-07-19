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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('log_name')->nullable();
            $table->string('description');
            $table->uuid('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->uuid('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->json('properties')->nullable();
            $table->string('event')->nullable(); // login, wallet_recharge, service_booking, profile_update
            $table->string('level')->default('info'); // debug, info, notice, warning, error, critical, alert, emergency
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('log_name');
            $table->index('event');
            $table->index('level');
            $table->index(['subject_id', 'subject_type']);
            $table->index(['causer_id', 'causer_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
