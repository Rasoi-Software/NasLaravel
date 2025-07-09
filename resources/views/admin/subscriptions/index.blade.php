@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                        <h6 class="text-white text-capitalize mb-0">User Subscriptions</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table table-bordered table-striped align-items-center mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th class="text-center">Plan ID</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Currency</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Starts At</th>
                                    <th class="text-center">Ends At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subscriptions as $index => $sub)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex px-2 py-1 align-items-center">
                                                <div>
                                                    <img src="{{ $sub->user->profile_image ?? 'https://ui-avatars.com/api/?name='.urlencode($sub->user->name) }}" class="avatar avatar-sm me-3 border-radius-lg" alt="user">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $sub->user->name ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $sub->user->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $sub->price_id }}</td>
                                        <td class="text-center">₹{{ number_format($sub->amount, 2) }}</td>
                                        <td class="text-center">{{ strtoupper($sub->currency) }}</td>
                                        <td class="text-center">
                                            @if($sub->status === 'active')
                                                <span class="badge bg-gradient-success">{{ ucfirst($sub->status) }}</span>
                                            @else
                                                <span class="badge bg-gradient-warning text-dark">{{ ucfirst($sub->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-xs">{{ \Carbon\Carbon::parse($sub->starts_at)->format('d M, Y') }}</td>
                                        <td class="text-center text-xs">{{ \Carbon\Carbon::parse($sub->ends_at)->format('d M, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No subscriptions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3 px-3">
                            {{ $subscriptions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection
