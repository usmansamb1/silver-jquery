<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the unique constraint already exists
        if (!$this->constraintExists('users', 'users_customer_no_unique')) {
            // First, check if there are any duplicate customer_no values and fix them
            $this->fixDuplicateCustomerNumbers();
            
            // Add unique constraint to customer_no column
            Schema::table('users', function (Blueprint $table) {
                $table->unique('customer_no', 'users_customer_no_unique');
            });
            
            echo "Added unique constraint to customer_no column\n";
        } else {
            echo "Unique constraint 'users_customer_no_unique' already exists, skipping...\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->constraintExists('users', 'users_customer_no_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_customer_no_unique');
            });
        }
    }

    /**
     * Check if a constraint exists
     */
    private function constraintExists(string $table, string $constraintName): bool
    {
        $database = DB::connection()->getDatabaseName();
        
        $constraint = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$database, $table, $constraintName]);

        return !empty($constraint);
    }

    /**
     * Fix any existing duplicate customer numbers before adding unique constraint
     */
    private function fixDuplicateCustomerNumbers(): void
    {
        // Find duplicated customer numbers
        $duplicates = DB::select("
            SELECT customer_no, COUNT(*) as count 
            FROM users 
            WHERE customer_no IS NOT NULL 
            GROUP BY customer_no 
            HAVING COUNT(*) > 1
        ");

        if (empty($duplicates)) {
            echo "No duplicate customer numbers found\n";
            return;
        }

        echo "Found " . count($duplicates) . " duplicate customer numbers, fixing...\n";

        foreach ($duplicates as $duplicate) {
            // Get all users with this duplicate customer_no
            $users = DB::table('users')
                ->where('customer_no', $duplicate->customer_no)
                ->orderBy('created_at')
                ->get();

            // Keep the first one, update the rest
            $first = true;
            foreach ($users as $user) {
                if ($first) {
                    $first = false;
                    continue; // Keep the first occurrence
                }

                // Generate new unique customer number for duplicates
                $newCustomerNo = $this->generateUniqueCustomerNumber($user->id);
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['customer_no' => $newCustomerNo]);

                echo "Updated user {$user->id} customer_no from {$duplicate->customer_no} to {$newCustomerNo}\n";
            }
        }
    }

    /**
     * Generate a unique customer number for migration
     */
    private function generateUniqueCustomerNumber(string $userId): int
    {
        $maxRetries = 50;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            // Use timestamp + random approach for migration
            $timestamp = substr((string)time(), -8);
            $random = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $customerNo = (int)($timestamp . $random);

            // Ensure it's 12 digits
            if ($customerNo < 100000000000) {
                $customerNo += 100000000000;
            }

            // Check if this number already exists
            $exists = DB::table('users')
                ->where('customer_no', $customerNo)
                ->where('id', '!=', $userId)
                ->exists();

            if (!$exists) {
                return $customerNo;
            }

            $attempt++;
        }

        // Ultimate fallback using microtime + user ID hash
        $microtime = (int)(microtime(true) * 1000000);
        $userHash = abs(crc32($userId)) % 10000;
        return ($microtime % 99999999) * 10000 + $userHash + 100000000000;
    }
}; 