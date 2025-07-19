@extends('layouts.app')

@section('title', __('Book a Service'))

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ __('Book a Service') }}</h3>
            <a href="{{ route('services.booking.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> {{ __('Back to Bookings') }}
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('services.booking.store') }}" method="POST">
                @csrf
                
                <!-- Service Selection -->
                <div class="mb-4">
                    <h5>{{ __('Service Selection') }}</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            @if(isset($service))
                                <div class="alert alert-info">
                                    {{ __('You are booking :service service.', ['service' => $service->name]) }}
                                    <br>{{ __('Base price: :price', ['price' => 'SAR ' . number_format($service->price, 2)]) }}
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="service_id">{{ __('Select Service') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                                        <option value="">{{ __('-- Select Service --') }}</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" 
                                                    data-price="{{ $service->price }}"
                                                    data-vat="{{ config('app.vat_rate', 15) }}"
                                                    {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} (SAR {{ number_format($service->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Information -->
                <div class="mb-4">
                    <h5>{{ __('Vehicle Information') }}</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="use_existing_vehicle" id="use_existing_no" value="0" {{ old('use_existing_vehicle') === '0' ? 'checked' : '' }} checked>
                                    <label class="form-check-label" for="use_existing_no">
                                        {{ __('Enter new vehicle information') }}
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="use_existing_vehicle" id="use_existing_yes" value="1" {{ old('use_existing_vehicle') === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_existing_yes">
                                        {{ __('Select from my vehicles') }}
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Existing Vehicle Selector (initially hidden) -->
                            <div id="existing_vehicle_section" class="mb-4" style="display: none;">
                                <div class="form-group">
                                    <label for="vehicle_id">{{ __('Select Vehicle') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
                                        <option value="">{{ __('-- Select a vehicle --') }}</option>
                                        @php
                                            $vehicles = \App\Models\Vehicle::where('user_id', auth()->id())->get();
                                        @endphp
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->manufacturer }} {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    @if($vehicles->isEmpty())
                                        <div class="text-info mt-2">
                                            {{ __('You don\'t have any saved vehicles.') }} <a href="{{ route('vehicles.create') }}" target="_blank">{{ __('Add a vehicle') }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- New Vehicle Fields -->
                            <div id="new_vehicle_section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="vehicle_make">{{ __('Vehicle Make') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('vehicle_make') is-invalid @enderror" id="vehicle_make" name="vehicle_make" value="{{ old('vehicle_make') }}">
                                            @error('vehicle_make')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="vehicle_model">{{ __('Vehicle Model') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}">
                                            @error('vehicle_model')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="vehicle_year">{{ __('Vehicle Year') }} <span class="text-danger">*</span></label>
                                            <select class="form-control @error('vehicle_year') is-invalid @enderror" id="vehicle_year" name="vehicle_year">
                                                <option value="">{{ __('-- Select Year --') }}</option>
                                                @for($i = date('Y'); $i >= 1990; $i--)
                                                    <option value="{{ $i }}" {{ old('vehicle_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @error('vehicle_year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="plate_number">{{ __('License Plate Number') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('plate_number') is-invalid @enderror" id="plate_number" name="plate_number" value="{{ old('plate_number') }}">
                                            @error('plate_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="save_vehicle" id="save_vehicle" value="1" {{ old('save_vehicle') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="save_vehicle">
                                        {{ __('Save this vehicle for future bookings') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Scheduling Information -->
                <div class="mb-4">
                    <h5>{{ __('Schedule') }}</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="booking_date">{{ __('Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('booking_date') is-invalid @enderror" id="booking_date" name="booking_date" value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}">
                                        @error('booking_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="booking_time">{{ __('Time') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('booking_time') is-invalid @enderror" id="booking_time" name="booking_time">
                                            <option value="">{{ __('-- Select Time --') }}</option>
                                            @for($hour = 8; $hour <= 17; $hour++)
                                                @foreach(['00', '30'] as $minute)
                                                    @if(!($hour == 17 && $minute == '30'))
                                                        @php 
                                                            $time = sprintf("%02d:%s", $hour, $minute);
                                                            $displayTime = date("h:i A", strtotime($time));
                                                        @endphp
                                                        <option value="{{ $time }}" {{ old('booking_time') == $time ? 'selected' : '' }}>{{ $displayTime }}</option>
                                                    @endif
                                                @endforeach
                                            @endfor
                                        </select>
                                        @error('booking_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="mb-4">
                    <h5>{{ __('Payment Method') }}</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_wallet" value="wallet" {{ old('payment_method') == 'wallet' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_wallet">
                                        {{ __('Pay with Wallet') }} 
                                        <span class="text-muted">({{ __('Current Balance: :balance', ['balance' => 'SAR ' . (auth()->user()->wallet ? number_format(auth()->user()->wallet->balance, 2) : '0.00')]) }})</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_credit_card" value="credit_card" {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_credit_card">
                                        {{ __('Pay with Credit Card') }}
                                    </label>
                                </div>
                                
                                @error('payment_method')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Price Summary -->
                <div class="mb-4">
                    <h5>{{ __('Price Summary') }}</h5>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Base Price:') }}</span>
                                <span id="basePrice">SAR 0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('VAT (15%):') }}</span>
                                <span id="vatAmount">SAR 0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>{{ __('Total:') }}</strong>
                                <strong id="totalAmount">SAR 0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('services.booking.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Submit Booking') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const serviceSelect = document.getElementById('service_id');
        const basePrice = document.getElementById('basePrice');
        const vatAmount = document.getElementById('vatAmount');
        const totalAmount = document.getElementById('totalAmount');
        
        // Initialize price if service is preselected
        updatePriceFromService();
        
        // Update price when service changes
        if (serviceSelect) {
            serviceSelect.addEventListener('change', updatePriceFromService);
        }
        
        function updatePriceFromService() {
            let price = 0;
            let vat = 0;
            
            if (serviceSelect && serviceSelect.selectedOptions.length > 0 && serviceSelect.value !== '') {
                const selectedOption = serviceSelect.selectedOptions[0];
                price = parseFloat(selectedOption.dataset.price || 0);
                vat = parseFloat(selectedOption.dataset.vat || 15) / 100;
            } else {
                // For preselected service
                const hiddenServiceInput = document.querySelector('input[name="service_id"]');
                if (hiddenServiceInput) {
                    try {
                        const serviceInfo = document.querySelector('.alert-info');
                        if (serviceInfo) {
                            const priceText = serviceInfo.innerText.match(/SAR\s([\d,.]+)/);
                            if (priceText && priceText[1]) {
                                price = parseFloat(priceText[1].replace(/,/g, ''));
                                vat = 0.15; // Default 15%
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing service price', e);
                    }
                }
            }
            
            const vatValue = price * vat;
            const totalValue = price + vatValue;
            
            basePrice.textContent = formatCurrency(price);
            vatAmount.textContent = formatCurrency(vatValue);
            totalAmount.textContent = formatCurrency(totalValue);
        }
        
        function formatCurrency(amount) {
            return 'SAR ' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
        
        // Vehicle form toggle functionality
        const useExistingYes = document.getElementById('use_existing_yes');
        const useExistingNo = document.getElementById('use_existing_no');
        const existingVehicleSection = document.getElementById('existing_vehicle_section');
        const newVehicleSection = document.getElementById('new_vehicle_section');
        const vehicleSelect = document.getElementById('vehicle_id');
        
        // Initial state
        toggleVehicleForm();
        
        // Add event listeners
        useExistingYes.addEventListener('change', toggleVehicleForm);
        useExistingNo.addEventListener('change', toggleVehicleForm);
        
        function toggleVehicleForm() {
            if (useExistingYes.checked) {
                existingVehicleSection.style.display = 'block';
                newVehicleSection.style.display = 'none';
                
                // Disable new vehicle form inputs
                enableDisableNewVehicleForm(true);
                
                // Make vehicle_id required if using existing vehicle
                if (vehicleSelect) {
                    vehicleSelect.setAttribute('required', 'required');
                }
            } else {
                existingVehicleSection.style.display = 'none';
                newVehicleSection.style.display = 'block';
                
                // Enable new vehicle form inputs
                enableDisableNewVehicleForm(false);
                
                // Remove required from vehicle_id when not using existing
                if (vehicleSelect) {
                    vehicleSelect.removeAttribute('required');
                }
            }
        }
        
        function enableDisableNewVehicleForm(disabled) {
            const inputs = newVehicleSection.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.disabled = disabled;
            });
        }
    });
</script>
@endpush
@endsection
