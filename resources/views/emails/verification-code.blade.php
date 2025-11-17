<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { font-size: 32px; font-weight: bold; color: #4F46E5; letter-spacing: 5px; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Email Verification Code</h2>
        <p>Your verification code is:</p>
        <p class="code">{{ $code }}</p>
        <p>This code will expire in 15 minutes.</p>
        <p>If you didn't request this code, please ignore this email.</p>
        <div class="footer">
            <p>© 2025 Post Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
