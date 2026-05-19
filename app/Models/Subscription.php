<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'client_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'current_period_end',
        'starts_at',
        'ends_at',
    ];
    public function client()
    {
        return $this->belongsTo(Client::class); // ✅ manquait
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
