@component('mail::message')
# Subscription Reminder

Hello {{ $subscription->user->name }},

Your subscription is expiring on **{{ \Carbon\Carbon::parse($subscription->ends_at)->format('d M Y H:i') }}**.

Please renew before it expires.
Thanks,<br>
{{ config('app.name') }}
@endcomponent
