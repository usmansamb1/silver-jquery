@extends('layouts.app')

@section('title', 'Test Hyperpay Integration')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Hyperpay Integration Test</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p>This page tests the Hyperpay integration using our hosted checkout solution.</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="testAmount" class="form-label">Test Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">SAR</span>
                            <input type="number" id="testAmount" class="form-control" value="100" min="10">
                            <button class="btn btn-primary" id="testPaymentBtn">
                                <i class="fas fa-credit-card me-2"></i>Test Payment
                            </button>
                        </div>
                    </div>
                    
                    <div id="payment-result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Test payment button click
    $('#testPaymentBtn').on('click', function() {
        const amount = parseFloat($('#testAmount').val()) || 100;
        
        if (amount < 10) {
            alert('Please enter an amount of at least 10 SAR');
            return;
        }
        
        // Show loading state
        $('#payment-result').html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6>Preparing Test Payment</h6>
                <p class="text-muted mb-0">Amount: <strong>${amount} SAR</strong></p>
            </div>
        `);
        
        // Call the redirectToHyperpay endpoint
        $.ajax({
            url: '{{ route("wallet.hyperpay.redirect") }}',
            method: 'POST',
            data: {
                amount: amount,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.html) {
                    $('#payment-result').html(response.html);
                    console.log('✅ Payment redirect is ready');
                } else {
                    showError(response.message || 'Failed to initialize payment');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Payment initialization failed';
                
                if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                $('#payment-result').html(`
                    <div class="alert alert-danger">
                        <h5>Error</h5>
                        <p>${errorMessage}</p>
                        <button class="btn btn-sm btn-outline-danger" onclick="location.reload()">
                            <i class="fas fa-redo me-1"></i>Try Again
                        </button>
                    </div>
                `);
            }
        });
    });
});
</script>
@endpush 