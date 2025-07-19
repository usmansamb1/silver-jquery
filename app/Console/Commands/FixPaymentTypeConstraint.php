<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPaymentTypeConstraint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:payment-constraint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the payment_type constraint to allow all valid payment types';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Attempting to fix payment_type constraint...');

        try {
            // First try the direct constraint name
            DB::statement('ALTER TABLE payments DROP CONSTRAINT IF EXISTS CK__payments__paymen__18640752');
            $this->info('Dropped constraint CK__payments__paymen__18640752 if it existed');
        } catch (\Exception $e) {
            $this->error('Error dropping specific constraint: ' . $e->getMessage());
        }

        try {
            // Check database driver and handle constraints accordingly
            if (DB::connection()->getDriverName() === 'sqlsrv') {
                // SQL Server specific constraint handling
                $constraints = DB::select("SELECT name FROM sys.check_constraints 
                    WHERE OBJECT_NAME(parent_object_id) = 'payments' 
                    AND OBJECT_NAME(parent_column_id) = 'payment_type'");
                    
                if (!empty($constraints)) {
                    foreach ($constraints as $constraint) {
                        $this->info('Found constraint: ' . $constraint->name);
                        try {
                            DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS {$constraint->name}");
                            $this->info('Successfully dropped constraint: ' . $constraint->name);
                        } catch (\Exception $e) {
                            $this->error('Error dropping constraint ' . $constraint->name . ': ' . $e->getMessage());
                        }
                    }
                } else {
                    $this->info('No constraints found on payment_type column.');
                }
            } else {
                // MySQL - check for existing constraints using information_schema
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME as name 
                    FROM information_schema.CHECK_CONSTRAINTS 
                    WHERE TABLE_NAME = 'payments' 
                    AND TABLE_SCHEMA = DATABASE()
                ");
                
                if (!empty($constraints)) {
                    foreach ($constraints as $constraint) {
                        $this->info('Found MySQL constraint: ' . $constraint->name);
                        try {
                            DB::statement("ALTER TABLE payments DROP CHECK {$constraint->name}");
                            $this->info('Successfully dropped constraint: ' . $constraint->name);
                        } catch (\Exception $e) {
                            $this->error('Error dropping constraint ' . $constraint->name . ': ' . $e->getMessage());
                        }
                    }
                } else {
                    $this->info('No constraints found on payment_type column.');
                }
            }
        } catch (\Exception $e) {
            $this->error('Error finding constraints: ' . $e->getMessage());
        }

        try {
            // Check if our constraint already exists based on database driver
            if (DB::connection()->getDriverName() === 'sqlsrv') {
                $existingConstraint = DB::select("SELECT name FROM sys.check_constraints 
                    WHERE name = 'payments_payment_type_check'");
                    
                if (!empty($existingConstraint)) {
                    $this->info('Constraint payments_payment_type_check already exists. Dropping it first.');
                    DB::statement("ALTER TABLE payments DROP CONSTRAINT payments_payment_type_check");
                }
                
                // Add SQL Server constraint
                DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_type_check 
                    CHECK (payment_type IN ('credit_card', 'bank_transfer', 'bank_guarantee', 'bank_lc'))");
            } else {
                // MySQL constraint handling
                $existingConstraint = DB::select("
                    SELECT CONSTRAINT_NAME as name 
                    FROM information_schema.CHECK_CONSTRAINTS 
                    WHERE CONSTRAINT_NAME = 'payments_payment_type_check' 
                    AND TABLE_SCHEMA = DATABASE()
                ");
                
                if (!empty($existingConstraint)) {
                    $this->info('Constraint payments_payment_type_check already exists. Dropping it first.');
                    DB::statement("ALTER TABLE payments DROP CHECK payments_payment_type_check");
                }
                
                // Add MySQL constraint
                DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_payment_type_check 
                    CHECK (payment_type IN ('credit_card', 'bank_transfer', 'bank_guarantee', 'bank_lc'))");
            }
            
            $this->info('Added new constraint with all payment types');
        } catch (\Exception $e) {
            $this->error('Error adding new constraint: ' . $e->getMessage());
        }

        $this->info('Constraint fix completed.');
    }
} 