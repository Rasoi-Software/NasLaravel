@php
    $isRenewal = $is_renewal ?? false;
@endphp

<div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 600px; margin: auto;">

    <p>Hi <strong>{{ $customer_name }}</strong>,</p>

    <p>
        {!! $isRenewal 
            ? "🎉 Great news — your <strong>$plan_name - $duration </strong> plan was <strong>renewed</strong> on <strong>$renewal_date</strong> for <strong>$amount_paid</strong>, charged to your card ending in ••••<strong>$card_last4</strong>." 
            : "👋 Welcome! Your <strong>$plan_name - $duration </strong>  plan is now <strong>active</strong> as of <strong>$renewal_date</strong> for <strong>$amount_paid</strong>, charged to your card ending in ••••<strong>$card_last4</strong>." 
        !!}
    </p>

    <p>
        📄 <strong>Plan Description:</strong><br/>
        {!! $description !!}
    </p>

    <hr style="border: none; border-top: 1px solid #eee;">

    <p>
        🔗 <strong>Quick Links:</strong><br>
        • <a href="{{ $billing_url }}" style="color: #1a73e8;">Billing & Invoices</a><br>
        • 📧 24/7 Support: <a href="mailto:admin@doitdigital.agency" style="color: #1a73e8;">admin@doitdigital.agency</a>
    </p>

    <p>
        We {{ $isRenewal ? 'appreciate your continued trust' : 'are excited to have you on board' }} with <strong>Do It Digital</strong> — here’s to a smooth, worry-free hosting experience!
    </p>

    <p>
        Sincerely,<br/>
        The Do It Digital Team
    </p>
</div>
