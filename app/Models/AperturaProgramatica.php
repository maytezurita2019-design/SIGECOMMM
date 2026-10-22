<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AperturaProgramatica extends Model
{
    protected $table = 'aperturas_programaticas';

    protected $fillable = [
        'codigo',
        'descripcion',
        'fuente_financiamiento_id',
        'organismo_financiador_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function fuenteFinanciamiento(): BelongsTo
    {
        return $this->belongsTo(
            FuenteFinanciamiento::class,
            'fuente_financiamiento_id'
        );
    }

    public function organismoFinanciador(): BelongsTo
    {
        return $this->belongsTo(
            OrganismoFinanciador::class,
            'organismo_financiador_id'
        );
    }

    public function valesCombustible(): HasMany
    {
        return $this->hasMany(
            ValeCombustible::class,
            'apertura_programatica_id'
        );
    }
}