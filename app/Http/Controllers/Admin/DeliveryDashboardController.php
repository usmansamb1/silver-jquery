<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Models\Vehicle;
use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\RfidDeliveryNotification;

class DeliveryDashboardController extends Controller
{
    /**
     * Display the RFID delivery dashboard
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pendingCount = ServiceBooking::whereIn('payment_status', ['paid', 'approved'])
            ->where('delivery_status', 'pending')->orWhereNull('delivery_status')
            ->count();
            
        $deliveredCount = ServiceBooking::whereIn('payment_status', ['paid', 'approved'])
            ->where('delivery_status', 'delivered')
            ->count();
        
        $services = ServiceBooking::with(['user', 'service', 'vehicle'])
            ->whereIn('payment_status', ['paid', 'approved'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.delivery.dashboard', compact('pendingCount', 'deliveredCount', 'services'));
    }
    
    /**
     * Get filtered services for the RFID delivery dashboard
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getServices(Request $request)
    {
        $query = ServiceBooking::with(['user', 'service', 'vehicle'])
            ->whereIn('payment_status', ['paid', 'approved']);
        
        if ($request->filled('booking_id')) {
            $query->where('id', $request->booking_id);
        }
        
        if ($request->filled('customer_id')) {
            $query->where('user_id', $request->customer_id);
        }
        
        if ($request->filled('mobile')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->mobile . '%');
            });
        }
        
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status)->orWhereNull('delivery_status');
        }
        
        $services = $query->orderBy('created_at', 'desc')->paginate(15);
        
        if ($request->ajax()) {
            return view('admin.delivery.services-table', compact('services'))->render();
        }
        
        return view('admin.delivery.services', compact('services'));
    }
    
    /**
     * Update RFID number and delivery status
     * 
     * @param Request $request
     * @param ServiceBooking $booking
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRfid(Request $request, ServiceBooking $booking)
    {
        $request->validate([
            'rfid_number' => 'required|string|max:100',
        ]);
        
        // Check if the current user has the delivery role
        if (!auth()->user()->hasRole('delivery')) {
            return response()->json([
                'success' => false,
                'message' => 'Only delivery agents can update RFID information',
            ], 403);
        }
     
        // Check if service is paid and delivery is pending
        if (!in_array($booking->payment_status, ['paid', 'approved']) || ($booking->delivery_status !== 'pending' && $booking->delivery_status !== null)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update RFID for a service that is not paid or already delivered',
            ], 422);
        }
          
        DB::beginTransaction();
        
        try {
            // Update the ServiceBooking record
            $booking->update([
                'rfid_number' => $request->rfid_number,
                'delivery_status' => 'delivered'
            ]);
            
            // Update the associated Vehicle record if it exists
            if ($booking->vehicle_id) {
                $vehicle = Vehicle::find($booking->vehicle_id);
                
                if ($vehicle) {
                    $vehicle->update([
                        'rfid_number' => $request->rfid_number,
                        'rfid_status' => 'active'
                    ]);
                }
            } else if (!empty($booking->plate_number)) {
                // If no direct vehicle link exists, try to find the vehicle by plate number and user
                $vehicle = Vehicle::where('plate_number', $booking->plate_number)
                    ->where('user_id', $booking->user_id)
                    ->first();
                
                if ($vehicle) {
                    $vehicle->update([
                        'rfid_number' => $request->rfid_number,
                        'rfid_status' => 'active'
                    ]);
                    
                    // Link the vehicle to this booking for future reference
                    $booking->vehicle_id = $vehicle->id;
                    $booking->save();
                } else {
                    // Create a new vehicle record if one doesn't exist
                    $vehicle = Vehicle::create([
                        'user_id' => $booking->user_id,
                        'plate_number' => $booking->plate_number,
                        'make' => $booking->vehicle_make,
                        'manufacturer' => $booking->vehicle_manufacturer,
                        'model' => $booking->vehicle_model,
                        'year' => $booking->vehicle_year,
                        'rfid_number' => $request->rfid_number,
                        'rfid_status' => 'active',
                        'status' => 'active',
                        'rfid_balance' => $booking->refule_amount > 0 ? $booking->refule_amount : 0
                    ]);
                    
                    // Link the new vehicle to this booking
                    $booking->vehicle_id = $vehicle->id;
                    $booking->save();
                }
            }
            
            // Use the specialized logging method that safely handles ServiceBooking IDs
            LogHelper::logServiceRfidDelivery($booking, "RFID delivered", [
                'rfid_number' => $request->rfid_number,
                'delivery_status' => 'delivered',
                'updated_by' => auth()->id()
            ]);
            
            // Send email notification to customer
            if ($booking->user) {
                try {
                    $booking->user->notify(new RfidDeliveryNotification($booking));
                    Log::info('RFID delivery notification sent', [
                        'user_id' => $booking->user->id, 
                        'booking_id' => $booking->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send RFID delivery notification', [
                        'user_id' => $booking->user->id,
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'RFID updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating RFID: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync RFID information from a booking to its associated vehicle
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncVehicleRfid(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|string',
            'rfid_number' => 'required|string|max:100',
            'vehicle_id' => 'nullable|string'
        ]);
        
        // Check if the current user has the delivery role
        if (!auth()->user()->hasRole('delivery')) {
            return response()->json([
                'success' => false,
                'message' => 'Only delivery agents can update RFID information',
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            // Find the booking
            $booking = ServiceBooking::findOrFail($request->booking_id);
            
            // Find the vehicle
            $vehicle = null;
            
            if (!empty($request->vehicle_id)) {
                // Use the provided vehicle ID
                $vehicle = Vehicle::find($request->vehicle_id);
            } else if ($booking->vehicle_id) {
                // Use the booking's associated vehicle
                $vehicle = Vehicle::find($booking->vehicle_id);
            } else if (!empty($booking->plate_number)) {
                // Find by plate number
                $vehicle = Vehicle::where('plate_number', $booking->plate_number)
                    ->where('user_id', $booking->user_id)
                    ->first();
            }
            
            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'No vehicle found to update'
                ], 404);
            }
            
            // Update the vehicle with RFID information
            $vehicle->update([
                'rfid_number' => $request->rfid_number,
                'rfid_status' => 'active'
            ]);
            
            // If the booking has a refueling amount, add it to the vehicle's balance
            if ($booking->refule_amount > 0) {
                $currentBalance = $vehicle->rfid_balance ?? 0;
                $vehicle->rfid_balance = $currentBalance + $booking->refule_amount;
                $vehicle->save();
            }
            
            // If the booking isn't already linked to the vehicle, link it
            if ($booking->vehicle_id !== $vehicle->id) {
                $booking->vehicle_id = $vehicle->id;
                $booking->save();
            }
            
            // Log the update
            Log::info('Vehicle RFID synchronized', [
                'booking_id' => $booking->id,
                'vehicle_id' => $vehicle->id,
                'rfid_number' => $request->rfid_number,
                'user_id' => auth()->id()
            ]);
            
            // Send email notification to customer
            if ($booking->user) {
                try {
                    $booking->user->notify(new RfidDeliveryNotification($booking));
                    Log::info('RFID delivery notification sent', [
                        'user_id' => $booking->user->id, 
                        'booking_id' => $booking->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send RFID delivery notification', [
                        'user_id' => $booking->user->id,
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Vehicle RFID information updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error syncing vehicle RFID: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing RFID: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update all vehicles with missing RFID information
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchSyncVehicleRfids()
    {
        // Check if the current user has the delivery role
        if (!auth()->user()->hasRole('delivery')) {
            return response()->json([
                'success' => false,
                'message' => 'Only delivery agents can update RFID information',
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            // Find all delivered service bookings with RFID numbers
            $bookings = ServiceBooking::where('delivery_status', 'delivered')
                ->whereNotNull('rfid_number')
                ->get();
                
            $updatedCount = 0;
            $errors = [];
            
            foreach ($bookings as $booking) {
                try {
                    $vehicle = null;
                    
                    // Try to find associated vehicle in several ways
                    if ($booking->vehicle_id) {
                        $vehicle = Vehicle::find($booking->vehicle_id);
                    }
                    
                    if (!$vehicle && !empty($booking->plate_number)) {
                        $vehicle = Vehicle::where('plate_number', $booking->plate_number)
                            ->where('user_id', $booking->user_id)
                            ->first();
                    }
                    
                    if ($vehicle) {
                        // Only update if the RFID number is missing or different
                        if (!$vehicle->rfid_number || $vehicle->rfid_number !== $booking->rfid_number) {
                            $vehicle->update([
                                'rfid_number' => $booking->rfid_number,
                                'rfid_status' => 'active'
                            ]);
                            
                            // Link the vehicle to the booking if not already
                            if ($booking->vehicle_id !== $vehicle->id) {
                                $booking->vehicle_id = $vehicle->id;
                                $booking->save();
                            }
                            
                            $updatedCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error with booking {$booking->id}: {$e->getMessage()}";
                    continue;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} vehicles with RFID information",
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch RFID sync error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error during batch sync: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync all vehicle RFID balances based on service booking refueling amounts
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncVehicleRfidBalances()
    {
        // Check if the current user has the delivery role
        if (!auth()->user()->hasRole('delivery')) {
            return response()->json([
                'success' => false,
                'message' => 'Only delivery agents can update RFID information',
            ], 403);
        }
        
        DB::beginTransaction();
        
        try {
            // Find all service bookings with refueling amounts
            $bookings = ServiceBooking::whereNotNull('vehicle_id')
                ->where('payment_status', 'paid')
                ->where('refule_amount', '>', 0)
                ->get();
                
            $updatedCount = 0;
            $errors = [];
            
            foreach ($bookings as $booking) {
                try {
                    // Only process bookings that have a valid vehicle
                    if ($booking->vehicle_id) {
                        $vehicle = Vehicle::find($booking->vehicle_id);
                        
                        if ($vehicle) {
                            // Store existing balance to log the change
                            $oldBalance = $vehicle->rfid_balance ?? 0;
                            
                            // Reset balance to zero if null
                            if ($vehicle->rfid_balance === null) {
                                $vehicle->rfid_balance = 0;
                            }
                            
                            // Add booking's refueling amount
                            $vehicle->rfid_balance += $booking->refule_amount;
                            $vehicle->save();
                            
                            // Log the balance update
                            Log::info("Updated vehicle RFID balance", [
                                'vehicle_id' => $vehicle->id,
                                'booking_id' => $booking->id,
                                'old_balance' => $oldBalance,
                                'refuel_amount' => $booking->refule_amount,
                                'new_balance' => $vehicle->rfid_balance
                            ]);
                            
                            $updatedCount++;
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error with booking {$booking->id}: {$e->getMessage()}";
                    continue;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} vehicles with refueling amounts",
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RFID balance sync error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error during balance sync: ' . $e->getMessage()
            ], 500);
        }
    }
}
