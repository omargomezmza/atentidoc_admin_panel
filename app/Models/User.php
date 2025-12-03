<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
    */
    protected $fillable = [
        'api_user_id',
        'email',
        'status',
        'role',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
    */
    protected $hidden = [
        // No tenemos password local
    ];

    /**
     * Relación con el token de API
    */
    public function apiToken()
    {
        return $this->hasOne(ApiToken::class);
    }

    /**
     * Obtener el token de acceso válido
    */
    public function getValidToken(): ?string
    {
        $token = $this->apiToken;
        
        if (!$token) {
            return null;
        }
        
        // Si el token está expirado, retornar null
        if ($token->expires_at->isPast()) {
            return null;
        }
        
        return $token->access_token;
    }

    /**
     * Verificar si tiene un token válido
    */
    public function hasValidToken(): bool
    {
        return $this->getValidToken() !== null;
    }
}
