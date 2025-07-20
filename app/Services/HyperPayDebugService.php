<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class HyperPayDebugService
{
    /**
     * Log HyperPay API request
     */
    public static function logRequest(array $requestData, string $url, string $context = 'API_REQUEST'): void
    {
        // Mask sensitive data for logging
        $maskedData = self::maskSensitiveData($requestData);
        
        Log::channel('hyperpay')->info("HyperPay {$context}", [
            'url' => $url,
            'request_data' => $maskedData,
            'timestamp' => now()->toISOString(),
            'context' => $context
        ]);
    }
    
    /**
     * Log HyperPay API response
     */
    public static function logResponse(Response $response, string $context = 'API_RESPONSE'): void
    {
        $responseData = $response->json();
        
        Log::channel('hyperpay')->info("HyperPay {$context}", [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'response_data' => $responseData,
            'result_code' => $responseData['result']['code'] ?? 'N/A',
            'result_description' => $responseData['result']['description'] ?? 'N/A',
            'checkout_id' => $responseData['id'] ?? 'N/A',
            'timestamp' => now()->toISOString(),
            'context' => $context
        ]);
    }
    
    /**
     * Log HyperPay error
     */
    public static function logError(string $message, array $context = [], string $errorContext = 'ERROR'): void
    {
        // Mask sensitive data in context
        $maskedContext = self::maskSensitiveData($context);
        
        Log::channel('hyperpay')->error("HyperPay {$errorContext}: {$message}", [
            'context' => $maskedContext,
            'timestamp' => now()->toISOString(),
            'error_type' => $errorContext
        ]);
    }
    
    /**
     * Log configuration validation
     */
    public static function logConfigValidation(array $validationResults): void
    {
        Log::channel('hyperpay')->info('HyperPay Configuration Validation', [
            'validation_results' => $validationResults,
            'timestamp' => now()->toISOString(),
            'context' => 'CONFIG_VALIDATION'
        ]);
    }
    
    /**
     * Log user action
     */
    public static function logUserAction(string $action, int $userId, array $data = []): void
    {
        // Mask sensitive data
        $maskedData = self::maskSensitiveData($data);
        
        Log::channel('hyperpay')->info("HyperPay User Action: {$action}", [
            'user_id' => $userId,
            'action' => $action,
            'data' => $maskedData,
            'timestamp' => now()->toISOString(),
            'context' => 'USER_ACTION'
        ]);
    }
    
    /**
     * Test HyperPay API connectivity
     */
    public static function testApiConnectivity(string $brandType = 'credit_card'): array
    {
        $startTime = microtime(true);
        
        try {
            $entityId = $brandType === 'mada_card' 
                ? config('services.hyperpay.entity_id_mada')
                : config('services.hyperpay.entity_id_credit');
            
            if (empty($entityId)) {
                return [
                    'success' => false,
                    'error' => "Entity ID not configured for {$brandType}",
                    'duration' => 0
                ];
            }
            
            $requestData = [
                'entityId' => $entityId,
                'amount' => '100.00',
                'currency' => config('services.hyperpay.currency', 'SAR'),
                'paymentType' => 'DB',
                'merchantTransactionId' => 'DEBUG-' . time(),
                'customer.email' => 'debug@example.com',
                'testMode' => 'EXTERNAL'
            ];
            
            self::logRequest($requestData, config('services.hyperpay.base_url') . 'v1/checkouts', 'DEBUG_TEST');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', $requestData);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            self::logResponse($response, 'DEBUG_TEST');
            
            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['id'])) {
                return [
                    'success' => true,
                    'checkout_id' => $responseData['id'],
                    'duration' => $duration,
                    'status' => $response->status()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseData['result']['description'] ?? 'Unknown error',
                    'error_code' => $responseData['result']['code'] ?? 'UNKNOWN',
                    'duration' => $duration,
                    'status' => $response->status()
                ];
            }
            
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            self::logError('API connectivity test failed', [
                'brand_type' => $brandType,
                'error' => $e->getMessage(),
                'duration' => $duration
            ], 'DEBUG_TEST_ERROR');
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'duration' => $duration
            ];
        }
    }
    
    /**
     * Validate HyperPay configuration
     */
    public static function validateConfiguration(): array
    {
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
                $errors[] = "{$displayName} is missing or empty";
            }
        }
        
        // Validate specific values
        $baseUrl = config('services.hyperpay.base_url');
        if (!empty($baseUrl)) {
            if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
                $errors[] = "Base URL is not a valid URL format";
            } elseif (!str_ends_with($baseUrl, '/')) {
                $warnings[] = "Base URL should end with a forward slash (/)";
            }
        }
        
        $currency = config('services.hyperpay.currency');
        if (!empty($currency) && $currency !== 'SAR') {
            $warnings[] = "Currency is set to '{$currency}' - ensure this is correct";
        }
        
        $mode = config('services.hyperpay.mode');
        if (!empty($mode) && !in_array($mode, ['test', 'live'])) {
            $errors[] = "Mode must be either 'test' or 'live'";
        }
        
        // Check access token format
        $accessToken = config('services.hyperpay.access_token');
        if (!empty($accessToken) && strlen($accessToken) < 50) {
            $warnings[] = "Access token seems too short - verify it's correct";
        }
        
        // Check entity ID formats
        $entityIdCredit = config('services.hyperpay.entity_id_credit');
        $entityIdMada = config('services.hyperpay.entity_id_mada');
        
        if (!empty($entityIdCredit) && strlen($entityIdCredit) !== 32) {
            $warnings[] = "Credit Card Entity ID should be 32 characters long";
        }
        
        if (!empty($entityIdMada) && strlen($entityIdMada) !== 32) {
            $warnings[] = "MADA Card Entity ID should be 32 characters long";
        }
        
        $result = [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'config_values' => [
                'base_url' => config('services.hyperpay.base_url'),
                'has_access_token' => !empty(config('services.hyperpay.access_token')),
                'has_entity_id_credit' => !empty(config('services.hyperpay.entity_id_credit')),
                'has_entity_id_mada' => !empty(config('services.hyperpay.entity_id_mada')),
                'currency' => config('services.hyperpay.currency'),
                'mode' => config('services.hyperpay.mode')
            ]
        ];
        
        self::logConfigValidation($result);
        
        return $result;
    }
    
    /**
     * Get HyperPay error guidance based on error code
     */
    public static function getErrorGuidance(string $errorCode): string
    {
        return match ($errorCode) {
            '200.300.404' => 'Invalid or missing parameter. Check your request parameters.',
            '800.100.150' => 'Invalid entity ID. Verify your entity IDs are correct.',
            '800.100.151' => 'Invalid payment type. Check paymentType parameter.',
            '800.100.152' => 'Invalid currency. Verify currency is set correctly.',
            '800.100.153' => 'Invalid amount. Check amount format and value.',
            '800.100.162' => 'Invalid access token. Verify your access token is correct.',
            '800.100.155' => 'Invalid merchant transaction ID format.',
            '800.100.156' => 'Invalid customer email format.',
            '800.100.157' => 'Invalid customer information.',
            '800.100.158' => 'Invalid billing address.',
            '800.100.159' => 'Invalid shipping address.',
            '800.100.160' => 'Invalid payment method.',
            '800.100.161' => 'Invalid card information.',
            '800.100.163' => 'Invalid test mode setting.',
            '800.100.164' => 'Invalid custom parameters.',
            '800.100.165' => 'Invalid notification URL.',
            '800.100.166' => 'Invalid redirect URL.',
            '800.100.167' => 'Invalid webhook URL.',
            '800.100.168' => 'Invalid merchant configuration.',
            '800.100.169' => 'Invalid payment brand.',
            '800.100.170' => 'Invalid payment descriptor.',
            default => "Check HyperPay documentation for error code: {$errorCode}"
        };
    }
    
    /**
     * Mask sensitive data for logging
     */
    private static function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'customer.email',
            'access_token',
            'authorization',
            'password',
            'card_number',
            'cvv',
            'card.number',
            'card.cvv'
        ];
        
        $masked = $data;
        
        foreach ($sensitiveKeys as $key) {
            if (isset($masked[$key])) {
                $value = $masked[$key];
                if (is_string($value) && strlen($value) > 4) {
                    $masked[$key] = substr($value, 0, 4) . '***MASKED***';
                } else {
                    $masked[$key] = '***MASKED***';
                }
            }
        }
        
        return $masked;
    }
} 