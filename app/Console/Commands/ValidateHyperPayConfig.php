<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class ValidateHyperPayConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hyperpay:validate 
                            {--test-api : Test API connectivity with a sample request}
                            {--show-config : Show current configuration values}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate HyperPay configuration and test API connectivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 HyperPay Configuration Validator');
        $this->info('=====================================');
        
        // Check basic configuration
        $configValid = $this->validateConfiguration();
        
        if ($this->option('show-config')) {
            $this->showConfiguration();
        }
        
        if ($this->option('test-api') && $configValid) {
            $this->testApiConnectivity();
        }
        
        if ($configValid) {
            $this->info('✅ HyperPay configuration validation completed successfully!');
            return 0;
        } else {
            $this->error('❌ HyperPay configuration validation failed. Please fix the issues above.');
            return 1;
        }
    }
    
    /**
     * Validate HyperPay configuration
     */
    private function validateConfiguration(): bool
    {
        $this->info('📋 Checking configuration values...');
        
        $errors = [];
        $warnings = [];
        
        // Required configuration values
        $requiredConfigs = [
            'services.hyperpay.base_url' => 'Base URL',
            'services.hyperpay.access_token' => 'Access Token',
            'services.hyperpay.entity_id_credit' => 'Credit Card Entity ID',
            'services.hyperpay.entity_id_mada' => 'MADA Card Entity ID',
            'services.hyperpay.currency' => 'Currency',
            'services.hyperpay.mode' => 'Mode'
        ];
        
        foreach ($requiredConfigs as $configKey => $displayName) {
            $value = config($configKey);
            
            if (empty($value)) {
                $errors[] = "❌ {$displayName} is missing or empty";
            } else {
                $this->line("✅ {$displayName}: " . $this->maskSensitiveValue($configKey, $value));
            }
        }
        
        // Validate specific values
        $baseUrl = config('services.hyperpay.base_url');
        if (!empty($baseUrl)) {
            if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
                $errors[] = "❌ Base URL is not a valid URL format";
            } elseif (!str_ends_with($baseUrl, '/')) {
                $warnings[] = "⚠️  Base URL should end with a forward slash (/)";
            }
        }
        
        $currency = config('services.hyperpay.currency');
        if (!empty($currency) && $currency !== 'SAR') {
            $warnings[] = "⚠️  Currency is set to '{$currency}' - ensure this is correct for your region";
        }
        
        $mode = config('services.hyperpay.mode');
        if (!empty($mode) && !in_array($mode, ['test', 'live'])) {
            $errors[] = "❌ Mode must be either 'test' or 'live'";
        }
        
        // Check access token format
        $accessToken = config('services.hyperpay.access_token');
        if (!empty($accessToken)) {
            if (strlen($accessToken) < 50) {
                $warnings[] = "⚠️  Access token seems too short - verify it's correct";
            }
        }
        
        // Check entity ID formats
        $entityIdCredit = config('services.hyperpay.entity_id_credit');
        $entityIdMada = config('services.hyperpay.entity_id_mada');
        
        if (!empty($entityIdCredit) && strlen($entityIdCredit) !== 32) {
            $warnings[] = "⚠️  Credit Card Entity ID should be 32 characters long";
        }
        
        if (!empty($entityIdMada) && strlen($entityIdMada) !== 32) {
            $warnings[] = "⚠️  MADA Card Entity ID should be 32 characters long";
        }
        
        // Display warnings
        if (!empty($warnings)) {
            $this->newLine();
            $this->warn('⚠️  Warnings:');
            foreach ($warnings as $warning) {
                $this->line($warning);
            }
        }
        
        // Display errors
        if (!empty($errors)) {
            $this->newLine();
            $this->error('❌ Configuration Errors:');
            foreach ($errors as $error) {
                $this->line($error);
            }
            
            $this->newLine();
            $this->info('💡 To fix these issues:');
            $this->line('1. Copy .env.example to .env if you haven\'t already');
            $this->line('2. Add the missing HyperPay configuration values to your .env file');
            $this->line('3. Run: php artisan config:clear');
            $this->line('4. Run this command again to validate');
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Show current configuration values
     */
    private function showConfiguration(): void
    {
        $this->newLine();
        $this->info('📊 Current HyperPay Configuration:');
        $this->info('==================================');
        
        $configs = [
            'Base URL' => config('services.hyperpay.base_url'),
            'Access Token' => config('services.hyperpay.access_token'),
            'Credit Entity ID' => config('services.hyperpay.entity_id_credit'),
            'MADA Entity ID' => config('services.hyperpay.entity_id_mada'),
            'Currency' => config('services.hyperpay.currency'),
            'Mode' => config('services.hyperpay.mode')
        ];
        
        foreach ($configs as $name => $value) {
            $displayValue = $this->maskSensitiveValue($name, $value ?? 'NOT SET');
            $this->line("{$name}: {$displayValue}");
        }
    }
    
    /**
     * Test API connectivity
     */
    private function testApiConnectivity(): void
    {
        $this->newLine();
        $this->info('🌐 Testing API connectivity...');
        
        try {
            // Test with credit card entity
            $this->testEntityConnectivity('credit_card', 'Credit Card');
            
            // Test with MADA entity
            $this->testEntityConnectivity('mada_card', 'MADA Card');
            
        } catch (\Exception $e) {
            $this->error("❌ API test failed: " . $e->getMessage());
        }
    }
    
    /**
     * Test connectivity for a specific entity
     */
    private function testEntityConnectivity(string $brandType, string $displayName): void
    {
        $entityId = $brandType === 'mada_card' 
            ? config('services.hyperpay.entity_id_mada')
            : config('services.hyperpay.entity_id_credit');
        
        if (empty($entityId)) {
            $this->warn("⚠️  Skipping {$displayName} test - Entity ID not configured");
            return;
        }
        
        $this->line("🔄 Testing {$displayName} connectivity...");
        
        $requestData = [
            'entityId' => $entityId,
            'amount' => '100.00',
            'currency' => config('services.hyperpay.currency', 'SAR'),
            'paymentType' => 'DB',
            'merchantTransactionId' => 'TEST-' . time(),
            'customer.email' => 'test@example.com',
            'testMode' => 'EXTERNAL'
        ];
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', $requestData);
            
            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['id'])) {
                $this->info("✅ {$displayName} API test successful!");
                $this->line("   Checkout ID: " . $responseData['id']);
            } else {
                $resultCode = $responseData['result']['code'] ?? 'UNKNOWN';
                $resultDescription = $responseData['result']['description'] ?? 'Unknown error';
                
                $this->error("❌ {$displayName} API test failed:");
                $this->line("   Status: " . $response->status());
                $this->line("   Code: {$resultCode}");
                $this->line("   Description: {$resultDescription}");
                
                // Provide specific guidance based on error codes
                $this->provideErrorGuidance($resultCode);
            }
            
        } catch (\Exception $e) {
            $this->error("❌ {$displayName} API test error: " . $e->getMessage());
        }
    }
    
    /**
     * Provide guidance based on error codes
     */
    private function provideErrorGuidance(string $errorCode): void
    {
        $guidance = match ($errorCode) {
            '200.300.404' => 'Invalid or missing parameter - check your configuration values',
            '800.100.150' => 'Invalid entity ID - verify your entity IDs are correct',
            '800.100.151' => 'Invalid payment type - this should not happen with our configuration',
            '800.100.152' => 'Invalid currency - verify currency is set correctly',
            '800.100.153' => 'Invalid amount - this should not happen with our test amount',
            '800.100.162' => 'Invalid access token - verify your access token is correct',
            '800.100.155' => 'Invalid merchant transaction ID format',
            default => 'Check HyperPay documentation for error code: ' . $errorCode
        };
        
        $this->line("   💡 Guidance: {$guidance}");
    }
    
    /**
     * Mask sensitive configuration values for display
     */
    private function maskSensitiveValue(string $configKey, string $value): string
    {
        if (str_contains(strtolower($configKey), 'token') || str_contains(strtolower($configKey), 'access')) {
            return strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value;
        }
        
        return $value;
    }
} 