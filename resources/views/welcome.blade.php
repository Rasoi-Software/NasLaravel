@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="text-center mb-4">Choose Your Hosting Plan</h2>

     <div class="row justify-content-center mb-4">
        <div class="col-md-4">
            <label for="billing-cycle" class="form-label">Billing Cycle</label>
            <select class="form-select" id="billing-cycle">
                <option value="monthly" selected>Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
    </div>

    <div class="row justify-content-center">
        @foreach ($stripeProducts as $product)
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-center bg-dark text-white">
                    <h4>{{ $product['name'] }}</h4>
                </div>
                <div class="card-body text-center">
                    <h2>${{ number_format($product['amount'] / 100, 2) }}</h2>
                    <p class="text-muted">per month</p>
                    <ul class="list-unstyled mt-3 mb-4">
                        <li>Includes Hosting + Email + SSL</li>
                        <li>Auto-renews monthly</li>
                        <li>Cancel anytime</li>
                    </ul>
                    <form id="subscribe-form" action="{{ route('hosting.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="price_id" value="{{ $product['price_id'] }}">
                        <input type="hidden" name="name" id="input-name">
                        <input type="hidden" name="email" id="input-email">

                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal -->
    <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userInfoModalLabel">Enter Your Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="modal-name" placeholder="Enter your name">
                    </div>
                    <div class="mb-3">
                        <label for="modal-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="modal-email" placeholder="Enter your email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitSubscribeForm()">Proceed</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection