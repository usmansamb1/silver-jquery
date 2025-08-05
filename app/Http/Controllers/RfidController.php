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
     * Display the RFID management dashboard.
     */
    public function index()
    {
        $vehicles = Vehicle::where('user_id', auth()->id())
            ->whereNotNull('rfid_number')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingTransfers = RfidTransfer::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // /dd($vehicles);
        return view('rfid.index', compact('vehicles', 'pendingTransfers'));
    }

    /**
     * Show the form for transferring an RFID from one vehicle to another.
     */
    public function transferForm(Request $request)
    {
        $sourceVehicles = Vehicle::where('user_id', auth()->id())
            ->whereNotNull('rfid_number')
            ->orderBy('plate_number')
            ->get();

        $targetVehicles = Vehicle::where('user_id', auth()->id())
            ->whereNull('rfid_number')
            ->orderBy('plate_number')
            ->get();

        // Pre-select source vehicle if specified in query
        $selectedSourceId = null;
        if ($request->has('source') && $sourceVehicles->contains('id', $request->source)) {
            $selectedSourceId = $request->source;
        }

        return view('rfid.transfer', compact('sourceVehicles', 'targetVehicles', 'selectedSourceId'));
    }

    /**
     * Initiate the RFID transfer process.
     */
    public function initiateTransfer(Request $request)
    {
        $validated = $request->validate([
            'source_vehicle_id' => 'required|exists:vehicles,id',
            'target_vehicle_id' => 'required|exists:vehicles,id|different:source_vehicle_id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Validate vehicles belong to the user and have proper RFID status
        $sourceVehicle = Vehicle::where('id', $validated['source_vehicle_id'])
            ->where('user_id', auth()->id())
            ->whereNotNull('rfid_number')
            ->first();

        $targetVehicle = Vehicle::where('id', $validated['target_vehicle_id'])
            ->where('user_id', auth()->id())
            ->whereNull('rfid_number')
            ->first();

        if (! $sourceVehicle || ! $targetVehicle) {
            return back()->with('error', 'Invalid source or target vehicle selection.');
        }

        // Generate OTP code
        $otpCode = random_int(100000, 999999);
        $otpExpiry = now()->addMinutes(10);

        // Create transfer record
        DB::beginTransaction();

        try {
            $transfer = RfidTransfer::create([
                'user_id' => auth()->id(),
                'source_vehicle_id' => $sourceVehicle->id,
                'target_vehicle_id' => $targetVehicle->id,
                'rfid_number' => $sourceVehicle->rfid_number,
                'otp_code' => $otpCode,
                'otp_expires_at' => $otpExpiry,
                'status' => 'pending',
                'notes' => $validated['notes'],
                'transfer_details' => [
                    'source_vehicle' => [
                        'plate_number' => $sourceVehicle->plate_number,
                        'make' => $sourceVehicle->make,
                        'model' => $sourceVehicle->model,
                        'rfid_balance' => $sourceVehicle->rfid_balance,
                    ],
                    'target_vehicle' => [
                        'plate_number' => $targetVehicle->plate_number,
                        'make' => $targetVehicle->make,
                        'model' => $targetVehicle->model,
                    ],
                    'initiated_at' => now()->toIso8601String(),
                ],
            ]);

            // Send OTP to user via notification
            auth()->user()->notify(new RfidTransferOtp($transfer, $otpCode));

            // Log the initiation of RFID transfer
            LogHelper::log('rfid_transfer_initiated', 'Initiated RFID transfer from '.$sourceVehicle->plate_number.' to '.$targetVehicle->plate_number, $transfer, [
                'source_vehicle' => [
                    'id' => $sourceVehicle->id,
                    'plate_number' => $sourceVehicle->plate_number,
                    'rfid_number' => $sourceVehicle->rfid_number,
                ],
                'target_vehicle' => [
                    'id' => $targetVehicle->id,
                    'plate_number' => $targetVehicle->plate_number,
                ],
            ]);

            DB::commit();

            return redirect()->route('rfid.verify-transfer', $transfer->id)
                ->with('success', 'Transfer initiated. An OTP has been sent to your email and phone for verification.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to initiate transfer: '.$e->getMessage());
        }
    }

    /**
     * Show the verification form for an RFID transfer.
     */
    public function verifyTransferForm(RfidTransfer $transfer)
    {
        // Ensure transfer belongs to authenticated user and is pending
        if ($transfer->user_id !== auth()->id() || $transfer->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        // Check if OTP is expired
        if ($transfer->isOtpExpired()) {
            return redirect()->route('rfid.transfer')
                ->with('error', 'OTP has expired. Please initiate the transfer again.');
        }

        return view('rfid.verify-transfer', compact('transfer'));
    }

    /**
     * Verify the OTP and complete the RFID transfer.
     */
    public function verifyTransfer(Request $request, RfidTransfer $transfer)
    {
        // Ensure transfer belongs to authenticated user and is pending
        if ($transfer->user_id !== auth()->id() || $transfer->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        // Validate OTP
        $validated = $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        // Check if OTP matches and is not expired
        if ($transfer->otp_code !== $validated['otp_code']) {
            return back()->with('error', 'Invalid OTP code. Please try again.');
        }

        if ($transfer->isOtpExpired()) {
            return redirect()->route('rfid.transfer')
                ->with('error', 'OTP has expired. Please initiate the transfer again.');
        }

        // Process transfer
        DB::beginTransaction();

        try {
            $sourceVehicle = $transfer->sourceVehicle;
            $targetVehicle = $transfer->targetVehicle;

            // Update source vehicle - remove RFID
            $sourceVehicle->update([
                'rfid_number' => null,
                'rfid_balance' => 0.00,
                'rfid_status' => null,
            ]);

            // Update target vehicle - assign RFID
            $targetVehicle->update([
                'rfid_number' => $transfer->rfid_number,
                'rfid_status' => 'active',
                'rfid_balance' => $transfer->transfer_details['source_vehicle']['rfid_balance'] ?? 0.00,
            ]);

            // Update transfer record
            $transfer->update([
                'verified_at' => now(),
                'status' => 'completed',
                'transfer_details' => array_merge($transfer->transfer_details ?? [], [
                    'completed_at' => now()->toIso8601String(),
                ]),
            ]);

            // Log the completion of RFID transfer
            LogHelper::log('rfid_transfer_completed', 'Completed RFID transfer from '.$sourceVehicle->plate_number.' to '.$targetVehicle->plate_number, $transfer, [
                'source_vehicle' => [
                    'id' => $sourceVehicle->id,
                    'plate_number' => $sourceVehicle->plate_number,
                ],
                'target_vehicle' => [
                    'id' => $targetVehicle->id,
                    'plate_number' => $targetVehicle->plate_number,
                    'rfid_number' => $transfer->rfid_number,
                    'rfid_balance' => $targetVehicle->rfid_balance,
                ],
            ]);

            DB::commit();

            return redirect()->route('rfid.index')
                ->with('success', 'RFID transfer completed successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to complete transfer: '.$e->getMessage());
        }
    }

    /**
     * Cancel an RFID transfer.
     */
    public function cancelTransfer(RfidTransfer $transfer)
    {
        // Ensure transfer belongs to authenticated user and is pending
        if ($transfer->user_id !== auth()->id() || $transfer->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        // Cancel transfer
        $transfer->update([
            'status' => 'cancelled',
            'transfer_details' => array_merge($transfer->transfer_details ?? [], [
                'cancelled_at' => now()->toIso8601String(),
            ]),
        ]);

        // Log the cancellation of RFID transfer
        LogHelper::log('rfid_transfer_cancelled', 'Cancelled RFID transfer', $transfer, [
            'source_vehicle' => $transfer->sourceVehicle->plate_number,
            'target_vehicle' => $transfer->targetVehicle->plate_number,
            'rfid_number' => $transfer->rfid_number,
        ]);

        return redirect()->route('rfid.index')
            ->with('success', 'RFID transfer has been cancelled.');
    }

    /**
     * Show the form for recharging an RFID.
     */
    public function rechargeForm()
    {
        $vehicles = Vehicle::where('user_id', auth()->id())
            ->whereNotNull('rfid_number')
            ->orderBy('plate_number')
            ->get();

        $walletBalance = auth()->user()->wallet->balance ?? 0.00;

        return view('rfid.recharge', compact('vehicles', 'walletBalance'));
    }

    /**
     * Process an RFID recharge request.
     */
    public function processRecharge(Request $request)
    {
        $validated = $request->validate([
            'vehicles' => 'required|array|min:1',
            'vehicles.*' => 'exists:vehicles,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:wallet,credit_card',
        ]);

        // Validate vehicles belong to the user and have RFID
        $vehicles = Vehicle::whereIn('id', $validated['vehicles'])
            ->where('user_id', auth()->id())
            ->whereNotNull('rfid_number')
            ->get();

        if ($vehicles->count() !== count($validated['vehicles'])) {
            return back()->with('error', 'One or more selected vehicles are invalid or don\'t have an RFID.');
        }

        // Calculate total amount
        $amount = $validated['amount'];
        $totalAmount = $amount * $vehicles->count();

        // Process payment
        DB::beginTransaction();

        try {
            // Handle payment based on method
            if ($validated['payment_method'] === 'wallet') {
                // Check wallet balance
                $wallet = auth()->user()->wallet;

                if (! $wallet || $wallet->balance < $totalAmount) {
                    return back()->with('error', 'Insufficient wallet balance.');
                }

                // Deduct from wallet
                $wallet->withdraw($totalAmount, 'RFID recharge for '.$vehicles->count().' vehicles.');
                $paymentReference = 'wallet-'.Str::random(10);
                $paymentStatus = 'paid';
            } else {
                // Credit card payment - redirect to HyperPay
                return redirect()->route('rfid.recharge')->with('error', 'Please use the HyperPay payment form for credit card payments.');
            }

            // Process each vehicle recharge
            foreach ($vehicles as $vehicle) {
                // Create transaction record
                $transaction = RfidTransaction::create([
                    'vehicle_id' => $vehicle->id,
                    'user_id' => auth()->id(),
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'transaction_reference' => $paymentReference,
                    'status' => 'completed',
                    'payment_status' => $paymentStatus,
                    'transaction_details' => [
                        'previous_balance' => $vehicle->rfid_balance,
                        'new_balance' => $vehicle->rfid_balance + $amount,
                        'recharge_date' => now()->toIso8601String(),
                    ],
                ]);

                // Update vehicle RFID balance
                $vehicle->update([
                    'rfid_balance' => $vehicle->rfid_balance + $amount,
                    'rfid_status' => 'active',
                ]);

                // Log the RFID recharge
                LogHelper::log('rfid_recharge', 'Recharged RFID for vehicle '.$vehicle->plate_number, $transaction, [
                    'vehicle_id' => $vehicle->id,
                    'plate_number' => $vehicle->plate_number,
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'previous_balance' => $transaction->transaction_details['previous_balance'],
                    'new_balance' => $transaction->transaction_details['new_balance'],
                ]);

                // Send email notification
                try {
                    auth()->user()->notify(new RfidRechargeSuccess($vehicle, $amount, $paymentReference));
                } catch (Exception $e) {
                    Log::error('Failed to send RFID recharge email', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id(),
                        'vehicle_id' => $vehicle->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('rfid.index')
                ->with('success', 'RFID recharge completed successfully for '.$vehicles->count().' vehicles.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to process recharge: '.$e->getMessage());
        }
    }

    /**
     * Show RFID transaction history.
     */
    public function transactionHistory()
    {
        $vehicles = Vehicle::where('user_id', auth()->id())
            ->whereNotNull('rfid_number')
            ->pluck('id');

        $transactions = RfidTransaction::whereIn('vehicle_id', $vehicles)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('rfid.transactions', compact('transactions'));
    }

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
                'payment_method' => 'required|in:credit_card,mada_card,AMEX,STC_PAY,URPAY',
                'brand' => 'nullable|string|in:credit_card,mada_card,AMEX,STC_PAY,URPAY',
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

            // Get the selected brand (prefer 'brand' parameter over 'payment_method')
            $brand = $validated['brand'] ?? $validated['payment_method'];

            // Define payment method configurations with correct HyperPay brand identifiers
            $paymentMethods = [
                'credit_card' => [
                    'entity_id' => config('services.hyperpay.entity_id_credit'),
                    'form_brand' => 'VISA MASTER',
                    'display_name' => 'Visa / MasterCard'
                ],
                'mada_card' => [
                    'entity_id' => config('services.hyperpay.entity_id_mada'),
                    'form_brand' => 'MADA',
                    'display_name' => 'MADA Card'
                ],
                'AMEX' => [
                    'entity_id' => config('services.hyperpay.entity_id_credit'),
                    'form_brand' => 'AMEX VISA MASTER', // HyperPay only supports VISA MASTER or MADA
                    'display_name' => 'American Express'
                ],
                'STC_PAY' => [
                    'entity_id' => config('services.hyperpay.entity_id_credit'),
                    'form_brand' => 'STC_PAY', // HyperPay only supports VISA MASTER or MADA
                    'display_name' => 'STC Pay'
                ],
                'URPAY' => [
                    'entity_id' => config('services.hyperpay.entity_id_credit'),
                    'form_brand' => 'URPAY', // HyperPay only supports VISA MASTER or MADA
                    'display_name' => 'URPay'
                ]
            ];

            // Get payment configuration or default to credit_card
            $paymentConfig = $paymentMethods[$brand] ?? $paymentMethods['credit_card'];
            $entityId = $paymentConfig['entity_id'];
            $formBrand = $paymentConfig['form_brand'];
            $displayName = $paymentConfig['display_name'];

            Log::info('RFID HyperPay payment method configuration', [
                'brand' => $brand,
                'entity_id' => $entityId,
                'form_brand' => $formBrand, 
                'display_name' => $displayName,
                'payment_config' => $paymentConfig
            ]);

            $user = auth()->user();
            $merchantTransactionId = uniqid('rfid_');

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

            $response = $client->post(config('services.hyperpay.base_url').'v1/checkouts', [
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

            // Store session data (matching wallet and service booking implementation)
            session([
                'rfid_hyperpay_amount' => $totalAmount,
                'rfid_hyperpay_entity_id' => $entityId,
                'rfid_hyperpay_vehicles' => $validated['vehicles'],
                'rfid_hyperpay_amount_per_vehicle' => $amount,
                'rfid_hyperpay_merchant_transaction_id' => $merchantTransactionId,
                'rfid_hyperpay_brand' => $brand,
                'rfid_hyperpay_display_name' => $displayName,
                'rfid_hyperpay_form_brand' => $formBrand,
            ]);

            // Return the form HTML using same approach as other controllers
            $formHtml = view('rfid.partials.hyperpay-form', [
                'checkoutId' => $checkoutId,
                'amount' => $totalAmount,
                'brand' => $brand,
                'formBrand' => $formBrand,
                'displayName' => $displayName,
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

    /**
     * Handle HyperPay payment status callback.
     */
    public function hyperpayStatus(Request $request)
    {
        $checkoutId = $request->input('id');
        $resourcePath = $request->input('resourcePath');
        $user = auth()->user();

        // Validate session data
        $sessionAmount = session('rfid_hyperpay_amount');
        $sessionEntityId = session('rfid_hyperpay_entity_id');
        $sessionVehicles = session('rfid_hyperpay_vehicles');
        $sessionAmountPerVehicle = session('rfid_hyperpay_amount_per_vehicle');

        if (! $sessionAmount || ! $sessionEntityId || ! $sessionVehicles) {
            return redirect()->route('rfid.recharge')->with('error', 'Invalid payment session');
        }

        // Handle test mode
        if ($checkoutId === 'demo-checkout-id') {
            $result = [
                'id' => 'test-'.uniqid(),
                'amount' => $sessionAmount,
                'currency' => 'SAR',
                'result' => [
                    'code' => '000.100.110',
                    'description' => 'Request successfully processed in TEST mode',
                ],
                'test_mode' => true,
                'paymentBrand' => 'VISA',
                'card' => [
                    'bin' => '411111',
                    'last4Digits' => '1111',
                ],
            ];

            return $this->processSuccessfulRfidHyperpayPayment($user, $sessionAmount, $sessionVehicles, $sessionAmountPerVehicle, $result, $resourcePath);
        }

        // Verify payment with HyperPay
        try {
            $client = new Client;
            $response = $client->get(config('services.hyperpay.base_url').ltrim($resourcePath, '/'), [
                'headers' => [
                    'Authorization' => 'Bearer '.config('services.hyperpay.access_token'),
                ],
                'query' => [
                    'entityId' => $sessionEntityId,
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            // Check if payment was successful
            $resultCode = $result['result']['code'] ?? '';
            $isSuccess = preg_match('/^(000\.000\.|000\.100\.1|000\.200)/', $resultCode) ||
                         preg_match('/^(000\.000\.000|000\.100\.110|000\.100\.111|000\.100\.112)$/', $resultCode);

            if ($isSuccess) {
                // Check for duplicate transaction
                $hyperpayTransactionId = $result['id'] ?? '';
                if ($hyperpayTransactionId && RfidTransaction::where('hyperpay_transaction_id', $hyperpayTransactionId)->exists()) {
                    return redirect()->route('rfid.index')->with('error', 'This transaction has already been processed.');
                }

                // Validate amount
                $returnedAmount = (float) ($result['amount'] ?? 0);
                if (abs($returnedAmount - $sessionAmount) > 0.01) {
                    $this->logFailedRfidHyperpayPayment($user, $sessionAmount, $resultCode, 'Amount mismatch', $result);

                    return redirect()->route('rfid.recharge')->with('error', 'Payment amount mismatch');
                }

                return $this->processSuccessfulRfidHyperpayPayment($user, $sessionAmount, $sessionVehicles, $sessionAmountPerVehicle, $result, $resourcePath);
            } else {
                $this->logFailedRfidHyperpayPayment($user, $sessionAmount, $resultCode, $result['result']['description'] ?? 'Unknown error', $result);

                return redirect()->route('rfid.recharge')->with('error', 'Payment failed: '.($result['result']['description'] ?? 'Unknown error'));
            }

        } catch (Exception $e) {
            Log::error('HyperPay RFID status error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'checkout_id' => $checkoutId,
            ]);

            return redirect()->route('rfid.recharge')->with('error', 'Payment verification failed');
        }
    }

    /**
     * Process successful HyperPay payment for RFID recharge.
     */
    private function processSuccessfulRfidHyperpayPayment($user, $totalAmount, $vehicleIds, $amountPerVehicle, $result, $resourcePath)
    {
        DB::beginTransaction();

        try {
            // Get vehicles
            $vehicles = Vehicle::whereIn('id', $vehicleIds)
                ->where('user_id', $user->id)
                ->whereNotNull('rfid_number')
                ->get();

            if ($vehicles->count() !== count($vehicleIds)) {
                DB::rollBack();

                return redirect()->route('rfid.recharge')->with('error', 'Vehicle validation failed');
            }

            $hyperpayTransactionId = $result['id'] ?? '';
            $cardBrand = $this->extractCardBrand($result);
            $paymentReference = 'hyperpay-'.$hyperpayTransactionId;

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_type' => 'credit_card',
                'card_brand' => $cardBrand,
                'amount' => $totalAmount,
                'status' => 'approved',
                'notes' => 'RFID recharge for '.$vehicles->count().' vehicles via HyperPay',
                'hyperpay_transaction_id' => $hyperpayTransactionId,
            ]);

            // Process each vehicle recharge
            foreach ($vehicles as $vehicle) {
                $transaction = RfidTransaction::create([
                    'vehicle_id' => $vehicle->id,
                    'user_id' => $user->id,
                    'amount' => $amountPerVehicle,
                    'payment_method' => 'credit_card',
                    'transaction_reference' => $paymentReference,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'hyperpay_transaction_id' => $hyperpayTransactionId,
                    'transaction_details' => [
                        'previous_balance' => $vehicle->rfid_balance,
                        'new_balance' => $vehicle->rfid_balance + $amountPerVehicle,
                        'recharge_date' => now()->toIso8601String(),
                        'payment_method' => 'credit_card',
                        'card_brand' => $cardBrand,
                        'hyperpay_result' => $result,
                    ],
                ]);

                // Update vehicle RFID balance
                $vehicle->update([
                    'rfid_balance' => $vehicle->rfid_balance + $amountPerVehicle,
                    'rfid_status' => 'active',
                ]);

                // Log the RFID recharge
                LogHelper::log('rfid_recharge_hyperpay', 'Recharged RFID for vehicle '.$vehicle->plate_number.' via HyperPay', $transaction, [
                    'vehicle_id' => $vehicle->id,
                    'plate_number' => $vehicle->plate_number,
                    'amount' => $amountPerVehicle,
                    'payment_method' => 'credit_card',
                    'card_brand' => $cardBrand,
                    'hyperpay_transaction_id' => $hyperpayTransactionId,
                    'previous_balance' => $transaction->transaction_details['previous_balance'],
                    'new_balance' => $transaction->transaction_details['new_balance'],
                ]);

                // Send email notification
                try {
                    $user->notify(new RfidRechargeSuccess($vehicle, $amountPerVehicle, $paymentReference, $cardBrand));
                } catch (Exception $e) {
                    Log::error('Failed to send RFID recharge email', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'vehicle_id' => $vehicle->id,
                    ]);
                }
            }

            // Clear session data
            session()->forget([
                'rfid_hyperpay_amount',
                'rfid_hyperpay_entity_id',
                'rfid_hyperpay_vehicles',
                'rfid_hyperpay_amount_per_vehicle',
                'rfid_hyperpay_merchant_transaction_id',
            ]);

            DB::commit();

            return redirect()->route('rfid.index')
                ->with('success', 'RFID recharge completed successfully for '.$vehicles->count().' vehicles via credit card.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process RFID HyperPay payment', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'amount' => $totalAmount,
            ]);

            return redirect()->route('rfid.recharge')->with('error', 'Failed to process payment');
        }
    }

    /**
     * Log failed HyperPay payment for RFID recharge.
     */
    private function logFailedRfidHyperpayPayment($user, $amount, $code, $description, $result)
    {
        LogHelper::log('rfid_hyperpay_payment_failed', 'RFID HyperPay payment failed', null, [
            'user_id' => $user->id,
            'amount' => $amount,
            'error_code' => $code,
            'error_description' => $description,
            'hyperpay_result' => $result,
        ]);
    }

    /**
     * Extract card brand from HyperPay result.
     */
    private function extractCardBrand($result)
    {
        // Try to get from paymentBrand
        if (isset($result['paymentBrand'])) {
            return strtoupper($result['paymentBrand']);
        }

        // Try to get from card BIN
        if (isset($result['card']['bin'])) {
            return $this->getBrandFromBin($result['card']['bin']);
        }

        // Try to get from descriptor template
        if (isset($result['descriptor']['template'])) {
            $template = strtoupper($result['descriptor']['template']);
            if (strpos($template, 'VISA') !== false) {
                return 'VISA';
            }
            if (strpos($template, 'MASTERCARD') !== false) {
                return 'MASTERCARD';
            }
            if (strpos($template, 'MADA') !== false) {
                return 'MADA';
            }
        }

        // Fallback to session entity ID
        $sessionEntityId = session('rfid_hyperpay_entity_id');
        if ($sessionEntityId === config('services.hyperpay.entity_id_mada')) {
            return 'MADA';
        }

        return 'VISA'; // Default fallback
    }

    /**
     * Get card brand from BIN.
     */
    private function getBrandFromBin($bin)
    {
        $bin = (string) $bin;

        // MADA card BIN ranges (Saudi domestic cards)
        $madaBins = [
            '588845', '627606', '636120', '968201', '968202', '968203', '968204', '968205', '968206',
            '968207', '968208', '968209', '968210', '968211', '446393', '457865', '968212', '968213',
            '588848', '588850', '588851', '588852', '588853', '588854', '588855', '588856', '588857',
            '588858', '588859', '588860', '588861', '588862', '588863', '588864', '588865', '588866',
            '588867', '588868', '588869', '588870', '588871', '588872', '588873', '588874', '588875',
            '588876', '588877', '588878', '588879', '588880', '588881', '588882', '588883', '588884',
            '588885', '588886', '588887', '588888', '588889', '588890', '588891', '588892', '588893',
            '588894', '588895', '588896', '588897', '588898', '588899', '589005', '589006', '589007',
            '589008', '589009', '589010', '589011', '589012', '589013', '589014', '589015', '589016',
            '589017', '589018', '589019', '589020', '589021', '589022', '589023', '589024', '589025',
            '589026', '589027', '589028', '589029', '589030', '589031', '589032', '589033', '589034',
            '589035', '589036', '589037', '589038', '589039', '589040', '589041', '589042', '589043',
            '589044', '589045', '589046', '589047', '589048', '589049', '589050', '589051', '589052',
            '589053', '589054', '589055', '589056', '589057', '589058', '589059', '589060', '589061',
            '589062', '589063', '589064', '589065', '589066', '589067', '589068', '589069', '589070',
            '589071', '589072', '589073', '589074', '589075', '589076', '589077', '589078', '589079',
            '589080', '589081', '589082', '589083', '589084', '589085', '589086', '589087', '589088',
            '589089', '589090', '589091', '589092', '589093', '589094', '589095', '589096', '589097',
            '589098', '589099', '530060', '530061', '530062', '530063', '530064', '530065', '530066',
            '530067', '530068', '530069', '530070', '530071', '530072', '530073', '530074', '530075',
            '530076', '530077', '530078', '530079', '530080', '530081', '530082', '530083', '530084',
            '530085', '530086', '530087', '530088', '530089', '530090', '530091', '530092', '530093',
            '530094', '530095', '530096', '530097', '530098', '530099', '605141', '968200',
        ];

        foreach ($madaBins as $madaBin) {
            if (substr($bin, 0, strlen($madaBin)) === $madaBin) {
                return 'MADA';
            }
        }

        // VISA cards start with 4
        if (substr($bin, 0, 1) === '4') {
            return 'VISA';
        }

        // Mastercard starts with 5 or 2221-2720
        if (substr($bin, 0, 1) === '5') {
            return 'MASTERCARD';
        }

        if (substr($bin, 0, 4) >= '2221' && substr($bin, 0, 4) <= '2720') {
            return 'MASTERCARD';
        }

        return 'VISA'; // Default fallback
    }

    /**
     * Validate checkout session.
     */
    public function validateCheckoutSession(Request $request)
    {
        $checkoutId = $request->input('checkout_id');

        if (! $checkoutId) {
            return response()->json(['error' => 'Checkout ID required'], 400);
        }

        try {
            $client = new Client;
            $entityId = session('rfid_hyperpay_entity_id');

            $response = $client->get(config('services.hyperpay.base_url').'v1/checkouts/'.$checkoutId.'/payment', [
                'headers' => [
                    'Authorization' => 'Bearer '.config('services.hyperpay.access_token'),
                ],
                'query' => [
                    'entityId' => $entityId,
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            return response()->json([
                'valid' => isset($result['result']['code']),
                'result' => $result,
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Validation failed'], 500);
        }
    }
}
