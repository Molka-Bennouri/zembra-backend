<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;  // ← import manquant

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id',      // ← était 'client_id'
        'type',
        'title',        // ← nouveau
        'message',
        'seen',
        'payment_id',   // ← nouveau
    ];

    protected $casts = [
        'seen' => 'boolean',
    ];

    public function user(): BelongsTo     // ← était client()
    {
        return $this->belongsTo(User::class);  // ← était Client::class
    }

    public function payment(): BelongsTo  // ← nouveau
    {
        return $this->belongsTo(Payment::class);
    }
}
