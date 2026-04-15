<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false; // disable auto timestamps

    const CREATED_AT = 'created_at'; // manually keep created_at

    protected $fillable = ['message', 'type', 'seen', 'client_id'];

    protected $casts = [
        'seen' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
