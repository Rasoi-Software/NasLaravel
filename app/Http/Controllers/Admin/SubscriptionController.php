<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Mail\ReminderMail;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('user')->latest()->paginate(10);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

   public function sendReminder($id)
  {
    $subscription = Subscription::findOrFail($id);

    // You can send to the user or admin or both
    $email = $subscription->user->email;  // assuming Subscription belongsTo User

    Mail::to($email)->send(new ReminderMail($subscription));

    return back()->with('success', 'Reminder sent!');
  }
}
