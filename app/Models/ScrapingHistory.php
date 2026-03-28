<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapingHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'scraping_histories';
    protected $fillable = ['network', 'slug', 'fields', 'response', 'executed_at'];
    protected $casts = ['fields' => 'array', 'response' => 'array'];
}
