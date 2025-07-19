@extends('layouts.app')

@section('title', 'HyperPay Debug Test')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-credit-card me-2"></i>HyperPay Configuration & API Test</h3>
            <p class="mb-0 text-muted">Debug tool to validate HyperPay configuration and test API connectivity</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fa fa-cog me-2"></i>Configuration Test</h5>
                    <p class="text-muted">Check if all HyperPay configuration values are properly set</p>
                    <button type="button" class="btn btn-primary" id="testConfig">
                        <i class="fa fa-play me-1"></i>Test Configuration
                    </button>
                    <div id="configResult" class="mt-3"></div>
                </div>
                
                <div class="col-md-6">
                    <h5><i class="fa fa-globe me-2"></i>API Test</h5>
                    <p class="text-muted">Test direct API call to HyperPay checkout endpoint</p>
                    <form id="apiTestForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount (SAR)</label>
                            <input type="number" class="form-control" name="amount" value="100.00" step="0.01" min="10" max="50000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Card Brand</label>
                            <select class="form-select" name="brand">
                                <option value="credit_card">Credit Card (VISA/MASTER)</option>
                                <option value="mada_card">MADA Card</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-rocket me-1"></i>Test API
                        </button>
                    </form>
                    <div id="apiResult" class="mt-3"></div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-12">
                    <h5><i class="fa fa-list-alt me-2"></i>Quick Checklist</h5>
                    <div class="alert alert-info">
                        <strong>Before testing, ensure:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Your .env file has all HYPERPAY_* variables set</li>
                            <li>You have valid HyperPay test credentials</li>
                            <li>Your user profile has a valid email address</li>
                            <li>Laravel cache is cleared: <code>php artisan config:clear</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test configuration
    document.getElementById('testConfig').addEventListener('click', async function() {
        const resultDiv = document.getElementById('configResult');
        const btn = this;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Testing...';
        resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Checking configuration...';
        
        try {
            const response = await fetch('/debug-hyperpay-config');
            const data = await response.json();
            
            let configHtml = '<div class="alert alert-success"><h6><i class="fa fa-check-circle me-1"></i>Configuration Status:</h6>';
            
            // Check each config item
            const config = data.config_status;
            const user = data.user_status;
            
            configHtml += '<table class="table table-sm mt-2">';
            configHtml += '<tr><td><strong>Base URL:</strong></td><td>' + (config.base_url || '<span class="text-danger">Missing</span>') + '</td></tr>';
            configHtml += '<tr><td><strong>Access Token:</strong></td><td>' + (config.has_access_token ? '<span class="text-success">✓ Set</span>' : '<span class="text-danger">✗ Missing</span>') + '</td></tr>';
            configHtml += '<tr><td><strong>Credit Entity ID:</strong></td><td>' + (config.entity_id_credit || '<span class="text-danger">Missing</span>') + '</td></tr>';
            configHtml += '<tr><td><strong>MADA Entity ID:</strong></td><td>' + (config.entity_id_mada || '<span class="text-danger">Missing</span>') + '</td></tr>';
            configHtml += '<tr><td><strong>Currency:</strong></td><td>' + (config.currency || '<span class="text-danger">Missing</span>') + '</td></tr>';
            configHtml += '<tr><td><strong>Mode:</strong></td><td>' + (config.mode || '<span class="text-danger">Missing</span>') + '</td></tr>';
            configHtml += '<tr><td><strong>User Email:</strong></td><td>' + (user.email_valid ? '<span class="text-success">✓ Valid</span>' : '<span class="text-danger">✗ Invalid</span>') + '</td></tr>';
            configHtml += '</table></div>';
            
            resultDiv.innerHTML = configHtml;
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fa fa-exclamation-triangle me-1"></i>Configuration Test Failed</h6>
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-play me-1"></i>Test Configuration';
        }
    });
    
    // Test API
    document.getElementById('apiTestForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const resultDiv = document.getElementById('apiResult');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Testing API...';
        resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm"></div> Testing HyperPay API...';
        
        const formData = new FormData(this);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        try {
            const response = await fetch('/debug-hyperpay-api', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6><i class="fa fa-check-circle me-1"></i>API Test Successful!</h6>
                        <p><strong>Checkout ID:</strong> ${data.response_body.id || 'N/A'}</p>
                        <details>
                            <summary>Full Response</summary>
                            <pre class="mt-2">${JSON.stringify(data, null, 2)}</pre>
                        </details>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fa fa-exclamation-triangle me-1"></i>API Test Failed</h6>
                        <p><strong>Status:</strong> ${data.response_status || 'Unknown'}</p>
                        <details>
                            <summary>Error Details</summary>
                            <pre class="mt-2">${JSON.stringify(data, null, 2)}</pre>
                        </details>
                    </div>
                `;
            }
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fa fa-exclamation-triangle me-1"></i>API Test Error</h6>
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-rocket me-1"></i>Test API';
        }
    });
});
</script>
@endsection 