<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $payment->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .invoice-box {
            border: 1px solid #eee;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Invoice #{{ $payment->id }}</h2>
        <p><strong>Payment Intent:</strong> {{ $payment->payment_intent_id }}</p>
        <p><strong>Amount:</strong> {{ $payment->amount }} {{ strtoupper($payment->currency) }}</p>
        <p><strong>Status:</strong> {{ $payment->status }}</p>
        <p><strong>Description:</strong> {{ $payment->description }}</p>
        <p><strong>Date:</strong> {{ $payment->created_at }}</p>
    </div>
</body>
</html>
