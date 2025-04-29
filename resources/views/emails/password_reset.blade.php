<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 10px;">
        <h2 style="color: #333;">Reset Your Password</h2>
        <p style="color: #555;">You requested to reset your password. Click the button below:</p>

        <a href="{{ $resetUrl }}" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #e3342f; color: white; text-decoration: none; border-radius: 5px;">
            Reset Password
        </a>

        <p style="margin-top: 30px; font-size: 12px; color: #888;">If you did not request this, you can safely ignore this email.</p>
    </div>
</body>
</html>
