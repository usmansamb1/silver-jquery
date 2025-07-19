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
        // Status table for tracking application statuses
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->default('#FFC107'); // Default color for visual representation
            $table->timestamps();
        });

        // Approval policies/workflows
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('model_type')->nullable(); // For polymorphic relationship - which model uses this workflow
            $table->boolean('notify_by_email')->default(true);
            $table->boolean('notify_by_sms')->default(false);
            $table->timestamps();
        });

        // Approval steps for each workflow (in sequence)
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('approval_workflow_id');
            $table->foreign('approval_workflow_id')
                  ->references('id')
                  ->on('approval_workflows')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->string('name');
            $table->integer('sequence'); // Order of approval
            $table->string('approver_type'); // user, role, department
            $table->string('approver_id'); // ID of user, role, or department
            $table->boolean('is_required')->default(true);
            $table->integer('timeout_hours')->nullable(); // Optional timeout
            $table->timestamps();
        });

        // Approval instances (when a workflow is applied to an item)
        Schema::create('approval_instances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('approval_workflow_id');
            $table->foreign('approval_workflow_id')
                  ->references('id')
                  ->on('approval_workflows')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->uuidMorphs('approvable'); // Polymorphic relationship to the approved item (payment, etc)
            $table->uuid('initiated_by')->nullable();
            $table->foreign('initiated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->enum('status', ['pending', 'in_progress', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Individual approvals for each step
        Schema::create('approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('approval_instance_id');
            $table->foreign('approval_instance_id')
                  ->references('id')
                  ->on('approval_instances')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->uuid('approval_step_id');
            $table->foreign('approval_step_id')
                  ->references('id')
                  ->on('approval_steps')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->enum('action', ['pending', 'approved', 'rejected', 'transferred'])->default('pending');
            $table->text('comments')->nullable();
            $table->string('file_path')->nullable(); // For attachments
            $table->uuid('transferred_to')->nullable(); // If approval is transferred
            $table->foreign('transferred_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action')
                  ->onUpdate('no action');
            $table->timestamps();
        });

        // Only add the payments column if the payments table exists
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                // Check if column doesn't already exist before adding it
                if (!Schema::hasColumn('payments', 'approval_instance_id')) {
                    $table->uuid('approval_instance_id')->nullable()->after('status');
                    $table->foreign('approval_instance_id')
                          ->references('id')
                          ->on('approval_instances')
                          ->onDelete('no action')
                          ->onUpdate('no action');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'approval_instance_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['approval_instance_id']);
                $table->dropColumn('approval_instance_id');
            });
        }

        Schema::dropIfExists('approvals');
        Schema::dropIfExists('approval_instances');
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approval_workflows');
        Schema::dropIfExists('statuses');
    }
}; 