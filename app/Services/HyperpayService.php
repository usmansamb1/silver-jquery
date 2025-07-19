<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Service to interact with Hyperpay APIs.
 */
final class HyperpayService
{
    /**
     * Create a Hyperpay checkout session and return the checkout ID.
     * Replace this stub with real API integration.
     */
    public static function createCheckoutSession(float $amount): string
    {
        // Example API call - adjust endpoint and credentials
        // $response = Http::withBasicAuth(config('services.hyperpay.username'), config('services.hyperpay.password'))
        //     ->post('https://eu-test.oppwa.com/v1/checkouts', [
        //         'entityId' => config('services.hyperpay.entity_id'),
        //         'amount' => number_format($amount, 2, '.', ''),
        //         'currency' => 'SAR',
        //         'paymentType' => 'DB'
        //     ]);
        // return $response->json('id');

        // Stub: generate a unique ID for demonstration purposes
        return 'checkout_' . bin2hex(random_bytes(8));
    }
} 