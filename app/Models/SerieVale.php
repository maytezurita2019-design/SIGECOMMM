<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SerieVale extends Model
{
    protected $table = 'series_vales';

    protected $fillable = [
        'nombre',
        'numero_inicial',
        'ultimo_numero',
        'estado',
        'motivo',
    ];

    protected $casts = [
        'numero_inicial' => 'integer',
        'ultimo_numero' => 'integer',
        'estado' => 'boolean',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(
            LoteVale::class,
            'serie_vale_id'
        );
    }

    public function vales(): HasMany
    {
        return $this->hasMany(
            ValeCombustible::class,
            'serie_vale_id'
        );
    }

    public function serie(): BelongsTo
{
    return $this->belongsTo(
        SerieVale::class,
        'serie_vale_id'
    );
}
}