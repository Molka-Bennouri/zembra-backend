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
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
