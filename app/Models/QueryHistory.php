<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'query_histories';

    protected $fillable = [
        'client_id',
        'type',
        'network',
        'slug',
        'fields',
        'filters',
        'status',
        'response',
        'executed_at',
    ];

    protected $casts = [
        'fields'  => 'array',
        'filters' => 'array',
        'response' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
