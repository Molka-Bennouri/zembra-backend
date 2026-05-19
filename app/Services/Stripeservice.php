<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeService
{

    /**
     * Récupère ou crée un Stripe Customer pour le client.
     */
    public function resolveCustomer(Client $client): string
    {
        if ($client->stripe_customer_id) {
            return $client->stripe_customer_id;
        }

        $customer = Customer::create([
            'email' => $client->email,
            'name'  => $client->full_name,
            'metadata' => ['client_id' => $client->id],
        ]);

        $client->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }


    /**
     * Crée une Stripe Checkout Session et retourne l'URL de redirection.
     */
    public function createCheckoutSession(Client $client, Plan $plan): string
    {
        $customerId = $this->resolveCustomer($client);

        $session = Session::create([
            'customer'   => $customerId,
            'mode'       => 'subscription',           // one-time payment
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

    /**
     * Vérifie la signature du webhook Stripe et retourne l'événement.
     *
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    public function constructWebhookEvent(string $payload, string $sigHeader): \Stripe\Event
    {
        return Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    }


    /**
     * Traite checkout.session.completed :
     * crée la souscription + enregistre le paiement.
     */
    public function handleCheckoutCompleted(array $sessionData, string $eventId): void
    {
        // Idempotence : ignorer si cet événement a déjà été traité
        if (Payment::where('stripe_event_id', $eventId)->exists()) {
            Log::info('Stripe event already processed', ['event_id' => $eventId]);
            return;
        }

        $clientId = (int) $sessionData['metadata']['client_id'];
        $planId   = (int) $sessionData['metadata']['plan_id'];

        $client = Client::findOrFail($clientId);
        $plan   = Plan::findOrFail($planId);

        // Crée la souscription
        Subscription::create([
            'client_id'              => $client->id,
            'plan_id'                => $plan->id,
            'stripe_subscription_id' => $sessionData['subscription'],   // ✅ corrigé
            'stripe_price_id'        => $plan->stripe_price_id,         // ✅ ajouté
            'status'                 => 'active',
            'starts_at'              => now(),
            'ends_at'                => now()->addDays($plan->duration_days),
            'current_period_end'     => now()->addDays($plan->duration_days), // ✅ ajouté
        ]);

        // Enregistre le paiement
        Payment::create([
            'client_id'                => $client->id,
            'stripe_event_id'          => $eventId,                      // ✅ ajouté
            'stripe_payment_intent_id' => $sessionData['payment_intent'] ?? null,
            'stripe_invoice_id'        => $sessionData['invoice'] ?? null,
            'amount'                   => $sessionData['amount_total'] / 100,
            'status'                   => 'succeeded',
            'paid_at'                  => now(),
            'description'              => "Plan {$plan->name}",
        ]);
    }

    /**
     * Traite payment_intent.payment_failed :
     * enregistre le paiement échoué.
     */
    public function handlePaymentFailed(array $intentData): void
    {
        $customerId = $intentData['customer'];
        $client     = Client::where('stripe_customer_id', $customerId)->first();

        if (!$client) return;

        Payment::create([
            'client_id'               => $client->id,
            'stripe_payment_intent_id'=> $intentData['id'],
            'amount'                  => $intentData['amount'] / 100,
            'status'                  => 'failed',
            'description'             => $intentData['description'] ?? 'Payment failed',
        ]);
    }
}
