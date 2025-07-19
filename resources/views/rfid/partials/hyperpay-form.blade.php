
<!-- Copy the exact working structure from wallet -->
<form id="hyperpay-payment-form" action="{{ route('rfid.hyperpay.status') }}" class="paymentWidgets"
    data-brands="VISA MASTER MADA"
    data-checkout-id="{{ $checkoutId }}">
    <input type="hidden" name="expected_amount" id="expected-amount" value="{{ $amount }}">
    @csrf
</form>

