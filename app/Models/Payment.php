<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'client_id',
        'stripe_event_id',
        'stripe_payment_intent_id',
        'stripe_invoice_id',
        'invoice_pdf_url',
        'amount',
        'status',
        'paid_at',
        'description',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
