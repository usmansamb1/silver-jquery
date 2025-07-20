<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>JoilYaseeir - Your OTP Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #eee;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 30px 20px;
        }
        .otp-container {
            text-align: center;
            padding: 20px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #2c3e50;
            background-color: #f8f9fa;
            padding: 10px 25px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
            display: inline-block;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #888;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .warning {
            padding: 15px;
            background-color: #fff3cd;
            color: #856404;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 14px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
            }
            .otp-code {
                font-size: 28px;
                letter-spacing: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="JoilYaseeir Logo" class="logo">
            <h2>JoilYaseeir Authentication</h2>
        </div>
        
        <div class="content">
            <h3>Hello,</h3>
            <p>Your one-time password (OTP) for JoilYaseeir authentication is:</p>
            
            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <p>This code will expire in 5 minutes. Please do not share this code with anyone.</p>
            
            <div class="warning">
                <strong>Security Notice:</strong> If you didn't request this code, please ignore this email or contact support if you're concerned about your account security.
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Joil Yaseeir. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html> 