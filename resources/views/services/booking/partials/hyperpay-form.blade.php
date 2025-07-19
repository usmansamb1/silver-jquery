<form id="hyperpay-payment-form" action="{{ route('services.booking.hyperpay.status') }}" class="paymentWidgets"
    data-brands="{{ $brand === 'MADA' ? 'MADA' : 'VISA MASTER' }}"
    data-checkout-id="{{ $checkoutId }}">
    <input type="hidden" name="expected_amount" id="expected-amount" value="{{ $amount }}">
    @csrf
</form>