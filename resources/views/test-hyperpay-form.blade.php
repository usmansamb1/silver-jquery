<!DOCTYPE html>
<html>
<head>
    <title>Test Hyperpay Form AJAX</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .result { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .success { border-color: #28a745; background: #d4edda; color: #155724; }
        .error { border-color: #dc3545; background: #f8d7da; color: #721c24; }
        .loading { border-color: #ffc107; background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Hyperpay Form AJAX</h1>
        
        <div class="form-group">
            <label for="amount">Amount (SAR):</label>
            <input type="number" id="amount" value="100" min="10" max="50000">
        </div>
        
        <div class="form-group">
            <button onclick="testHyperpayForm()">Test Load Hyperpay Form</button>
        </div>
        
        <div id="result" class="result" style="display: none;"></div>
        
        <div id="hyperpay-form-container" style="margin-top: 30px;"></div>
    </div>

    <script>
        function testHyperpayForm() {
            const amount = document.getElementById('amount').value;
            const resultDiv = document.getElementById('result');
            
            if (!amount || amount < 10) {
                showResult('Please enter an amount of at least 10 SAR', 'error');
                return;
            }
            
            showResult('Loading Hyperpay form...', 'loading');
            
            $.ajax({
                url: '{{ route("wallet.hyperpay.get-form") }}',
                method: 'POST',
                data: {
                    amount: amount,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showResult(`✅ Success! Checkout ID: ${response.checkout_id}, Amount: ${response.amount} SAR`, 'success');
                        
                        // Display the form HTML
                        document.getElementById('hyperpay-form-container').innerHTML = `
                            <h3>Generated Form HTML:</h3>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0;">
                                ${response.html}
                            </div>
                            <h3>Raw Response:</h3>
                            <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">${JSON.stringify(response, null, 2)}</pre>
                        `;
                        
                        // Load the actual Hyperpay script
                        loadHyperpayScript(response.checkout_id);
                    } else {
                        showResult(`❌ Error: ${response.message}`, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    let errorMessage = 'Request failed';
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        errorMessage = errorResponse.message || errorMessage;
                    } catch (e) {
                        errorMessage = `${status}: ${error}`;
                    }
                    showResult(`❌ AJAX Error: ${errorMessage}`, 'error');
                }
            });
        }
        
        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            resultDiv.className = `result ${type}`;
            resultDiv.innerHTML = message;
            resultDiv.style.display = 'block';
        }
        
        function loadHyperpayScript(checkoutId) {
            // Remove existing scripts
            $('script[src*="paymentWidgets.js"]').remove();
            
            const script = document.createElement('script');
            script.src = `https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=${checkoutId}`;
            script.async = true;
            
            script.onload = function() {
                showResult('✅ Hyperpay widget script loaded successfully!', 'success');
                console.log('Hyperpay script loaded for checkout:', checkoutId);
            };
            
            script.onerror = function() {
                showResult('❌ Failed to load Hyperpay widget script', 'error');
                console.error('Failed to load Hyperpay script');
            };
            
            document.head.appendChild(script);
        }
        
        // Auto-test on page load
        window.onload = function() {
            console.log('Test page loaded. Click "Test Load Hyperpay Form" to test the AJAX functionality.');
        };
    </script>
</body>
</html> 