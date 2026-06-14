<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function resolveCustomer(User $client): string
    {
        if ($client->stripe_customer_id) {
            return $client->stripe_customer_id;
        }

        $customer = $this->stripe->customers->create([
            'email'    => $client->email,
            'name'     => $client->full_name,
            'metadata' => ['client_id' => $client->id],
        ]);

        $client->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    public function createCheckoutSession(User $client, Plan $plan): string
    {
        $customerId = $this->resolveCustomer($client);

        $session = $this->stripe->checkout->sessions->create([
            'customer' => $customerId,
            'mode'     => 'subscription',
            'metadata' => [
                'client_id' => $client->id,
                'plan_id'   => $plan->id,
            ],
            'subscription_data' => [
                'metadata' => [
                    'client_id' => $client->id,
                    'plan_id'   => $plan->id,
                ],
            ],
            'line_items' => [[
                'price'    => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => config('app.frontend_url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => config('app.frontend_url') . '/payment/cancel',
        ]);

        return $session->url;
    }

    public function constructWebhookEvent(string $payload, string $sigHeader): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    }

    public function handleCheckoutCompleted($event): void
    {
        $session  = $event->data->object;
        $clientId = $session->metadata->client_id ?? null;
        $planId   = $session->metadata->plan_id   ?? null;

        if (!$clientId || !$planId) {
            Log::warning('Missing metadata, skipping', ['event_id' => $event->id]);
            return;
        }

        if (Payment::where('stripe_event_id', $event->id)->exists()) {
            return;
        }

        $client = User::find($clientId);
        $plan   = Plan::find($planId);

        if (!$client || !$plan) return;

        Subscription::create([
            'client_id'              => $client->id,
            'plan_id'                => $plan->id,
            'stripe_subscription_id' => $session->subscription,
            'status'                 => 'active',
            'starts_at'              => now(),
            'ends_at'                => now()->addDays($plan->duration_days),
        ]);

        $payment=Payment::create([
            'client_id'                => $client->id,
            'stripe_event_id'          => $event->id,
            'stripe_payment_intent_id' => $this->stripe->invoices->retrieve($session->invoice)->payment_intent ?? null,
            'stripe_invoice_id'        => $session->invoice        ?? null,
            'amount'                   => ($session->amount_total  ?? 0) / 100,
            'status'                   => 'succeeded',
            'paid_at'                  => now(),
            'description'              => "Plan {$plan->name}",
        ]);

        Log::info('Payment created successfully', [
            'client_id' => $clientId,
            'plan_id'   => $planId,
        ]);
        Notification::create([
            'user_id'    => $client->id,
            'type'       => 'success',
            'message'    => "Your payment of {$payment->amount} € for the {$plan->name} plan has been confirmed.",
            'payment_id' => $payment->id,
            'seen'       => false,
        ]);
    }

    public function handlePaymentFailed($intent): void
    {
        $customerId = $intent['customer'] ?? null;
        $client     = User::where('stripe_customer_id', $customerId)->first();
        if (!$client) return;

        $payment=Payment::create([
            'client_id'                => $client->id,
            'stripe_payment_intent_id' => $intent['id'],
            'amount'                   => ($intent['amount'] ?? 0) / 100,
            'status'                   => 'failed',
            'description'              => $intent['description'] ?? 'Payment failed',
        ]);
        Notification::create([
            'user_id'    => $client->id,
            'type'       => 'error',
            'message'    => "Your payment of {$payment->amount} € has failed. Please check your payment method.",
            'payment_id' => $payment->id,
            'seen'       => false,
        ]);
    }


}
