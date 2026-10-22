<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FuenteFinanciamiento extends Model
{
    protected $table = 'fuentes_financiamiento';

    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function aperturasProgramaticas(): HasMany
    {
        return $this->hasMany(
            AperturaProgramatica::class,
            'fuente_financiamiento_id'
        );
    }
}