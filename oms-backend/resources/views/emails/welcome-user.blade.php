<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>

    <p>Welcome to our platform 🎉</p>

    <p>Your account has been created successfully.</p>

    <p>
        Email: {{ $user->email }} <br>
        Role: {{ ucfirst($user->role) }}
    </p>

    <p>Thank you,<br>Team</p>
</body>
</html>
