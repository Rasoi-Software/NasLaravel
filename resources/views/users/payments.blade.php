@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Payments</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Payment Intent</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Status</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Invoice</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->payment_intent_id }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ strtoupper($payment->currency) }}</td>
                    <td>{{ $payment->status }}</td>
                    <td>{{ $payment->description }}</td>
                    <td>{{ $payment->created_at }}</td>
                    <td>
                        <a href="{{ route('users.payments.invoice', $payment->id) }}" 
                           class="btn btn-sm btn-primary" target="_blank">
                            Download PDF
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
