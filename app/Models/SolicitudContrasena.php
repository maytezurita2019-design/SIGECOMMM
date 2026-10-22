<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudContrasena extends Model
{
    protected $table = 'solicitudes_contrasena';

    protected $fillable = [
        'usuario_id',
        'estado',
        'fecha_atencion',
        'atendido_por',
        'ip_solicitud',
    ];

    protected function casts(): array
    {
        return [
            'fecha_atencion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function atendidoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'atendido_por');
    }
}