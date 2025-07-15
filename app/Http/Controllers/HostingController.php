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
use App\Models\HostingPlan;

class HostingController extends Controller
{
    public function index()
    {
        $plans = HostingPlan::all();
        return view('admin.hosting_plans.index', compact('plans'));
    }
    public function listforuser()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $prices = Price::all(['limit' => 100]);

        $stripeProducts = [
            'monthly' => [],
            'yearly' => [],
        ];

        foreach ($prices->data as $price) {
            $product = Product::retrieve($price->product);
            $interval = $price->recurring->interval;

            if (in_array($interval, ['month', 'year'])) {
                HostingPlan::firstOrCreate(
                    ['stripe_price_id' => $price->id], // match by this
                    [
                        'stripe_product_id' => $product->id,
                        'name'        => $product->name,
                        'description' => $product->description,
                        'interval'    => $interval === 'month' ? 'monthly' : 'yearly',
                        'amount'      => $price->unit_amount,
                        'currency'    => $price->currency,
                    ]
                );
            }
        }


        // Now fetch from DB instead of Stripe
        $plans = HostingPlan::all()->groupBy('interval');

        return view('welcome', [
            'stripeProducts' => [
                'monthly' => $plans['monthly'] ?? [],
                'yearly'  => $plans['yearly'] ?? [],
            ]
        ]);
    }



    public function subscribe(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $plan = HostingPlan::where('stripe_price_id', $request->price_id)->firstOrFail();

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $request->email,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('checkout.cancel'),
            'metadata' => [
                'guest_email' => $request->email,
                'guest_name'  => $request->name,
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

            $isRenewal = false;

            $existingSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('ends_at', '>=', now()) // still active
                ->first();

            if ($existingSubscription) {
                $isRenewal = true;
            }

            $priceId = $subscription->items->data[0]->price->id;

            // Get the hosting plan
            $hostingPlan = \App\Models\HostingPlan::where('stripe_price_id', $priceId)->first();

            if (!$hostingPlan) {
                \Log::error("HostingPlan not found for price_id: {$priceId}");
                return redirect('/')->with('error', 'Hosting plan not found.');
            }

            // prepare email variables
            $planName = $hostingPlan->name;
            $description = $hostingPlan->description;
            if ($hostingPlan->interval === 'yearly') {
                $duration = '12-Month';
            } elseif ($hostingPlan->interval === 'monthly') {
                $duration = '1-Month';
            } else {
                $duration = ucfirst($hostingPlan->interval); // fallback
            }

            \Mail::send('emails.subscription', [
                'customer_name' => $customer_name,
                'renewal_date'  => now()->toFormattedDateString(),
                'amount_paid'   => number_format($invoice->amount_paid / 100, 2) . ' ' . strtoupper($invoice->currency),
                'card_last4'    => $session->payment_method_types[0] ?? '****',
                'billing_url'   => url('/users/payments'),
                'is_renewal'    => $isRenewal,
                'plan_name'     => $planName,
                'duration'      => $duration,
                'description'   => $description,
            ], function ($message) use ($customer_email, $isRenewal) {
                $message->to($customer_email)
                        ->subject($isRenewal ? 'Your Hosting Plan Has Been Renewed!' : 'Welcome! Your Hosting Plan is Active!');
            });



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

    public function edit($id)
    {
        $plan = HostingPlan::findOrFail($id);
        return view('admin.hosting_plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = HostingPlan::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'description' => 'nullable|string',
        ]);

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.hosting_plans.index')->with('success', 'Hosting Plan updated successfully.');
    }
}
