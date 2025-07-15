@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Choose Your Hosting Plan</h2>
        <p class="text-muted">Flexible pricing for your needs</p>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="billingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="monthly" aria-selected="true">Monthly</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly" type="button" role="tab" aria-controls="yearly" aria-selected="false">Yearly</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="billingTabsContent">
        <!-- Monthly -->
        <div class="tab-pane fade show active" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
            <div class="row g-4 justify-content-center">
                @foreach ($stripeProducts['monthly'] as $product)
                <div class="col-md-4">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body text-center">
                            <h5 class="fw-bold">{{ $product['name'] }}</h5>
                            <h3 class="my-3">${{ number_format($product['amount'] / 100, 2) }} <small class="text-muted fs-6">/year</small></h3>
                            <p class=""> {!! Str::limit(nl2br(e($product->description)), 60) !!}</p>
                            
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
        </div>

        <!-- Yearly -->
        <div class="tab-pane fade" id="yearly" role="tabpanel" aria-labelledby="yearly-tab">
            <div class="row g-4 justify-content-center">
                @foreach ($stripeProducts['yearly'] as $product)
                <div class="col-md-4">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body text-center">
                            <h5 class="fw-bold">{{ $product['name'] }}</h5>
                            <h3 class="my-3">${{ number_format($product['amount'] / 100, 2) }} <small class="text-muted fs-6">/year</small></h3>
                            <p class=""> {!! Str::limit(nl2br(e($product->description)), 60) !!}</p>

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
        </div>
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

<script>
    function submitSubscribeForm() {
        const name = document.getElementById('modal-name').value;
        const email = document.getElementById('modal-email').value;

        document.querySelectorAll('#subscribe-form').forEach(form => {
            form.querySelector('#input-name').value = name;
            form.querySelector('#input-email').value = email;
            form.submit();
        });
    }
</script>
@endsection