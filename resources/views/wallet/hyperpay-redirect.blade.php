@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
  <div class="container my-5">
    <h2 class="mb-4">Secure Payment</h2>
    <div class="card p-4">
      <form id="hyperpay-payment-form" action="{{ route('wallet.hyperpay.status') }}" class="paymentWidgets" data-brands="VISA MASTER MADA" data-checkout-id="{{ $checkoutId }}">
        <input type="hidden" name="expected_amount" value="">
      </form>
      <p class="text-muted mt-3">If the form does not load, <a href="javascript:location.reload()">click here to reload</a>.</p>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId={{ $checkoutId }}"></script>
@endpush 