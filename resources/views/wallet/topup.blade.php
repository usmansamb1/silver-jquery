@extends('layouts.app')

@section('title', 'Wallet Top-Up')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@emran-alhaddad/saudi-riyal-font/index.css">

    <style>
        .title-block {
            border-left: 7px solid #a2c943 !important;
        }

        .title-block h4 + span, .title-block .h4 + span {
            font-size: 1.10rem !important;
        }
        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method:hover {
            border-color: #0061f2;
            background-color: rgba(0, 97, 242, 0.05);
        }
        .payment-method.active {
            border-color: #0061f2;
            background-color: rgba(0, 97, 242, 0.1);
        }
        .payment-method-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #363d47;
            margin-bottom: 10px;
        }
        .hidden-content {
            display: none;
        }
        .selected-payment-type {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #0061f2;
        }
        .selected-payment-type h4 {
            color: #363d47;
            margin: 0;
            font-size: 1.2rem;
        }
        .form-heading {
            color: #363d47 !important;
            font-weight: 600;
        }
        hr {
            margin: 30px 0;
        }
        .currentbalance{    background-color: #f5f5f5;
            line-height: 41px;
            padding: 5px;
            border-radius: 10px;}

        /* Highlight effect for bank payment form focus */
        .highlight-form {
            animation: formHighlight 2s ease-in-out;
            box-shadow: 0 0 20px rgba(0, 97, 242, 0.3);
            border: 2px solid #0061f2;
            border-radius: 10px;
        }

        @keyframes formHighlight {
            0% {
                box-shadow: 0 0 0 rgba(0, 97, 242, 0);
                border-color: transparent;
            }
            50% {
                box-shadow: 0 0 25px rgba(0, 97, 242, 0.5);
                border-color: #0061f2;
            }
            100% {
                box-shadow: 0 0 20px rgba(0, 97, 242, 0.3);
                border-color: #0061f2;
            }
        }

        /* Enhanced focus styles for bank payment form inputs */
        #bankPaymentForm .form-control:focus,
        #bankPaymentForm .form-select:focus {
            border-color: #0061f2;
            box-shadow: 0 0 0 0.2rem rgba(0, 97, 242, 0.25);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Active field indicator */
        #bankPaymentForm .form-control:focus {
            background-color: rgba(0, 97, 242, 0.02);
        }

        /* File upload field specific styling */
        #paymentFiles:focus {
            border-color: #0061f2;
            background-color: rgba(0, 97, 242, 0.05);
        }

        /* Notes field specific styling */
        #paymentNotes:focus {
            border-color: #0061f2;
            background-color: rgba(0, 97, 242, 0.02);
        }

        /* Smooth transition for form visibility */
        #bankPaymentForm {
            transition: all 0.3s ease;
        }

        ul.tert-nav {
            float: right;
            position: absolute;
            margin: 0;
            padding: 0;
            right: 0;
            top: 0;
            list-style: none;
        }

        ul.tert-nav li {
            float: right;
            width: 100%;
            height: 28px;

            text-align: center;
            margin-left: 2px;
            cursor: pointer;
            transition: all .2s ease;
            -o-transition: all .2s ease;
            -moz-transition: all .2s ease;
            -webkit-transition: all .2s ease;
        }

        ul.tert-nav li:hover {

        }

        ul.tert-nav .search {
            width: 246px;
            text-align: left;
            cursor: default;
        }

        ul.tert-nav .search:hover {

        }

        ul.tert-nav .searchbox {
            display: none;
            width: 100%;
        }

        ul.tert-nav .searchbox .closesearch {
            float: left;
            margin: 6px 4px 0px 4px;
            cursor: pointer;
        }

        ul.tert-nav .searchbox .closesearch:hover {
            opacity: 0.8;
        }

        ul.tert-nav .searchbox input[type=text] {
            float: left;
            width: 184px;
            height: 24px;
            padding: 0px 0px 0px 10px;
            margin: 2px 0px 0px 0px;
            border: none;
            background:  no-repeat;
            outline: none;
        }

        ul.tert-nav .searchbox input[type=submit] {
            float: left;
            width: 26px;
            height: 24px;
            margin: 2px 0px 0px 0px;
            padding: 0px;
            border: none;
            background: url(images/search-btn.png) no-repeat;
            outline: none;
            cursor: pointer;
        }
        .searchinputstyle{
            background-color: aliceblue;
            border: 1px solid #000 !important;
        }


        .input-group{
            flex-direction: row-reverse;
        }
        .input-group-append{
            display: inline-block;
            position: absolute;
            right: 0px;
            top: 0px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #ddd;

            border-radius: 8px;
            height: 40px;
            padding-top: 5px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #0073bd !important;
        }

        #ofssearchbtn {
            background-color: transparent;
            color: #6c757d;
        }

        .ofssearchbtn-selected{
            background-color: var(--bs-btn-hover-bg) !important;
            color: #fff !important;
        }
        .quantity-input {
            max-width: 150px;
            margin: 0 10px;
        }
        .btn-quantity {
            background-color: white;
            border: 1px solid #dee2e6;
            width: 40px;
            height: 40px;
        }
        .summary-box {
            background-color: #f2f4f8;
            border-radius: 10px;
            padding: 20px;
        }
        .summary-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .total-row {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
        .place-order-btn {
            background-color: #008eca;
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .payment-option {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .footnote {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 10px;
        }
        .check-circle {
            color: white;
            background-color: #275abd;
            border-radius: 50%;
            padding: 2px;
            margin-left: 5px;
        }
        .circle-placeholder {
            width: 20px;
            height: 20px;
            background-color: #dee2e6;
            border-radius: 50%;
            display: inline-block;
        }

        .payment-option label {
            width: 87%;
            line-height: 42px;
        }

        /* File Upload Styles */
        .file-preview {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .file-preview-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px;
            margin: 5px 0;
            background: #f8f9fa;
            border-radius: 3px;
        }
        .file-preview-item .remove-file {
            color: #dc3545;
            cursor: pointer;
        }
        .upload-progress {
            height: 4px;
            margin-top: 10px;
            display: none;
        }
        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
        }

        /* Selected Payment Method Badge */
        .selected-method-badge {
            display: inline-block;
            font-size: 1.0rem;
            font-weight: 500;
            color: #fff !important;
            background-color: #0061f2;
            padding: 4px 10px;
            border-radius: 20px;
            margin-left: 10px;
            vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0, 97, 242, 0.15);
            transition: all 0.3s ease;
        }
        
        .payment-method.active {
            border-color: #0061f2;
            background-color: rgba(0, 97, 242, 0.1);
        }

        #bankPaymentForm .card {
    color: #000 !important;
}

#bankPaymentForm .card-body{
    color: #000 !important;
}

#bankPaymentForm .card-title {
    color: #000 !important;
}

        /* Hyperpay Section */
        #hyperpay-section {
            transition: all 0.3s ease;
            /* Default hidden but can be shown via JS */
        }

        /* Button states */
        #show-hyperpay-form:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Smooth transitions for payment method switching */
        .payment-method {
            transition: all 0.3s ease;
        }

        .payment-method .hidden-content {
            transition: all 0.3s ease;
        }

        /* Widget loading states */
        .widget-loading {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Amount update indicator */
        #amount-update-indicator {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        /* Hyperpay current amount highlight */
        #hyperpay-current-amount {
            font-weight: bold;
            color: #0061f2;
            transition: all 0.3s ease;
        }
        
        /* Button state transitions */
        #show-hyperpay-form, #change-payment-method {
            transition: opacity 0.3s ease, transform 0.2s ease;
        }
        
        #show-hyperpay-form.hiding, #change-payment-method.hiding {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
        }

        /* Widget success state */
        #widget-success-state {
            animation: slideInFromTop 0.5s ease-out;
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
}

    </style>
@endpush

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-sm-6">
                <h2 class="mb-3">{{ __('Balance topup') }}</h2>

                <div class="mb-3 currentbalance">
                    <strong>{{ __('Current balance') }}:</strong> <span class="icon-saudi_riyal"></span><span> {{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</span>
                </div>

                <!-- Topup Amount -->
                <div class="mb-3">
                    <label for="topupAmount" class="form-label">{{ __('Topup Amount') }}</label>
                    <input type="number" id="topupAmount" class="form-control" min="10" value="10" required>
                    <small>{{ __('Minimum amount is 10 SAR') }}</small>
                </div>

                <h5>{{ __('Select payment method') }}</h5>

                <!-- Payment Methods -->
                <div class="payment-method active" data-method="credit-card">
                    <input type="radio" name="paymentMethod" checked value="credit_card" hidden>
                    <img style="padding: 23px 2px;" src="{{ asset('theme_files/imgs/payment-gateways5.png') }}" class="img-thumbnail">
                    <div class="hidden-content" style="display: block;">
                        <p class="mt-3 mb-2"><strong>{{ __('Credit Card') }} {{ __('Payment') }}</strong></p>
                        <p class="text-muted small">{{ __('Use the form below to complete your payment securely.') }}</p>
                        <button type="button" class="btn btn-primary mt-2" id="show-hyperpay-form">
                                <i class="fas fa-lock me-2"></i>{{ __('Pay with Card') }}
                            </button>
                        <button type="button" class="btn btn-outline-secondary mt-2" id="change-payment-method" style="display: none;">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Change Payment Method') }}
                            </button>
                        <div id="amount-change-warning" class="alert alert-warning mt-2" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>{{ __('Amount changed! Click "Pay with Card" again to process the new amount.') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Hyperpay Form Section (positioned right after credit card option) -->
                <div id="hyperpay-section" class="mt-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-credit-card me-2"></i>{{ __('Credit Card') }} {{ __('Payment') }}
                            </h5>
                            <div class="alert alert-info mb-3" id="hyperpay-amount-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('Processing payment for') }}: <strong><span class="icon-saudi_riyal"></span> <span id="hyperpay-current-amount">0.00</span> {{ __('SAR') }}</strong>
                                <div id="amount-update-indicator" class="mt-2" style="display: none;">
                                    <small class="text-muted">
                                        <i class="fas fa-sync fa-spin me-1"></i>
                                        {{ __('Updating payment amount...') }}
                                    </small>
                                </div>
                            </div>
                            <!-- Card network selector -->
                            <div class="mb-3">
                                <label for="hyperpayBrand" class="form-label">{{ __('Select Card Network') }}</label>
                                <select id="hyperpayBrand" class="form-select">
                                    <option value="credit_card">{{ __('Visa / MasterCard') }}</option>
                                    <option value="mada_card">{{ __('MADA (مدى)') }}</option>
                                    <option value="AMEX">{{ __('AMEX') }}</option>
                                    <option value="STC_PAY">{{ __('STCPay') }}</option>
                                    <option value="URPAY">{{ __('URPay') }}</option>
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    {{ __('Select MADA for Saudi domestic cards') }}
                                </small>
                                {{-- <div class="alert alert-info mt-2" id="test-card-info" style="display: none;">
                                    <strong>{{ __('Test Environment') }}:</strong> {{ __('Use test card numbers for testing') }}:
                                    <ul class="mb-0 mt-1">
                                        <li><strong>Visa:</strong> 4200000000000000</li>
                                        <li><strong>MasterCard:</strong> 5200000000000000</li>
                                        <li><strong>MADA:</strong> 4464040000000007</li>
                                    </ul>
                                    <small>CVV: Any 3 digits, Expiry: Any future date</small>
                                </div> --}}
                            </div>
                            
                            <div id="hyperpay-widget">
                                <!-- Payment widget will be loaded here -->
                                <div class="text-center p-4">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <h6>{{ __('Preparing Secure Payment') }}</h6>
                                    <p class="text-muted mb-0">{{ __('Please wait...') }}</p>
                                </div>
                            </div>
                            <small class="text-muted d-block text-center">{{ __('Your payment is secured by Hyperpay') }}</small>
                        </div>
                    </div>
                </div>

                @if($user->registration_type=="company")
                <!-- Bank Transfer -->
                <div class="payment-method" data-method="bank-transfer">
                    <input type="radio" name="paymentMethod" value="bank_transfer" hidden>
                    <img src="{{ asset('theme_files/imgs/bank-transfer-icon-2.png') }}" class="img-thumbnail">
                    <div class="selected-payment-type d-none">
                        <h4>{{ __('Bank Transfer') }}</h4>
                    </div>
                    <div class="hidden-content">
                        <p><h4>{{ __('Bank Transfer Note') }}:</h4></p>
                        <ol class="ms-4">
                            <li>{{ __('Bank Name') }}: Al Ahli, IBAN: SA837468768682768390</li>
                            <li>{{ __('Once transfer please enter exact Amount in TOPUP Which you transfer And Fill Form below.') }}</li>
                        </ol>
                        <p class="mt-2"><strong>{{ __('Bank Transfer Form') }}</strong></p>
                    </div>
                </div>

                <!-- Bank Guarantee -->
                <div class="payment-method" data-method="bank-guarantee">
                    <input type="radio" name="paymentMethod" value="bank_guarantee" hidden>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-contract fa-2x me-3"></i>
                        <div>
                            <div class="payment-method-title">{{ __('Bank Guarantee') }}</div>
                            <small class="text-muted">{{ __('Submit bank guarantee document') }}</small>
                        </div>
                    </div>
                    <div class="selected-payment-type d-none">
                        <h4>{{ __('Bank Guarantee Payment') }}</h4>
                    </div>
                    <div class="hidden-content">
                        <p><h4>{{ __('Bank Guarantee Note') }}:</h4></p>
                        <ol class="ms-4">
                            <li>{{ __('Ensure to provide the grantee details when making the payment.') }}</li>
                            <li>{{ __('Fill in the form below with the exact amount transferred.') }}</li>
                        </ol>
                        <p class="mt-2"><strong>{{ __('Bank Guarantee Form') }}</strong></p>
                    </div>
                </div>

                <!-- Bank LC -->
                <div class="payment-method" data-method="bank-lc">
                    <input type="radio" name="paymentMethod" value="bank_lc" hidden>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-money-check fa-2x me-3"></i>
                        <div>
                            <div class="payment-method-title">{{ __('Bank LC') }}</div>
                            <small class="text-muted">{{ __('Submit letter of credit') }}</small>
                        </div>
                    </div>
                    <div class="selected-payment-type d-none">
                        <h4>{{ __('Bank LC Payment') }}</h4>
                    </div>
                    <div class="hidden-content">
                        <p><h4>{{ __('Bank LC Note') }}:</h4></p>
                        <ol class="ms-4">
                            <li>{{ __('Provide the Letter of Credit details for processing.') }}</li>
                            <li>{{ __('Complete the form below with the amount related to the LC.') }}</li>
                        </ol>
                        <p class="mt-2"><strong>{{ __('Bank LC Form') }}</strong></p>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-sm-6">
                <!-- Bank Payment Form -->
                <div id="bankPaymentForm" class="d-none">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title mb-4">{{ __('Payment Details') }} <span id="selectedPaymentMethodName" class="selected-method-badge"></span></h2>
                            <form id="bankPaymentDetailsForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="paymentFiles" class="form-label">{{ __('Upload Documents') }}</label>
                                    <input type="file" class="form-control" id="paymentFiles" name="payment_files[]" multiple>
                                    <small class="text-muted">{{ __('Upload payment proof, documents, or relevant files') }}</small>
                                </div>
                                <div class="mb-3">
                                    <label for="paymentNotes" class="form-label">{{ __('Notes') }}</label>
                                    <textarea class="form-control" id="paymentNotes" name="payment_notes" rows="4" placeholder="{{ __('Enter any additional notes or reference numbers') }}"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">{{ __('Submit Payment Details') }}</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div id="paymentSummary" class="summary-box">
                    <h2 class="summary-title">{{ __('Summary') }}</h2>
                    <div class="summary-row">
                        <span>{{ __('Amount (excl. VAT)') }}</span>
                        <span><span class="icon-saudi_riyal"></span> <span id="unitPrice">0.00</span></span>
                    </div>
                    <div class="summary-row">
                        <span>{{ __('VAT (15% included)') }}</span>
                        <span><span class="icon-saudi_riyal"></span> <span id="vatAmount">0.00</span></span>
                    </div>
                    <div class="summary-row total-row" id="totalSection">
                        <span>{{ __('Total Amount') }}</span>
                        <span><span class="icon-saudi_riyal"></span> <span id="totalAmount">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hyperpay Redirect Form -->
    <form id="hyperpay-redirect-form" action="{{ route('wallet.hyperpay.redirect') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="amount" id="redirect-amount" value="">
    </form>
@endsection

@push('scripts')
<script>
    // VAT Rate constant (15% inclusive)
    const VAT_RATE = 0.15;

    // File upload constraints
    const ALLOWED_FILE_TYPES = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    const MAX_FILE_SIZE = 2048; // KB

    // Global state management
    let currentPaymentMethod = 'credit_card';
    let isUpdatingPayment = false;
    let updateTimeout = null;

    // Update Summary function (VAT inclusive calculation)
    function updateSummary() {
        const totalAmount = parseFloat($("#topupAmount").val()) || 0;
        
        // Calculate VAT inclusive amounts
        const amountExclVat = totalAmount / (1 + VAT_RATE);
        const vatAmount = totalAmount - amountExclVat;

        $("#unitPrice").text(amountExclVat.toFixed(2));
        $("#vatAmount").text(vatAmount.toFixed(2));
        $("#totalAmount").text(totalAmount.toFixed(2));
    }

    // Show messages using SweetAlert
    function showError(message) {
        Swal.fire({
            title: '{{ __('Error!') }}',
            text: message,
            icon: 'error',
            confirmButtonText: '{{ __('OK') }}'
        });
    }

    function showSuccess(message) {
        Swal.fire({
            title: '{{ __('Success!') }}',
            text: message,
            icon: 'success',
            confirmButtonText: '{{ __('OK') }}'
        }).then(() => {
            window.location.reload();
        });
    }

    function showInfo(message) {
        Swal.fire({
            title: '{{ __('Information') }}',
            text: message,
            icon: 'info',
            confirmButtonText: '{{ __('Got it!') }}',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    }

    // CLEAN SOLUTION: Load Hyperpay form via AJAX
    function loadHyperpayForm(amount) {
        if (isUpdatingPayment) return;
        isUpdatingPayment = true;
        console.log('🔄 Preparing payment for amount:', amount);

        // Determine selected brand
        const brand = $('#hyperpayBrand').val();
        console.log('🔄 Loading payment form for brand:', brand, 'Amount:', amount);
        
        // Show loading state
        $("#hyperpay-widget").html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6>Preparing ${brand === 'mada_card' ? 'MADA' : 'Credit Card'} Payment</h6>
                <p class="text-muted mb-0">Amount: <strong>${amount} SAR</strong></p>
                <small class="text-muted">Please wait...</small>
            `);

        // Update amount display
        $("#hyperpay-current-amount").text(amount.toFixed(2));
        $("#hyperpay-amount-info").show();

        // AJAX request to get Hyperpay form
        $.ajax({
            url: '{{ route("wallet.hyperpay.get-form") }}',
            method: 'POST',
            data: {
                amount: amount,
                brand: brand,
                _token: '{{ csrf_token() }}'
            },
            timeout: 15000,
            success: function(response) {
                console.log('📡 HyperPay response received:', response);
                if (response.success && response.html && response.checkout_id) {
                    // Inject the payment form HTML
                    $("#hyperpay-widget").html(response.html);
                    // Render the Hyperpay payment widget script
                    loadHyperpayScript(response.checkout_id);
                    console.log('✅ Payment form loaded for brand:', brand, 'CheckoutID:', response.checkout_id);
                } else {
                    console.error('❌ Payment form failed:', response);
                    showHyperpayError(response.message || 'Failed to initialize payment');
                }
            },
            error: function(xhr, status, errorThrown) {
                console.error('❌ Failed to initialize payment:', status, errorThrown);
                let errorMessage = 'Payment initialization failed';
                
                if (status === 'timeout') {
                    errorMessage = 'Connection timeout. Please try again.';
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showHyperpayError(errorMessage);
            },
            complete: function() {
                isUpdatingPayment = false;
            }
        });
    }

    // Load Hyperpay widget script
    function loadHyperpayScript(checkoutId) {
        // Remove any old Hyperpay scripts to prevent conflicts
        $('script[src*="paymentWidgets.js"]').remove();
        console.log('⏳ Loading new Hyperpay script for checkout ID:', checkoutId);

        const script = document.createElement('script');
        script.src = `https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=${checkoutId}`;
        script.async = true;
        
        document.head.appendChild(script);

        // After adding the script, let's log the state of the form.
        // The script itself will handle the visual rendering.
        setTimeout(() => {
            const form = $('form.paymentWidgets');
            if (form.length > 0) {
                console.log('🔍 Found payment form after script load. Hyperpay should now take over.', form);
                // We can add a class to indicate the script is loaded
                $('#hyperpay-section').addClass('script-loaded');
            } else {
                console.error('❌ Could not find payment form after script load.');
            }
        }, 500);
    }

    // Show Hyperpay error
    function showHyperpayError(message) {
        $("#hyperpay-widget").html(`
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle mb-2"></i>
                <h6>Payment Form Error</h6>
                <p class="mb-2">${message}</p>
                <button class="btn btn-sm btn-outline-primary" onclick="retryHyperpayLoad()">
                    <i class="fas fa-redo me-1"></i>Retry
                </button>
            </div>
        `);
    }

    // Retry loading Hyperpay form
    function retryHyperpayLoad() {
        const amount = parseFloat($("#topupAmount").val()) || 0;
        if (amount >= 10) {
            loadHyperpayForm(amount);
        }
    }

    // EFFICIENT AMOUNT CHANGE HANDLER
    function handleAmountChange() {
        const amount = parseFloat($("#topupAmount").val()) || 0;
        
        // Update summary immediately
        updateSummary();
        
        // If credit card is selected and amount is valid, reload form
        if (currentPaymentMethod === 'credit_card' && amount >= 10 && $("#hyperpay-section").is(':visible')) {
            // Clear existing timeout
            if (updateTimeout) {
                clearTimeout(updateTimeout);
            }
            
            // Debounce the update (wait 800ms after user stops typing)
            updateTimeout = setTimeout(() => {
                console.log('💡 Amount changed to:', amount, '- Reloading Hyperpay form');
                loadHyperpayForm(amount);
            }, 800);
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        // Set initial amount and update summary
        const initialAmount = 100; // Default amount
        $("#topupAmount").val(initialAmount);
        updateSummary();
        
        // Initially hide hyperpay section
        $("#hyperpay-section").hide();
        
        // Show test card info in test environment
        @if(config('services.hyperpay.mode') === 'test' || str_contains(config('services.hyperpay.base_url'), 'test'))
            $("#test-card-info").show();
        @endif

        // Amount change handler with debouncing
        $("#topupAmount").on("input", handleAmountChange);

        // Payment method selection
        $(".payment-method").click(function () {
            $(".payment-method").removeClass("active").find(".hidden-content").slideUp();
            $(this).addClass("active").find(".hidden-content").slideDown();
            $(this).find("input").prop("checked", true);
            
            const paymentMethod = $(this).find("input").val();
            currentPaymentMethod = paymentMethod;
            
            // Reset forms and UI state
            $("#hyperpay-section").hide();
            $("#show-hyperpay-form").show();
            $("#change-payment-method").hide();
            $("#bankPaymentForm").addClass('d-none');
            $("#paymentSummary").removeClass('d-none');
            $("#amount-change-warning").hide();
            
            if (paymentMethod === 'bank_transfer' || paymentMethod === 'bank_guarantee' || paymentMethod === 'bank_lc') {
                $("#paymentSummary").addClass('d-none');
                $("#bankPaymentForm").removeClass('d-none');
                
                const methodNames = {
                    'bank_transfer': 'Bank Transfer',
                    'bank_guarantee': 'Bank Guarantee', 
                    'bank_lc': 'Bank LC'
                };
                $("#selectedPaymentMethodName").text(methodNames[paymentMethod] || '').show();
                
                // Focus on payment details form for bank transfer options
                setTimeout(function() {
                    // Smooth scroll to the bank payment form
                    $('html, body').animate({
                        scrollTop: $("#bankPaymentForm").offset().top - 100
                    }, 500);
                    
                    // Focus on the first input field (file upload) by default
                    // User can press Tab to move to notes field if they prefer
                    $("#paymentFiles").focus();
                    
                    // Add a subtle highlight effect to draw attention
                    $("#bankPaymentForm").addClass('highlight-form');
                    setTimeout(function() {
                        $("#bankPaymentForm").removeClass('highlight-form');
                    }, 2000);
                    
                    // Show a helpful tooltip or message
                    if (paymentMethod === 'bank_transfer') {
                       // showInfo('Please upload your bank transfer receipt and add any reference numbers in the notes field.');
                    } else if (paymentMethod === 'bank_guarantee') {
                        //showInfo('Please upload your bank guarantee document and provide any additional details in the notes field.');
                    } else if (paymentMethod === 'bank_lc') {
                       // showInfo('Please upload your letter of credit document and provide any reference numbers in the notes field.');
                    }
                }, 300);
            } else {
                $("#selectedPaymentMethodName").hide();
            }
        });

        // Pay with Card button click
        $("#show-hyperpay-form").on('click', function(e) {
            e.preventDefault();
            
            const amount = parseFloat($("#topupAmount").val()) || 0;
            if (amount < 10) {
                showError('Please enter a valid amount (minimum 10 SAR)');
                return;
            }
            
            // Hide "Pay with Card" button with transition
            $(this).addClass('hiding');
            setTimeout(() => {
                $(this).hide().removeClass('hiding');
                $("#change-payment-method").show().removeClass('hiding');
            }, 300);
            
            // Force show the hyperpay section with multiple techniques
            $("#hyperpay-section").show();
            $("#hyperpay-section").css({
                'display': 'block !important',
                'visibility': 'visible',
                'opacity': '1'
            });
            
            // Load Hyperpay form
            loadHyperpayForm(amount);
            
            // Extra check - ensure section is visible after a brief delay
            setTimeout(function() {
                if ($("#hyperpay-section").is(":hidden")) {
                    console.log("⚠️ Hyperpay section still hidden, forcing display");
                    $("#hyperpay-section").attr('style', 'display: block !important');
                }
            }, 100);
        });

        // Change Payment Method button click
        $("#change-payment-method").on('click', function(e) {
            e.preventDefault();
            
            // Hide the "Change Payment Method" button with transition
            $(this).addClass('hiding');
            
            // Hide the HyperPay form and show the "Pay with Card" button
            $("#hyperpay-section").slideUp(300, function() {
                $("#show-hyperpay-form").show().removeClass('hiding');
            });
            
            setTimeout(() => {
                $(this).hide().removeClass('hiding');
            }, 300);
            
            $("#amount-change-warning").hide();
            
            console.log('🔄 User changed payment method - hiding HyperPay form');
        });

        // File upload handling
        const filePreviewContainer = $('<div class="file-preview d-none"></div>');
        $('#paymentFiles').after(filePreviewContainer);
        
        const progressBar = $('<div class="progress upload-progress"><div class="progress-bar" role="progressbar" style="width: 0%"></div></div>');
        filePreviewContainer.after(progressBar);
        
        $("#paymentFiles").on('change', function(e) {
            const files = Array.from(e.target.files);
            filePreviewContainer.empty();
            
            let hasError = false;
            files.forEach(file => {
                if (!ALLOWED_FILE_TYPES.includes(file.type)) {
                    showError('Only PDF, JPG, and PNG files are allowed.');
                    hasError = true;
                    return;
                }
                if (file.size > MAX_FILE_SIZE * 1024) {
                    showError(`File ${file.name} exceeds maximum size of ${MAX_FILE_SIZE}KB`);
                    hasError = true;
                    return;
                }
                
                const previewItem = $(`
                    <div class="file-preview-item">
                        <span><i class="fas fa-file me-2"></i>${file.name}</span>
                        <i class="fas fa-times remove-file"></i>
                    </div>
                `);
                filePreviewContainer.append(previewItem);
            });
            
            if (files.length > 0 && !hasError) {
                filePreviewContainer.removeClass('d-none');
            }
        });
        
        // Remove file handler
        filePreviewContainer.on('click', '.remove-file', function() {
            const index = $(this).parent().index();
            const dt = new DataTransfer();
            const input = document.getElementById('paymentFiles');
            const { files } = input;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) dt.items.add(files[i]);
            }
            
            input.files = dt.files;
            $(this).parent().remove();
            
            if (filePreviewContainer.children().length === 0) {
                filePreviewContainer.addClass('d-none');
            }
        });

        // Enhanced keyboard navigation for bank payment form
        $("#paymentFiles").on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                $("#paymentNotes").focus();
            }
        });

        $("#paymentNotes").on('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                $("#bankPaymentDetailsForm").submit();
            }
        });

        // Bank payment form submission
        $("#bankPaymentDetailsForm").on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalBtnText = submitBtn.text();
            
            const amount = $("#topupAmount").val();
            if (!amount || amount < 10) {
                showError('Please enter a valid amount (minimum 10 SAR)');
                return;
            }
            
            const files = $("#paymentFiles")[0].files;
            if (files.length === 0) {
                showError('Please upload at least one document');
                return;
            }
            
            submitBtn.prop('disabled', true).text('Processing...');
            progressBar.show();
            
            const formData = new FormData(this);
            formData.append('amount', amount);
            formData.append('payment_method', $("input[name='paymentMethod']:checked").val());
            
            $.ajax({
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = (evt.loaded / evt.total) * 100;
                            progressBar.find('.progress-bar').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                url: '{{ route("wallet.bank-payment") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showSuccess('Payment details submitted successfully. You will be notified once reviewed.');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorMessages = Object.values(errors).flat();
                        showError(errorMessages.join('\n'));
                    } else {
                        showError('Failed to submit payment details. Please try again.');
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalBtnText);
                    progressBar.hide().find('.progress-bar').css('width', '0%');
                }
        });
    });

        // Reload Hyperpay form when card network changes
        $('#hyperpayBrand').on('change', function() {
            const amount = parseFloat($("#topupAmount").val()) || 0;
            if (amount >= 10) {
                loadHyperpayForm(amount);
            }
        });
    });

    // Global retry function
    window.retryHyperpayLoad = retryHyperpayLoad;
</script>
@endpush
