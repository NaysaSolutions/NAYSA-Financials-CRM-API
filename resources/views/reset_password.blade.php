<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body>
    <h1>Password Reset Request</h1>
    <p>Hello {{ $username }},</p>
    <p>You have requested to reset your password. Please click the link below to reset your password:</p>
    <a href="{{ $resetLink }}">Reset Password</a>

    <p>If you did not request a password reset, please ignore this email.</p>
    <p>Thank you, <br> Your App Team</p>
</body>
</html>
