<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                        => $this->id,
            'description'               => $this->description,
            'amount'                    => $this->amount,           // déjà en TND (divisé par 100 dans le webhook)
            'status'                    => $this->status,           // succeeded | failed | pending | refunded
            'paid_at'                   => $this->paid_at?->toISOString(),
            'created_at'                => $this->created_at->toISOString(),
            'stripe_payment_intent_id'  => $this->stripe_payment_intent_id,
            'stripe_invoice_id'         => $this->stripe_invoice_id,
        ];
    }
}
