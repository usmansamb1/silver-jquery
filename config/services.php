<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'hyperpay' => [
        'base_url' => env('HYPERPAY_BASE_URL', 'https://test.oppwa.com/'),
        'widget_url' => env('HYPERPAY_WIDGET_URL', 'https://eu-test.oppwa.com/'),
        'access_token' => env('HYPERPAY_ACCESS_TOKEN', 'OGFjN2E0Yzg5NzBmYjBjZDAxOTcxMTkxZDg1MDA0OTF8ZCVnWm1pdDR1bW1lRyFAZ2F3VE0='),
        'entity_id_credit' => env('HYPERPAY_ENTITY_ID_VISA', '8ac7a4c8970fb0cd0197119241840497'),
        'entity_id_mada' => env('HYPERPAY_ENTITY_ID_MADA', '8ac7a4c8970fb0cd019711929808049d'),
        'currency' => env('HYPERPAY_CURRENCY', 'SAR'),
        'mode' => env('HYPERPAY_MODE', 'test'),
        'webhook_secret' => env('HYPERPAY_WEBHOOK_SECRET'),
    ],

];
