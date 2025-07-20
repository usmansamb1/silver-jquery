<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('v2_wallet_approval_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('request_id')->constrained('v2_wallet_approval_requests')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->string('role')->default('finance');
            $table->string('status')->default('pending');
            $table->text('comment')->nullable();
            $table->integer('step_order')->default(1);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('v2_wallet_approval_steps');
    }
}; 