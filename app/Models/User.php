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
        'email',
        'password_hash', // ← Nota: el campo real en la BD
        'first_name',
        'last_name',
        'phone',
        'document_id',
        'birth_date',
        'gender',
        'role',
        'status',
        'email_verified',
        'avatar_url',
    ];

    /* 
        id: 1,
        avatar_url: null,
        created_at: "2025-12-01 13:47:38.755721",
        email: "admin@atentidoc.com",
        email_verified: true,
        fcm_token: "f96lTP56SvCMQThNCSI4Td:APA91bGMEKhq0uNWa2oreu2_n4r_o0WQzSmt2_OYqFy39te3lGCbQtXrgo8XM8F_S7mjFpMInnYqdV0zNkGMvHT18HOEIlfyMb4f28PeIDatyAH4-CQDp0g",
        google_subject: null,
        password_hash: "$2a$12$/lMsIVhJKOSS6udaJWPJxeGqFYWbx2bcpMwz7OJIDfd07NwgGeMeO",
        provider: "LOCAL",
        status: "ACTIVE",
        first_name: null,
        last_name: null,
        phone: null,
        document_id: null,
        birth_date: null,
        gender: null,
        latitude: null,
        longitude: null,    
    */

    /**
     * The attributes that should be hidden for serialization.
    */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * CRÍTICO: Indicar a Laravel que use 'password_hash' en lugar de 'password'
     * Este método es llamado por Auth::attempt()
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * CRÍTICO: Nombre del campo de contraseña para queries
     */
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }



    public function roles() {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function patient() {
        return $this->hasOne(Patient::class, 'user_id');
    }

    public function doctor() {
        return $this->hasOne(Doctor::class, 'user_id');
    }

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
        /* if ($token->expires_at->isPast()) {
            return null;
        } */
        
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
