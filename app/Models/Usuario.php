<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'correo',
        'contrasena',
        'rol_id',
        'estado',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'contrasena' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function valesCombustible()
    {
        return $this->hasMany(ValeCombustible::class, 'usuario_id');
    }

    public function getNameAttribute(): string
{
    $nombreCompleto = trim(
        (string) $this->nombre . ' ' .
        (string) $this->apellido
    );

    return $nombreCompleto !== ''
        ? $nombreCompleto
        : (string) $this->correo;
}


public function adminlte_desc(): string
{
    return $this->rol->nombre ?? 'USUARIO';
}
}