<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
</head>
<body>
    <h2>Hello, {{ $user->name }}</h2>
    <p>Your OTP code is:</p>
    <h1>{{ $otp }}</h1>
    <p>This code will expire in 10 minutes.</p>
</body>
</html>
