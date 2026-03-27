<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Client extends Authenticatable implements JWTSubject
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'provider',      // Ajouter pour SSO
        'provider_id',   // Ajouter pour SSO
    ];

    protected $hidden = ['password'];

    // Required by JWTSubject
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
