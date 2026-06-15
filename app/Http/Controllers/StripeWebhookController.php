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
            $event = app(StripeService::class)
                ->constructWebhookEvent($payload, $sigHeader);
        } catch (\Exception $e) {
            Log::error('Stripe signature invalid', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'invalid signature'], 400);
        }

        Log::info('Stripe event received', [
            'type' => $event->type,
            'id'   => $event->id
        ]);

        try {
            match ($event->type) {

                'checkout.session.completed' =>
                app(StripeService::class)
                    ->handleCheckoutCompleted($event),

                'payment_intent.payment_failed' =>
                app(StripeService::class)
                    ->handlePaymentFailed($event->data->object->toArray()),

                default =>
                Log::info('Stripe event ignored', [
                    'type' => $event->type
                ]),
            };
        } catch (\Exception $e) {
            Log::error('Stripe webhook handler failed', [
                'type'  => $event->type,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'handler error'], 500);
        }

        return response()->json(['received' => true], 200);
    }
}
