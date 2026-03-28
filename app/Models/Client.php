<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable; // ✅ Ajouter

class Client extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable; // ✅ Ajouter Notifiable

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'provider',      // SSO
        'provider_id',   // SSO
    ];

    protected $hidden = ['password'];

    // Required by JWTSubject
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    // ✅ Notification pour reset password
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetClientPasswordNotification($token));
    }
}
