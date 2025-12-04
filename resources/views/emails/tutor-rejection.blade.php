<!DOCTYPE html>
<html>

<head>
    <title>Your Tutor Request Has Been Rejected</title>
</head>

<body>
    <p>Dear {{ $tutor->user->name }},</p>
    <p>Your tutor request has been rejected. Reason: {{ $reason }}</p>
    <p>Thank you,</p>
    <p>The Team</p>
</body>

</html>
