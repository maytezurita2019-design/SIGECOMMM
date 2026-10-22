<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstacionServicio extends Model
{
    protected $table = 'estaciones_servicio';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function lotesVales(): HasMany
    {
        return $this->hasMany(LoteVale::class, 'estacion_servicio_id');
    }
}