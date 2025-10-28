<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Local Number Request</title>
</head>
<body>
    <p>Dear {{ $user_name }},</p>

    <p>We’ve received your request for a <strong>{{ ucfirst($type) }}</strong> number.</p>

    <p>Here are the details:</p>
    <ul>
        <li>Company: {{ $company_name }}</li>
        <li>Price: ₦{{ number_format($price, 2) }}</li>
        <li>VAT: ₦{{ number_format($vat, 2) }}</li>
        <li>Total: ₦{{ number_format($total, 2) }}</li>
    </ul>

    <p>Your request is being processed. You will receive another email once your number is ready,this ususally take 2 to 4 days.</p>

    <p>Best regards,<br>
    <strong>The Naitalk Team</strong></p>
</body>
</html>
