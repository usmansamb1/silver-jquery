<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the ConnectSaudi SMS service.
    |
    */

    'url' => env('CONNECTSAUDI_SMS_URL', 'https://sms.connectsaudi.com/sendurl.aspx'),
    'username' => env('CONNECTSAUDI_SMS_USER'),
    'password' => env('CONNECTSAUDI_SMS_PASSWORD'), 
    'sender_id' => env('CONNECTSAUDI_SMS_SENDER_ID', 'Advance Dig'),
    'country_code' => env('CONNECTSAUDI_SMS_COUNTRY_CODE', '966'),
    'priority' => env('CONNECTSAUDI_SMS_PRIORITY', 'High'),

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    */
    
    'timeout' => env('SMS_API_TIMEOUT', 30), // API request timeout in seconds
    'max_retries' => env('SMS_MAX_RETRIES', 3), // Maximum retry attempts
    'retry_delay' => env('SMS_RETRY_DELAY', 5), // Delay between retries in seconds
    
    // Message settings
    'max_message_length' => env('SMS_MAX_MESSAGE_LENGTH', 160),
    'enable_logging' => env('SMS_ENABLE_LOGGING', true),
];
