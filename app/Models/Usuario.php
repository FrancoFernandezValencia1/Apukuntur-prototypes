<?php

namespace App\Models;

use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasFactory, Notifiable;

    public const ROL_FABRICA = 'fabrica';
    public const ROL_EU = 'eu';
    public const ROL_ADMIN = 'admin';

    public const CREATED_AT = 'creado_en';
    public const UPDATED_AT = 'actualizado_en';

    public const ROLES = [
        self::ROL_FABRICA => 'Fábrica',
        self::ROL_EU => 'EU',
        self::ROL_ADMIN => 'Admin',
    ];

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'correo', 'contrasena', 'rol'];

    protected $hidden = ['contrasena', 'recordar_token'];

    protected function casts(): array
    {
        return [
            'correo_verificado_en' => 'datetime',
            'contrasena' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->contrasena;
    }

    public function getRememberTokenName(): string
    {
        return 'recordar_token';
    }

    public function getEmailForPasswordReset(): string
    {
        return (string) $this->correo;
    }

    public function isEu(): bool
    {
        return $this->rol === self::ROL_EU;
    }

    public function isFactory(): bool
    {
        return $this->rol === self::ROL_FABRICA;
    }

    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function etiquetaRol(): string
    {
        return self::ROLES[$this->rol] ?? (string) $this->rol;
    }
}
