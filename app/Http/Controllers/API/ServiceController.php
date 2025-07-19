<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\Wallet;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\Order;
use App\Models\User;
use App\Models\SavedCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use App\Helpers\LogHelper;

class ServiceController extends Controller
{
    public function orderService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'service_type' => 'required|in:RFID,None-RFID,Yaseeir',
            'payment_type' => 'required|in:prepaid,bank_transfer',
            'amount' => 'required|numeric|min:1',
            'file' => 'required_if:payment_type,bank_transfer',
            'notes' => 'nullable'
        ]);

        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $serviceOrder = ServiceOrder::create([
            'user_id' => $request->user_id,
            'service_type' => $request->service_type,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
            'file' => $request->file,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        if ($request->payment_type == 'prepaid'){
            // Process prepaid payment (simulate credit card integration)
            $wallet = Wallet::where('user_id', $request->user_id)->first();
            if ($wallet->balance >= $request->amount) {
                $wallet->balance -= $request->amount;
                $wallet->save();
                $serviceOrder->update(['status' => 'approved']);
            } else {
                return response()->json(['message' => 'Insufficient wallet balance'], 400);
            }
        }

        if ($request->service_type == 'RFID'){
            $user = \App\Models\User::find($request->user_id);
            $dummyApiUrl = 'http://dummy-api-url.local/api/notify';
            // Send user and service order data to an external (dummy) API endpoint
            Http::post($dummyApiUrl, [
                'user' => $user,
                'service' => $serviceOrder
            ]);
        }

        return response()->json(['message' => 'Service order placed', 'service_order' => $serviceOrder]);
    }
    
    /**
     * Book a service via API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookService(Request $request)
    {
        try {
            // Validate the form data
            $validator = Validator::make($request->all(), [
                'pickup_location' => 'required|string|max:255',
                'services' => 'required|array|min:1',
                'services.*.service_type' => 'required|string',
                'services.*.service_id' => 'required|string',
                'services.*.refule_amount' => 'required|numeric|min:0',
                'services.*.vehicle_make' => 'required|string|max:50',
                'services.*.vehicle_manufacturer' => 'nullable|string|max:50',
                'services.*.vehicle_model' => 'required|string|max:50',
                'services.*.vehicle_year' => 'required|string|max:20',
                'services.*.plate_number' => 'required|string|max:20',
                'payment_method' => 'required|in:wallet,credit_card',
                'card_id' => 'required_if:payment_method,credit_card',
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get user
            $user = User::findOrFail($request->user_id);

            // Generate a unique reference number
            $referenceNumber = 'SRV-' . strtoupper(Str::random(8));
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Create order record
                $order = new Order();
                $order->reference_number = $referenceNumber;
                $order->user_id = $request->user_id;
                $order->pickup_location = $request->input('pickup_location');
                $order->status = 'pending';
                $order->payment_method = $request->input('payment_method');
                
                // Calculate total amount (services + refueling)
                $serviceCount = count($request->input('services'));
                $baseServicePrice = 150.00; // Base price per service
                
                // Calculate service cost
                $serviceAmount = $serviceCount * $baseServicePrice;
                
                // Calculate refueling amount
                $refuelingAmount = 0;
                foreach ($request->input('services') as $service) {
                    $refuelingAmount += floatval($service['refule_amount']);
                }
                
                // Calculate subtotal
                $subtotal = $serviceAmount + $refuelingAmount;
                
                // Apply VAT (15%)
                $vatRate = 0.15;
                $vatAmount = $subtotal * $vatRate;
                
                // Calculate grand total
                $totalAmount = $subtotal + $vatAmount;
                
                // Update order with financial details
                $order->subtotal = $subtotal;
                $order->vat = $vatAmount;
                $order->total_amount = $totalAmount;
                $order->save();
                
                // Create service bookings
                $services = [];
                foreach ($request->input('services') as $index => $serviceData) {
                    $booking = new ServiceBooking();
                    $booking->reference_number = $referenceNumber . '-' . ($index + 1);
                    $booking->order_id = $order->id;
                    $booking->user_id = $request->user_id;
                    $booking->service_type = $serviceData['service_type'];
                    
                    // Fix service_id to use numeric IDs instead of string codes
                    $booking->service_id = Service::getServiceIdFromCode($serviceData['service_id']);
                    
                    $booking->vehicle_make = $serviceData['vehicle_make'];
                    
                    // Ensure vehicle_manufacturer field exists in database and is handled correctly
                    if (isset($serviceData['vehicle_manufacturer']) && Schema::hasColumn('service_bookings', 'vehicle_manufacturer')) {
                        $booking->vehicle_manufacturer = $serviceData['vehicle_manufacturer'];
                    }
                    
                    $booking->vehicle_model = $serviceData['vehicle_model'];
                    $booking->vehicle_year = $serviceData['vehicle_year'];
                    $booking->plate_number = $serviceData['plate_number'];
                    $booking->refule_amount = $serviceData['refule_amount'];
                    
                    // Calculate the base price, VAT and total for this booking
                    $booking->base_price = $baseServicePrice;
                    $booking->vat_amount = $baseServicePrice * $vatRate;
                    $booking->total_amount = $booking->base_price + $booking->vat_amount + $booking->refule_amount;
                    
                    // Set status pending until payment is confirmed
                    $booking->status = 'pending';
                    
                    $booking->save();
                    
                    $services[] = [
                        'type' => Service::getServiceTypeById($serviceData['service_type']),
                        'name' => Service::getServiceNameById($serviceData['service_id']),
                        'price' => $baseServicePrice,
                        'vehicle' => $serviceData['vehicle_make'] . ' ' . $serviceData['vehicle_model'] . ' (' . $serviceData['vehicle_year'] . ')',
                        'plate' => $serviceData['plate_number'],
                        'refueling' => $serviceData['refule_amount']
                    ];
                }
                
                // Process payment based on selected method
                if ($request->input('payment_method') === 'wallet') {
                    // Check wallet balance
                    $wallet = $user->wallet;
                    
                    if (!$wallet || $wallet->balance < $totalAmount) {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Insufficient wallet balance. Your current balance is ' . 
                                   ($wallet ? $wallet->formatted_balance : '0.00 SAR') . 
                                   ' but the required amount is ' . number_format($totalAmount, 2) . ' SAR.'
                        ], 422);
                    }
                    
                    // Deduct from wallet
                    $wallet->withdraw($totalAmount, 'Payment for service order ' . $referenceNumber, $order);
                    $order->payment_status = 'paid';
                    $order->save();
                    
                    // Update all bookings to paid
                    ServiceBooking::where('order_id', $order->id)
                        ->update(['payment_status' => 'paid', 'status' => 'approved']);
                    
                    // Log the activity
                    LogHelper::logServiceBooking($booking, "API Service booked: " . $services[0]['name'], [
                        'service_type' => $services[0]['type'],
                        'vehicle' => $services[0]['vehicle'],
                        'payment_method' => 'wallet',
                        'amount' => $totalAmount
                    ]);
                    
                } else {
                    // Process credit card payment using saved card
                    if ($request->filled('card_id')) {
                        $savedCard = SavedCard::where('id', $request->card_id)
                            ->where('user_id', $request->user_id)
                            ->first();
                            
                        if (!$savedCard) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Invalid card selected.'
                            ], 422);
                        }
                        
                        Stripe::setApiKey(config('services.stripe.secret'));
                        
                        try {
                            // Convert amount to cents for Stripe
                            $amountInCents = round($totalAmount * 100);
                            
                            // Create a charge with Stripe
                            $charge = Charge::create([
                                'amount' => $amountInCents,
                                'currency' => 'sar',
                                'customer' => $user->stripe_customer_id,
                                'payment_method' => $savedCard->stripe_payment_method_id,
                                'description' => 'Order ' . $referenceNumber,
                                'metadata' => [
                                    'order_id' => $order->id,
                                    'user_id' => $user->id
                                ]
                            ]);
                            
                            // Update order and bookings
                            $order->payment_status = 'paid';
                            $order->transaction_id = $charge->id;
                            $order->save();
                            
                            ServiceBooking::where('order_id', $order->id)
                                ->update(['payment_status' => 'paid', 'status' => 'approved']);
                                
                            // Log the activity
                            LogHelper::logServiceBooking($booking, "API Service booked: " . $services[0]['name'], [
                                'service_type' => $services[0]['type'],
                                'vehicle' => $services[0]['vehicle'],
                                'payment_method' => 'credit_card',
                                'amount' => $totalAmount
                            ]);
                            
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Payment failed: ' . $e->getMessage()
                            ], 422);
                        }
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Card ID is required for credit card payments.'
                        ], 422);
                    }
                }
                
                DB::commit();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Service booked successfully',
                    'data' => [
                        'order_reference' => $referenceNumber,
                        'total_amount' => number_format($totalAmount, 2),
                        'services' => $services,
                        'payment_method' => $request->input('payment_method'),
                        'status' => 'approved'
                    ]
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to book service: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get saved cards for a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSavedCards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $user = User::findOrFail($request->user_id);
            $savedCards = SavedCard::where('user_id', $user->id)->get();
            
            $formattedCards = $savedCards->map(function($card) {
                return [
                    'id' => $card->id,
                    'card_type' => $card->card_type,
                    'last_four' => $card->last_four,
                    'expiry_month' => $card->expiry_month,
                    'expiry_year' => $card->expiry_year,
                    'is_default' => $card->is_default,
                    'created_at' => $card->created_at->format('Y-m-d H:i:s')
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'cards' => $formattedCards
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve saved cards: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all available services
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServices()
    {
        try {
            $services = Service::where('is_active', true)->get();
            
            $formattedServices = $services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'type' => $service->service_type,
                    'base_price' => number_format($service->base_price, 2),
                    'vat_percentage' => $service->vat_percentage,
                    'total_price' => number_format($service->calculateTotalPrice(), 2),
                    'estimated_duration' => $service->estimated_duration
                ];
            });
            
            // Get the service type mapping for mobile app use
            $serviceTypes = [
                'rfid_car' => 'RFID Chip for Cars',
                'rfid_truck' => 'RFID Chip for Trucks',
                'oil_change' => 'Oil Change Service',
            ];
            
            $serviceIds = [
                'rfid_80mm' => 'RFID Chip for Trucks Size 80mm',
                'rfid_120mm' => 'RFID Chip for Trucks Size 120mm',
                'oil_change' => 'Oil Change Service',
            ];
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'services' => $formattedServices,
                    'service_types' => $serviceTypes,
                    'service_ids' => $serviceIds
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve services: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get service booking history for a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:5|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            
            $bookings = ServiceBooking::with(['service'])
                ->where('user_id', $request->user_id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
            
            $formattedBookings = $bookings->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'reference_number' => $booking->reference_number,
                    'service' => $booking->service ? $booking->service->name : 'Unknown Service',
                    'service_type' => Service::getServiceTypeById($booking->service_type) ?: $booking->service_type,
                    'vehicle' => $booking->vehicle_make . ' ' . $booking->vehicle_model . ' (' . $booking->vehicle_year . ')',
                    'plate_number' => $booking->plate_number,
                    'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                    'booking_time' => $booking->booking_time ? $booking->booking_time->format('H:i') : null,
                    'total_amount' => number_format($booking->total_amount, 2),
                    'payment_method' => ucfirst($booking->payment_method),
                    'payment_status' => ucfirst($booking->payment_status),
                    'status' => ucfirst($booking->status),
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s')
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'bookings' => $formattedBookings,
                    'pagination' => [
                        'total' => $bookings->total(),
                        'per_page' => $bookings->perPage(),
                        'current_page' => $bookings->currentPage(),
                        'last_page' => $bookings->lastPage(),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve booking history: ' . $e->getMessage()
            ], 500);
        }
    }
}
