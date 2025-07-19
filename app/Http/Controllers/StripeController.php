<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:10',
            ]);

            // Set your Stripe secret key
            Stripe::setApiKey(config('services.stripe.secret'));

            // Amount should be in cents
            $amount = round($request->amount * 100);

            // Create a PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'metadata' => [
                    'user_id' => Auth::id(),
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handlePaymentSuccess(Request $request)
    {
        try {
            $request->validate([
                'paymentIntent' => 'required',
                'amount' => 'required|numeric|min:10',
            ]);

            // Start transaction
            \DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'payment_type' => 'credit_card',
                'amount' => $request->amount,
                'status' => 'approved',
                'notes' => 'Stripe payment: ' . $request->paymentIntent,
            ]);

            // Update wallet balance
            $wallet = Wallet::where('user_id', Auth::id())->firstOrFail();
            $wallet->balance += $request->amount;
            $wallet->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment' => $payment,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 