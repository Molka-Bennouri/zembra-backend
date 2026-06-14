<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Stripe\Invoice;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    /**
     * GET /api/payments
     * Retourne les paiements de l'utilisateur connecté.
     */
    public function index(Request $request)
    {
        $payments = Payment::where('client_id', $request->user()->id)
            ->orderByDesc('paid_at')
            ->orderByDesc('created_at')
            ->get();

        return PaymentResource::collection($payments);
    }

    /**
     * POST /api/checkout
     * Crée une session Stripe Checkout.
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|exists:plans,id'
            ]);

            $plan = Plan::findOrFail($request->plan_id);

            $url = app(\App\Services\StripeService::class)
                ->createCheckoutSession($request->user(), $plan);

            return response()->json([
                'checkout_url' => $url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
