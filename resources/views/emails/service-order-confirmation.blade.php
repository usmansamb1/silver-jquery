<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0066cc;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .payment-info {
            background-color: #e6f7ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your order!</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $data['user']->name }},</p>
            
            <p>Thank you for placing your order with us. Your order has been confirmed and is now being processed.</p>
            
            <h2>Order Details</h2>
            <p><strong>Order Reference:</strong> {{ $data['order']->reference_number }}</p>
            <p><strong>Date:</strong> {{ $data['date'] }}</p>
            <p><strong>Pickup Location:</strong> {{ $data['order']->pickup_location }}</p>
            
            <h2>Services Ordered</h2>
            <table>
                <tr>
                    <th>Service Type</th>
                    <th>Service</th>
                    <th>Vehicle</th>
                    <th>Plate No.</th>
                    <th>Refueling</th>
                    <th>Price</th>
                </tr>
                
                @foreach($data['services'] as $service)
                <tr>
                    <td>{{ $service['type'] }}</td>
                    <td>{{ $service['name'] }}</td>
                    <td>{{ $service['vehicle'] }}</td>
                    <td>{{ $service['plate'] }}</td>
                    <td>SAR {{ number_format($service['refueling'], 2) }}</td>
                    <td>SAR {{ number_format($service['price'], 2) }}</td>
                </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="4">Subtotal</td>
                    <td>SAR {{ number_format($data['order']->subtotal, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4">VAT (15%)</td>
                    <td>SAR {{ number_format($data['order']->vat, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4"><strong>Total</strong></td>
                    <td><strong>SAR {{ number_format($data['order']->total_amount, 2) }}</strong></td>
                </tr>
            </table>
            
            <div class="payment-info">
                <h2>Payment Information</h2>
                <p><strong>Payment Method:</strong> {{ ucfirst($data['payment_method']) }}</p>
                
                @if($data['payment_method'] == 'wallet')
                    <p>Your wallet has been charged SAR {{ number_format($data['order']->total_amount, 2) }}.</p>
                @elseif($data['payment_method'] == 'credit_card')
                    <p>Your credit card has been charged SAR {{ number_format($data['order']->total_amount, 2) }}.</p>
                @endif
                
                <p><strong>Payment Status:</strong> {{ ucfirst($data['order']->payment_status) }}</p>
            </div>
            
            <p>If you have any questions about your order, please contact our customer service team.</p>
            
            <p>Thank you for choosing our services!</p>
            
            <p>Best regards,<br>
            Joil Yaseeir Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Joil Yaseeir. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html> 