<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoteVale extends Model
{
    protected $table = 'lotes_vales';

    protected $fillable = [
        'numero_inicial',
        'numero_final',
        'fecha_recepcion',
        'periodo_id',
        'estado',
    ];

    protected $casts = [
        'fecha_recepcion' => 'date',
        'estado' => 'boolean',
    ];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function valesCombustible(): HasMany
    {
        return $this->hasMany(ValeCombustible::class, 'lote_vale_id');
    }

    public function getSiguienteNumeroAttribute(): ?int
    {
        $siguiente = NumeracionVale::siguiente(
            (int) $this->periodo->anio
        );

        return $siguiente >= $this->numero_inicial
            && $siguiente <= $this->numero_final
            && $siguiente <= 999999
                ? $siguiente
                : null;
    }

    public function getDisponiblesAttribute(): int
    {
        return max(
            0,
            $this->numero_final
            - max(
                $this->numero_inicial,
                NumeracionVale::siguiente(
                    (int) $this->periodo->anio
                )
            )
            + 1
        );
    }
}
