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
    /**
     * GET /api/payments/{id}/invoice
     * Télécharge le PDF de la facture Stripe.
     */
    public function invoice(Request $request, int $id)
    {
        $payment = Payment::where('client_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if (!$payment->stripe_invoice_id) {
            return response()->json([
                'error' => 'Aucune facture disponible pour ce paiement.'
            ], 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $invoice = Invoice::retrieve($payment->stripe_invoice_id);

        if (!$invoice->invoice_pdf) {
            return response()->json([
                'error' => 'PDF non disponible.'
            ], 404);
        }

        $pdfContent = file_get_contents($invoice->invoice_pdf);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>
                "attachment; filename=\"facture-{$payment->id}.pdf\"",
        ]);
    }
}
