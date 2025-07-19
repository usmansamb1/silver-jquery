<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Payment;
use App\Models\RfidTransaction;
use App\Models\RfidTransfer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Notifications\RfidRechargeSuccess;
use App\Notifications\RfidTransferOtp;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RfidController extends Controller
{
    /**
     * Get HyperPay form for RFID recharge.
     */
    public function getHyperpayForm(Request $request)
    {
        try {
            Log::info('RFID HyperPay form request started', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'vehicles' => 'required|array|min:1',
                'vehicles.*' => 'exists:vehicles,id',
                'amount' => 'required|numeric|min:1|max:50000',
                'payment_method' => 'required|in:credit_card,mada_card',
            ]);

            Log::info('RFID HyperPay validation passed', ['validated_data' => $validated]);

            // Validate vehicles belong to the user and have RFID
            $vehicles = Vehicle::whereIn('id', $validated['vehicles'])
                ->where('user_id', auth()->id())
                ->whereNotNull('rfid_number')
                ->get();

            if ($vehicles->count() !== count($validated['vehicles'])) {
                return response()->json(['error' => 'One or more selected vehicles are invalid.'], 400);
            }

            // Calculate total amount
            $amount = $validated['amount'];
            $totalAmount = $amount * $vehicles->count();

            // Determine entity ID based on payment method
            $entityId = $validated['payment_method'] === 'mada_card'
                ? config('services.hyperpay.entity_id_mada')
                : config('services.hyperpay.entity_id_credit');

            $user = auth()->user();
            $merchantTransactionId = 'rfid-'.time().'-'.$user->id;

            // Create HyperPay checkout session
            $client = new Client;
            $payload = [
                'entityId' => $entityId,
                'amount' => number_format($totalAmount, 2, '.', ''),
                'currency' => config('services.hyperpay.currency'),
                'paymentType' => 'DB',
                'merchantTransactionId' => $merchantTransactionId,
                'customer.email' => $user->email,
                'testMode' => 'EXTERNAL',
                'customParameters[3DS2_enrolled]' => 'true',
                'customParameters[3DS2_flow]' => 'challenge',
            ];

            $response = $client->post(config('services.hyperpay.base_url').'/checkouts', [
                'headers' => [
                    'Authorization' => 'Bearer '.config('services.hyperpay.access_token'),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $payload,
            ]);

            $result = json_decode($response->getBody(), true);

            if (! isset($result['id'])) {
                return response()->json(['error' => 'Failed to create checkout session'], 400);
            }

            $checkoutId = $result['id'];

            // Store session data
            session([
                'rfid_hyperpay_amount' => $totalAmount,
                'rfid_hyperpay_entity_id' => $entityId,
                'rfid_hyperpay_vehicles' => $validated['vehicles'],
                'rfid_hyperpay_amount_per_vehicle' => $amount,
                'rfid_hyperpay_merchant_transaction_id' => $merchantTransactionId,
            ]);

            // Return the form HTML
            $formHtml = view('rfid.partials.hyperpay-form', [
                'checkoutId' => $checkoutId,
                'amount' => $totalAmount,
                'entityId' => $entityId,
                'testMode' => config('app.env') !== 'production',
                'returnUrl' => route('rfid.hyperpay.status'),
            ])->render();

            return response()->json([
                'success' => true,
                'form' => $formHtml,
                'checkout_id' => $checkoutId,
                'amount' => $totalAmount,
            ]);

        } catch (Exception $e) {
            Log::error('HyperPay RFID checkout error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'amount' => $request->input('amount', 0),
            ]);

            return response()->json(['error' => 'Payment system error: ' . $e->getMessage()], 500);
        }
    }
}