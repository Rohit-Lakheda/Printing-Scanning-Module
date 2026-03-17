<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Event Access Credentials</title>
</head>
<body>
    <p>Dear {{ $user->Name }},</p>

    <p>Thank you for registering. Below are your login credentials:</p>

    <ul>
        <li><strong>Username:</strong> {{ $credential->username }}</li>
        @if($rawPassword)
            <li><strong>Password:</strong> {{ $rawPassword }}</li>
        @else
            <li><strong>Password:</strong> (unchanged)</li>
        @endif
    </ul>

    <p>
        @if(!is_null($credential->max_devices))
            You can log in from up to <strong>{{ $credential->max_devices }}</strong> device(s) at the same time.
        @else
            You can log in from an unlimited number of devices.
        @endif
    </p>

    <p>Please keep this information confidential.</p>

    <p>Best regards,<br>Event Team</p>
</body>
</html>

