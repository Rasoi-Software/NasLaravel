@component('mail::message')
# Subscription Reminder

Hello {{ $subscription->user->name }},

@php
    $endsAt = \Carbon\Carbon::parse($subscription->ends_at);
@endphp

@if($endsAt->isPast())
Your subscription **has expired** on **{{ $endsAt->format('d M Y H:i') }}**.
@else
Your subscription is expiring on **{{ $endsAt->format('d M Y H:i') }}**.
@endif

Please renew before it expires.
Thanks,<br>
{{ config('app.name') }}
@endcomponent
