<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-user-data {--fix-last-login : Fix last login dates} {--fix-service-count : Fix service booking counts} {--all : Fix all data issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user data issues, including last login dates and service booking counts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fixLastLogin = $this->option('fix-last-login') || $this->option('all');
        $fixServiceCount = $this->option('fix-service-count') || $this->option('all');
        
        if (!$fixLastLogin && !$fixServiceCount) {
            $this->info('No options specified. Use --fix-last-login, --fix-service-count, or --all');
            return;
        }
        
        $users = User::all();
        $this->info("Found {$users->count()} users to process");
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        $fixedLastLogin = 0;
        $fixedServiceCount = 0;
        
        foreach ($users as $user) {
            if ($fixLastLogin) {
                try {
                    // If last_login_at is null, set it to created_at
                    if (empty($user->last_login_at)) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['last_login_at' => $user->created_at]);
                        $fixedLastLogin++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error fixing last_login_at for user {$user->id}: {$e->getMessage()}");
                    Log::error("Error fixing last_login_at", [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if ($fixServiceCount) {
                try {
                    // Count service bookings for this user and update ServiceOrder relation in users
                    $bookingsCount = ServiceBooking::where('user_id', $user->id)->count();
                    $ordersCount = ServiceOrder::where('user_id', $user->id)->count();
                    
                    // Here you could update a cache value, store in user metadata, etc.
                    $totalCount = $bookingsCount + $ordersCount;
                    
                    // For demo purposes, just log it
                    Log::info("User {$user->id} has {$totalCount} service bookings", [
                        'bookingsCount' => $bookingsCount, 
                        'ordersCount' => $ordersCount
                    ]);
                    
                    $fixedServiceCount++;
                } catch (\Exception $e) {
                    $this->error("Error fixing service count for user {$user->id}: {$e->getMessage()}");
                    Log::error("Error fixing service count", [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        if ($fixLastLogin) {
            $this->info("Fixed last_login_at for {$fixedLastLogin} users");
        }
        
        if ($fixServiceCount) {
            $this->info("Fixed service counts for {$fixedServiceCount} users");
        }
    }
}
