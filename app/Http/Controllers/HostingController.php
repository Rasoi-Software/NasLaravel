<?php

namespace App\Http\Controllers;

use App\Models\HostingPackage;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Price;
use Stripe\Product;
use Stripe\Subscription as StripeSubscription;;
use Carbon\Carbon;
use Stripe\Invoice;
use Stripe\PaymentIntent;

class HostingController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $prices = Price::all(['limit' => 10]);

        $stripeProducts = [];

        foreach ($prices->data as $price) {
            $product = Product::retrieve($price->product);

            $stripeProducts[] = [
                'name'     => $product->name,
                'price_id' => $price->id,
                'amount'   => $price->unit_amount,
                'currency' => $price->currency,
            ];
        }

        return view('welcome', compact('stripeProducts'));
    }

    public function subscribe(Request $request)
    {
        //$user = auth()->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'customer_email' => $request->email,
            'line_items' => [[
                'price'    => $request->price_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('checkout.cancel'),
            'metadata' => [
                'user_id' => $request->email,
                'user_name' => $request->name,
            ],
        ]);

        return redirect($checkoutSession->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect('/')->with('error', 'Session ID missing.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Retrieve the checkout session
            $session = StripeSession::retrieve($sessionId);

            // Retrieve the subscription ID from the session
            if (!$session->subscription) {
                return redirect('/')->with('error', 'Subscription not found in session.');
            }

            // Retrieve the Subscription
            $subscription = StripeSubscription::retrieve($session->subscription);
            $stripeCustomerId = $subscription->customer;
           // dd($subscription->items->data[0]->current_period_end);
            if (isset($subscription->items->data[0]->current_period_end)) {
                $timestamp = $subscription->items->data[0]->current_period_end;
                $current_period_end = Carbon::createFromTimestamp($timestamp);
            } else {
                $current_period_end = Carbon::now()->addDays(30);
            }


            // Retrieve the latest invoice from the subscription
            $invoice = Invoice::retrieve($subscription->latest_invoice);
            $customer_email = $invoice->customer_email;
            $customer_name  = $invoice->customer_name;

            // Create user if not exists
            $user = \App\Models\User::firstOrCreate(
                ['email' => $customer_email],
                [
                    'name'     => $customer_name ?? 'Guest User',
                    'password' => bcrypt(str()->random(12)), // temporary random password
                ]
            );


            // Retrieve the payment intent from the invoice

            // Optionally, find the user (you can use metadata if auth() not available)

            // Save to Payment model
            \App\Models\Payment::create([
                'user_id'           => $user->id, // or get from $session->metadata if guest checkout
                'payment_intent_id' => $invoice->id, // Store invoice ID if payment_intent is null
                'payment_method_id' => null, // Not provided in this case
                'amount'            => $invoice->amount_paid,
                'currency'          => $invoice->currency,
                'status'            => $invoice->status, // 'paid'
                'description'       => 'Subscription created via Stripe',
                'response'          => json_encode($invoice),
            ]);

            \App\Models\Subscription::updateOrCreate(
                ['stripe_subscription_id' => $subscription->id],
                [
                    'user_id'              => $user->id,
                    'stripe_customer_id'   => $subscription->customer,
                    'price_id'             => $subscription->items->data[0]->price->id,
                    'amount'               => $subscription->items->data[0]->price->unit_amount / 100,
                    'currency'             => $subscription->currency,
                    'status'               => $subscription->status,
                    'starts_at'            => now(),
                    'ends_at'              => $current_period_end,
                ]
            );



            return view('success', compact('subscription'));
        } catch (\Exception $e) {
            \Log::error('Stripe Checkout Error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Unable to verify payment. ' . $e->getMessage());
        }
    }


    public function cancel(Request $request)
    {
        return view('failed'); // optional
    }
}
