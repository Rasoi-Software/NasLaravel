<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hosting Reminder</title>
</head>
<body>
<p>Hi {{ $user->name }},</p>

@php
    use Carbon\Carbon;
    $endsAt = Carbon::parse($subscription->ends_at);
@endphp

@if ($days == 30)
    <p>This is a friendly reminder that your {{ $planName }} is due in <strong>30 days</strong>.
    If payment is not received by <strong>{{ $endsAt->format('Y-m-d') }}</strong>,
    your hosting account will be suspended, which may result in website downtime and loss of access to all hosted files.</p>

    <p><strong>Invoice Details:</strong><br>
    Plan: {{ $planName }}<br>
    Due Date: {{ $endsAt->format('Y-m-d') }} (30 days from today)</p>

    <p>To ensure uninterrupted service, please submit your payment by the due date.
    Accounts unpaid beyond 7 days may be permanently canceled and deleted, and may be unretrievable.</p>

    <p>If you have any questions, contact us at <a href="mailto:billing@doitdigital.com">billing@doitdigital.com</a></p>

@elseif ($days == 14)
    <p>This is an important reminder that your {{ $planName }} is due in <strong>14 days</strong>.
    If payment is not received by <strong>{{ $endsAt->format('Y-m-d') }}</strong>,
    your hosting account will be suspended, which may result in website downtime and loss of access to all hosted files.</p>

    <p><strong>Invoice Details:</strong><br>
    Plan: {{ $planName }}<br>
    Due Date: {{ $endsAt->format('Y-m-d') }} (14 days from today)</p>

    <p>Please submit your payment by the due date to avoid any disruption.
    Accounts unpaid beyond 7 days may be permanently canceled and deleted, and may be unretrievable.</p>

    <p>If you need assistance, our team is here 24/7 at <a href="mailto:billing@doitdigital.com">billing@doitdigital.com</a></p>

@elseif ($days == 7)
    <p>This is a <strong>critical</strong> reminder that your {{ $planName }} is due in <strong>7 days</strong>.
    If payment is not received by <strong>{{ $endsAt->format('Y-m-d') }}</strong>,
    your hosting account will be suspended — and with it, your website and all hosted files will go offline.</p>

    <p><strong>Invoice Details:</strong><br>
    Plan: {{ $planName }}<br>
    Due Date: {{ $endsAt->format('Y-m-d') }} (7 days from today)</p>

    <p>To avoid service interruption, please submit your payment by the due date.
    Accounts unpaid beyond 7 days may be permanently canceled and deleted, and may be unretrievable.</p>

    <p>For questions or help, reach us anytime at <a href="mailto:billing@doitdigital.com">billing@doitdigital.com</a></p>

@elseif ($days == 3)
    <p>This is an <strong>urgent</strong> notice that your {{ $planName }} is due in <strong>3 days</strong>.
    If payment is not received by <strong>{{ $endsAt->format('Y-m-d') }}</strong>,
    your hosting account will be suspended immediately, and your website will go offline.</p>

    <p><strong>Invoice Details:</strong><br>
    Plan: {{ $planName }}<br>
    Due Date: {{ $endsAt->format('Y-m-d') }} (3 days from today)</p>

    <p>Please take action now to keep your site live.</p>

    <p>If you have any questions or need assistance, our team is standing by at <a href="mailto:billing@doitdigital.com">billing@doitdigital.com</a></p>

@elseif ($days == 0)
    <p><strong>Today is the due date</strong> for your {{ $planName }} hosting renewal.
    If payment isn’t received by end of day, your account will be suspended, and your website will go offline.</p>

    <p><strong>Invoice Details:</strong><br>
    Plan: {{ $planName }}<br>
    Due Date: Today ({{ $endsAt->format('Y-m-d') }})</p>

    <p>To maintain uninterrupted service, please submit payment now.
    Accounts unpaid 7 days beyond today will be permanently canceled and deleted, with all data irretrievable.</p>

    <p>If you need help, email us at <a href="mailto:billing@doitdigital.com">billing@doitdigital.com</a></p>

@elseif ($days == -7)
    <p>We’re sorry to say your {{ $planName }} hosting plan expired on <strong>{{ $endsAt->format('Y-m-d') }}</strong>, and your website is currently offline.</p>

    <p>You have 7 days (until <strong>{{ $endsAt->copy()->addDays(7)->format('Y-m-d') }}</strong>) to:</p>

    <ol>
        <li>Pay your outstanding invoice</li>
        <li>Update your payment method</li>
    </ol>

    <p>After that, all files and databases will be permanently deleted and cannot be recovered.</p>

    <p>If there’s anything we can do to help you restore service, please contact us immediately at <a href="mailto:billing@doitdigital.com">billing@doitdigital.com</a></p>

    <p>We hope to welcome you back soon.</p>
@endif

</body>
</html>
