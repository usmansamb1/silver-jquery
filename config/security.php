<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | JoilYaseeir application including rate limiting, bot detection,
    | and other security measures.
    |
    */

    'rate_limiting' => [
        'enabled' => env('RATE_LIMITING_ENABLED', true),
        
        'limits' => [
            'login' => [
                'max_attempts' => env('LOGIN_MAX_ATTEMPTS', 3),
                'base_delay' => env('LOGIN_BASE_DELAY', 300), // 5 minutes
                'multiplier' => env('LOGIN_DELAY_MULTIPLIER', 2.0),
                'max_delay' => env('LOGIN_MAX_DELAY', 3600), // 1 hour
                'block_threshold' => env('LOGIN_BLOCK_THRESHOLD', 10),
                'block_duration' => env('LOGIN_BLOCK_DURATION', 7200), // 2 hours
            ],
            
            'registration' => [
                'max_attempts' => env('REGISTRATION_MAX_ATTEMPTS', 5),
                'base_delay' => env('REGISTRATION_BASE_DELAY', 180), // 3 minutes
                'multiplier' => env('REGISTRATION_DELAY_MULTIPLIER', 1.5),
                'max_delay' => env('REGISTRATION_MAX_DELAY', 1800), // 30 minutes
                'block_threshold' => env('REGISTRATION_BLOCK_THRESHOLD', 15),
                'block_duration' => env('REGISTRATION_BLOCK_DURATION', 3600), // 1 hour
            ],
            
            'otp' => [
                'max_attempts' => env('OTP_MAX_ATTEMPTS', 3),
                'base_delay' => env('OTP_BASE_DELAY', 600), // 10 minutes
                'multiplier' => env('OTP_DELAY_MULTIPLIER', 3.0),
                'max_delay' => env('OTP_MAX_DELAY', 7200), // 2 hours
                'block_threshold' => env('OTP_BLOCK_THRESHOLD', 5),
                'block_duration' => env('OTP_BLOCK_DURATION', 14400), // 4 hours
            ],
            
            'wallet' => [
                'max_attempts' => env('WALLET_MAX_ATTEMPTS', 10),
                'base_delay' => env('WALLET_BASE_DELAY', 120), // 2 minutes
                'multiplier' => env('WALLET_DELAY_MULTIPLIER', 1.5),
                'max_delay' => env('WALLET_MAX_DELAY', 1800), // 30 minutes
                'block_threshold' => env('WALLET_BLOCK_THRESHOLD', 25),
                'block_duration' => env('WALLET_BLOCK_DURATION', 3600), // 1 hour
            ],
            
            'default' => [
                'max_attempts' => env('DEFAULT_MAX_ATTEMPTS', 15),
                'base_delay' => env('DEFAULT_BASE_DELAY', 60), // 1 minute
                'multiplier' => env('DEFAULT_DELAY_MULTIPLIER', 1.2),
                'max_delay' => env('DEFAULT_MAX_DELAY', 900), // 15 minutes
                'block_threshold' => env('DEFAULT_BLOCK_THRESHOLD', 50),
                'block_duration' => env('DEFAULT_BLOCK_DURATION', 1800), // 30 minutes
            ]
        ]
    ],

    'bot_detection' => [
        'enabled' => env('BOT_DETECTION_ENABLED', true),
        
        'honeypot' => [
            'enabled' => env('HONEYPOT_ENABLED', true),
            'field_name' => env('HONEYPOT_FIELD_NAME', 'website'),
            'auto_block' => env('HONEYPOT_AUTO_BLOCK', true),
            'block_duration' => env('HONEYPOT_BLOCK_DURATION', 86400), // 24 hours
        ],
        
        'javascript_required' => env('JAVASCRIPT_REQUIRED', true),
        
        'user_agent_filtering' => [
            'enabled' => env('USER_AGENT_FILTERING_ENABLED', true),
            'blocked_patterns' => [
                'bot', 'crawler', 'spider', 'scraper', 'automated',
                'curl', 'wget', 'python', 'java', 'selenium'
            ],
            'suspicious_patterns' => [
                'headless', 'phantom', 'nightmare', 'puppeteer'
            ]
        ],
        
        'timing_analysis' => [
            'enabled' => env('TIMING_ANALYSIS_ENABLED', true),
            'min_form_time' => env('MIN_FORM_TIME', 3), // 3 seconds
            'max_form_time' => env('MAX_FORM_TIME', 3600), // 1 hour
        ]
    ],

    'recaptcha' => [
        'enabled' => env('RECAPTCHA_ENABLED', true),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
        'minimum_score' => env('RECAPTCHA_MINIMUM_SCORE', 0.5),
        'timeout' => env('RECAPTCHA_TIMEOUT', 5), // seconds
        'bypass_on_failure' => env('RECAPTCHA_BYPASS_ON_FAILURE', false),
        
        'actions' => [
            'login' => 'login',
            'register' => 'register',
            'otp_request' => 'otp_request',
            'otp_verify' => 'otp_verify',
            'wallet_topup' => 'wallet_topup',
            'service_booking' => 'service_booking',
        ],
        
        'score_thresholds' => [
            'login' => env('RECAPTCHA_LOGIN_THRESHOLD', 0.5),
            'register' => env('RECAPTCHA_REGISTER_THRESHOLD', 0.3),
            'otp_request' => env('RECAPTCHA_OTP_REQUEST_THRESHOLD', 0.4),
            'otp_verify' => env('RECAPTCHA_OTP_VERIFY_THRESHOLD', 0.6),
            'wallet_topup' => env('RECAPTCHA_WALLET_THRESHOLD', 0.7),
            'service_booking' => env('RECAPTCHA_BOOKING_THRESHOLD', 0.5),
        ],
        
        'triggers' => [
            'failed_attempts' => env('RECAPTCHA_FAILED_ATTEMPTS', 3),
            'suspicious_behavior' => env('RECAPTCHA_SUSPICIOUS_BEHAVIOR', true),
            'new_ip' => env('RECAPTCHA_NEW_IP', false),
            'bot_detected' => env('RECAPTCHA_BOT_DETECTED', true),
        ]
    ],

    'security_headers' => [
        'hsts' => [
            'enabled' => env('HSTS_ENABLED', true),
            'max_age' => env('HSTS_MAX_AGE', 31536000), // 1 year
            'include_subdomains' => env('HSTS_INCLUDE_SUBDOMAINS', true),
            'preload' => env('HSTS_PRELOAD', true),
        ],
        
        'content_type_options' => env('X_CONTENT_TYPE_OPTIONS', true),
        'frame_options' => env('X_FRAME_OPTIONS', 'DENY'),
        'xss_protection' => env('X_XSS_PROTECTION', true),
        'referrer_policy' => env('REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        
        'permissions_policy' => [
            'enabled' => env('PERMISSIONS_POLICY_ENABLED', true),
            'directives' => [
                'geolocation' => '()',
                'microphone' => '()',
                'camera' => '()',
                'payment' => '(self)',
                'sync-xhr' => '(self)',
            ]
        ]
    ],

    'session_security' => [
        'secure_cookies' => env('SECURE_COOKIES', true),
        'same_site' => env('SAME_SITE_COOKIES', 'lax'),
        'max_concurrent_sessions' => env('MAX_CONCURRENT_SESSIONS', 3),
        'session_timeout' => env('SESSION_TIMEOUT', 7200), // 2 hours
        'idle_timeout' => env('IDLE_TIMEOUT', 1800), // 30 minutes
    ],

    'monitoring' => [
        'enabled' => env('SECURITY_MONITORING_ENABLED', true),
        'log_level' => env('SECURITY_LOG_LEVEL', 'info'),
        'alert_threshold' => env('SECURITY_ALERT_THRESHOLD', 100),
        'notification_channels' => ['mail', 'slack'],
        
        'ip_whitelist' => array_filter(explode(',', env('SECURITY_IP_WHITELIST', ''))),
        'ip_blacklist' => array_filter(explode(',', env('SECURITY_IP_BLACKLIST', ''))),
    ],

    'otp_security' => [
        'max_attempts' => env('OTP_MAX_VERIFICATION_ATTEMPTS', 5),
        'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 5),
        'length' => env('OTP_LENGTH', 4),
        'resend_cooldown' => env('OTP_RESEND_COOLDOWN', 60), // 1 minute
        'max_resends' => env('OTP_MAX_RESENDS', 3),
        'lockout_duration' => env('OTP_LOCKOUT_DURATION', 3600), // 1 hour
    ]
];