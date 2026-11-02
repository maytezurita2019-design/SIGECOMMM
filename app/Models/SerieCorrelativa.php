<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SerieCorrelativa extends Model
{
    protected $table = 'series_correlativas';

    protected $fillable = [
        'codigo_serie',
        'institucion',
        'combustible',
        'ultimo_numero',
        'anio',
        'estado',
    ];

    protected $casts = [
        'ultimo_numero' => 'integer',
        'anio' => 'integer',
        'estado' => 'string',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(LoteVale::class, 'serie_correlativa_id');
    }

    public function vales(): HasMany
    {
        return $this->hasMany(ValeCombustible::class, 'serie_correlativa_id');
    }
}
