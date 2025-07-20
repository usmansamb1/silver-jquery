<div class="table-responsive">
    <table class="table table-bordered" id="services-table" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Service Type</th>
                <th>Customer</th>
                <th>Mobile</th>
                <th>Customer ID</th>
                <th>Vehicle</th>
                <th>Plate Number</th>
                <th>Date</th>
                <th>RFID Status</th>
                <th>Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $booking)
       
                <tr>
                    <td>{{ $booking->id }}</td>
                    <td>{{ $booking->service ? $booking->service->name : $booking->service_type }}</td>
                    <td>{{ $booking->user->name ?? $booking->user->company_name }}</td>
                    <td>{{ $booking->user->mobile }}</td>
                    <td>{{ $booking->user->customer_no ? str_pad($booking->user->customer_no, 6, '0', STR_PAD_LEFT) : $booking->user->id }}</td>
                    <td>
                        {{ $booking->vehicle_manufacturer }} {{ $booking->vehicle_make }} {{ $booking->vehicle_model }} ({{ $booking->vehicle_year }})
                    </td>
                    <td>{{ $booking->plate_number }}</td>
                    <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                    <td>
                        @if($booking->delivery_status == 'pending' || $booking->delivery_status == null)
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-success">Delivered</span>
                            <br>
                            <small>RFID: {{ $booking->rfid_number }}</small>
                            @if($booking->vehicle && $booking->vehicle->rfid_number === $booking->rfid_number)
                                <br><span class="badge badge-info">Vehicle Updated</span>
                            @elseif($booking->vehicle && $booking->vehicle->rfid_number && $booking->vehicle->rfid_number !== $booking->rfid_number)
                                <br><span class="badge badge-danger">Vehicle Mismatch!</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($booking->vehicle)
                            <span class="text-primary">{{ number_format($booking->vehicle->rfid_balance ?? 0, 2) }} SAR</span>
                            @if($booking->refule_amount > 0)
                                <br><small class="text-success">+{{ number_format($booking->refule_amount, 2) }}</small>
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($booking->delivery_status == 'pending' || $booking->delivery_status == null)
                            <button type="button" class="btn btn-sm btn-primary update-rfid-btn" data-booking-id="{{ $booking->id }}">
                               Undelivere
                            </button>
                        @else
                            <span class="text-success"><i class="fas fa-check-circle"></i> Delivered</span>
                            @if($booking->vehicle && (!$booking->vehicle->rfid_number || $booking->vehicle->rfid_number !== $booking->rfid_number))
                                <button type="button" class="btn btn-sm btn-warning mt-1 sync-vehicle-rfid-btn" 
                                    data-booking-id="{{ $booking->id }}" 
                                    data-rfid="{{ $booking->rfid_number }}"
                                    data-vehicle-id="{{ $booking->vehicle ? $booking->vehicle->id : '' }}">
                                    Sync Vehicle
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No service bookings found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $services->links() }} 