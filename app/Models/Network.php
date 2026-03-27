<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['name','label', 'slug_pattern'];
}
