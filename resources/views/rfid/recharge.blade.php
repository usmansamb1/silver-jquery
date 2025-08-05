@extends('layouts.app')

@section('title', __('Recharge RFID'))

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-wifi me-2"></i>{{ __('Recharge RFID') }}</h3>
                    <a href="{{ route('rfid.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to RFID Management') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($vehicles->isEmpty())
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>{{ __('No vehicles with RFID available') }}</h5>
                            <p>{{ __('You don\'t have any vehicles with RFID chips. You can get an RFID chip by booking a service.') }}</p>
                            <a href="{{ route('services.booking.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i>{{ __('Book a Service') }}
                            </a>
                        </div>
                    @else
                        <form action="{{ route('rfid.process-recharge') }}" method="POST" id="rechargeForm">
                            @csrf
                            
                            <!-- Vehicle Selection -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header bg-transparent border-0 pb-0">
                                            <h5 class="mb-3"><i class="fas fa-car me-2"></i>{{ __('Select Vehicles to Recharge') }}</h5>
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary me-2" id="selectAllVehicles">
                                                    <i class="fas fa-check-square me-1"></i>{{ __('Select All') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllVehicles">
                                                    <i class="far fa-square me-1"></i>{{ __('Deselect All') }}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="row g-3">
                                                @foreach($vehicles as $vehicle)
                                                    <div class="col-md-6">
                                                        <div class="card h-100 vehicle-card" data-vehicle-id="{{ $vehicle->id }}">
                                                            <div class="card-body p-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input vehicle-checkbox" type="checkbox" 
                                                                           name="vehicles[]" value="{{ $vehicle->id }}" 
                                                                           id="vehicle_{{ $vehicle->id }}" 
                                                                           {{ (request()->has('vehicle') && request()->vehicle == $vehicle->id) || (old('vehicles') && in_array($vehicle->id, old('vehicles'))) ? 'checked' : '' }}>
                                                                    <label class="form-check-label w-100" for="vehicle_{{ $vehicle->id }}">
                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                            <div>
                                                                                <h6 class="mb-1 text-primary">{{ $vehicle->manufacturer }} {{ $vehicle->make }}</h6>
                                                                                <p class="mb-1 text-muted small">{{ $vehicle->model }} • {{ $vehicle->plate_number }}</p>
                                                                                <div class="d-flex align-items-center">
                                                                                    <span class="badge bg-success me-2">{{ $vehicle->formatted_rfid_balance }}</span>
                                                                                    <small class="text-muted">{{ __('Current Balance') }}</small>
                                                                                </div>
                                                                            </div>
                                                                            <i class="fas fa-wifi text-primary"></i>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            @error('vehicles')
                                                <div class="text-danger mt-3"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Amount Input -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <label for="amount" class="form-label fw-bold">
                                                <i class="fas fa-money-bill-wave me-2"></i>{{ __('Recharge Amount (SAR)') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text">SAR</span>
                                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                                       id="amount" name="amount" value="{{ old('amount', '100') }}" 
                                                       min="1" step="1" required>
                                            </div>
                                            <div class="form-text">{{ __('Enter the amount to add to each selected vehicle.') }}</div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card border-0 bg-primary text-white">
                                        <div class="card-body">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-calculator me-2"></i>{{ __('Total Amount') }}
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-white text-primary border-0">SAR</span>
                                                <input type="text" class="form-control bg-white border-0 fw-bold text-primary" 
                                                       id="totalAmount" readonly value="0.00">
                                            </div>
                                            <div class="form-text text-white-50">{{ __('This is the total amount that will be charged.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>{{ __('Payment Method') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card payment-option" data-payment="wallet">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="payment_method" 
                                                               id="payment_wallet" value="wallet" 
                                                               {{ old('payment_method', 'wallet') == 'wallet' ? 'checked' : '' }}>
                                                        <label class="form-check-label w-100" for="payment_wallet">
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-wallet fa-2x text-success me-3"></i>
                                                                <div>
                                                                    <h6 class="mb-1">{{ __('Pay with Wallet') }}</h6>
                                                                    <small class="text-muted">{{ __('Current Balance: SAR') }} {{ number_format($walletBalance, 2) }}</small>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                                                <div class="col-md-6">
                            <div class="card payment-option" data-payment="credit_card">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_credit_card" value="credit_card" 
                                               {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="payment_credit_card">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                                                <div>
                                                    <h6 class="mb-1">{{ __('Pay with Credit Card') }}</h6>
                                                    <small class="text-muted">{{ __('VISA, MasterCard, MADA, AMEX, STC Pay, URPay') }}</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    </div>
                                    
                                    
                                    @error('payment_method')
                                        <div class="text-danger mt-3"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg px-4" id="rechargeButton">
                                    <i class="fas fa-bolt me-2"></i>{{ __('Recharge RFID') }}
                                </button>
                                <a href="{{ route('rfid.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar with Information Cards -->
        <div class="col-lg-4">
            <!-- Wallet Balance Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>{{ __('Wallet Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('Current Balance') }}</h6>
                            <h3 class="text-success mb-0">SAR {{ number_format($walletBalance, 2) }}</h3>
                        </div>
                        <i class="fas fa-coins fa-3x text-success opacity-25"></i>
                    </div>
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('wallet.topup') }}" class="btn btn-outline-success">
                            <i class="fas fa-plus me-2"></i>{{ __('Top Up Wallet') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- RFID Information Card -->
            {{-- <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-wifi me-2"></i>{{ __('RFID Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted">{{ __('Total Vehicles') }}</span>
                            <span class="badge bg-info fs-6">{{ $vehicles->count() }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted">{{ __('Total RFID Balance') }}</span>
                            <span class="fw-bold">SAR {{ number_format($vehicles->sum('rfid_balance'), 2) }}</span>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-muted mb-3">{{ __('How RFID Recharge Works:') }}</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Select vehicles to recharge') }}</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Same amount applies to each vehicle') }}</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Choose wallet or credit card payment') }}</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ __('Instant balance update') }}</li>
                    </ul>
                </div>
            </div>
             --}}
            @if(config('app.env') !== 'production')
            <!-- Test Mode Information -->
            {{-- <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-flask me-2"></i>{{ __('Test Mode') }}</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">{{ __('You\'re in test mode. Use these test cards:') }}</p>
                    <div class="small">
                        <div class="mb-1"><strong>VISA:</strong> 4111 1111 1111 1111</div>
                        <div class="mb-1"><strong>MasterCard:</strong> 5555 5555 5555 4444</div>
                        <div class="mb-1"><strong>MADA:</strong> 5888 5888 5888 5888</div>
                        <div class="text-muted">{{ __('CVV: Any 3 digits, Expiry: Any future date') }}</div>
                    </div>
                </div>
            </div> --}}
            @endif
            
            <!-- HyperPay Form Container -->
            <div id="hyperpayFormContainer" class="mt-4" style="display: none;">
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-lock me-2"></i>{{ __('Secure Payment') }}</h6>
                    </div>
                    <div class="card-body">
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('Select Payment Method') }}</label>
                            <small class="text-muted d-block mb-3">{{ __('All payment methods use secure HyperPay processing') }}</small>
                            <div class="row g-2">
                                <!-- Visa / MasterCard -->
                                <div class="col-md-6 mb-2">
                                    <div class="form-check card-brand-option p-2">
                                        <input class="form-check-input" type="radio" name="card_brand" id="visa_mastercard" value="credit_card" checked>
                                        <label class="form-check-label d-flex align-items-center small" for="visa_mastercard">
                                            <div class="card-brand-icons me-2">
                                                <svg class="visa-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#1A1F71"/>
                                                    <path d="M16.5 8.5L14 16.5H11.5L14 8.5H16.5ZM22.5 8.5L20.5 13.5L19.5 9.5C19.2 8.5 18.5 8.5 18.5 8.5H15.5L15.6 8.8C16.2 9 16.5 9.5 16.5 9.5L18.5 16.5H21L25.5 8.5H22.5ZM28.5 8.5H26.5C26.2 8.5 26 8.7 26 9V16.5H28.5V8.5Z" fill="white"/>
                                                </svg>
                                                <svg class="mastercard-icon ms-1" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                
                                <!-- MADA Card -->
                                <div class="col-md-6 mb-2">
                                    <div class="form-check card-brand-option p-2">
                                        <input class="form-check-input" type="radio" name="card_brand" id="mada_card" value="mada_card">
                                        <label class="form-check-label d-flex align-items-center small" for="mada_card">
                                            <div class="card-brand-icons me-2">
                                                <svg class="mada-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#0066CC"/>
                                                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12" font-weight="bold">مدى</text>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text">{{ __('MADA Card') }}</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- American Express -->
                                <div class="col-md-6 mb-2">
                                    <div class="form-check card-brand-option p-2">
                                        <input class="form-check-input" type="radio" name="card_brand" id="amex_card" value="AMEX">
                                        <label class="form-check-label d-flex align-items-center small" for="amex_card">
                                            <div class="card-brand-icons me-2">
                                                <svg class="amex-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#006FCF"/>
                                                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="8" font-weight="bold">AMEX</text>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text">{{ __('American Express') }}</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- STC Pay -->
                                <div class="col-md-6 mb-2">
                                    <div class="form-check card-brand-option p-2">
                                        <input class="form-check-input" type="radio" name="card_brand" id="stc_pay" value="STC_PAY">
                                        <label class="form-check-label d-flex align-items-center small" for="stc_pay">
                                            <div class="card-brand-icons me-2">
                                                <svg class="stc-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#662D91"/>
                                                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="7" font-weight="bold">STC PAY</text>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text">{{ __('STC Pay') }}</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- URPay -->
                                <div class="col-md-12 mb-2">
                                    <div class="form-check card-brand-option p-2">
                                        <input class="form-check-input" type="radio" name="card_brand" id="ur_pay" value="URPAY">
                                        <label class="form-check-label d-flex align-items-center small" for="ur_pay">
                                            <div class="card-brand-icons me-2">
                                                <svg class="urpay-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#22418b"/>
                                                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="8" font-weight="bold">URPAY</text>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text">{{ __('URPay') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Payment Method Display -->
                        <div id="selected-payment-method" class="alert alert-success mb-3" style="display: none;">
                            <div class="d-flex align-items-center">
                                <div id="selected-method-icon" class="me-3">
                                    <!-- Icon will be inserted here -->
                                </div>
                                <div>
                                    <strong>{{ __('Selected Method') }}: <span id="selected-method-name">Visa/MasterCard</span></strong>
                                    <br>
                                    <small class="text-muted">{{ __('Payment form will load below when ready') }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- HyperPay Widget Container -->
                        <div id="hyperpayFormBody">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">{{ __('Loading payment form...') }}</span>
                                </div>
                                <p class="mt-2 text-muted">{{ __('Loading secure payment form...') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Card Brand Selection Styles */
.card-brand-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #fff;
}

.card-brand-option:hover {
    border-color: #0061f2;
    box-shadow: 0 2px 8px rgba(0, 97, 242, 0.15);
    transform: translateY(-1px);
}

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

/* Card Brand Icons */
.card-brand-icons {
    display: flex;
    align-items: center;
    gap: 4px;
}

.visa-icon, .mastercard-icon, .mada-icon, .amex-icon, .stc-icon, .urpay-icon {
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card-brand-option:hover .visa-icon,
.card-brand-option:hover .mastercard-icon,
.card-brand-option:hover .mada-icon,
.card-brand-option:hover .amex-icon,
.card-brand-option:hover .stc-icon,
.card-brand-option:hover .urpay-icon {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.card-brand-text {
    font-size: 12px;
    font-weight: 500;
    color: #363d47;
    transition: color 0.3s ease;
}

.card-brand-option:hover .card-brand-text {
    color: #0061f2;
}

/* Selected Payment Method Display */
#selected-payment-method {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-left: 4px solid #0061f2;
    border-radius: 8px;
    margin-bottom: 1rem;
    animation: fadeInDown 0.3s ease-out;
}

#selected-payment-method .d-flex {
    align-items: center;
}

#selected-method-icon {
    min-width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#selected-method-icon svg {
    margin: 0 1px;
}

#selected-method-name {
    color: #0061f2;
    font-weight: 600;
    font-size: 1rem;
}

@keyframes fadeInDown {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-brand-option {
        padding: 8px !important;
    }
    
    .card-brand-icons {
        gap: 2px;
    }
    
    .visa-icon, .mastercard-icon, .mada-icon, .amex-icon, .stc-icon, .urpay-icon {
        width: 25px;
        height: 16px;
    }
    
    .card-brand-text {
        font-size: 11px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Configure Axios defaults
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    document.addEventListener('DOMContentLoaded', function() {
        // Get references to form elements
        const form = document.getElementById('rechargeForm');
        const amountInput = document.getElementById('amount');
        const totalAmountField = document.getElementById('totalAmount');
        const vehicleCheckboxes = document.querySelectorAll('.vehicle-checkbox');
        const paymentWallet = document.getElementById('payment_wallet');
        const paymentCreditCard = document.getElementById('payment_credit_card');
        const hyperpayFormContainer = document.getElementById('hyperpayFormContainer');
        const hyperpayFormBody = document.getElementById('hyperpayFormBody');
        const rechargeButton = document.getElementById('rechargeButton');
        const selectAllBtn = document.getElementById('selectAllVehicles');
        const deselectAllBtn = document.getElementById('deselectAllVehicles');
        
        let currentCheckoutId = null;
        let hyperpayFormLoaded = false;
        let isLoadingForm = false;
        let loadingTimeout = null;
        
        // Payment method display mapping (moved to top to avoid hoisting issues)
        const paymentMethodMapping = {
            'credit_card': {
                name: 'Visa / MasterCard',
                icon: `<svg class="visa-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#1A1F71"/>
                    <path d="M16.5 8.5L14 16.5H11.5L14 8.5H16.5ZM22.5 8.5L20.5 13.5L19.5 9.5C19.2 8.5 18.5 8.5 18.5 8.5H15.5L15.6 8.8C16.2 9 16.5 9.5 16.5 9.5L18.5 16.5H21L25.5 8.5H22.5ZM28.5 8.5H26.5C26.2 8.5 26 8.7 26 9V16.5H28.5V8.5Z" fill="white"/>
                </svg>
                <svg class="mastercard-icon ms-1" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#EB001B"/>
                    <rect x="20" width="20" height="25" rx="3" fill="#F79E1B"/>
                    <circle cx="17" cy="12.5" r="6" fill="#FF5F00"/>
                    <circle cx="23" cy="12.5" r="6" fill="#FF5F00"/>
                </svg>`,
                instruction: 'Enter your Visa or MasterCard details below'
            },
            'mada_card': {
                name: 'MADA Card',
                icon: `<svg class="mada-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#0066CC"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12" font-weight="bold">مدى</text>
                </svg>`,
                instruction: 'Enter your MADA card details below'
            },
            'AMEX': {
                name: 'American Express',
                icon: `<svg class="amex-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#006FCF"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="8" font-weight="bold">AMEX</text>
                </svg>`,
                instruction: 'Enter your American Express card details below'
            },
            'STC_PAY': {
                name: 'STC Pay',
                icon: `<svg class="stc-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#662D91"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="7" font-weight="bold">STC PAY</text>
                </svg>`,
                instruction: 'Enter your card details to process via STC Pay'
            },
            'URPAY': {
                name: 'URPay',
                icon: `<svg class="urpay-icon" width="30" height="19" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#00A651"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="8" font-weight="bold">URPAY</text>
                </svg>`,
                instruction: 'Enter your card details to process via URPay'
            }
        };

        // Helper function to get display name for payment method
        function getDisplayName(brand) {
            return paymentMethodMapping[brand]?.name || 'Credit Card';
        }

        // Function to update payment method display
        function updatePaymentMethodDisplay(selectedValue) {
            const selectedMethodDiv = document.getElementById('selected-payment-method');
            const selectedMethodIcon = document.getElementById('selected-method-icon');
            const selectedMethodName = document.getElementById('selected-method-name');
            const instructionText = selectedMethodDiv.querySelector('.text-muted');
            
            if (selectedMethodDiv && selectedMethodIcon && selectedMethodName) {
                const methodInfo = paymentMethodMapping[selectedValue] || paymentMethodMapping['credit_card'];
                
                selectedMethodIcon.innerHTML = methodInfo.icon;
                selectedMethodName.textContent = methodInfo.name;
                instructionText.textContent = methodInfo.instruction;
                selectedMethodDiv.style.display = 'block';
            }
        }
        
        // Retry loading HyperPay form with state reset
        function retryHyperpayLoad() {
            if (paymentCreditCard.checked) {
                // Reset all states before retry
                isLoadingForm = false;
                hyperpayFormLoaded = false;
                currentCheckoutId = null;
                loadHyperpayForm();
            }
        }
        
        // Select/Deselect All functionality
        selectAllBtn.addEventListener('click', function() {
            vehicleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
                updateVehicleCardHighlight(checkbox);
            });
            updateTotalAmount();
        });
        
        deselectAllBtn.addEventListener('click', function() {
            vehicleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                updateVehicleCardHighlight(checkbox);
            });
            updateTotalAmount();
        });
        
        // Vehicle selection highlights
        vehicleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateVehicleCardHighlight(this);
                updateTotalAmount();
            });
            // Initialize highlight
            updateVehicleCardHighlight(checkbox);
        });
        
        // Payment method highlights
        document.querySelectorAll('.payment-option').forEach(card => {
            card.addEventListener('click', function() {
                const paymentType = this.dataset.payment;
                if (paymentType === 'wallet') {
                    paymentWallet.checked = true;
                } else {
                    paymentCreditCard.checked = true;
                }
                updatePaymentMethodHighlight();
                toggleHyperpayForm();
            });
        });
        
        // Update total amount when amount changes
        amountInput.addEventListener('input', updateTotalAmount);
        
        // Payment method change handlers
        paymentWallet.addEventListener('change', function() {
            updatePaymentMethodHighlight();
            toggleHyperpayForm();
        });
        
        paymentCreditCard.addEventListener('change', function() {
            updatePaymentMethodHighlight();
            toggleHyperpayForm();
        });
        
        // Form submission handler
        form.addEventListener('submit', function(event) {
            if (paymentCreditCard.checked) {
                event.preventDefault();
                handleCreditCardPayment();
            } else {
                // Wallet payment - validate selection
                const selectedCount = getSelectedVehiclesCount();
                if (selectedCount === 0) {
                    event.preventDefault();
                    alert('{{ __('Please select at least one vehicle to recharge.') }}');
                    return false;
                }
            }
        });
        
        // Initialize on page load
        updateTotalAmount();
        updatePaymentMethodHighlight();
        toggleHyperpayForm();
        
        // Initialize payment method selection handlers
        initializePaymentMethodSelection();
        
        // Helper function to update vehicle card highlight
        function updateVehicleCardHighlight(checkbox) {
            const card = checkbox.closest('.vehicle-card');
            if (checkbox.checked) {
                card.classList.add('border-primary', 'bg-primary-subtle');
            } else {
                card.classList.remove('border-primary', 'bg-primary-subtle');
            }
        }
        
        // Helper function to update payment method highlight
        function updatePaymentMethodHighlight() {
            document.querySelectorAll('.payment-option').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary-subtle');
            });
            
            if (paymentWallet.checked) {
                document.querySelector('[data-payment="wallet"]').classList.add('border-success', 'bg-success-subtle');
            } else if (paymentCreditCard.checked) {
                document.querySelector('[data-payment="credit_card"]').classList.add('border-primary', 'bg-primary-subtle');
            }
        }
        
        // Helper function to update total amount
        function updateTotalAmount() {
            const amount = parseFloat(amountInput.value) || 0;
            const selectedVehiclesCount = getSelectedVehiclesCount();
            const total = amount * selectedVehiclesCount;
            
            totalAmountField.value = total.toFixed(2);
        }
        
        // Helper function to get count of selected vehicles
        function getSelectedVehiclesCount() {
            let count = 0;
            vehicleCheckboxes.forEach(checkbox => {
                if (checkbox.checked) count++;
            });
            return count;
        }
        
        // Toggle HyperPay form visibility and Recharge button
        function toggleHyperpayForm() {
            if (paymentCreditCard.checked) {
                // Credit card selected: show HyperPay form, hide Recharge button
                hyperpayFormContainer.style.display = 'block';
                rechargeButton.style.display = 'none';
                if (!hyperpayFormLoaded) {
                    loadHyperpayForm();
                }
            } else {
                // Wallet selected: hide HyperPay form, show Recharge button
                hyperpayFormContainer.style.display = 'none';
                rechargeButton.style.display = 'inline-block';
            }
        }
        
        // Load HyperPay form via AJAX with anti-flickering protection
        function loadHyperpayForm() {
            // Prevent multiple simultaneous loads
            if (isLoadingForm) {
                console.log('⚠️ Form already loading, skipping duplicate request');
                return;
            }
            
            const selectedVehicles = Array.from(vehicleCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selectedVehicles.length === 0) {
                hyperpayFormBody.innerHTML = '<div class="text-warning text-center"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Please select at least one vehicle first.') }}</div>';
                return;
            }
            
            const amount = parseFloat(amountInput.value) || 0;
            if (amount < 1) {
                hyperpayFormBody.innerHTML = '<div class="text-warning text-center"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Please enter a valid amount.') }}</div>';
                return;
            }
            
            // Get selected payment brand
            const selectedBrand = document.querySelector('input[name="card_brand"]:checked')?.value || 'credit_card';
            
            // Set loading state to prevent duplicates
            isLoadingForm = true;
            console.log('🔄 Loading RFID HyperPay form for amount:', amount, 'brand:', selectedBrand);
            
            // Clear any existing timeout
            if (loadingTimeout) {
                clearTimeout(loadingTimeout);
                loadingTimeout = null;
            }
            
            // Show loading state with smooth transition
            $(hyperpayFormBody).fadeOut(200, function() {
                hyperpayFormBody.innerHTML = `
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                        <h6>{{ __('Preparing Payment') }}</h6>
                        <p class="text-muted mb-0">{{ __('Amount:') }} <strong>${amount} {{ __('SAR') }}</strong></p>
                        <p class="text-muted mb-0">{{ __('Method:') }} <strong>${getDisplayName(selectedBrand)}</strong></p>
                        <small class="text-muted">{{ __('Please wait...') }}</small>
                    </div>
                `;
                $(hyperpayFormBody).fadeIn(200);
            });
            
            // AJAX request to get HyperPay form
            $.ajax({
                url: '{{ route("rfid.hyperpay.get-form") }}',
                method: 'POST',
                data: {
                    vehicles: selectedVehicles,
                    amount: amount,
                    payment_method: selectedBrand,
                    brand: selectedBrand,
                    _token: '{{ csrf_token() }}'
                },
                timeout: 15000,
                success: function(response) {
                    console.log('📡 RFID HyperPay response received:', response);
                    if (response.success && response.form && response.checkout_id) {
                        // Smooth transition to form
                        $(hyperpayFormBody).fadeOut(200, function() {
                            hyperpayFormBody.innerHTML = response.form;
                            currentCheckoutId = response.checkout_id;
                            hyperpayFormLoaded = true;
                            
                            $(hyperpayFormBody).fadeIn(200, function() {
                                // Load HyperPay script after fade in completes
                                loadHyperpayScript(response.checkout_id);
                            });
                        });
                        
                        console.log('✅ RFID payment form loaded, CheckoutID:', response.checkout_id);
                    } else {
                        console.error('❌ RFID payment form failed:', response);
                        showHyperpayError(response.error || 'Failed to initialize payment');
                    }
                },
                error: function(xhr, status, errorThrown) {
                    console.error('❌ Failed to initialize RFID payment:', status, errorThrown);
                    let errorMessage = 'Payment initialization failed';
                    
                    if (status === 'timeout') {
                        errorMessage = 'Connection timeout. Please try again.';
                    } else if (xhr.responseJSON?.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    
                    showHyperpayError(errorMessage);
                },
                complete: function() {
                    // Reset loading state after request completes
                    isLoadingForm = false;
                }
            });
        }
        
        // Load HyperPay widget script with duplicate prevention
        function loadHyperpayScript(checkoutId) {
            // Check if script is already loading/loaded for this checkout ID
            const existingScript = document.querySelector(`script[src*="${checkoutId}"]`);
            if (existingScript) {
                console.log('⚠️ Script already exists for checkout ID:', checkoutId);
                return;
            }
            
            // Remove any old HyperPay scripts to prevent conflicts
            $('script[src*="paymentWidgets.js"]').remove();
            console.log('⏳ Loading new HyperPay script for RFID checkout ID:', checkoutId);

            const script = document.createElement('script');
            script.src = `https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=${checkoutId}`;
            script.async = true;
            script.setAttribute('data-checkout-id', checkoutId);
            
            script.onload = function() {
                console.log('✅ HyperPay script loaded successfully');
                // Add visual indicator that script is loaded
                hyperpayFormBody.parentElement.classList.add('script-loaded');
            };
            
            script.onerror = function() {
                console.error('❌ Failed to load HyperPay script');
                showHyperpayError('Failed to load payment form. Please try again.');
            };
            
            document.head.appendChild(script);

            // Check form state after script load with timeout
            setTimeout(() => {
                const form = document.querySelector('form.paymentWidgets');
                if (form) {
                    console.log('🔍 Found RFID payment form after script load. HyperPay should now take over.', form);
                } else {
                    console.error('❌ Could not find RFID payment form after script load.');
                }
            }, 1000);
        }
        
        // Show HyperPay error with smooth transition
        function showHyperpayError(message) {
            isLoadingForm = false; // Reset loading state
            $(hyperpayFormBody).fadeOut(200, function() {
                hyperpayFormBody.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <h6>{{ __('Payment Form Error') }}</h6>
                        <p class="mb-2">${message}</p>
                        <button class="btn btn-sm btn-outline-primary" onclick="retryHyperpayLoad()">
                            <i class="fas fa-redo me-1"></i>{{ __('Retry') }}
                        </button>
                    </div>
                `;
                $(hyperpayFormBody).fadeIn(200);
            });
        }
        
        // Show HyperPay error with smooth transition
        function showHyperpayError(message) {
            isLoadingForm = false; // Reset loading state
            $(hyperpayFormBody).fadeOut(200, function() {
                hyperpayFormBody.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <h6>{{ __('Payment Form Error') }}</h6>
                        <p class="mb-2">${message}</p>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.retryHyperpayLoad()">
                            <i class="fas fa-redo me-1"></i>{{ __('Retry') }}
                        </button>
                    </div>
                `;
                $(hyperpayFormBody).fadeIn(200);
            });
        }
        
        // Retry loading HyperPay form with state reset
        function retryHyperpayLoad() {
            if (paymentCreditCard.checked) {
                // Reset all states before retry
                isLoadingForm = false;
                hyperpayFormLoaded = false;
                currentCheckoutId = null;
                loadHyperpayForm();
            }
        }
        
        // Handle credit card payment submission
        function handleCreditCardPayment() {
            const selectedCount = getSelectedVehiclesCount();
            if (selectedCount === 0) {
                alert('{{ __('Please select at least one vehicle to recharge.') }}');
                return false;
            }
            
            const amount = parseFloat(amountInput.value) || 0;
            if (amount < 1) {
                alert('{{ __('Please enter a valid amount.') }}');
                return false;
            }
            
            if (!hyperpayFormLoaded || !currentCheckoutId) {
                alert('{{ __('Payment form not loaded. Please wait and try again.') }}');
                return false;
            }
            
            // Note: The HyperPay widget handles its own form submission
            // This function is called when user tries to submit the main form
            // but credit card is selected, so we prevent default submission
            console.log('Credit card payment will be handled by HyperPay widget');
            return false;
        }
        
        // Reset form when vehicles or amount changes with debouncing
        vehicleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (paymentCreditCard.checked && hyperpayFormLoaded && !isLoadingForm) {
                    hyperpayFormLoaded = false;
                    currentCheckoutId = null;
                    // Use timeout to debounce multiple rapid changes
                    if (loadingTimeout) {
                        clearTimeout(loadingTimeout);
                    }
                    loadingTimeout = setTimeout(() => {
                        toggleHyperpayForm();
                    }, 500);
                }
            });
        });
        
        amountInput.addEventListener('input', function() {
            if (paymentCreditCard.checked && hyperpayFormLoaded && !isLoadingForm) {
                hyperpayFormLoaded = false;
                currentCheckoutId = null;
                // Use timeout to debounce rapid typing
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                }
                loadingTimeout = setTimeout(() => {
                    toggleHyperpayForm();
                }, 1000); // Longer delay for typing
            }
        });
        
        // Initialize payment method selection handlers
        function initializePaymentMethodSelection() {
            const cardBrandOptions = document.querySelectorAll('.card-brand-option');
            
            cardBrandOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                
                // Make entire card clickable
                option.addEventListener('click', function(e) {
                    if (e.target !== radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                
                // Update visual state and reload widget when radio changes
                radio.addEventListener('change', function() {
                    // Remove active class from all options
                    cardBrandOptions.forEach(opt => {
                        opt.classList.remove('active');
                    });
                    
                    // Add active class to selected option
                    if (this.checked) {
                        option.classList.add('active');
                        // Update payment method display
                        updatePaymentMethodDisplay(this.value);
                        
                        // CRITICAL: Reload HyperPay widget with new brand selection
                        if (paymentCreditCard && paymentCreditCard.checked) {
                            console.log('🔄 RFID Brand changed to:', this.value, '- Reloading HyperPay widget...');
                            
                            // Reset form loading states
                            hyperpayFormLoaded = false;
                            currentCheckoutId = null;
                            
                            // Show loading state immediately
                            if (hyperpayFormBody) {
                                hyperpayFormBody.innerHTML = `
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Updating payment method...</p>
                                    </div>
                                `;
                            }
                            
                            // Reload the widget with new brand
                            setTimeout(() => {
                                loadHyperpayForm();
                            }, 100);
                        }
                    }
                });
            });

            // Initialize payment method display
            const initialSelectedBrand = document.querySelector('input[name="card_brand"]:checked');
            if (initialSelectedBrand) {
                updatePaymentMethodDisplay(initialSelectedBrand.value);
                initialSelectedBrand.closest('.card-brand-option').classList.add('active');
            }
        }
    });
    
    // Make retry function globally accessible
    window.retryHyperpayLoad = function() {
        // Find the element references since we're in global scope
        const paymentCreditCard = document.getElementById('payment_credit_card');
        if (paymentCreditCard && paymentCreditCard.checked) {
            // We need to trigger the existing function by dispatching a payment method change
            if (paymentCreditCard) {
                paymentCreditCard.dispatchEvent(new Event('change'));
            }
        }
    };
</script>
@endpush
@endsection