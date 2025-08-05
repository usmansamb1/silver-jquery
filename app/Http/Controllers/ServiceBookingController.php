<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\SavedCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceOrderConfirmation;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Helpers\LogHelper;
use App\Services\StatusTransitionService;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use App\Services\HyperPayDebugService;

class ServiceBookingController extends Controller
{
    public function index()
    {
        $bookings = ServiceBooking::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('services.booking.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $service = null;
        
        if ($request->has('service_id')) {
            $service = Service::findOrFail($request->service_id);
        }
        
        return view('services.booking.create', compact('service'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'vehicle_make' => 'required|string',
            'vehicle_model' => 'required|string',
            'vehicle_year' => 'required|integer|min:1900|max:'.(date('Y') + 1),
            'plate_number' => 'required|string',
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required',
            'payment_method' => 'required|in:wallet,credit_card',
            'use_existing_vehicle' => 'nullable|boolean',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        
        // Handle vehicle data
        $vehicle_id = null;
        if (!empty($validated['plate_number'])) {
            if (!empty($validated['use_existing_vehicle']) && !empty($validated['vehicle_id'])) {
                // Use existing vehicle (but verify it belongs to user)
                $vehicle = Vehicle::where('id', $validated['vehicle_id'])
                    ->where('user_id', auth()->id())
                    ->first();
                    
                if ($vehicle) {
                    $vehicle_id = $vehicle->id;
                }
            } else {
                // Store vehicle data separately if user opted to
                if (!empty($validated['save_vehicle'])) {
                    $vehicle = Vehicle::firstOrCreate(
                        [
                            'plate_number' => $validated['plate_number'],
                            'user_id' => auth()->id()
                        ],
                        [
                            'make' => $validated['vehicle_make'],
                            'manufacturer' => $validated['vehicle_manufacturer'] ?? $validated['vehicle_make'],
                            'model' => $validated['vehicle_model'],
                            'year' => $validated['vehicle_year'],
                            'rfid_status' => 'pending',
                            'status' => 'active'
                        ]
                    );

                    // Update existing vehicle's RFID status if needed
                    if (!$vehicle->wasRecentlyCreated && 
                        (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive')) {
                        $vehicle->rfid_status = 'pending';
                        $vehicle->save();
                    }
                    
                    $vehicle_id = $vehicle->id;
                }
            }
        }

        $booking = ServiceBooking::create([
            'user_id' => auth()->id(),
            'service_id' => $validated['service_id'],
            'vehicle_id' => $vehicle_id, // Optional link to vehicle
            'vehicle_make' => $validated['vehicle_make'],
            'vehicle_model' => $validated['vehicle_model'],
            'vehicle_year' => $validated['vehicle_year'],
            'plate_number' => $validated['plate_number'],
            'booking_date' => $validated['booking_date'],
            'booking_time' => $validated['booking_time'],
            'base_price' => $service->base_price,
            'vat_amount' => $service->base_price * ($service->vat_percentage / 100),
            'total_amount' => $service->calculateTotalPrice(),
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'delivery_status' => 'pending',
            'reference_number' => 'SB-' . Str::random(10)
        ]);
        
        // Log the service booking activity
        LogHelper::logServiceBooking($booking, "Service booked: {$service->name}", [
            'service_name' => $service->name,
            'service_id' => $service->id,
            'vehicle_make' => $validated['vehicle_make'],
            'vehicle_model' => $validated['vehicle_model'],
            'booking_date' => $validated['booking_date'],
            'payment_method' => $validated['payment_method'],
            'amount' => $booking->total_amount
        ]);

        return redirect()
            ->route('services.booking.show', $booking)
            ->with('success', 'Booking created successfully.');
    }

    public function show(ServiceBooking $booking)
    {
        if ($booking->user_id !== auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('services.booking.show', compact('booking'));
    }

    public function history()
    {
        $bookings = ServiceBooking::with(['vehicle', 'service'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('services.booking.history', compact('bookings'));
    }

    /**
     * Show the form for creating a multi-service order.
     */
    public function orderForm()
    {
        $services = Service::where('is_active', true)->get();
        
        // Get user's existing vehicles WITHOUT RFID numbers
        $vehicles = Vehicle::where('user_id', auth()->id())
            ->whereNull('rfid_number')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get the current user's wallet balance
        $wallet = auth()->user()->wallet;
        $walletBalance = $wallet ? number_format($wallet->balance, 2) : '0.00';
        
        // Get RFID service pricing from database
        $rfidServices = Service::where('is_active', true)
            ->whereIn('service_type', ['rfid_car', 'rfid_truck'])
            ->get()
            ->keyBy('service_type');
        
        // Get individual service prices for mapping
        $servicePrices = Service::where('is_active', true)
            ->get()
            ->mapWithKeys(function ($service) {
                // Map service code to database service
                $code = 'unknown';
                if (str_contains(strtolower($service->name), '80mm')) {
                    $code = 'rfid_80mm';
                } elseif (str_contains(strtolower($service->name), '120mm')) {
                    $code = 'rfid_120mm';
                } elseif (str_contains(strtolower($service->name), 'oil')) {
                    $code = 'oil_change';
                }
                return [$code => $service->base_price];
            });
        
        return view('services.booking.order-form', compact('services', 'walletBalance', 'vehicles', 'rfidServices', 'servicePrices'));
    }

    /**
     * Process the service order
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processOrder(Request $request)
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
                'services.*.vehicle_model' => 'required|string|max:50',
                'services.*.vehicle_year' => 'required|string|max:20',
                'services.*.plate_number' => 'required|string|max:20',
                'services.*.vehicle_manufacturer' => 'required|string|max:50',
                'payment_method' => 'required|in:wallet,credit_card',
                'save_card' => 'nullable|in:1'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Generate a unique reference number
            $referenceNumber = 'SRV-' . strtoupper(Str::random(8));
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Create order record
                $order = new Order();
                $order->reference_number = $referenceNumber ?? 'SRV-' . strtoupper(Str::random(8));
                $order->user_id = auth()->id();
                $order->pickup_location = $request->input('pickup_location');
                $order->status = 'pending';
                $order->payment_method = $request->input('payment_method');
                
                // Calculate total amount (services + refueling)
                $serviceCount = count($request->input('services'));
                
                // Get base service price from database (default RFID service)
                $defaultService = Service::where('is_active', true)
                    ->where('service_type', 'rfid_car')
                    ->first();
                $baseServicePrice = $defaultService ? $defaultService->base_price : 150.00;
                
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
                $grandTotal = $subtotal + $vatAmount;
                
                // Set payment amounts
                $order->subtotal = $subtotal;
                $order->vat_amount = $vatAmount;
                $order->total_amount = $grandTotal;
                $order->save();
                
                // Process each service - create service bookings
                foreach ($request->input('services') as $serviceData) {
                    // Store or update vehicle data if plate number is provided
                    $vehicle_id = null;
                    if (!empty($serviceData['plate_number'])) {
                        // Check if user wants to link to an existing vehicle
                        if (!empty($serviceData['use_existing_vehicle']) && !empty($serviceData['vehicle_id'])) {
                            // Use existing vehicle
                            $vehicle = Vehicle::where('id', $serviceData['vehicle_id'])
                                ->where('user_id', auth()->id())
                                ->first();
                                
                            if ($vehicle) {
                                // If the vehicle has no RFID status or inactive status, update it to pending
                                if (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive') {
                                    $vehicle->rfid_status = 'pending';
                                    $vehicle->save();
                                }
                                $vehicle_id = $vehicle->id;
                            }
                        } else {
                            // Create or find vehicle
                            $vehicle = Vehicle::firstOrCreate(
                                [
                                    'plate_number' => $serviceData['plate_number'], 
                                    'user_id' => auth()->id()
                                ],
                                [
                                    'make' => $serviceData['vehicle_make'],
                                    'manufacturer' => $serviceData['vehicle_manufacturer'],
                                    'model' => $serviceData['vehicle_model'],
                                    'year' => $serviceData['vehicle_year'],
                                    'rfid_status' => 'pending',
                                    'status' => 'active'
                                ]
                            );

                            // Update existing vehicle's RFID status if needed
                            if (!$vehicle->wasRecentlyCreated && 
                                (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive')) {
                                $vehicle->rfid_status = 'pending';
                                $vehicle->save();
                            }
                            
                            $vehicle_id = $vehicle->id;
                        }
                    }
                
                    // Create the service booking
                    $booking = new ServiceBooking();
                    $booking->user_id = auth()->id();
                    $booking->order_id = $order->id;
                    $booking->service_id = $serviceData['service_id'];
                    $booking->service_type = $serviceData['service_type'];
                    $booking->vehicle_id = $vehicle_id; // Optional vehicle association
                    $booking->vehicle_make = $serviceData['vehicle_make'];
                        $booking->vehicle_manufacturer = $serviceData['vehicle_manufacturer'];
                    $booking->vehicle_model = $serviceData['vehicle_model'];
                    $booking->vehicle_year = $serviceData['vehicle_year'];
                    $booking->plate_number = $serviceData['plate_number'];
                    $booking->refule_amount = floatval($serviceData['refule_amount']);
                    
                    // Other booking data
                    $booking->base_price = $baseServicePrice;
                    $booking->vat_amount = $baseServicePrice * $vatRate;
                    $booking->total_amount = $baseServicePrice * (1 + $vatRate) + $booking->refule_amount;
                    $booking->payment_method = $request->input('payment_method');
                    $booking->payment_status = 'pending';
                    $booking->status = 'pending';
                    $booking->delivery_status = 'pending';
                    $booking->save();
                    
                    // If vehicle exists and there's a refueling amount, update the vehicle's RFID balance
                    if ($vehicle_id && !empty($booking->refule_amount) && $booking->refule_amount > 0) {
                        $vehicle = Vehicle::find($vehicle_id);
                        if ($vehicle) {
                            // Add the refueling amount to the existing balance (or initialize if null)
                            $currentBalance = $vehicle->rfid_balance ?? 0;
                            $vehicle->rfid_balance = $currentBalance + $booking->refule_amount;
                            $vehicle->save();
                        }
                    }
                }
                
                // Process payment based on selected method
                if ($request->input('payment_method') === 'wallet') {
                    // Check wallet balance
                    $user = auth()->user();
                    $wallet = $user->wallet;
                    
                    if (!$wallet || $wallet->balance < $grandTotal) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'Insufficient wallet balance. Your current balance is ' . 
                                   ($wallet ? $wallet->getFormattedBalanceAttribute() : '0.00 SAR') . 
                                   ' but the required amount is ' . number_format($grandTotal, 2) . ' SAR.')
                            ->withInput();
                    }
                    
                    // Deduct from wallet
                    $wallet->withdraw($grandTotal, 'Payment for service order ' . $referenceNumber, $order);
                    $order->payment_status = 'paid';
                    $order->save();
                    
                    // Update all bookings to approved
                    ServiceBooking::where('order_id', $order->id)
                        ->update(['payment_status' => 'approved', 'status' => 'approved']);
                        
                    // Send email confirmation for wallet payment
                    $this->sendOrderConfirmationEmail($user, $order, $request->input('services'), 'wallet');
                    
                } else {
                    // Credit card payment will be processed with HyperPay
                    // For now, mark as pending - will be implemented with HyperPay integration
                    $order->payment_status = 'pending';
                    $order->save();
                    
                    // Update all bookings to pending payment
                    ServiceBooking::where('order_id', $order->id)
                        ->update(['payment_status' => 'pending', 'status' => 'pending']);
                }
                
                // Commit the transaction
                DB::commit();
                
                return redirect()->route('services.booking.index')
                    ->with('success', 'Your service order has been placed successfully! Reference: ' . $referenceNumber . 
                    '. Your vehicle RFID status is now pending and will be updated by our delivery team.');
                    
            } catch (\Exception $e) {
                // Rollback the transaction in case of an error
                DB::rollBack();
                
                // Log the error
                Log::error('Service order processing error: ' . $e->getMessage());
                
                return redirect()->back()
                    ->with('error', 'An error occurred while processing your order. Please try again.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Service order validation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while validating your order. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Send order confirmation email to the customer
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @param array $services
     * @param string $paymentMethod
     * @return void
     */
    private function sendOrderConfirmationEmail($user, $order, $services, $paymentMethod)
    {
        $emailData = [
            'user' => $user,
            'order' => $order,
            'services' => $services,
            'payment_method' => $paymentMethod,
            'date' => Carbon::now()->format('Y-m-d H:i:s')
        ];
        
        Mail::to($user->email)->send(new ServiceOrderConfirmation($emailData));
    }
    
    /**
     * Display list of saved credit cards for the authenticated user
     * 
     * @return \Illuminate\View\View
     */
    public function savedCards()
    {
        $cards = SavedCard::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('services.booking.saved-cards', compact('cards'));
    }

    /**
     * Set a card as the default payment method
     *
     * @param SavedCard $card
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefaultCard(SavedCard $card)
    {
        // Check if the card belongs to the authenticated user
        if ($card->user_id !== auth()->id()) {
            return redirect()->route('services.booking.saved-cards')
                ->with('error', 'Unauthorized action.');
        }
        
        // Reset default status for all user's cards
        SavedCard::where('user_id', auth()->id())
            ->update(['is_default' => false]);
            
        // Set the selected card as default
        $card->is_default = true;
        $card->save();
        
        return redirect()->route('services.booking.saved-cards')
            ->with('success', 'Default payment method updated successfully.');
    }
    
    /**
     * Delete a saved card
     *
     * @param SavedCard $card
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCard(SavedCard $card)
    {
        // Check if the card belongs to the authenticated user
        if ($card->user_id !== auth()->id()) {
            return redirect()->route('services.booking.saved-cards')
                ->with('error', 'Unauthorized action.');
        }
        
        // Delete the card
        $card->delete();
        
        return redirect()->route('services.booking.saved-cards')
            ->with('success', 'Card removed successfully.');
    }

    /**
     * Process the service order via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processOrderJson(Request $request): JsonResponse
    {
        try {
            // Debug: Log the incoming request data
            \Log::info('Service booking order request data:', [
                'pickup_location' => $request->input('pickup_location'),
                'payment_method' => $request->input('payment_method'),
                'use_existing_vehicle' => $request->input('use_existing_vehicle'),
                'vehicle_id' => $request->input('vehicle_id'),
                'save_vehicle' => $request->input('save_vehicle'),
                'services_count' => is_array($request->input('services')) ? count($request->input('services')) : 'NOT_ARRAY',
                'services_data' => $request->input('services'),
                'all_request_data' => $request->all()
            ]);
            
            // Validate the request
            $validator = Validator::make($request->all(), [
                'pickup_location' => 'required|string|max:255',
                'payment_method' => 'required|in:wallet,credit_card',
                'services' => 'required|array|min:1',
                'services.*.service_id' => 'required|string',
                'services.*.service_type' => 'required|string',
                'services.*.vehicle_make' => 'required|string',
                'services.*.vehicle_manufacturer' => 'required|string',
                'services.*.vehicle_model' => 'required|string',
                'services.*.vehicle_year' => 'required|string',
                'services.*.plate_number' => 'required|string',
                'services.*.refule_amount' => 'required|numeric|min:0',
                'services.*.use_existing_vehicle' => 'sometimes|boolean',
                'services.*.vehicle_id' => 'sometimes|exists:vehicles,id',
                'save_vehicle' => 'sometimes|boolean',
                'use_existing_vehicle' => 'sometimes|boolean',
                'vehicle_id' => 'required_if:use_existing_vehicle,true|exists:vehicles,id',
                'save_card' => 'sometimes|boolean',
            ]);
            
            // Custom validation for services with existing vehicles
            $validator->after(function ($validator) use ($request) {
                $services = $request->input('services', []);
                
                foreach ($services as $index => $service) {
                    // Check if this service uses an existing vehicle
                    if (!empty($service['use_existing_vehicle']) && $service['use_existing_vehicle']) {
                        // Ensure vehicle_id is provided for this service
                        if (empty($service['vehicle_id'])) {
                            $validator->errors()->add("services.{$index}.vehicle_id", 'Vehicle ID is required when using existing vehicle.');
                        } else {
                            // Check if the vehicle exists and belongs to the user
                            $vehicle = \App\Models\Vehicle::where('id', $service['vehicle_id'])
                                ->where('user_id', auth()->id())
                                ->first();
                            
                            if (!$vehicle) {
                                $validator->errors()->add("services.{$index}.vehicle_id", 'The selected vehicle is invalid or does not belong to you.');
                            }
                        }
                    }
                }
                
                // Check global use_existing_vehicle setting
                if ($request->input('use_existing_vehicle') && !$request->input('vehicle_id')) {
                    $validator->errors()->add('vehicle_id', 'Vehicle ID is required when using existing vehicle.');
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            // Calculate total amount - get actual service prices from database
            $vatRate = 0.15; // Default VAT rate
            
            // Calculate total based on actual service prices
            $totalBaseServices = 0;
            foreach ($request->input('services') as $service) {
                $serviceRecord = Service::getServiceIdFromCode($service['service_id']);
                if ($serviceRecord && is_numeric($serviceRecord)) {
                    $serviceModel = Service::find($serviceRecord);
                    if ($serviceModel) {
                        $totalBaseServices += $serviceModel->base_price;
                        $vatRate = $serviceModel->vat_percentage / 100; // Use service-specific VAT
                    } else {
                        $totalBaseServices += 150.00; // Fallback price
                    }
                } else {
                    $totalBaseServices += 150.00; // Fallback price
                }
            }
            
            $baseServicePrice = $totalBaseServices / count($request->input('services')); // Average for backward compatibility
            
            $totalVat = $totalBaseServices * $vatRate;
            $totalRefuelAmount = 0;
            
            foreach ($request->input('services') as $service) {
                $totalRefuelAmount += floatval($service['refule_amount']);
            }
            
            $totalAmount = $totalBaseServices + $totalVat + $totalRefuelAmount;
            
            // Begin transaction
            DB::beginTransaction();
            
            try {
                // Process payment based on method
                if ($request->input('payment_method') === 'wallet') {
                    // Check if user has sufficient wallet balance
                    if (!$user->wallet || $user->wallet->balance < $totalAmount) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Insufficient wallet balance'
                        ], 400);
                    }
                    
                    // Create order first to ensure we have a reference for wallet transaction
                    $order = Order::create([
                        'user_id' => $user->id,
                        'reference_number' => 'ORD-' . Str::random(10),
                        'order_number' => 'ORD-' . Str::random(10),
                        'total_amount' => $totalAmount,
                        'subtotal' => $totalBaseServices + $totalRefuelAmount,
                        'vat' => $totalVat,
                        'pickup_location' => $request->input('pickup_location'),
                        'payment_method' => $request->input('payment_method'),
                        'payment_status' => 'pending',
                    ]);
                    
                    // Process wallet payment with explicit reference
                    $transaction = $user->wallet->withdraw($totalAmount, 'Payment for service booking', $order);
                    $paymentStatus = 'paid';
                    $paymentReference = $transaction->id;
                    
                    // Update order payment status
                    $order->payment_status = 'paid';
                    $order->payment_reference = $paymentReference;
                    $order->save();
                    
                } else {
                    // Credit card payment with HyperPay - create order first
                    $paymentStatus = 'pending';
                    $paymentReference = null;
                    
                    // Create the order first for HyperPay processing
                    $order = Order::create([
                        'user_id' => $user->id,
                        'reference_number' => 'ORD-' . Str::random(10),
                        'order_number' => 'ORD-' . Str::random(10),
                        'total_amount' => $totalAmount,
                        'subtotal' => $totalBaseServices + $totalRefuelAmount,
                        'vat' => $totalVat,
                        'pickup_location' => $request->input('pickup_location'),
                        'payment_method' => $request->input('payment_method'),
                        'payment_status' => $paymentStatus,
                        'payment_reference' => $paymentReference
                    ]);
                }
                
                // Order should already exist at this point
                
                // Handle global use_existing_vehicle parameter (affects all services)
                $globalUseExistingVehicle = (bool)$request->input('use_existing_vehicle', false);
                $globalVehicleId = $request->input('vehicle_id');
                
                // Enable IDENTITY_INSERT to allow explicit UUID values
                $this->enableIdentityInsertForServiceBookings();
                
                // Create service bookings
                $bookings = [];
                
                foreach ($request->input('services') as $index => $serviceData) {
                    $booking = new ServiceBooking();
                    $booking->reference_number = 'SB-' . strtoupper(Str::random(6)) . '-' . ($index + 1);
                    $booking->order_id = $order->id;
                    $booking->user_id = auth()->id();
                    $booking->service_type = $serviceData['service_type'];
                    
                    // Fix service_id to use numeric IDs instead of string codes
                    // Use the Service model's method to get the correct numeric ID
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
                    
                    // Set status to paid instead of approved for successful bookings
                    $booking->payment_status = $paymentStatus === 'paid' ? 'paid' : 'pending';
                    $booking->payment_method = $request->input('payment_method');
                    $booking->status = $paymentStatus === 'paid' ? 'approved' : 'pending';
                    
                    // Handle vehicle ID assignment
                    $vehicle_id = null;
                    
                    // First check if the service has its own use_existing_vehicle flag
                    if (!empty($serviceData['use_existing_vehicle']) && !empty($serviceData['vehicle_id'])) {
                        // Verify the vehicle belongs to the user
                        $vehicle = Vehicle::where('id', $serviceData['vehicle_id'])
                            ->where('user_id', $user->id)
                            ->first();
                        
                        if ($vehicle) {
                            // If the vehicle has no RFID status or inactive status, update it to pending
                            if (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive') {
                                $vehicle->rfid_status = 'pending';
                                $vehicle->save();
                            }
                            $vehicle_id = $vehicle->id;
                        }
                    } 
                    // Then check if the global flag is set
                    else if ($globalUseExistingVehicle && $globalVehicleId) {
                        // Verify the vehicle belongs to the user
                        $vehicle = Vehicle::where('id', $globalVehicleId)
                            ->where('user_id', $user->id)
                            ->first();
                        
                        if ($vehicle) {
                            // If the vehicle has no RFID status or inactive status, update it to pending
                            if (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive') {
                                $vehicle->rfid_status = 'pending';
                                $vehicle->save();
                            }
                            $vehicle_id = $vehicle->id;
                        }
                    }
                    // Otherwise, create a new vehicle if save_vehicle is true
                    else if ($request->input('save_vehicle', false)) {
                        // Create a new vehicle record
                        $vehicle = Vehicle::firstOrCreate(
                            [
                                'plate_number' => $serviceData['plate_number'],
                                'user_id' => $user->id
                            ],
                            [
                                'make' => $serviceData['vehicle_make'],
                                'manufacturer' => $serviceData['vehicle_manufacturer'],
                                'model' => $serviceData['vehicle_model'],
                                'year' => $serviceData['vehicle_year'],
                                'rfid_status' => 'pending',
                                'status' => 'active'
                            ]
                        );
                        
                        // Update existing vehicle to ensure RFID status is pending if not already set
                        if (!$vehicle->wasRecentlyCreated && 
                            (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive')) {
                            $vehicle->rfid_status = 'pending';
                            $vehicle->save();
                        }
                        
                        $vehicle_id = $vehicle->id;
                    }
                    
                    // Assign vehicle ID to booking
                    $booking->vehicle_id = $vehicle_id;
                    $booking->save();
                    $bookings[] = $booking;
                    
                    // Update the vehicle's RFID balance with refueling amount if applicable
                    if ($vehicle_id && !empty($booking->refule_amount) && $booking->refule_amount > 0) {
                        $vehicle = Vehicle::find($vehicle_id);
                        if ($vehicle) {
                            $currentBalance = $vehicle->rfid_balance ?? 0;
                            $vehicle->rfid_balance = $currentBalance + $booking->refule_amount;
                            $vehicle->save();
                        }
                    }
                    
                    // Log the service booking activity - using safe method to avoid UUID errors
                    LogHelper::safeLogServiceBooking($booking, 'Booked service: ' . $serviceData['service_type'], [
                        'service_type' => $serviceData['service_type'],
                        'vehicle_make' => $serviceData['vehicle_make'], 
                        'vehicle_model' => $serviceData['vehicle_model'],
                        'plate_number' => $serviceData['plate_number'],
                        'payment_method' => $request->input('payment_method'),
                        'payment_status' => $paymentStatus,
                        'amount' => $booking->total_amount,
                        'reference_number' => $booking->reference_number
                    ]);
                }
                
                // Handle different payment methods in response
                if ($request->input('payment_method') === 'credit_card') {
                    // For credit card payments, return checkout information
                    $cardBrand = $request->input('card_brand', 'credit_card');
                    
                    // Get HyperPay checkout form
                    $checkoutRequest = new Request([
                        'amount' => $totalAmount,
                        'brand' => $cardBrand,
                        'order_id' => $order->id
                    ]);
                    
                    $hyperpayResponse = $this->getHyperpayForm($checkoutRequest);
                    $hyperpayData = $hyperpayResponse->getData(true);
                    
                    if ($hyperpayData['status'] === 'success') {
                        // Commit transaction only after successful HyperPay checkout creation
                        DB::commit();
                        
                        return response()->json([
                            'status' => 'success',
                            'payment_method' => 'credit_card',
                            'message' => 'Order created successfully. Please complete payment.',
                            'data' => [
                                'order_id' => $order->id,
                                'order_number' => $order->order_number,
                                'total_amount' => $totalAmount,
                                'payment_status' => $paymentStatus,
                                'checkout_id' => $hyperpayData['checkout_id'],
                                'payment_html' => $hyperpayData['html']
                            ]
                        ]);
                    } else {
                        // HyperPay checkout creation failed, rollback
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to initialize payment. Please try again.',
                            'errors' => $hyperpayData['errors'] ?? []
                        ], 500);
                    }
                } else {
                    // Commit transaction for wallet payments
                    DB::commit();
                    
                    // Wallet payment success response
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Service booking successful. Your vehicle RFID status is now pending and will be updated by our delivery team.',
                        'redirect' => route('services.booking.history'),
                        'data' => [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'bookings' => $bookings,
                            'total_amount' => $totalAmount,
                            'payment_status' => $paymentStatus
                        ]
                    ]);
                }
                
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Service booking failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user_id' => $user->id ?? null,
                    'request' => $request->all()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error processing order: ' . $e->getMessage()
                ], 500);
            }
            
        } catch (Exception $e) {
            Log::error('Service booking validation error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper method to get service type label
     */
    private function getServiceTypeLabel($type)
    {
        $types = [
            'rfid_car' => 'RFID Chip for Cars',
            'rfid_truck' => 'RFID Chip for Trucks',
            'oil_change' => 'Oil Change Service'
        ];
        
        return $types[$type] ?? $type;
    }
    
    /**
     * Helper method to get fuel type label
     */
    private function getFuelTypeLabel($id)
    {
        $types = [
            'rfid_80mm' => 'Unleaded 91',
            'rfid_120mm' => 'Premium 95',
            'oil_change' => 'Diesel'
        ];
        
        return $types[$id] ?? $id;
    }

    /**
     * Update the status of a service booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceBooking  $booking
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, ServiceBooking $booking)
    {
        $request->validate([
            'status' => 'required|string|in:pending,approved,rejected,cancelled,completed',
            'comment' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->input('status');
        $comment = $request->input('comment');
        
        // Initialize the status transition service with our model
        $statusService = new StatusTransitionService($booking);
        
        // Check if the transition is allowed
        if (!$statusService->canTransitionTo($newStatus)) {
            return back()->with('error', "Cannot change status from {$booking->status} to {$newStatus}");
        }
        
        // Check if the user has permission to perform this transition
        if (!$statusService->userCanTransition($newStatus)) {
            return back()->with('error', 'You do not have permission to perform this status change');
        }
        
        // Option 1: Use the transitionTo method (includes permission checks)
        $success = $statusService->transitionTo($newStatus, [
            'comment' => $comment,
            // Add any additional metadata here
        ]);
        
        // Option 2: Use the standalone changeStatus method
        // $statusService = app(StatusTransitionService::class);
        // $metadata = [
        //     'requested_by' => auth()->id(),
        //     'ip_address' => $request->ip(),
        // ];
        // $success = $statusService->changeStatus($booking, $newStatus, $comment, $metadata);
        
        if ($success) {
            // Record this status change in the status history table
            return redirect()->route('services.booking.show', $booking)
                ->with('success', "Status updated to {$newStatus} successfully");
        }
        
        return back()->with('error', 'Failed to update status. Please try again.');
    }

    /**
     * Enable IDENTITY_INSERT for the service_bookings table to allow explicit UUID values
     * This is a temporary workaround until the table is properly migrated to use UUIDs
     * Note: This method is now deprecated as we've migrated to MySQL
     */
    protected function enableIdentityInsertForServiceBookings()
    {
        try {
            // Only run this for SQL Server connections (legacy support)
            if (DB::connection()->getDriverName() === 'sqlsrv') {
                // Check if the service_bookings table has an identity column
                $hasIdentityColumn = DB::select("
                    SELECT COUNT(*) as has_identity 
                    FROM sys.identity_columns 
                    WHERE OBJECT_NAME(object_id) = 'service_bookings'
                ");
                
                if ($hasIdentityColumn[0]->has_identity > 0) {
                    // Enable IDENTITY_INSERT if table has identity column
                    DB::statement('SET IDENTITY_INSERT service_bookings ON');
                    Log::info('IDENTITY_INSERT enabled for service_bookings table');
                }
            }
            // For MySQL, no action needed as it doesn't use IDENTITY_INSERT
        } catch (\Exception $e) {
            Log::error('Failed to enable IDENTITY_INSERT: ' . $e->getMessage());
        }
    }

    /**
     * Create HyperPay checkout form for service booking
     */
    public function getHyperpayForm(Request $request)
    {
        $user = Auth::user();
        
        // Enhanced validation with better error messages
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10|max:50000',
            'brand' => 'required|string|in:credit_card,mada_card,AMEX,STC_PAY,URPAY,VISA MASTER,MADA',
            'order_id' => 'nullable|exists:orders,id',
            'pickup_location' => 'nullable|string|max:255',
            'services' => 'nullable|array',
            'services.*.service_type' => 'required_with:services|string',
            'services.*.service_id' => 'required_with:services|string',
            'services.*.vehicle_make' => 'required_with:services|string',
            'services.*.vehicle_manufacturer' => 'required_with:services|string',
            'services.*.vehicle_model' => 'required_with:services|string',
            'services.*.vehicle_year' => 'required_with:services|string',
            'services.*.plate_number' => 'required_with:services|string',
            'services.*.refule_amount' => 'required_with:services|numeric|min:0',
        ], [
            'amount.required' => 'Payment amount is required',
            'amount.numeric' => 'Payment amount must be a valid number',
            'amount.min' => 'Minimum payment amount is 10 SAR',
            'amount.max' => 'Maximum payment amount is 50,000 SAR',
            'brand.required' => 'Card brand is required',
            'brand.in' => 'Invalid payment method. Must be credit_card, mada_card, AMEX, STC_PAY, or URPAY'
        ]);

        if ($validator->fails()) {
            Log::error('HyperPay validation failed', [
                'errors' => $validator->errors(),
                'request_data' => $request->all(),
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
                'debug_info' => 'Check request parameters: amount, brand, order_id'
            ], 422);
        }

        $amount = floatval($request->input('amount'));
        $brand = $request->input('brand');
        $orderId = $request->input('order_id');
        $pickupLocation = $request->input('pickup_location', 'Payment via HyperPay');
        $services = $request->input('services', []);

        // Debug logging for service booking HyperPay requests
        Log::info('ServiceBooking HyperPay form request received', [
            'user_id' => $user->id,
            'request_brand' => $brand,
            'amount' => $amount,
            'order_id' => $orderId,
            'services_count' => count($services),
            'full_request' => $request->all()
        ]);

        // Define payment method configurations with correct HyperPay brand identifiers
        $paymentMethods = [
            'credit_card' => [
                'entity_id' => config('services.hyperpay.entity_id_credit'),
                'form_brand' => 'VISA MASTER',
                'normalized_brand' => 'credit_card',
                'display_name' => 'Credit Card (Visa/MasterCard)'
            ],
            'mada_card' => [
                'entity_id' => config('services.hyperpay.entity_id_mada'),
                'form_brand' => 'MADA',
                'normalized_brand' => 'mada_card',
                'display_name' => 'MADA Card'
            ],
            // AMEX uses the same widget as Visa/MasterCard
            'AMEX' => [
                'entity_id' => config('services.hyperpay.entity_id_credit'),
                'form_brand' => 'AMEX VISA MASTER',
                'normalized_brand' => 'AMEX',
                'display_name' => 'American Express'
            ],
            // Digital wallets use the same credit card widget but process differently
            'STC_PAY' => [
                'entity_id' => config('services.hyperpay.entity_id_credit'),
                'form_brand' => 'STC_PAY',
                'normalized_brand' => 'STC_PAY',
                'display_name' => 'STC Pay'
            ],
            'URPAY' => [
                'entity_id' => config('services.hyperpay.entity_id_credit'),
                'form_brand' => 'URPAY',
                'normalized_brand' => 'URPAY',
                'display_name' => 'URPay'
            ],
            // Legacy support for old values
            'VISA MASTER' => [
                'entity_id' => config('services.hyperpay.entity_id_credit'),
                'form_brand' => 'VISA MASTER',
                'normalized_brand' => 'credit_card',
                'display_name' => 'Credit Card (Visa/MasterCard)'
            ],
            'MADA' => [
                'entity_id' => config('services.hyperpay.entity_id_mada'),
                'form_brand' => 'MADA',
                'normalized_brand' => 'mada_card',
                'display_name' => 'MADA Card'
            ]
        ];

        // Get payment method configuration
        $paymentConfig = $paymentMethods[$brand] ?? $paymentMethods['credit_card'];
       
        $entityId = $paymentConfig['entity_id'];
        $paymentBrand = $paymentConfig['form_brand'];
        $normalizedBrand = $paymentConfig['normalized_brand'];
        $displayName = $paymentConfig['display_name'];
        
        // Pre-flight configuration check
        $configErrors = [];
        if (empty($entityId)) $configErrors[] = "Entity ID not configured for {$normalizedBrand}";
        if (empty(config('services.hyperpay.access_token'))) $configErrors[] = 'Access token not configured';
        if (empty(config('services.hyperpay.base_url'))) $configErrors[] = 'Base URL not configured';
        if (empty($user->email)) $configErrors[] = 'User email is missing';
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) $configErrors[] = 'User email is invalid';
        
        if (!empty($configErrors)) {
            Log::error('HyperPay configuration errors', [
                'errors' => $configErrors,
                'brand' => $normalizedBrand,
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Payment system configuration error',
                'debug_info' => implode(', ', $configErrors)
            ], 500);
        }

        // Generate merchant transaction ID (must be under 41 characters for HyperPay)
        $merchantTransactionId = uniqid('SRV_');

        try {
            // CRITICAL FIX: Ensure amount is properly formatted as string
            $formattedAmount = number_format($amount, 2, '.', '');
            
            $requestData = [
                'entityId' => $entityId,
                'amount' => $formattedAmount,
                'currency' => 'SAR',
                'paymentType' => 'DB',
                'merchantTransactionId' => $merchantTransactionId,
                'customer.email' => $user->email
            ];
            
            // Only add essential parameters for test mode
            if (config('services.hyperpay.mode') === 'test') {
                $requestData['testMode'] = 'EXTERNAL';
            }
            
            // CRITICAL FIX: Add 3DS2 parameters like wallet implementation
            $requestData['customParameters[3DS2_enrolled]'] = 'true';
            $requestData['customParameters[3DS2_flow]'] = 'challenge';
            
            // Add minimal custom parameters only if order exists
            if ($orderId) {
                $requestData['customParameters[order_id]'] = $orderId;
            }
            $requestData['customParameters[user_id]'] = $user->id;
            $requestData['customParameters[payment_type]'] = 'service_booking';
            
            // Log request using debug service
            HyperPayDebugService::logRequest($requestData, config('services.hyperpay.base_url') . 'v1/checkouts', 'SERVICE_BOOKING_REQUEST');
            
            Log::info('HyperPay API request', [
                'url' => config('services.hyperpay.base_url') . 'v1/checkouts',
                'data' => array_merge($requestData, ['customer.email' => '***HIDDEN***']),
                'entity_id' => $entityId,
                'amount_raw' => $amount,
                'amount_formatted' => $formattedAmount,
                'brand_original' => $brand,
                'brand_normalized' => $normalizedBrand,
                'user_id' => $user->id,
                'order_id' => $orderId,
                'services_count' => count($services),
                'pickup_location' => $pickupLocation,
                'config_entity_credit' => config('services.hyperpay.entity_id_credit'),
                'config_entity_mada' => config('services.hyperpay.entity_id_mada')
            ]);
            
            // Create checkout session with HyperPay (use same URL as script)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post('https://eu-test.oppwa.com/v1/checkouts', $requestData);

            $responseBody = $response->body();
            $responseData = $response->json();
            
            // Log response using debug service
            HyperPayDebugService::logResponse($response, 'SERVICE_BOOKING_RESPONSE');
            
            Log::info('HyperPay API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_length' => strlen($responseBody),
                'has_id' => isset($responseData['id']),
                'result_code' => $responseData['result']['code'] ?? 'N/A',
                'result_description' => $responseData['result']['description'] ?? 'N/A',
                'full_response' => $responseData
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['id'])) {
                    Log::error('HyperPay response missing checkout ID', [
                        'response_data' => $data
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid payment response. Please try again.',
                        'debug_info' => 'Missing checkout ID in HyperPay response'
                    ], 500);
                }
                
                $checkoutId = $data['id'];
                
                Log::info('HyperPay checkout created successfully', [
                    'checkout_id' => $checkoutId,
                    'services_count' => count($services)
                ]);

                // Store session information (matching wallet implementation)
                session([
                    'hyperpay_checkout_id' => $checkoutId,
                    'hyperpay_amount' => $amount,
                    'hyperpay_entity_id' => $entityId, // CRITICAL: Store entity ID like wallet
                    'hyperpay_brand' => $normalizedBrand,
                    'hyperpay_display_name' => $displayName,
                    'hyperpay_original_brand' => $brand,
                    'hyperpay_order_id' => $orderId,
                    'hyperpay_merchant_tx_id' => $merchantTransactionId,
                    'hyperpay_pickup_location' => $pickupLocation,
                    'hyperpay_services' => $services // CRITICAL: Store service data for later use
                ]);

                // Generate form HTML using the same approach as WalletController
                $viewData = [
                    'checkoutId' => $checkoutId,
                    'amount' => $amount,
                    'brand' => $brand,
                    'formBrand' => $paymentBrand,
                    'displayName' => $displayName,
                ];
                
                Log::info('HyperPay form view data', [
                    'view_data' => $viewData,
                    'original_brand' => $brand,
                    'payment_brand' => $paymentBrand,
                    'display_name' => $displayName
                ]);
                
                $formHtml = view('services.booking.partials.hyperpay-form', $viewData)->render();
                
                Log::info('HyperPay widget HTML generated', [
                    'html_length' => strlen($formHtml),
                    'form_contains_form_tag' => strpos($formHtml, '<form') !== false,
                    'form_contains_paymentWidgets' => strpos($formHtml, 'paymentWidgets') !== false,
                    'services_stored' => count($services),
                    'html_preview' => $formHtml
                ]);

                return response()->json([
                    'status' => 'success',
                    'checkout_id' => $checkoutId,
                    'amount' => $amount,
                    'brand' => $brand,
                    'display_name' => $displayName,
                    'html' => $formHtml,
                    'script_url' => 'https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=' . $checkoutId,
                    'widget_options' => [
                        'style' => 'card',
                        'locale' => 'en',
                        'showPlaceholders' => true,
                        'brandDetection' => true,
                        'showCVVHint' => true,
                        'brands' => $paymentBrand
                    ]
                ]);
            } else {
                // Enhanced error handling with specific HyperPay error codes
                $errorMessage = 'Unable to initialize payment. Please try again.';
                $errorDetails = '';
                
                try {
                    $errorData = $response->json();
                    if (isset($errorData['result'])) {
                        $resultCode = $errorData['result']['code'] ?? 'UNKNOWN';
                        $resultDescription = $errorData['result']['description'] ?? 'Unknown error';
                        $errorDetails = "Code: {$resultCode}, Description: {$resultDescription}";
                        
                        // Provide user-friendly error messages based on common HyperPay error codes
                        switch ($resultCode) {
                            case '200.300.404':
                                $errorMessage = 'Invalid or missing parameter. Please check your payment details.';
                                break;
                            case '800.100.153':
                                $errorMessage = 'Invalid transaction amount. Please verify the amount and try again.';
                                break;
                            case '800.100.152':
                                $errorMessage = 'Invalid currency. Payment processing error.';
                                break;
                            case '800.100.151':
                                $errorMessage = 'Invalid payment type. Please contact support.';
                                break;
                            case '800.100.150':
                                $errorMessage = 'Invalid entity ID. Payment system configuration error.';
                                break;
                            default:
                                if (strpos($resultDescription, 'parameter') !== false) {
                                    $errorMessage = 'Payment failed due to invalid or missing parameter. Please try again.';
                                } else if (strpos($resultDescription, 'amount') !== false) {
                                    $errorMessage = 'Invalid payment amount. Please verify and try again.';
                                }
                                break;
                        }
                    }
                } catch (\Exception $parseError) {
                    Log::warning('Could not parse HyperPay error response', [
                        'response_body' => $response->body(),
                        'parse_error' => $parseError->getMessage()
                    ]);
                }
                
                Log::error('HyperPay checkout creation failed', [
                    'response' => $response->body(),
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'error_details' => $errorDetails,
                    'request_data' => array_merge($requestData, ['customer.email' => '***HIDDEN***'])
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'debug_info' => $errorDetails ?: 'HTTP ' . $response->status()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('HyperPay API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => isset($requestData) ? array_merge($requestData, ['customer.email' => '***HIDDEN***']) : 'Not set'
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Payment system temporarily unavailable. Please try again later.',
                'debug_info' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle HyperPay payment result
     */
    public function hyperpayStatus(Request $request)
    {
        $user = Auth::user();
        $checkoutId = $request->input('id');
        $resourcePath = $request->input('resourcePath');
        
        Log::info('HyperPay status callback received', [
            'checkout_id' => $checkoutId,
            'user_id' => $user->id,
            'request_params' => $request->all()
        ]);
        
        if (!$checkoutId) {
            Log::error('HyperPay status callback missing checkout ID', [
                'request_params' => $request->all()
            ]);
            return redirect()->route('services.booking.order.form')
                ->with('error', 'Invalid payment session.');
        }

        // Verify session data
        $sessionCheckoutId = session('hyperpay_checkout_id');
        $sessionAmount = session('hyperpay_amount');
        $sessionOrderId = session('hyperpay_order_id');
        $sessionBrand = session('hyperpay_brand');
        $sessionEntityId = ($sessionBrand === 'MADA' || $sessionBrand === 'mada_card') ? 
            config('services.hyperpay.entity_id_mada') : 
            config('services.hyperpay.entity_id_credit');
        
        if ($sessionCheckoutId !== $checkoutId) {
            return redirect()->route('services.booking.order.form')
                ->with('error', 'Payment session mismatch.');
        }
        
        // Validate that order exists and belongs to current user
        if ($sessionOrderId) {
            $order = Order::where('id', $sessionOrderId)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$order) {
                Log::error('Order not found during Hyperpay status check', [
                    'order_id' => $sessionOrderId,
                    'user_id' => $user->id,
                    'checkout_id' => $checkoutId
                ]);
                
                return redirect()->route('services.booking.order.form')
                    ->with('error', 'Order not found or access denied.');
            }
        } 
        
        try {
            // Query payment status from HyperPay using the same pattern as wallet topup
            $requestUrl = $resourcePath ? 
                config('services.hyperpay.base_url') . ltrim($resourcePath, '/') :
                config('services.hyperpay.base_url') . "v1/checkouts/{$checkoutId}/payment";
                
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->get($requestUrl, [
                'entityId' => $sessionEntityId
            ]);
 
            if ($response->successful()) {
                $paymentData = $response->json();
                $resultCode = $paymentData['result']['code'] ?? '';
                $hyperpayTransactionId = $paymentData['id'] ?? null;
                $amount = floatval($paymentData['amount'] ?? $sessionAmount);
                
                // Validate expected amount from session
                if ($sessionAmount > 0 && $amount > 0 && abs($amount - $sessionAmount) > 0.01) {
                    Log::warning('Amount mismatch detected in service booking payment', [
                        'user_id' => $user->id,
                        'hyperpay_amount' => $amount,
                        'session_amount' => $sessionAmount,
                        'difference' => abs($amount - $sessionAmount),
                        'hyperpay_transaction_id' => $hyperpayTransactionId,
                        'order_id' => $sessionOrderId
                    ]);
                    
                    // For critical amount mismatches, halt payment processing
                    if (abs($amount - $sessionAmount) > 10) {
                        Log::error('Critical amount mismatch in service booking - payment processing halted', [
                            'user_id' => $user->id,
                            'hyperpay_amount' => $amount,
                            'session_amount' => $sessionAmount,
                            'difference' => abs($amount - $sessionAmount),
                            'order_id' => $sessionOrderId
                        ]);
                        
                        return redirect()->route('services.booking.order.form')
                            ->with('error', 'Payment amount mismatch detected. Please contact support.');
                    }
                }
                
                // Check for duplicate processing using HyperPay transaction ID
                if ($this->isSuccessfulPayment($resultCode) && $hyperpayTransactionId) {
                    $existingOrder = Order::where('transaction_id', $hyperpayTransactionId)
                        ->where('payment_status', 'paid')
                        ->first();
                        
                    if ($existingOrder) {
                        Log::info('Duplicate service booking payment attempt detected', [
                            'user_id' => $user->id,
                            'hyperpay_transaction_id' => $hyperpayTransactionId,
                            'existing_order_id' => $existingOrder->id,
                            'current_order_id' => $sessionOrderId,
                            'amount' => $amount
                        ]);
                        
                        // Clear session and redirect to history
                        session()->forget(['hyperpay_checkout_id', 'hyperpay_amount', 'hyperpay_order_id', 'hyperpay_brand', 'hyperpay_merchant_tx_id', 'hyperpay_pickup_location', 'hyperpay_services']);
                        
                        return redirect()->route('services.booking.history')
                            ->with('info', 'This payment has already been processed.');
                    }
                }
                
                // Check if payment was successful
                if ($this->isSuccessfulPayment($resultCode)) {
                    return $this->processSuccessfulServicePayment($paymentData, $sessionOrderId, $amount, $sessionBrand);
                } else {
                    $this->logFailedServicePayment($paymentData, $sessionOrderId, $amount);
                    return redirect()->route('services.booking.order.form')
                        ->with('error', 'Payment failed. ' . ($paymentData['result']['description'] ?? 'Please try again.'));
                }
            } else {
                Log::error('HyperPay status check failed', [
                    'checkout_id' => $checkoutId,
                    'response' => $response->body(),
                    'request_url' => $requestUrl
                ]);

                return redirect()->route('services.booking.order.form')
                    ->with('error', 'Unable to verify payment status. Please contact support.');
            }
        } catch (\Exception $e) {
            Log::error('HyperPay status check error: ' . $e->getMessage(), [
                'checkout_id' => $checkoutId,
                'order_id' => $sessionOrderId,
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('services.booking.order.form')
                ->with('error', 'Payment verification failed. Please contact support.');
        } finally {
            // Clear session data
            session()->forget(['hyperpay_checkout_id', 'hyperpay_amount', 'hyperpay_order_id', 'hyperpay_brand', 'hyperpay_merchant_tx_id', 'hyperpay_pickup_location', 'hyperpay_services']);
        }
    }

    /**
     * Generate HyperPay widget HTML
     */
    private function generateHyperpayWidget($checkoutId, $brand)
    {
        $baseUrl = config('services.hyperpay.base_url');
        $brandText = $brand === 'MADA' ? 'MADA' : 'Visa/MasterCard';
        
        // Log the generation
        Log::info('Generating HyperPay widget', [
            'checkout_id' => $checkoutId,
            'brand' => $brand,
            'brand_text' => $brandText,
            'base_url' => $baseUrl,
            'widget_url' => $baseUrl . 'v1/paymentWidgets.js?checkoutId=' . $checkoutId,
            'status_url' => route('services.booking.hyperpay.status')
        ]);
        
        return "
        <div class='hyperpay-container mb-4'>
            <div class='alert alert-info small mb-3'>
                <i class='fa fa-info-circle me-1'></i>
                Secure payment powered by HyperPay - {$brandText}
            </div>
            
            <!-- HyperPay Payment Form -->
            <form action='" . route('services.booking.hyperpay.status') . "' class='paymentWidgets' data-brands='{$brand}' data-checkout-id='{$checkoutId}' id='hyperpay-form-{$checkoutId}'>
                <div class='text-center py-4' id='loading-{$checkoutId}'>
                    <div class='spinner-border text-primary' role='status'>
                        <span class='visually-hidden'>Loading...</span>
                    </div>
                    <p class='mt-2 text-muted'>Loading secure payment form...</p>
                </div>
            </form>
            
            <div class='text-center mt-3'>
                <small class='text-muted'>
                    <i class='fa fa-shield-alt me-1'></i>Secured by HyperPay | <i class='fa fa-lock me-1'></i>PCI DSS Level 1
                </small>
            </div>
        </div>
        ";
    }
    
    /**
     * Generate HyperPay form HTML only (without script)
     */
    private function generateHyperpayFormOnly($checkoutId, $formBrand, $originalBrand = 'credit_card', $displayName = 'Credit Card')
    {
        // Use the exact same approach as wallet controller - simple HTML generation
        $route = route('services.booking.hyperpay.status');
        $brandData = $formBrand === 'MADA' ? 'MADA' : 'VISA MASTER';
        
        $html = '<form id="hyperpay-payment-form" action="' . $route . '" class="paymentWidgets" ';
        $html .= 'data-brands="' . $brandData . '" ';
        $html .= 'data-checkout-id="' . $checkoutId . '">';
        $html .= '<input type="hidden" name="expected_amount" id="expected-amount" value="' . session('hyperpay_amount', 0) . '">';
        $html .= '<input type="hidden" name="payment_brand" value="' . htmlspecialchars($originalBrand) . '">';
        $html .= '<input type="hidden" name="display_name" value="' . htmlspecialchars($displayName) . '">';
        $html .= csrf_field();
        $html .= '</form>';
        
        Log::info('HyperPay widget HTML generated', [
            'html_length' => strlen($html),
            'form_contains_form_tag' => strpos($html, '<form') !== false,
            'form_contains_paymentWidgets' => strpos($html, 'paymentWidgets') !== false,
            'brand_data' => $brandData,
            'original_brand' => $originalBrand,
            'display_name' => $displayName,
            'html_preview' => $html
        ]);
        
        return $html;
    }

    /**
     * Get payment brands for HyperPay widget
     */
    private function getPaymentBrands($brand)
    {
        if ($brand === 'mada_card') {
            return 'MADA';
        } else {
            return 'VISA MASTER';
        }
    }

    /**
     * Check if payment result code indicates success
     */
    private function isSuccessfulPayment($resultCode)
    {
        $successCodes = [
            '000.000.000', // Transaction succeeded
            '000.100.110', // Request successfully processed in 'Merchant in Integrator Test Mode'
            '000.100.111', // Request successfully processed in 'Merchant in Validator Test Mode'
            '000.100.112', // Request successfully processed in 'Merchant in Connector Test Mode'
        ];

        return in_array($resultCode, $successCodes) || 
               (strpos($resultCode, '000.000.') === 0) || 
               (strpos($resultCode, '000.100.1') === 0);
    }

    /**
     * Process successful service payment
     */
    public function processSuccessfulServicePayment($paymentData, $orderId, $amount, $brand)
    {
        $user = Auth::user();
        
        try {
            DB::beginTransaction();

            // Get session data for service bookings
            $sessionServices = session('hyperpay_services', []);
            $sessionPickupLocation = session('hyperpay_pickup_location', 'Payment via HyperPay');

            // Handle case where order doesn't exist - create a basic order
            $order = null;
            if ($orderId) {
                $order = Order::find($orderId);
            }
            
            if (!$order) {
                // Create a basic order for successful payment without existing order
                $order = Order::create([
                    'user_id' => $user->id,
                    'reference_number' => 'ORD-' . Str::random(10),
                    'order_number' => 'ORD-' . Str::random(10),
                    'total_amount' => $amount,
                    'subtotal' => $amount * 0.87, // Approximate subtotal (removing VAT)
                    'vat' => $amount * 0.13, // Approximate VAT
                    'pickup_location' => $sessionPickupLocation,
                    'payment_method' => 'credit_card',
                    'payment_status' => 'paid',
                    'transaction_id' => $paymentData['id'] ?? null,
                ]);
                
                Log::info('Created order for successful payment without pre-existing order', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_id' => $paymentData['id'] ?? null,
                    'pickup_location' => $sessionPickupLocation
                ]);
            } else {
                // Update existing order
                $order->payment_status = 'paid';
                $order->transaction_id = $paymentData['id'] ?? null;
                $order->save();
                
                Log::info('Updated existing order with payment details', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'payment_id' => $paymentData['id'] ?? null
                ]);
            }

            // CRITICAL FIX: Always try to update existing service bookings first
            $updatedBookings = ServiceBooking::where('order_id', $order->id)
                ->update(['payment_status' => 'paid', 'status' => 'approved']);

            Log::info('Updated existing service bookings for order', [
                'order_id' => $order->id,
                'updated_bookings_count' => $updatedBookings
            ]);

            // If we have existing service bookings, ensure their vehicles are properly linked
            if ($updatedBookings > 0) {
                $existingBookings = ServiceBooking::where('order_id', $order->id)->get();
                
                foreach ($existingBookings as $booking) {
                    // Ensure vehicle is linked if plate number exists
                    if (!$booking->vehicle_id && $booking->plate_number) {
                        $vehicle = Vehicle::where('plate_number', $booking->plate_number)
                            ->where('user_id', $user->id)
                            ->first();
                            
                        if ($vehicle) {
                            $booking->vehicle_id = $vehicle->id;
                            $booking->save();
                            
                            Log::info('Linked existing vehicle to credit card booking', [
                                'booking_id' => $booking->id,
                                'vehicle_id' => $vehicle->id,
                                'plate_number' => $booking->plate_number
                            ]);
                        }
                    }
                    
                    // Update RFID balance if needed
                    if ($booking->vehicle_id && $booking->refule_amount > 0) {
                        $vehicle = Vehicle::find($booking->vehicle_id);
                        if ($vehicle) {
                            $currentBalance = $vehicle->rfid_balance ?? 0;
                            $vehicle->rfid_balance = $currentBalance + $booking->refule_amount;
                            $vehicle->save();
                            
                            Log::info('Updated RFID balance for credit card payment', [
                                'vehicle_id' => $vehicle->id,
                                'added_amount' => $booking->refule_amount,
                                'new_balance' => $vehicle->rfid_balance
                            ]);
                        }
                    }
                }
            }

            // FALLBACK: Create service bookings if none exist and we have session data
            if ($updatedBookings === 0 && !empty($sessionServices)) {
                Log::info('Creating service bookings from session data', [
                    'order_id' => $order->id,
                    'services_count' => count($sessionServices),
                    'session_services' => $sessionServices
                ]);

                foreach ($sessionServices as $index => $serviceData) {
                    // Map service codes to actual service IDs
                    $serviceId = Service::getServiceIdFromCode($serviceData['service_id']);
                    if (!$serviceId) {
                        Log::warning('Could not find service ID for code', [
                            'service_code' => $serviceData['service_id'],
                            'order_id' => $order->id
                        ]);
                        continue;
                    }

                    // Get service details for pricing
                    $service = Service::find($serviceId);
                    $basePrice = $service ? $service->base_price : 150.00;
                    $vatRate = $service ? ($service->vat_percentage / 100) : 0.15;
                    $vatAmount = $basePrice * $vatRate;
                    $refuleAmount = floatval($serviceData['refule_amount'] ?? 0);

                    // Create or find vehicle (similar to processOrder method)
                    $vehicleId = null;
                    if (!empty($serviceData['plate_number'])) {
                        // Check if user wants to link to an existing vehicle
                        if (!empty($serviceData['use_existing_vehicle']) && !empty($serviceData['vehicle_id'])) {
                            // Use existing vehicle
                            $vehicle = Vehicle::where('id', $serviceData['vehicle_id'])
                                ->where('user_id', $user->id)
                                ->first();
                                
                            if ($vehicle) {
                                // If the vehicle has no RFID status or inactive status, update it to pending
                                if (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive') {
                                    $vehicle->rfid_status = 'pending';
                                    $vehicle->save();
                                }
                                $vehicleId = $vehicle->id;
                            }
                        } else {
                            // Create or find vehicle
                            $vehicle = Vehicle::firstOrCreate(
                                [
                                    'plate_number' => $serviceData['plate_number'], 
                                    'user_id' => $user->id
                                ],
                                [
                                    'make' => $serviceData['vehicle_make'],
                                    'manufacturer' => $serviceData['vehicle_manufacturer'] ?? $serviceData['vehicle_make'],
                                    'model' => $serviceData['vehicle_model'],
                                    'year' => $serviceData['vehicle_year'],
                                    'rfid_status' => 'pending',
                                    'status' => 'active'
                                ]
                            );

                            // Update existing vehicle's RFID status if needed
                            if (!$vehicle->wasRecentlyCreated && 
                                (!$vehicle->rfid_status || $vehicle->rfid_status === 'inactive')) {
                                $vehicle->rfid_status = 'pending';
                                $vehicle->save();
                            }
                            
                            $vehicleId = $vehicle->id;
                        }
                    }

                    // Create service booking
                    $booking = ServiceBooking::create([
                        'reference_number' => 'SB-' . strtoupper(Str::random(6)) . '-' . ($index + 1),
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'service_id' => $serviceId,
                        'service_type' => $serviceData['service_type'],
                        'vehicle_id' => $vehicleId,
                        'vehicle_make' => $serviceData['vehicle_make'],
                        'vehicle_manufacturer' => $serviceData['vehicle_manufacturer'] ?? $serviceData['vehicle_make'],
                        'vehicle_model' => $serviceData['vehicle_model'],
                        'vehicle_year' => $serviceData['vehicle_year'],
                        'plate_number' => $serviceData['plate_number'],
                        'refule_amount' => $refuleAmount,
                        'base_price' => $basePrice,
                        'vat_amount' => $vatAmount,
                        'total_amount' => $basePrice + $vatAmount + $refuleAmount,
                        'payment_status' => 'paid',
                        'payment_method' => 'credit_card',
                        'status' => 'approved'
                    ]);

                    // If vehicle exists and there's a refueling amount, update the vehicle's RFID balance
                    if ($vehicleId && !empty($refuleAmount) && $refuleAmount > 0) {
                        $vehicle = Vehicle::find($vehicleId);
                        if ($vehicle) {
                            // Add the refueling amount to the existing balance (or initialize if null)
                            $currentBalance = $vehicle->rfid_balance ?? 0;
                            $vehicle->rfid_balance = $currentBalance + $refuleAmount;
                            $vehicle->save();
                        }
                    }

                    Log::info('Created service booking from session data', [
                        'booking_id' => $booking->id,
                        'order_id' => $order->id,
                        'service_type' => $serviceData['service_type'],
                        'plate_number' => $serviceData['plate_number'],
                        'vehicle_id' => $vehicleId,
                        'total_amount' => $booking->total_amount
                    ]);
                }

                $updatedBookings = count($sessionServices);
            }

            // Extract card information
            $cardBrand = $this->extractCardBrand($paymentData);
            $lastFour = substr($paymentData['card']['number'] ?? '****', -4);

            // Log successful payment
            LogHelper::log('payment_completed', 'Service payment completed via HyperPay', $order, [
                'payment_method' => 'credit_card',
                'card_brand' => $cardBrand,
                'card_last_four' => $lastFour,
                'amount' => $amount,
                'transaction_id' => $paymentData['id'] ?? null,
                'payment_brand' => $brand,
                'order_created' => !$orderId, // Flag to indicate if order was created during payment
                'existing_bookings_count' => $updatedBookings,
                'services_from_session' => !empty($sessionServices)
            ]);

            DB::commit();

            return redirect()->route('services.booking.history')
                ->with('success', 'Payment successful! Your service order has been confirmed.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful service payment: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => $user->id,
                'amount' => $amount,
                'payment_data' => $paymentData,
                'session_services' => session('hyperpay_services', []),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('services.booking.order.form')
                ->with('error', 'Payment was successful but there was an error processing your order. Please contact support.');
        }
    }

    /**
     * Log failed service payment
     */
    private function logFailedServicePayment($paymentData, $orderId, $amount)
    {
        $user = Auth::user();
        $resultCode = $paymentData['result']['code'] ?? 'UNKNOWN';
        $resultDescription = $paymentData['result']['description'] ?? 'Unknown error';

        LogHelper::log('payment_failed', 'Service payment failed via HyperPay', null, [
            'payment_method' => 'credit_card',
            'amount' => $amount,
            'order_id' => $orderId,
            'error_code' => $resultCode,
            'error_description' => $resultDescription,
            'payment_data' => $paymentData
        ]);
    }

    /**
     * Extract card brand from HyperPay response
     */
    private function extractCardBrand($paymentData)
    {
        // Try to get from paymentBrand field first
        if (!empty($paymentData['paymentBrand'])) {
            return $this->normalizeCardBrand($paymentData['paymentBrand']);
        }

        // Try to get from card.brand
        if (!empty($paymentData['card']['brand'])) {
            return $this->normalizeCardBrand($paymentData['card']['brand']);
        }

        // Try to extract from BIN
        if (!empty($paymentData['card']['number'])) {
            return $this->getBrandFromBin($paymentData['card']['number']);
        }

        return 'UNKNOWN';
    }

    /**
     * Normalize card brand name
     */
    private function normalizeCardBrand($brand)
    {
        $brand = strtoupper(trim($brand));
        
        switch ($brand) {
            case 'VISA':
                return 'VISA';
            case 'MASTER':
            case 'MASTERCARD':
                return 'MASTERCARD';
            case 'MADA':
                return 'MADA';
            default:
                return $brand;
        }
    }

    /**
     * Get card brand from BIN number
     */
    private function getBrandFromBin($cardNumber)
    {
        $bin = substr(str_replace(' ', '', $cardNumber), 0, 6);
        
        // MADA BIN ranges
        $madaRanges = [
            '446404', '446405', '446406', '446407', '446408', '446409',
            '457865', '457866', '457867', '457868', '457869',
            '968201', '968202', '968203', '968204', '968205',
            '968206', '968207', '968208', '968209', '968210',
            '968211'
        ];
        
        foreach ($madaRanges as $range) {
            if (strpos($bin, $range) === 0) {
                return 'MADA';
            }
        }
        
        // Visa starts with 4
        if (substr($cardNumber, 0, 1) === '4') {
            return 'VISA';
        }
        
        // MasterCard starts with 5 or 2221-2720
        if (substr($cardNumber, 0, 1) === '5') {
            return 'MASTERCARD';
        }
        
        $firstFour = substr($cardNumber, 0, 4);
        if ($firstFour >= 2221 && $firstFour <= 2720) {
            return 'MASTERCARD';
        }
        
        return 'UNKNOWN';
    }
} 