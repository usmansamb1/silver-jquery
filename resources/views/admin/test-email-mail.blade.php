<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jerei System Test Email</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
        .email-container { background: #fff; border-radius: 5px; padding: 20px; }
        .header { font-size: 1.25rem; margin-bottom: 10px; color: #333; }
        .content { font-size: 1rem; color: #555; }
        .footer { margin-top: 20px; font-size: 0.875rem; color: #999; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">Jerei System Test Email</div>
        <div class="content">
            <p>{{ $body }}</p>
        </div>
        <div class="footer">
            <p>If you did not request this email, please ignore.</p>
        </div>
    </div>
</body>
</html> 