<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
        ]);

        $user = auth()->user();

        // Create or retrieve Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        // Create subscription
        $subscription = StripeSubscription::create([
            'customer' => $user->stripe_customer_id,
            'items' => [
                ['price' => $request->price_id]
            ],
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        // Store in DB (optional)
        Subscription::create([
            'user_id' => $user->id,
            'stripe_subscription_id' => $subscription->id,
            'status' => $subscription->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subscription created successfully.',
            'subscription' => $subscription
        ]);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        switch ($event->type) {
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $userId = $invoice->metadata->user_id ?? null;
                Log::info("Payment failed for user ID: $userId");

                // TODO: Call email provider to send retry/failure email
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $userId = $invoice->metadata->user_id ?? null;
                Log::info("Payment succeeded for user ID: $userId");

                // Optional: Update DB if needed
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }
}
