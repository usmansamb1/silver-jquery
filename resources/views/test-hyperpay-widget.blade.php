<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HyperPay Widget Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>HyperPay Widget Test</h1>
        <div id="test-results" class="alert alert-info">
            <h5>Test Results:</h5>
            <ul id="test-list"></ul>
        </div>
        
        <button id="create-checkout" class="btn btn-primary">Create Test Checkout</button>
        
        <div id="widget-container" class="mt-4" style="display: none;">
            <h3>Payment Form</h3>
            <div id="hyperpay-widget"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    let testResults = [];
    
    function addTestResult(message, status = 'info') {
        testResults.push({ message, status, time: new Date().toLocaleTimeString() });
        updateTestResults();
    }
    
    function updateTestResults() {
        const list = document.getElementById('test-list');
        list.innerHTML = '';
        testResults.forEach(result => {
            const li = document.createElement('li');
            li.innerHTML = `<span class="badge bg-${result.status === 'success' ? 'success' : 'warning'}">${result.time}</span> ${result.message}`;
            list.appendChild(li);
        });
    }
    
    document.getElementById('create-checkout').addEventListener('click', async function() {
        addTestResult('Creating HyperPay checkout...', 'info');
        
        try {
            const response = await fetch('/services/booking/hyperpay/get-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: 100.00,
                    brand: 'VISA MASTER',
                    order_id: null
                })
            });
            
            if (response.ok) {
                const data = await response.json();
                addTestResult('✅ Checkout created: ' + data.checkout_id, 'success');
                
                if (data.status === 'success') {
                    // Show widget container
                    document.getElementById('widget-container').style.display = 'block';
                    
                    // Insert the form HTML
                    document.getElementById('hyperpay-widget').innerHTML = data.html;
                    addTestResult('✅ Widget HTML inserted', 'success');
                    
                    // Load the HyperPay script
                    const script = document.createElement('script');
                    script.src = data.script_url;
                    script.onload = function() {
                        addTestResult('✅ HyperPay script loaded', 'success');
                        
                        // Set widget options
                        window.wpwlOptions = {
                            style: 'card',
                            locale: 'en',
                            showPlaceholders: true,
                            brandDetection: true,
                            showCVVHint: true,
                            brands: data.widget_options.brands,
                            onReady: function() {
                                addTestResult('✅ Widget ready - payment fields should be visible', 'success');
                                
                                // Check if form fields exist
                                setTimeout(() => {
                                    const form = document.querySelector('.paymentWidgets');
                                    const inputs = form ? form.querySelectorAll('input') : [];
                                    const wpwlInputs = form ? form.querySelectorAll('[class*="wpwl"]') : [];
                                    
                                    addTestResult(`📊 Form analysis: ${inputs.length} inputs, ${wpwlInputs.length} HyperPay elements`, 'info');
                                    
                                    if (inputs.length === 0) {
                                        addTestResult('❌ NO INPUT FIELDS FOUND - Widget not working!', 'error');
                                    } else {
                                        addTestResult('✅ Input fields found - Widget working', 'success');
                                    }
                                }, 1000);
                            },
                            onError: function(error) {
                                addTestResult('❌ Widget error: ' + JSON.stringify(error), 'error');
                            }
                        };
                        
                        addTestResult('✅ Widget options set', 'success');
                    };
                    
                    script.onerror = function() {
                        addTestResult('❌ Failed to load HyperPay script', 'error');
                    };
                    
                    document.head.appendChild(script);
                } else {
                    addTestResult('❌ Checkout creation failed: ' + data.message, 'error');
                }
            } else {
                addTestResult('❌ HTTP Error: ' + response.status, 'error');
            }
        } catch (error) {
            addTestResult('❌ Error: ' + error.message, 'error');
        }
    });
    </script>
</body>
</html>