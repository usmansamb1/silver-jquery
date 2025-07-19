<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('v2_wallet_approval_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->string('status')->default('pending');
            $table->integer('current_step')->default(1);
            $table->string('reference_no')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('v2_wallet_approval_requests');
    }
}; 