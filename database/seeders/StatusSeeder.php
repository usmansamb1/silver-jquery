<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovalStatus;
use App\Models\StepStatus;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed approval statuses if the table exists
        if (DB::getSchemaBuilder()->hasTable('approval_statuses')) {
            // First check if we already have statuses
            $count = DB::table('approval_statuses')->count();
            
            if ($count === 0) {
                $this->command->info('Seeding approval statuses...');
                foreach (ApprovalStatus::getDefaultStatuses() as $status) {
                    DB::table('approval_statuses')->insert(array_merge(
                        $status, 
                        ['created_at' => now(), 'updated_at' => now()]
                    ));
                }
            } else {
                $this->command->info('Approval statuses already exist. Skipping...');
            }
        }
        
        // Seed step statuses if the table exists
        if (DB::getSchemaBuilder()->hasTable('step_statuses')) {
            // First check if we already have statuses
            $count = DB::table('step_statuses')->count();
            
            if ($count === 0) {
                $this->command->info('Seeding step statuses...');
                foreach (StepStatus::getDefaultStatuses() as $status) {
                    DB::table('step_statuses')->insert(array_merge(
                        $status, 
                        ['created_at' => now(), 'updated_at' => now()]
                    ));
                }
            } else {
                $this->command->info('Step statuses already exist. Skipping...');
            }
        }
        
        // Seed custom step statuses for finance and validation workflow
        if (DB::getSchemaBuilder()->hasTable('step_statuses')) {
            // Check if we already have the custom statuses
            $customStatuses = [
                'finance_approved' => 'Finance Approved',
                'validation_approved' => 'Validation Approved'
            ];
            
            foreach ($customStatuses as $code => $name) {
                $exists = DB::table('step_statuses')->where('code', $code)->exists();
                
                if (!$exists) {
                    $this->command->info("Adding custom step status: {$name}");
                    DB::table('step_statuses')->insert([
                        'name' => $name,
                        'code' => $code,
                        'color' => '#6610f2', // Purple color for workflow-specific steps
                        'description' => "{$name} in the approval workflow",
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
} 