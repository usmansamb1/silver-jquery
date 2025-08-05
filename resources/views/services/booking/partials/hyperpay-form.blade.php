{{-- Clear Payment Method Indicator --}}
{{-- <div class="alert alert-info mb-3" style="border-left: 4px solid #0061f2;">
    <div class="d-flex align-items-center">
        <div class="me-3">
            @if($brand === 'STC_PAY')
                <svg width="32" height="20" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#662D91"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="7" font-weight="bold">STC PAY</text>
                </svg>
            @elseif($brand === 'URPAY')
                <svg width="32" height="20" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#00A651"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="8" font-weight="bold">URPAY</text>
                </svg>
            @elseif($brand === 'AMEX')
                <svg width="32" height="20" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#006FCF"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="8" font-weight="bold">AMEX</text>
                </svg>
            @elseif($brand === 'mada_card')
                <svg width="32" height="20" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#0066CC"/>
                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12" font-weight="bold">مدى</text>
                </svg>
            @else
                <svg width="32" height="20" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="3" fill="#1A1F71"/>
                    <path d="M16.5 8.5L14 16.5H11.5L14 8.5H16.5ZM22.5 8.5L20.5 13.5L19.5 9.5C19.2 8.5 18.5 8.5 18.5 8.5H15.5L15.6 8.8C16.2 9 16.5 9.5 16.5 9.5L18.5 16.5H21L25.5 8.5H22.5ZM28.5 8.5H26.5C26.2 8.5 26 8.7 26 9V16.5H28.5V8.5Z" fill="white"/>
                </svg>
            @endif
        </div>
       <div>
            <strong>{{ __('Processing via') }}: {{ $displayName ?? 'Credit Card' }}</strong>
            <br>
            <small class="text-muted">
                @if(in_array($brand, ['STC_PAY', 'URPAY', 'AMEX']))
                    {{ __('Your payment will be processed securely via') }} {{ $displayName }}. {{ __('Enter your card details below.') }}
                @elseif($brand === 'mada_card')
                    {{ __('Enter your MADA card details below to complete the payment.') }}
                @else
                    {{ __('Enter your Visa or MasterCard details below to complete the payment.') }}
                @endif
            </small>
        </div>  
    </div>
</div> --}}
 
<form id="hyperpay-payment-form" action="{{ route('services.booking.hyperpay.status') }}" class="paymentWidgets"
    data-brands="{{ $formBrand ?? ($brand === 'mada_card' ? 'MADA' : 'VISA MASTER') }}"
    data-checkout-id="{{ $checkoutId }}">
    <input type="hidden" name="expected_amount" id="expected-amount" value="{{ $amount }}">
    <input type="hidden" name="payment_brand" value="{{ $brand ?? 'credit_card' }}">
    <input type="hidden" name="display_name" value="{{ $displayName ?? 'Credit Card' }}">
    @csrf
</form>