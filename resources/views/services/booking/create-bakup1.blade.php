@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Book Service - {{ $service->name }}</h5>
                    <a href="{{ route('services.booking.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Services
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('services.booking.store') }}" method="POST" id="bookingForm">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                        <!-- Service Details -->
                        <div class="mb-4">
                            <h6>Service Details</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-1"><strong>Type:</strong> {{ $service->service_type }}</p>
                                    <p class="mb-1"><strong>Duration:</strong> {{ $service->estimated_duration }} minutes</p>
                                    <p class="mb-1"><strong>Base Price:</strong> SAR {{ number_format($service->base_price, 2) }}</p>
                                    <p class="mb-1"><strong>VAT ({{ $service->vat_percentage }}%):</strong> SAR {{ number_format($service->base_price * $service->vat_percentage / 100, 2) }}</p>
                                    <p class="mb-0"><strong>Total:</strong> SAR {{ number_format($service->calculateTotalPrice(), 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Details -->
                        <div class="mb-4">
                            <h6>Vehicle Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vehicle_make" class="form-label">Make</label>
                                        <input type="text" class="form-control @error('vehicle_make') is-invalid @enderror" 
                                               id="vehicle_make" name="vehicle_make" value="{{ old('vehicle_make') }}" required>
                                        @error('vehicle_make')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vehicle_model" class="form-label">Model</label>
                                        <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" 
                                               id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}" required>
                                        @error('vehicle_model')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vehicle_year" class="form-label">Year</label>
                                        <input type="number" class="form-control @error('vehicle_year') is-invalid @enderror" 
                                               id="vehicle_year" name="vehicle_year" value="{{ old('vehicle_year') }}" 
                                               min="1900" max="{{ date('Y') + 1 }}" required>
                                        @error('vehicle_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="plate_number" class="form-label">Plate Number</label>
                                        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                               id="plate_number" name="plate_number" value="{{ old('plate_number') }}" required>
                                        @error('plate_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Date & Time -->
                        <div class="mb-4">
                            <h6>Booking Schedule</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="booking_date" class="form-label">Date</label>
                                        <input type="date" class="form-control @error('booking_date') is-invalid @enderror" 
                                               id="booking_date" name="booking_date" 
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                               value="{{ old('booking_date') }}" required>
                                        @error('booking_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="booking_time" class="form-label">Time</label>
                                        <input type="time" class="form-control @error('booking_time') is-invalid @enderror" 
                                               id="booking_time" name="booking_time" value="{{ old('booking_time') }}" required>
                                        @error('booking_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <h6>Payment Method</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="wallet" value="wallet" {{ old('payment_method') == 'wallet' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="wallet">
                                    Wallet (Current Balance: SAR {{ number_format(auth()->user()->wallet?->balance ?? 0, 2) }})
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                       id="credit_card" value="credit_card" {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                <label class="form-check-label" for="credit_card">
                                    Credit Card
                                </label>
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for booking
    const bookingDate = document.getElementById('booking_date');
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    bookingDate.min = tomorrow.toISOString().split('T')[0];
    
    // Form validation
    const form = document.getElementById('bookingForm');
    form.addEventListener('submit', function(event) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            event.preventDefault();
            alert('Please select a payment method');
        }
    });
});
</script>
@endpush
@endsection 