<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    protected $table = 'periodos';

    protected $fillable = [
       
        'mes',
        'anio',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado' => 'boolean',
    ];

    public function lotesVales(): HasMany
    {
        return $this->hasMany(LoteVale::class, 'periodo_id');
    }

    public function getNombreAttribute(): string
    {
        return sprintf('%02d/%d', $this->mes, $this->anio);
    }
}
