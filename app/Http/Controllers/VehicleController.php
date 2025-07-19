<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\LogHelper;

class VehicleController extends Controller
{
    /**
     * Display a listing of user's vehicles.
     */
    public function index(Request $request)
    {
        // Set up query for vehicles
        $query = Vehicle::where('user_id', auth()->id());
        
        // Filter by RFID status if requested
        if ($request->has('rfid_status') && $request->rfid_status != 'all') {
            if ($request->rfid_status === 'with_rfid') {
                $query->whereNotNull('rfid_number');
            } elseif ($request->rfid_status === 'without_rfid') {
                $query->whereNull('rfid_number');
            }
        }
        
        // Filter by delivery status if requested
        if ($request->has('delivery_status') && $request->delivery_status != 'all') {
            $query->where('status', $request->delivery_status);
        }
        
        // Get the vehicles
        $vehicles = $query->orderBy('created_at', 'desc')->get();
        
        // Get delivery status counts for summary
        $statusCounts = [
            'all' => Vehicle::where('user_id', auth()->id())->count(),
            'active' => Vehicle::where('user_id', auth()->id())->where('status', 'active')->count(),
            'pending_delivery' => Vehicle::where('user_id', auth()->id())->where('status', 'pending_delivery')->count(),
            'delivered' => Vehicle::where('user_id', auth()->id())->where('status', 'delivered')->count(),
            'with_rfid' => Vehicle::where('user_id', auth()->id())->whereNotNull('rfid_number')->count(),
            'without_rfid' => Vehicle::where('user_id', auth()->id())->whereNull('rfid_number')->count(),
        ];
            
        return view('vehicles.index', compact('vehicles', 'statusCounts'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'make' => 'required|string|max:50',
            'manufacturer' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|string|max:4',
        ]);
        
        // Check if vehicle with same plate number already exists for user
        $existingVehicle = Vehicle::where('user_id', auth()->id())
            ->where('plate_number', $validated['plate_number'])
            ->first();
            
        if ($existingVehicle) {
            return redirect()->route('vehicles.index')
                ->with('error', __('A vehicle with this plate number already exists.'));
        }
        
        // Create new vehicle
        DB::beginTransaction();
        
        try {
            $vehicle = Vehicle::create([
                'user_id' => auth()->id(),
                'plate_number' => $validated['plate_number'],
                'make' => $validated['make'],
                'manufacturer' => $validated['manufacturer'],
                'model' => $validated['model'],
                'year' => $validated['year'],
                'rfid_status' => null,  // No RFID assigned by default
                'rfid_balance' => 0.00,
                'status' => 'active'    // Default status is active
            ]);
            
            // Log the vehicle creation
            LogHelper::log('vehicle_created', 'Added new vehicle: ' . $vehicle->make . ' ' . $vehicle->model . ' (' . $vehicle->plate_number . ')', $vehicle, [
                'plate_number' => $vehicle->plate_number,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'year' => $vehicle->year
            ]);
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', __('Vehicle added successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('vehicles.index')
                ->with('error', __('Failed to add vehicle: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        // Ensure vehicle belongs to authenticated user
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get related service bookings
        $serviceBookings = ServiceBooking::where('vehicle_id', $vehicle->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get RFID transactions if RFID is assigned
        $rfidTransactions = [];
        if ($vehicle->hasRfid()) {
            $rfidTransactions = $vehicle->rfidTransactions()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }
            
        return view('vehicles.show', compact('vehicle', 'serviceBookings', 'rfidTransactions'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        // Ensure vehicle belongs to authenticated user
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Ensure vehicle belongs to authenticated user
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'make' => 'required|string|max:50',
            'manufacturer' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|string|max:4',
        ]);
        
        // Check if another vehicle with same plate number exists for user
        $existingVehicle = Vehicle::where('user_id', auth()->id())
            ->where('plate_number', $validated['plate_number'])
            ->where('id', '!=', $vehicle->id)
            ->first();
            
        if ($existingVehicle) {
            return redirect()->route('vehicles.edit', $vehicle)
                ->with('error', __('Another vehicle with this plate number already exists.'));
        }
        
        // Store old values for logging
        $oldValues = [
            'plate_number' => $vehicle->plate_number,
            'make' => $vehicle->make,
            'manufacturer' => $vehicle->manufacturer,
            'model' => $vehicle->model,
            'year' => $vehicle->year
        ];
        
        DB::beginTransaction();
        
        try {
            // Update vehicle
            $vehicle->update($validated);
            
            // Log the vehicle update
            LogHelper::log('vehicle_updated', 'Updated vehicle: ' . $vehicle->make . ' ' . $vehicle->model . ' (' . $vehicle->plate_number . ')', $vehicle, [
                'old_values' => $oldValues,
                'new_values' => $validated
            ]);
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', __('Vehicle updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('vehicles.edit', $vehicle)
                ->with('error', __('Failed to update vehicle: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Ensure vehicle belongs to authenticated user
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if vehicle is linked to any service bookings
        $hasBookings = ServiceBooking::where('vehicle_id', $vehicle->id)->exists();
        
        if ($hasBookings) {
            return redirect()->route('vehicles.index')
                ->with('error', __('Cannot delete vehicle as it is linked to service bookings.'));
        }
        
        // Check if vehicle has an RFID assigned
        if ($vehicle->hasRfid()) {
            return redirect()->route('vehicles.index')
                ->with('error', __('Cannot delete vehicle as it has an active RFID chip. Please transfer or remove the RFID first.'));
        }
        
        DB::beginTransaction();
        
        try {
            // Store vehicle details for logging
            $vehicleDetails = [
                'id' => $vehicle->id,
                'plate_number' => $vehicle->plate_number,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'year' => $vehicle->year
            ];
            
            // Delete vehicle
            $vehicle->delete();
            
            // Log the vehicle deletion
            LogHelper::log('vehicle_deleted', 'Deleted vehicle: ' . $vehicleDetails['make'] . ' ' . $vehicleDetails['model'] . ' (' . $vehicleDetails['plate_number'] . ')', null, $vehicleDetails);
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', __('Vehicle deleted successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('vehicles.index')
                ->with('error', __('Failed to delete vehicle: ') . $e->getMessage());
        }
    }
}
