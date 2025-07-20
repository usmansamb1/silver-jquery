<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\SmsLog;
use Exception;

final class SmsService
{
    /**
     * SMS configuration
     */
    private array $config;

    public function __construct()
    {
        $this->config = config('sms');
    }

    /**
     * Send SMS using ConnectSaudi API
     *
     * @param string $mobile Mobile number (with or without country code)
     * @param string $message SMS message content
     * @param array $options Additional options for SMS sending
     * @return array Response array with success status and details
     */
    public function sendSms(string $mobile, string $message, array $options = []): array
    {
        // Validate inputs
        if (empty($mobile) || empty($message)) {
            $error = 'Mobile number and message are required';
            Log::error('SMS validation failed', ['error' => $error]);
            return $this->errorResponse($error);
        }

        // Format mobile number
        $formattedMobile = $this->formatMobileNumber($mobile);
        
        // Truncate message if too long
        $truncatedMessage = $this->truncateMessage($message);
        
        // Create SMS log entry
        $smsLog = SmsLog::create([
            'mobile' => $formattedMobile,
            'message' => $truncatedMessage,
            'provider' => 'connectsaudi',
            'status' => 'pending',
            'purpose' => $options['purpose'] ?? 'general',
            'reference_id' => $options['reference_id'] ?? null,
        ]);

        // Log SMS attempt
        if ($this->config['enable_logging']) {
            Log::info('SMS sending initiated', [
                'sms_log_id' => $smsLog->id,
                'mobile' => $formattedMobile,
                'message_length' => strlen($truncatedMessage),
                'provider' => 'connectsaudi'
            ]);
        }

        $result = $this->sendViaConnectSaudi($formattedMobile, $truncatedMessage, $options, $smsLog);

        // Update statistics
        $this->updateStatistics($result['success']);

        return $result;
    }

    /**
     * Send SMS via ConnectSaudi API
     */
    private function sendViaConnectSaudi(string $mobile, string $message, array $options = [], ?SmsLog $smsLog = null): array
    {
        if (empty($this->config['username']) || empty($this->config['password'])) {
            return $this->errorResponse('ConnectSaudi SMS credentials not configured');
        }

        $params = [
            'user' => $this->config['username'],
            'pwd' => $this->config['password'],
            'senderid' => $options['sender_id'] ?? $this->config['sender_id'],
            'mobileno' => $mobile,
            'msgtext' => $message,
            'priority' => $options['priority'] ?? $this->config['priority'],
            'CountryCode' => $options['country_code'] ?? $this->config['country_code'],
        ];

        $url = $this->config['url'] . '?' . http_build_query($params);

        // Store request data in log
        if ($smsLog) {
            $smsLog->update(['request_data' => $params]);
        }

        return $this->makeHttpRequest($url, 'GET', [], [], $smsLog);
    }

    /**
     * Make HTTP request with retry logic
     */
    private function makeHttpRequest(string $url, string $method = 'GET', array $data = [], array $headers = [], ?SmsLog $smsLog = null): array
    {
        $maxRetries = $this->config['max_retries'];
        $retryDelay = $this->config['retry_delay'];
        $timeout = $this->config['timeout'];

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $http = Http::timeout($timeout);
                
                if (!empty($headers)) {
                    $http = $http->withHeaders($headers);
                }

                $response = $method === 'POST' 
                    ? $http->post($url, $data)
                    : $http->get($url);

            if ($response->successful()) {
                    $responseData = $this->parseResponse($response);
                    
                    // Mark SMS as sent in log
                    if ($smsLog) {
                        $smsLog->markAsSent($responseData);
                    }
                    
                    if ($this->config['enable_logging']) {
                Log::info('SMS sent successfully', [
                            'sms_log_id' => $smsLog?->id,
                            'attempt' => $attempt,
                            'response' => $responseData
                ]);
                    }
                    
                    return $this->successResponse('SMS sent successfully', $responseData);
                }

                // Log unsuccessful HTTP response
                Log::warning('SMS API returned non-success status', [
                    'attempt' => $attempt,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                if ($attempt < $maxRetries) {
                    if ($smsLog) {
                        $smsLog->incrementRetry();
                    }
                    sleep($retryDelay);
                    continue;
                }

                // Mark SMS as failed in log
                if ($smsLog) {
                    $smsLog->markAsFailed(
                        'SMS API returned error: ' . $response->status(),
                        ['status' => $response->status(), 'response' => $response->body()]
                    );
                }

                return $this->errorResponse(
                    'SMS API returned error: ' . $response->status(),
                    ['status' => $response->status(), 'response' => $response->body()]
                );

            } catch (Exception $e) {
                Log::error('SMS sending exception', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                if ($attempt < $maxRetries) {
                    if ($smsLog) {
                        $smsLog->incrementRetry();
                    }
                    sleep($retryDelay);
                    continue;
                }

                // Mark SMS as failed in log
                if ($smsLog) {
                    $smsLog->markAsFailed($e->getMessage());
                }

                return $this->errorResponse('SMS sending failed: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('SMS sending failed after all retry attempts');
    }

    /**
     * Parse API response
     */
    private function parseResponse($response): array
    {
        $body = $response->body();
        
        // Try to parse as JSON first
        $json = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }
        
        // For ConnectSaudi, response might be plain text
        return ['raw_response' => $body];
    }

    /**
     * Format mobile number for API
     */
    private function formatMobileNumber(string $mobile): string
    {
        // Remove any non-digit characters
        $mobile = preg_replace('/\D/', '', $mobile);
        
        // Remove leading + if present
        $mobile = ltrim($mobile, '+');
        
        // If starts with country code, use as is
        if (str_starts_with($mobile, '966')) {
            return $mobile;
        }
        
        // If starts with 0, replace with country code
        if (str_starts_with($mobile, '0')) {
            return '966' . substr($mobile, 1);
        }
        
        // If it's a 9-digit number, add country code
        if (strlen($mobile) === 9) {
            return '966' . $mobile;
        }
        
        return $mobile;
    }

    /**
     * Truncate message if it exceeds maximum length
     */
    private function truncateMessage(string $message): string
    {
        $maxLength = $this->config['max_message_length'];
        
        if (strlen($message) <= $maxLength) {
            return $message;
        }
        
        $truncated = substr($message, 0, $maxLength - 3) . '...';
        
        if ($this->config['enable_logging']) {
            Log::warning('SMS message truncated', [
                'original_length' => strlen($message),
                'truncated_length' => strlen($truncated)
            ]);
        }
        
        return $truncated;
    }

    /**
     * Create success response array
     */
    private function successResponse(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Create error response array
     */
    private function errorResponse(string $message, array $data = []): array
    {
            return [
                'success' => false,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Test SMS connectivity
     */
    public function testConnection(): array
    {
        $requiredFields = ['username', 'password', 'sender_id'];
        
        foreach ($requiredFields as $field) {
            if (empty($this->config[$field])) {
                return $this->errorResponse("Required field '{$field}' is not configured for ConnectSaudi SMS");
            }
        }
        
        return $this->successResponse('ConnectSaudi SMS configuration is valid');
    }

    /**
     * Get SMS sending statistics from database
     */
    public function getStatistics(): array
    {
        return SmsLog::getStatistics();
    }

    /**
     * Update SMS statistics (now handled by SmsLog model)
     */
    private function updateStatistics(bool $success): void
    {
        // Statistics are now handled by the SmsLog model
        // This method is kept for backward compatibility
        if ($this->config['enable_logging']) {
            Log::info('SMS statistics updated', [
                'success' => $success,
                'timestamp' => now()->toISOString()
            ]);
        }
    }
}
