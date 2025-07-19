{{-- Hyperpay Hosted Payment Redirect Template --}}
<div class="alert alert-info text-center p-4 border-0 shadow-sm rounded-3">
    <div class="mb-3">
        <i class="fas fa-external-link-alt fa-2x text-primary"></i>
    </div>
    
    <h5 class="mb-3 fw-bold">Secure Payment Ready</h5>
    
    <p class="mb-3">
        Click the button below to complete your payment of <strong>{{ number_format($amount, 2) }} SAR</strong> 
        securely on Hyperpay's website.
    </p>
    
    @if(isset($is_test_mode) && $is_test_mode)
        <div class="alert alert-warning mb-3">
            <strong>Test Mode:</strong> This is a demonstration of the payment flow.
        </div>
        
        <a href="{{ route('wallet.hyperpay.status') }}?resourcePath=/v1/checkouts/demo-checkout-id/payment&id=demo-checkout-id" 
           class="btn btn-success btn-lg px-4 py-2">
            <i class="fas fa-check-circle me-2"></i>Simulate Successful Payment
        </a>
    @else
        <a href="{{ $redirect_url }}" target="_blank" class="btn btn-primary btn-lg px-4 py-2">
            <i class="fas fa-lock me-2"></i>Complete Payment Securely
        </a>
    @endif
    
    <div class="mt-3">
        <small class="text-muted d-block">Payment will process in a new window</small>
        <small class="text-muted d-block">Session expires in 30 minutes</small>
    </div>
    
    <div class="mt-4">
        <small>
            <i class="fas fa-info-circle me-2"></i>
            After completing payment, you will be redirected back to this site automatically.
        </small>
    </div>
</div> 