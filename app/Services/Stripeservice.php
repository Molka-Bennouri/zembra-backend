<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{
    public function resolveCustomer(User $client): string
    {
        if ($client->stripe_customer_id) {
            return $client->stripe_customer_id;
        }

        $customer = Customer::create([
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

        $session = Session::create([
            'customer'   => $customerId,
            'mode'       => 'subscription',
            'line_items' => [[
                'price'    => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => config('app.frontend_url') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => config('app.frontend_url') . '/payment/cancel',
            'metadata'    => [
                'client_id' => $client->id,
                'plan_id'   => $plan->id,
            ],
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

    public function handleCheckoutCompleted(array $sessionData, string $eventId): void
    {
        if (Payment::where('stripe_event_id', $eventId)->exists()) {
            Log::info('Stripe event already processed', ['event_id' => $eventId]);
            return;
        }

        $clientId = (int) $sessionData['metadata']['client_id'];
        $planId   = (int) $sessionData['metadata']['plan_id'];

        $client = User::findOrFail($clientId);  // ← Client → User
        $plan   = Plan::findOrFail($planId);

        Subscription::create([
            'client_id'              => $client->id,
            'plan_id'                => $plan->id,
            'stripe_subscription_id' => $sessionData['subscription'],
            'status'                 => 'active',
            'starts_at'              => now(),
            'ends_at'                => now()->addDays($plan->duration_days),
        ]);

        Payment::create([
            'client_id'                => $client->id,
            'stripe_event_id'          => $eventId,
            'stripe_payment_intent_id' => $sessionData['payment_intent'] ?? null,
            'stripe_invoice_id'        => $sessionData['invoice'] ?? null,
            'amount'                   => $sessionData['amount_total'] / 100,
            'status'                   => 'succeeded',
            'paid_at'                  => now(),
            'description'              => "Plan {$plan->name}",
        ]);
    }

    public function handlePaymentFailed(array $intentData): void
    {
        $customerId = $intentData['customer'];
        $client     = User::where('stripe_customer_id', $customerId)->first();  // ← Client → User

        if (!$client) return;

        Payment::create([
            'client_id'                => $client->id,
            'stripe_payment_intent_id' => $intentData['id'],
            'amount'                   => $intentData['amount'] / 100,
            'status'                   => 'failed',
            'description'              => $intentData['description'] ?? 'Payment failed',
        ]);
    }
}
