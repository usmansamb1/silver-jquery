<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestHyperpayConfig extends Command
{
    protected $signature = 'hyperpay:test-config';
    protected $description = 'Test HyperPay configuration for both credit cards and MADA cards';

    public function handle()
    {
        $this->info('Testing HyperPay Configuration...');
        $this->newLine();

        // Test environment variables
        $this->info('1. Checking Environment Variables:');
        $baseUrl = config('services.hyperpay.base_url');
        $accessToken = config('services.hyperpay.access_token');
        $creditEntityId = config('services.hyperpay.entity_id_credit');
        $madaEntityId = config('services.hyperpay.entity_id_mada');
        $currency = config('services.hyperpay.currency');

        $this->table(['Variable', 'Value', 'Status'], [
            ['Base URL', $baseUrl, $baseUrl ? '✅ Set' : '❌ Missing'],
            ['Access Token', $accessToken ? substr($accessToken, 0, 10) . '...' : 'Not Set', $accessToken ? '✅ Set' : '❌ Missing'],
            ['Credit Entity ID', $creditEntityId, $creditEntityId ? '✅ Set' : '❌ Missing'],
            ['MADA Entity ID', $madaEntityId, $madaEntityId ? '✅ Set' : '❌ Missing'],
            ['Currency', $currency, $currency ? '✅ Set' : '❌ Missing'],
        ]);

        if (!$accessToken || !$creditEntityId || !$madaEntityId) {
            $this->error('❌ Missing required configuration. Please check your .env file.');
            return 1;
        }

        $this->newLine();
        
        // Test Credit Card Entity ID
        $this->info('2. Testing Credit Card Configuration:');
        $creditTest = $this->testEntityId($creditEntityId, 'VISA MASTER', 'credit_card');
        
        $this->newLine();
        
        // Test MADA Entity ID
        $this->info('3. Testing MADA Card Configuration:');
        $madaTest = $this->testEntityId($madaEntityId, 'MADA', 'mada_card');
        
        $this->newLine();
        
        // Summary
        $this->info('4. Configuration Summary:');
        if ($creditTest && $madaTest) {
            $this->info('✅ Both credit card and MADA configurations are working!');
            return 0;
        } elseif ($creditTest) {
            $this->warn('⚠️  Credit cards work, but MADA has issues.');
            return 1;
        } elseif ($madaTest) {
            $this->warn('⚠️  MADA works, but credit cards have issues.');
            return 1;
        } else {
            $this->error('❌ Both configurations have issues.');
            return 1;
        }
    }

    private function testEntityId($entityId, $brand, $type)
    {
        $this->line("Testing {$type} with Entity ID: {$entityId}");
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', [
                'entityId' => $entityId,
                'amount' => '10.00',
                'currency' => config('services.hyperpay.currency'),
                'paymentType' => 'DB',
                'merchantTransactionId' => 'test_' . uniqid(),
                'customer.email' => 'test@example.com',
                'testMode' => 'EXTERNAL',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $checkoutId = $data['id'] ?? null;
                
                if ($checkoutId) {
                    $this->info("✅ {$type} configuration is working. Checkout ID: {$checkoutId}");
                    return true;
                } else {
                    $this->error("❌ {$type} failed: No checkout ID returned");
                    $this->line("Response: " . json_encode($data, JSON_PRETTY_PRINT));
                    return false;
                }
            } else {
                $this->error("❌ {$type} failed with HTTP {$response->status()}");
                $responseBody = $response->json();
                
                if (isset($responseBody['result']['description'])) {
                    $this->error("Error: " . $responseBody['result']['description']);
                }
                
                $this->line("Response: " . json_encode($responseBody, JSON_PRETTY_PRINT));
                return false;
            }
        } catch (\Exception $e) {
            $this->error("❌ {$type} test failed with exception: " . $e->getMessage());
            return false;
        }
    }
}