<?php

declare(strict_types=1);

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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('mobile', 20)->index();
            $table->text('message');
            $table->string('provider', 50)->default('connectsaudi');
            $table->enum('status', ['pending', 'sent', 'failed', 'retry'])->default('pending');
            $table->string('purpose', 100)->nullable()->index(); // otp, notification, marketing, etc.
            $table->string('reference_id')->nullable()->index(); // For tracking related records
            $table->json('request_data')->nullable(); // Store API request data
            $table->json('response_data')->nullable(); // Store API response
            $table->string('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['purpose', 'created_at']);
            $table->index(['mobile', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
}; 