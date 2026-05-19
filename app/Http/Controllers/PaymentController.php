<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class PaymentController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripeService = $stripeService;
    }

    /**
     * Crée une Stripe Checkout Session et retourne l'URL
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $client = auth('api')->user();

        if (!$client) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $plan = Plan::findOrFail($request->plan_id);

        try {
            $url = $this->stripeService->createCheckoutSession($client, $plan);

            return response()->json(['checkout_url' => $url]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Liste les payment methods du client
     */
    public function listPaymentMethods()
    {
        $client = auth('api')->user();

        if (!$client || !$client->stripe_customer_id) {
            return response()->json(['payment_methods' => []]);
        }

        $methods = PaymentMethod::all([
            'customer' => $client->stripe_customer_id,
            'type'     => 'card',
        ]);

        return response()->json([
            'payment_methods' => collect($methods->data)->map(fn($pm) => [
                'id'        => $pm->id,
                'brand'     => $pm->card->brand ?? null,
                'last4'     => $pm->card->last4 ?? null,
                'exp_month' => $pm->card->exp_month ?? null,
                'exp_year'  => $pm->card->exp_year ?? null,
            ]),
        ]);
    }
}
