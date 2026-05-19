<?php
// app/Models/Plan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'duration_days',
        'stripe_price_id',
        'features',
        'recommended'
    ];

    protected $casts = [
        'features' => 'array',
        'recommended' => 'boolean'
    ];
}
