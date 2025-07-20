<form id="hyperpay-payment-form" action="{{ route('wallet.hyperpay.status') }}" class="paymentWidgets"
    data-brands="{{ $brand === 'mada_card' ? 'MADA' : 'VISA MASTER' }}"
    data-checkout-id="{{ $checkoutId }}">
    <input type="hidden" name="expected_amount" id="expected-amount" value="{{ $amount }}">
    @csrf
</form> 