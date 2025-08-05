<form id="hyperpay-payment-form" action="{{ route('wallet.hyperpay.status') }}" class="paymentWidgets"
    data-brands="{{ $formBrand ?? ($brand === 'mada_card' ? 'MADA' : 'VISA MASTER') }}"
    data-checkout-id="{{ $checkoutId }}">
    <input type="hidden" name="expected_amount" id="expected-amount" value="{{ $amount }}">
    <input type="hidden" name="payment_brand" value="{{ $brand }}">
    <input type="hidden" name="display_name" value="{{ $displayName ?? 'Credit Card' }}">
    @csrf
</form> 