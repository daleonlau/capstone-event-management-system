<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
</head>
<body>
    <h2>Payment Successful 🎉</h2>

    <p>Hi <strong>{{ $student->firstname }} {{ $student->lastname }}</strong>,</p>

    <p>We are happy to inform you that your payment has been successfully recorded.</p>

    <p><strong>Event:</strong> {{ $event->event_name }}</p>
    <p><strong>Amount Paid:</strong> ₱{{ number_format($event->event_fee, 2) }}</p>
    <p><strong>Status:</strong> Paid</p>

    <p>Thank you for completing your payment on time.</p>

    <br>
    <p>Best regards,</p>
    <p><strong>LITS Treasurer</strong></p>
</body>
</html>
