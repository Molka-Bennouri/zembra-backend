<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\StripeService;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = app(StripeService::class)->constructWebhookEvent($payload, $sigHeader);
        } catch (\Exception $e) {
            Log::error('Stripe signature invalid', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'invalid signature'], 400);
        }

        $type   = $event->type;
        $object = $event->data->object->toArray();

        Log::info('Stripe event received', ['type' => $type, 'id' => $event->id]);

        try {
            match ($type) {
                'checkout.session.completed'     => app(StripeService::class)->handleCheckoutCompleted($object),
                'payment_intent.payment_failed'  => app(StripeService::class)->handlePaymentFailed($object),
                default                          => Log::info('Stripe event ignored', ['type' => $type]),
            };
        } catch (\Exception $e) {
            Log::error('Stripe webhook handler failed', [
                'type'  => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Retourner 500 → Stripe réessaie automatiquement
            return response()->json(['error' => 'handler error'], 500);
        }

        return response()->json(['received' => true], 200);
    }
}
