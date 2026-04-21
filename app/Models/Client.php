<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;


class Client extends Authenticatable implements JWTSubject, CanResetPassword
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'provider',
        'provider_id',
        'stripe_customer_id',
    ];

    protected $hidden = ['password'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetClientPasswordNotification($token));
    }
}
