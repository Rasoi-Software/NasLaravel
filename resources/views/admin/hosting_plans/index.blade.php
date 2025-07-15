@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Hosting Plans</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Interval</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
            <tr>
                <td>{{ $plan->name }}</td>
                <td>{{ ucfirst($plan->interval) }}</td>
                <td>${{ number_format($plan->amount / 100, 2) }}</td>
                <td>{!! Str::limit(nl2br(e($plan->description)), 60) !!}</td>
                <td>
                    <a href="{{ route('admin.hosting_plans.edit', $plan->id) }}" class="btn btn-sm btn-primary">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection