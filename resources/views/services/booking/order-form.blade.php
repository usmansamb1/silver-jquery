@extends('layouts.app')

@section('title', __('Order Service'))

@section('content')
<div class="container-fluid py-4">
    <div id="form-errors"></div>
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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="text-center mb-0">{{ __('Order Service Form') }}</h3>
        </div>
        <div class="card-body">
            <form id="orderServiceForm" data-action="{{ route('services.booking.order.form.json') }}">
                @csrf
                <div class="row">
                    <!-- Left Column - Add New Service -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('Add New Service') }}</h4>
                                
                                <div class="mb-4">
                                    <label class="form-label"><i class="fa fa-tag me-2"></i>{{ __('Service Type') }}</label>
                                    <div class="input-group mt-2">
                                        <select id="service_type" name="service_type" class="form-select @error('service_type') is-invalid @enderror">
                                            <option value="">{{ __('Select Service Type') }}</option>
                                            <option value="rfid_car">{{ __('RFID Chip for Cars') }}</option>
                                            <option value="rfid_truck">{{ __('RFID Chip for Trucks') }}</option> 
                                    </select>
                                    </div>
                                    <small class="text-danger service-type-error d-none">{{ __('Please select service type') }}</small>
                                </div>
                                
                                <!-- Vehicle Selection Toggle -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="use_existing_vehicle" name="use_existing_vehicle">
                                        <label class="form-check-label fw-bold" for="use_existing_vehicle">
                                            <i class="fa fa-car-alt me-1"></i> {{ __('Use existing vehicle') }}
                                        </label>
                                        <div class="form-text">{{ __('Toggle to select from your saved vehicles') }}</div>
                                    </div>
                                </div>
                                
                                <!-- Existing Vehicle Dropdown -->
                                <div id="existing_vehicle_section" class="mb-3 d-none">
                                    <label class="form-label"><i class="fa fa-car me-2"></i>{{ __('Select Vehicle') }}</label>
                                    <select id="vehicle_id" name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror">
                                        <option value="">{{ __('-- Select a vehicle --') }}</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" 
                                                data-plate="{{ $vehicle->plate_number }}"
                                                data-make="{{ $vehicle->make }}"
                                                data-manufacturer="{{ $vehicle->manufacturer }}"
                                                data-model="{{ $vehicle->model }}"
                                                data-year="{{ $vehicle->year }}">
                                                {{ $vehicle->manufacturer }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <i class="fa fa-info-circle text-primary me-1"></i> 
                                        {{ __('Vehicle details will be automatically filled based on your selection') }}
                                    </div>
                                    @error('vehicle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- New Vehicle Section -->
                                <div id="new_vehicle_section">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-car me-2"></i>{{ __('Plate No') }}</label>
                                            <div class="input-group">
                                                <input type="text" id="plate_number" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" placeholder="{{ __('Plate Number') }}">
                                            </div>
                                            <div class="form-text">{{ __('Ex: 1234 ASD, 0012 RGF') }}</div>
                                            <small class="text-danger plate-error d-none">{{ __('Please enter plate number') }}</small>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-gas-pump me-2"></i>{{ __('Fuel Type') }}</label>
                                        <div class="input-group">
                                                <select id="service_id" name="service_id" class="form-select @error('service_id') is-invalid @enderror">
                                                    <option value="">{{ __('Select Fuel Type') }}</option>
                                                    <option value="rfid_80mm">{{ __('Unleaded 91') }}</option>
                                                    <option value="rfid_120mm">{{ __('Premium 95') }}</option>
                                                    <option value="oil_change">{{ __('Diesel') }}</option>
                                                </select>
                                            </div>
                                            <small class="text-danger service-error d-none">{{ __('Please select fuel type') }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-tag me-2"></i>{{ __('Name On Card/RFID (in English)') }}</label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_make" name="vehicle_make" class="form-control @error('vehicle_make') is-invalid @enderror" placeholder="{{ __('Name (in English)') }}">
                                            </div>
                                            <small class="text-danger make-error d-none">{{ __('Please enter name') }}</small>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-car me-2"></i>{{ __('Vehicle Make') }}</label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_manufacturer" name="vehicle_manufacturer" class="form-control @error('vehicle_manufacturer') is-invalid @enderror" placeholder="{{ __('Vehicle Manufacturer') }}">
                                            </div>
                                            <div class="form-text">{{ __('Ex: Toyota, BMW, Ford') }}</div>
                                            <small class="text-danger manufacturer-error d-none">{{ __('Please enter vehicle make') }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-calendar-alt me-2"></i>{{ __('Vehicle Model') }}</label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_model" name="vehicle_model" class="form-control @error('vehicle_model') is-invalid @enderror" placeholder="{{ __('Vehicle Model') }}">
                                            </div>
                                            <div class="form-text">{{ __('Ex: Camry, X5, Focus') }}</div>
                                            <small class="text-danger model-error d-none">{{ __('Please enter model') }}</small>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-calendar-alt me-2"></i>{{ __('Vehicle Year') }}</label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_year" name="vehicle_year" class="form-control @error('vehicle_year') is-invalid @enderror" placeholder="{{ __('Vehicle Year') }}">
                                            </div>
                                            <div class="form-text">{{ __('Ex: 2021, 2022') }}</div>
                                            <small class="text-danger year-error d-none">{{ __('Please enter a valid year') }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"><i class="fa fa-money-bill me-2"></i>{{ __('Fueling Amount (in SAR)') }}</label>
                                        <div class="input-group">
                                            <input type="text" id="refule_amount" name="refule_amount" class="form-control @error('refule_amount') is-invalid @enderror" placeholder="{{ __('Amount in SAR') }}">
                                        </div>
                                        <small class="text-danger refule-error d-none">{{ __('Please enter refule amount') }}</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_vehicle" name="save_vehicle" value="1" checked>
                                        <label class="form-check-label" for="save_vehicle">
                                            {{ __('Save vehicle information for future bookings') }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-3">
                                    <button type="button" id="addServiceBtn" class="btn btn-primary px-4">
                                        <i class="fa fa-plus"></i> {{ __('Save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- List of Purchase Services -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('List of purchase Services') }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Service') }}</th>
                                                <th>{{ __('Vehicle Details') }}</th> 
                                                <th>{{ __('Ref. amount') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="servicesList">
                                            <!-- Services will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="noServicesMessage" class="text-center py-3 {{ old('services') ? 'd-none' : '' }}">
                                    <p>{{ __('No services added yet. Please add at least one service.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Delivery Details and Summary -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('Delivery Details') }}</h4>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Pickup Station') }} <span class="text-danger">*</span></label>
                                    <select id="pickup_location" name="pickup_location" class="form-select @error('pickup_location') is-invalid @enderror">
                                        <option value="">{{ __('Choose location') }}</option>
                                        <option value="station1">{{ __('Station 1') }}</option>
                                        <option value="station2">{{ __('Station 2') }}</option>
                                        <option value="station3">{{ __('Station 3') }}</option>
                                    </select>
                                    <small class="text-danger location-error d-none">{{ __('Please select a pickup location') }}</small>
                                    @error('pickup_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3">{{ __('Summary') }}</h4>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Unit price') }}</span>
                                    <span id="unitPrice"> 150.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Quantity') }}</span>
                                    <span id="quantity">0</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Topup') }}</span>
                                    <span id="topupAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span id="subtotalAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('VAT (15%)') }}</span>
                                    <span id="vatAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-4 pb-2 border-bottom fw-bold">
                                    <span>{{ __('Total (including VAT)') }}</span>
                                    <span id="totalAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_wallet" value="wallet" checked>
                                        <label class="form-check-label d-flex align-items-center" for="payment_wallet">
                                            <i class="fa fa-wallet me-2"></i> {{ __('Wallet') }}
                                            <span class="ms-2 badge bg-success"> {{ $walletBalance }} <span class="icon-saudi_riyal"></span></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_credit_card" value="credit_card">
                                        <label class="form-check-label d-flex align-items-center" for="payment_credit_card">
                                            <i class="fa fa-credit-card me-2"></i> {{ __('Credit Card') }}
                                        </label>
                                    </div>
                                    
                                    @error('payment_method')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" id="placeOrderBtn" class="btn btn-primary w-100">{{ __('Place order') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- HyperPay Widget Container - MOVED OUTSIDE THE FORM -->
            <div id="credit-card-payment-container" class="d-none mt-4 col-md-6 offset-md-6" style="margin-top: -402px!important; padding-left: 11px;">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">{{ __('Secure Payment') }}</h4>
                        
                        <!-- Card Brand Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('Select Card Type') }}</label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check card-brand-option">
                                        <input class="form-check-input" type="radio" name="card_brand" id="visa_mastercard" value="VISA MASTER" checked>
                                        <label class="form-check-label d-flex align-items-center" for="visa_mastercard">
                                            <div class="card-brand-icons me-3">
                                                <svg class="visa-icon" width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#1A1F71"/>
                                                    <path d="M16.5 8.5L14 16.5H11.5L14 8.5H16.5ZM22.5 8.5L20.5 13.5L19.5 9.5C19.2 8.5 18.5 8.5 18.5 8.5H15.5L15.6 8.8C16.2 9 16.5 9.5 16.5 9.5L18.5 16.5H21L25.5 8.5H22.5ZM28.5 8.5H26.5C26.2 8.5 26 8.7 26 9V16.5H28.5V8.5Z" fill="white"/>
                                                </svg>
                                                <svg class="mastercard-icon ms-2" width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#EB001B"/>
                                                    <rect x="20" width="20" height="25" rx="3" fill="#F79E1B"/>
                                                    <circle cx="17" cy="12.5" r="6" fill="#FF5F00"/>
                                                    <circle cx="23" cy="12.5" r="6" fill="#FF5F00"/>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text">{{ __('Visa / MasterCard') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check card-brand-option">
                                        <input class="form-check-input" type="radio" name="card_brand" id="mada_card" value="MADA">
                                        <label class="form-check-label d-flex align-items-center" for="mada_card">
                                            <div class="card-brand-icons me-3">
                                                <svg class="mada-icon" width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#0066CC"/>
                                                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12" font-weight="bold">مدى</text>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text">{{ __('MADA Card') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Test Card Info (shown in test environment) -->
                        {{-- <div id="test-card-info" class="alert alert-info mb-3" style="display: none;">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Test Card Numbers</h6>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <div class="card test-card-copy" data-card="4200000000000000" style="cursor: pointer;">
                                        <div class="card-body p-2 text-center">
                                            <svg class="visa-icon mb-2" width="50" height="32" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="40" height="25" rx="3" fill="#1A1F71"/>
                                                <path d="M16.5 8.5L14 16.5H11.5L14 8.5H16.5ZM22.5 8.5L20.5 13.5L19.5 9.5C19.2 8.5 18.5 8.5 18.5 8.5H15.5L15.6 8.8C16.2 9 16.5 9.5 16.5 9.5L18.5 16.5H21L25.5 8.5H22.5ZM28.5 8.5H26.5C26.2 8.5 26 8.7 26 9V16.5H28.5V8.5Z" fill="white"/>
                                            </svg>
                                            <div class="small fw-bold">VISA</div>
                                            <div class="font-monospace small">4200000000000000</div>
                                            <i class="fas fa-copy text-muted small"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card test-card-copy" data-card="5200000000000000" style="cursor: pointer;">
                                        <div class="card-body p-2 text-center">
                                            <svg class="mastercard-icon mb-2" width="50" height="32" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="40" height="25" rx="3" fill="#EB001B"/>
                                                <rect x="20" width="20" height="25" rx="3" fill="#F79E1B"/>
                                                <circle cx="17" cy="12.5" r="6" fill="#FF5F00"/>
                                                <circle cx="23" cy="12.5" r="6" fill="#FF5F00"/>
                                            </svg>
                                            <div class="small fw-bold">MASTERCARD</div>
                                            <div class="font-monospace small">5200000000000000</div>
                                            <i class="fas fa-copy text-muted small"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card test-card-copy" data-card="4464040000000007" style="cursor: pointer;">
                                        <div class="card-body p-2 text-center">
                                            <svg class="mada-icon mb-2" width="50" height="32" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect width="40" height="25" rx="3" fill="#0066CC"/>
                                                <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12" font-weight="bold">مدى</text>
                                            </svg>
                                            <div class="small fw-bold">MADA</div>
                                            <div class="font-monospace small">4464040000000007</div>
                                            <i class="fas fa-copy text-muted small"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>CVV:</strong> Any 3 digits | <strong>Expiry:</strong> Any future date
                            </small>
                        </div> --}}
                        
                        <!-- HyperPay Widget Container -->
                        <div id="hyperpay-widget" style="min-height: 300px;">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading secure payment form...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- HyperPay will be loaded dynamically -->
<script>
    // Pass service prices to JavaScript
    window.servicePrices = @json($servicePrices ?? []);
</script>
<script src="{{ asset('js/order-form.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the form - key configuration will be picked up by the module
        
        // Add payment method radio button toggle for credit card form
        const paymentWallet = document.getElementById('payment_wallet');
        const paymentCreditCard = document.getElementById('payment_credit_card');
        const creditCardPaymentContainer = document.getElementById('credit-card-payment-container');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        
        function toggleCreditCardForm() {
            if (paymentCreditCard && paymentCreditCard.checked) {
                if (creditCardPaymentContainer) {
                    creditCardPaymentContainer.classList.remove('d-none');
                }
                // Hide place order button when credit card is selected - HyperPay will provide its own Pay Now button
                if (placeOrderBtn) {
                    placeOrderBtn.style.display = 'none';
                }
                
                // CRITICAL FIX: Don't load widget here - let order-form.js handle it
                // This prevents multiple loading and flickering
                // setTimeout(() => {
                //     // Only trigger widget loading if we have services added
                //     const serviceItems = document.querySelectorAll('.service-item');
                //     if (serviceItems.length > 0 && typeof window.loadHyperPayWidget === 'function') {
                //         console.log('🔄 Triggering HyperPay widget loading after payment method change...');
                //         window.loadHyperPayWidget();
                //     }
                // }, 100);

            } else {
                if (creditCardPaymentContainer) {
                    creditCardPaymentContainer.classList.add('d-none');
                }
                // Show place order button when wallet is selected
                if (placeOrderBtn) {
                    placeOrderBtn.style.display = 'block';
                    placeOrderBtn.innerHTML = '<i class="fa fa-wallet me-2"></i>Place Order (Wallet Payment)';
                }
                
                // Clear any existing HyperPay widget when switching to wallet
                const hyperpayWidget = document.getElementById('hyperpay-widget');
                if (hyperpayWidget) {
                    hyperpayWidget.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fa fa-wallet fa-2x text-muted"></i>
                            <p class="mt-2 text-muted">Wallet payment selected - click "Place Order" button below</p>
                        </div>
                    `;
                }
            }
        }
        
        if (paymentWallet) {
            paymentWallet.addEventListener('change', toggleCreditCardForm);
        }
        if (paymentCreditCard) {
            paymentCreditCard.addEventListener('change', toggleCreditCardForm);
        }
        
        // Initial toggle based on saved state
        toggleCreditCardForm();
        
        // Card brand selection enhancement
        document.querySelectorAll('.card-brand-option').forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            const label = option.querySelector('.form-check-label');
            
            // Make entire card clickable
            option.addEventListener('click', function(e) {
                if (e.target !== radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            
            // Update visual state when radio changes
            radio.addEventListener('change', function() {
                // Remove active class from all options
                document.querySelectorAll('.card-brand-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                
                // Add active class to selected option
                if (this.checked) {
                    option.classList.add('active');
                }
            });
        });
        
        // Show test card info in test environment
        @if(config('services.hyperpay.mode') === 'test' || str_contains(config('services.hyperpay.base_url'), 'test') || config('app.env') === 'local')
            const testCardInfo = document.getElementById('test-card-info');
            if (testCardInfo) {
                testCardInfo.style.display = 'block';
            }
        @endif
        
        // Add copy functionality for test cards
        document.addEventListener('click', function(e) {
            if (e.target.closest('.test-card-copy')) {
                const cardElement = e.target.closest('.test-card-copy');
                const cardNumber = cardElement.dataset.card;
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(cardNumber).then(() => {
                        // Show success toast
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-white bg-success border-0';
                        toast.style.position = 'fixed';
                        toast.style.top = '20px';
                        toast.style.right = '20px';
                        toast.style.zIndex = '9999';
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="fas fa-check me-2"></i>
                                    Card number copied: ${cardNumber}
                                </div>
                            </div>
                        `;
                        
                        document.body.appendChild(toast);
                        
                        // Auto-remove toast after 3 seconds
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.remove();
                            }
                        }, 3000);
                    });
                } else {
                    // Fallback for browsers without clipboard API
                    const textArea = document.createElement('textarea');
                    textArea.value = cardNumber;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    // Show success message
                    alert('Card number copied: ' + cardNumber);
                }
            }
        });
        
        // Note: HyperPay initialization is handled by order-form.js
    });
</script>

<style>
/* HyperPay Integration Styles */
#hyperpay-widget {
    min-height: 300px;
}

/* Override HyperPay default styles if needed */
.wpwl-form {
    color: #363d47;
}

.wpwl-button {
    background: #0061f2;
    border: none;
    padding: 10px 30px;
    font-size: 16px;
}

.wpwl-button:hover {
    background: #0051d2;
}

/* Card Brand Selection Styles */
.card-brand-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #fff;
}

.card-brand-option:hover {
    border-color: #0061f2;
    box-shadow: 0 2px 8px rgba(0, 97, 242, 0.15);
    transform: translateY(-1px);
}

.card-brand-option .form-check-input:checked + .form-check-label {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option .form-check-input:checked ~ .card-brand-option {
    border-color: #0061f2;
    background: rgba(0, 97, 242, 0.05);
}

.card-brand-option input[type="radio"]:checked + .form-check-label {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option input[type="radio"]:checked ~ .card-brand-option {
    border-color: #0061f2;
    background: rgba(0, 97, 242, 0.05);
}

/* Card Brand Icons */
.card-brand-icons {
    display: flex;
    align-items: center;
    gap: 8px;
}

.visa-icon, .mastercard-icon, .mada-icon {
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card-brand-option:hover .visa-icon,
.card-brand-option:hover .mastercard-icon,
.card-brand-option:hover .mada-icon {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.card-brand-text {
    font-size: 14px;
    font-weight: 500;
    color: #363d47;
    transition: color 0.3s ease;
}

.card-brand-option:hover .card-brand-text {
    color: #0061f2;
}

/* Selected state styling */
.card-brand-option input[type="radio"]:checked + .form-check-label .card-brand-text {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option input[type="radio"]:checked + .form-check-label .card-brand-icons svg {
    filter: drop-shadow(0 2px 4px rgba(0, 97, 242, 0.3));
}

/* Active state styling */
.card-brand-option.active {
    border-color: #0061f2;
    background: rgba(0, 97, 242, 0.05);
    box-shadow: 0 2px 8px rgba(0, 97, 242, 0.15);
}

.card-brand-option.active .card-brand-text {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option.active .card-brand-icons svg {
    filter: drop-shadow(0 2px 4px rgba(0, 97, 242, 0.3));
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-brand-option {
        padding: 12px;
    }
    
    .card-brand-icons {
        gap: 6px;
    }
    
    .visa-icon, .mastercard-icon, .mada-icon {
        width: 35px;
        height: 22px;
    }
    
    .card-brand-text {
        font-size: 13px;
    }
}

/* Test Card Copy Styles */
.test-card-copy {
    cursor: pointer;
    transition: all 0.2s ease;
}

.test-card-copy:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.test-card-copy:hover .fas.fa-copy {
    color: #007bff !important;
}

.test-card-copy .card-body {
    position: relative;
}

.test-card-copy .fas.fa-copy {
    font-size: 12px;
    opacity: 0.7;
    transition: all 0.2s ease;
}

/* Toast Animation */
.toast {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endpush
 