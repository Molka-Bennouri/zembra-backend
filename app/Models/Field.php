<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'label',
        'description',
        'context',
    ];

    public function scopeReview($query)
    {
        return $query->where('context', 'review');
    }

    public function scopeListing($query)
    {
        return $query->where('context', 'listing');
    }
}
