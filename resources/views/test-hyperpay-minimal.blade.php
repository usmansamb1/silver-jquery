<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Minimal HyperPay Test</title>
</head>
<body>
    <h1>Minimal HyperPay Widget Test</h1>
    
    <button onclick="testHyperPay()">Create Test Payment</button>
    
    <div id="result" style="margin-top: 20px;"></div>
    
    <script>
    async function testHyperPay() {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = 'Creating checkout...';
        
        try {
            // Step 1: Create checkout
            const response = await fetch('/services/booking/hyperpay/get-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    amount: 10.00,
                    brand: 'VISA MASTER',
                    order_id: null
                })
            });
            
            const data = await response.json();
            console.log('Checkout response:', data);
            
            if (data.status === 'success') {
                // Step 2: Create minimal form structure
                resultDiv.innerHTML = `
                    <h3>Checkout ID: ${data.checkout_id}</h3>
                    <form action="/services/booking/hyperpay/status" class="paymentWidgets" data-brands="VISA MASTER">
                        Loading payment form...
                    </form>
                `;
                
                // Step 3: Set wpwlOptions BEFORE loading script
                window.wpwlOptions = {
                    onReady: function() {
                        console.log('✅ Widget ready!');
                        // Check if fields were created
                        const form = document.querySelector('.paymentWidgets');
                        const inputs = form ? form.querySelectorAll('input') : [];
                        resultDiv.innerHTML += `<p style="color: green;">Widget loaded! Found ${inputs.length} input fields</p>`;
                        
                        // List all inputs
                        inputs.forEach((input, i) => {
                            console.log(`Input ${i}:`, input.name, input.type, input.className);
                        });
                    },
                    onError: function(error) {
                        console.error('Widget error:', error);
                        resultDiv.innerHTML += `<p style="color: red;">Error: ${JSON.stringify(error)}</p>`;
                    }
                };
                
                // Step 4: Load HyperPay script
                const script = document.createElement('script');
                script.src = data.script_url;
                script.onload = () => console.log('Script loaded');
                script.onerror = () => console.error('Script failed to load');
                document.head.appendChild(script);
                
            } else {
                resultDiv.innerHTML = `<p style="color: red;">Error: ${data.message}</p>`;
            }
        } catch (error) {
            resultDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
        }
    }
    </script>
</body>
</html>