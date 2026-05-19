<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',   // ← 'name' remplacé par 'full_name' (comme ton ancien Client)
        'email',
        'password',
        'role',        // ← ajouté
        'provider',      // ← ajouter
        'provider_id',   // ← ajouter
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // JWT — inchangé
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    // Helpers de rôle — ajoutés
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isClient(): bool {
        return $this->role === 'client';
    }

    // Relation profil client — ajoutée
    public function clientProfile() {
        return $this->hasOne(ClientProfile::class);
    }
}
