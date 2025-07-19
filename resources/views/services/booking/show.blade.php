@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h5 class="mb-0">Service Booking Details</h5>
            <a href="{{ route('services.booking.index') }}" class="btn btn-sm btn-light">Back to Bookings</a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Reference Number</h6>
                    <p class="font-weight-bold">{{ $booking->reference_number }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="text-muted">Status</h6>
                    <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                    @if($booking->status == 'paid' || $booking->payment_status == 'paid')
                    <div class="mt-2">
                        <h6 class="text-muted">RFID Status</h6>
                        <span class="badge bg-{{ $booking->delivery_status == 'delivered' ? 'success' : 'warning' }}">
                            {{ ucfirst($booking->delivery_status) }}
                        </span>
                        @if($booking->delivery_status == 'delivered' && $booking->rfid_number)
                            <p class="mt-1 mb-0"><small>RFID Number: <strong>{{ $booking->rfid_number }}</strong></small></p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Service Details -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Service Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Service:</strong> 
                                @if($booking->service)
                                    {{ $booking->service->name }}
                                @elseif($booking->service_type)
                                    {{ App\Models\Service::getServiceTypeById($booking->service_type) }}
                                @else
                                    <span class="text-muted">Unknown Service</span>
                                @endif
                            </p>
                            <p><strong>Description:</strong> 
                                @if($booking->service)
                                    {{ $booking->service->description }}
                                @else
                                    <span class="text-muted">No description available</span>
                                @endif
                            </p>
                            <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</p>
                            <p><strong>Booking Time:</strong> {{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Vehicle Details -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Vehicle Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Vehicle:</strong> {{ $booking->vehicle_make }} {{ $booking->vehicle_model }} ({{ $booking->vehicle_year }})</p>
                            <p><strong>Plate Number:</strong> {{ $booking->plate_number }}</p>
                            @if(isset($booking->refule_amount))
                            <p><strong>Refuel Amount:</strong> SAR {{ number_format($booking->refule_amount, 2) }}</p>
                            @endif
                            @if($booking->vehicle_id)
                            <p><strong>Vehicle ID:</strong> {{ $booking->vehicle_id }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong> {{ ucfirst($booking->payment_method) }}</p>
                            <p><strong>Payment Status:</strong> 
                                <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Price Breakdown</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Base Price:</span>
                                        <span>SAR {{ number_format($booking->base_price, 2) }}</span>
                                    </div>
                                    @if(isset($booking->refule_amount) && $booking->refule_amount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Refuel Amount:</span>
                                        <span>SAR {{ number_format($booking->refule_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>VAT:</span>
                                        <span>SAR {{ number_format($booking->vat_amount, 2) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        @php
                                            $total = $booking->base_price + $booking->vat_amount;
                                            if(isset($booking->refule_amount)) {
                                                $total += $booking->refule_amount;
                                            }
                                        @endphp
                                        <strong>SAR {{ number_format($total, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($booking->status == 'pending')
            <div class="text-end">
                <form action="{{ route('services.booking.cancel', $booking->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">
                        Cancel Booking
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 