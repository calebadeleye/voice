<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Local Number Request</title>
</head>
<body>
    <p>Dear Naitalk Team,</p>

    <p>A new <strong>{{ ucfirst($type) }}</strong> number request has been submitted and requires your attention.</p>

    <p><strong>Request Details:</strong></p>
    <ul>
        <li><strong>User:</strong> {{ $user_name }} ({{ $email }})</li>
        <li><strong>Company:</strong> {{ $company_name }}</li>
        <li><strong>Requested Type:</strong> {{ ucfirst($type) }}</li>
        <li><strong>Price:</strong> ₦{{ number_format($price, 2) }}</li>
        <li><strong>VAT:</strong> ₦{{ number_format($vat, 2) }}</li>
        <li><strong>Total Charged:</strong> ₦{{ number_format($total, 2) }}</li>
    </ul>

    <p>Please log in to the admin panel to assign or configure the number on Africa’s Talking.</p>

    <p>Regards,<br>
    <strong>System Notification</strong><br>
    <em>Naitalk AI Customer Assistant</em></p>
</body>
</html>
