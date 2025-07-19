<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Helpers\CustomerNumberHelper;
use Illuminate\Support\Facades\DB;

class TestCustomerNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-customer-number {--clean : Clean up test users after testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the new customer number generation system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Customer Number Generation Test ===');
        $this->newLine();

        try {
            // Test creating multiple users to see customer number generation
            $testUsers = [
                ['name' => 'Test User 1', 'email' => 'test1@example.com', 'mobile' => '0512345990'],
                ['name' => 'Test User 2', 'email' => 'test2@example.com', 'mobile' => '0512345991'],
                ['name' => 'Test User 3', 'email' => 'test3@example.com', 'mobile' => '0512345992'],
            ];

            $generatedNumbers = [];
            $createdUsers = [];

            foreach ($testUsers as $userData) {
                // Check if user already exists
                $existingUser = User::where('email', $userData['email'])->first();
                
                if ($existingUser) {
                    $this->warn("User {$userData['email']} already exists with customer_no: {$existingUser->customer_no}");
                    $generatedNumbers[] = $existingUser->customer_no;
                    continue;
                }

                $user = new User();
                $user->name = $userData['name'];
                $user->email = $userData['email'];
                $user->mobile = $userData['mobile'];
                $user->password = bcrypt('password123');
                $user->save();

                $createdUsers[] = $user->id;

                $this->info("Created user: {$user->name}");
                $this->line("  Email: {$user->email}");
                $this->line("  Customer No: {$user->customer_no}");
                $this->line("  Formatted: {$user->formatted_customer_no}");

                $generatedNumbers[] = $user->customer_no;

                // Parse components
                $components = CustomerNumberHelper::parseComponents($user->customer_no);
                $this->line("  Format: {$components['format']}");
                
                if ($components['format'] === 'date_based') {
                    $this->line("  Date: {$components['date']->format('Y-m-d')}");
                    $this->line("  Sequence: {$components['sequence']}");
                }
                $this->newLine();
            }

            // Test uniqueness
            $this->info('=== Uniqueness Test ===');
            $unique = array_unique($generatedNumbers);
            $this->line("Generated numbers: " . count($generatedNumbers));
            $this->line("Unique numbers: " . count($unique));
            $isUnique = count($generatedNumbers) === count($unique);
            $this->line("All unique: " . ($isUnique ? 'YES' : 'NO'));
            
            if (!$isUnique) {
                $this->error('DUPLICATE CUSTOMER NUMBERS DETECTED!');
                $duplicates = array_diff_assoc($generatedNumbers, $unique);
                $this->error('Duplicates: ' . implode(', ', $duplicates));
            }
            
            $this->newLine();

            // Get statistics
            $this->info('=== Statistics ===');
            $stats = CustomerNumberHelper::getStatistics();
            foreach ($stats as $key => $value) {
                $this->line(ucfirst(str_replace('_', ' ', $key)) . ": {$value}");
            }

            $this->newLine();

            // Test concurrent generation simulation
            $this->info('=== Concurrent Generation Test ===');
            $this->line('Testing 5 rapid successive generations...');
            
            $concurrentNumbers = [];
            for ($i = 0; $i < 5; $i++) {
                $user = new User();
                $user->name = "Concurrent Test User $i";
                $user->email = "concurrent$i@example.com";
                $user->mobile = "051234599$i";
                $user->password = bcrypt('password123');
                $user->save();
                
                $createdUsers[] = $user->id;
                $concurrentNumbers[] = $user->customer_no;
                $this->line("  Generated: {$user->customer_no}");
                
                // Small delay to simulate rapid but not simultaneous creation
                usleep(10000); // 10ms
            }
            
            $uniqueConcurrent = array_unique($concurrentNumbers);
            $isConcurrentUnique = count($concurrentNumbers) === count($uniqueConcurrent);
            $this->line("Concurrent test result: " . ($isConcurrentUnique ? 'PASSED' : 'FAILED'));
            
            $this->newLine();

            // Clean up if requested
            if ($this->option('clean') && !empty($createdUsers)) {
                $this->info('=== Cleanup ===');
                $deleted = User::whereIn('id', $createdUsers)->delete();
                $this->line("Deleted {$deleted} test users");
            } else if (!empty($createdUsers)) {
                $this->warn('Test users created. Use --clean option to remove them automatically.');
                $this->line('Or manually delete users with IDs: ' . implode(', ', $createdUsers));
            }

            $this->newLine();
            $this->info('=== Test Completed Successfully ===');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . ":" . $e->getLine());
            return Command::FAILURE;
        }
    }
} 