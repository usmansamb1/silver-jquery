<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\HyperpayService;

final class HyperpayController extends Controller
{
    /**
     * Handle initial redirect request: create checkout session and redirect to hosted page.
     */
    public function redirectToCheckout(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10'],
        ]);

        $amount = $validated['amount'];

        // Create Hyperpay checkout session
        $checkoutId = HyperpayService::createCheckoutSession((float) $amount);

        // Redirect to local checkout page
        return redirect()->route('wallet.hyperpay.checkoutPage', ['checkoutId' => $checkoutId]);
    }

    /**
     * Show the checkout page with embedded Hyperpay widget.
     */
    public function showCheckoutPage(Request $request)
    {
        $checkoutId = $request->query('checkoutId');
        if (!is_string($checkoutId) || $checkoutId === '') {
            abort(400, 'Invalid checkout ID.');
        }

        return view('wallet.hyperpay-redirect', ['checkoutId' => $checkoutId]);
    }
} 