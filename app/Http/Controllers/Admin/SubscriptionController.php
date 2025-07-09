<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('user')->latest()->paginate(10);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}
