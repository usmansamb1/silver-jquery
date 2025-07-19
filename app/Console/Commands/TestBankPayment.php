<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Payment;
use App\Models\WalletApprovalRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TestBankPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bank-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test bank payment functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing bank payment functionality...');
        
        // Find a test user
        $user = User::first();
        if (!$user) {
            $this->error('No user found. Create a user first.');
            return 1;
        }
        
        $this->info("Using user: {$user->name} ({$user->email})");
        
        try {
            DB::beginTransaction();
            
            // Create a payment record
            $payment = Payment::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'payment_type' => 'bank_transfer',
                'amount' => 1500,
                'status' => 'pending',
                'notes' => 'Test payment from command line',
                'files' => json_encode(['test_file.pdf'])
            ]);
            
            $this->info("Created payment: " . $payment->id);
            
            // Create approval request
            $approvalRequest = new WalletApprovalRequest();
            $approvalRequest->id = Str::uuid();
            $approvalRequest->payment_id = $payment->id;
            $approvalRequest->user_id = $user->id;
            $approvalRequest->status = 'pending';
            $approvalRequest->current_step = 1;
            $approvalRequest->amount = 1500;
            $approvalRequest->description = 'Test payment from command line';
            $approvalRequest->reference_no = 'JOiL-' . strtoupper(Str::random(8));
            
            if (!$approvalRequest->save()) {
                throw new \Exception("Failed to save approval request");
            }
            
            $this->info("Created approval request: " . $approvalRequest->id);
            $this->info("Reference number: " . $approvalRequest->reference_no);
            
            DB::commit();
            
            $this->info('Transaction completed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error('Transaction failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            
            return 1;
        }
    }
}
