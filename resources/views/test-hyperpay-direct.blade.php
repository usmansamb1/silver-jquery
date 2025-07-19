<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Direct HyperPay Test</title>
    <script>
        // Set wpwlOptions BEFORE anything else
        var wpwlOptions = {
            style: "card",
            locale: "en",
            showPlaceholders: true,
            onReady: function(){
                console.log("✅ Widget is ready!");
                var form = document.querySelector("form.paymentWidgets");
                if(form) {
                    var inputs = form.querySelectorAll("input");
                    alert("Widget loaded! Found " + inputs.length + " input fields");
                }
            }
        }
    </script>
</head>
<body>
    <h1>Direct HyperPay Integration Test</h1>
    
    <div id="test-area"></div>
    
    <button onclick="runTest()">Run Direct Test</button>
    
    <script>
    async function runTest() {
        try {
            // Step 1: Get checkout ID
            const response = await fetch('/services/booking/hyperpay/get-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    amount: 10.00,
                    brand: 'VISA MASTER'
                })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                // Step 2: Insert form HTML directly
                document.getElementById('test-area').innerHTML = `
                    <h3>Checkout ID: ${data.checkout_id}</h3>
                    <form action="/services/booking/hyperpay/status" class="paymentWidgets" data-brands="VISA MASTER">
                        <!-- HyperPay will inject fields here -->
                    </form>
                `;
                
                // Step 3: Load script directly
                var script = document.createElement('script');
                script.src = 'https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=' + data.checkout_id;
                document.body.appendChild(script);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }
    </script>
</body>
</html>