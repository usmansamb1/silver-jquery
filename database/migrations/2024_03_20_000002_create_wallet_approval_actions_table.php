<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_approval_actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('no action');
            $table->string('action')->comment('approve, reject');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('request_id')
                  ->references('id')
                  ->on('v2_wallet_approval_requests')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_approval_actions');
    }
}; 