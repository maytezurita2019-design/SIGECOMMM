<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCombustible extends Model
{
    protected $table = 'tipos_combustible';

    protected $fillable = [
        'nombre',
        'unidad_medida',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function lotesVales(): HasMany
    {
        return $this->hasMany(LoteVale::class, 'tipo_combustible_id');
    }
}