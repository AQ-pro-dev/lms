<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>

<body>
    <p>Hello,</p>
    <p>You requested a password reset. Click the link below to reset your password:</p>
    <a href="{{ $resetUrl }}"
        style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none;">
        Reset Password
    </a>
    <p>If you didn't request a password reset, you can ignore this email.</p>
</body>

</html>
