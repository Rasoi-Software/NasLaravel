@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Subscriptions</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subscription ID</th>
                <th>Customer ID</th>
                <th>Price ID</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Status</th>
                <th>Starts At</th>
                <th>Ends At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->id }}</td>
                    <td>{{ $subscription->stripe_subscription_id }}</td>
                    <td>{{ $subscription->stripe_customer_id }}</td>
                    <td>{{ $subscription->price_id }}</td>
                    <td>{{ $subscription->amount }}</td>
                    <td>{{ strtoupper($subscription->currency) }}</td>
                    <td>{{ $subscription->status }}</td>
                    <td>{{ $subscription->starts_at }}</td>
                    <td>{{ $subscription->ends_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
