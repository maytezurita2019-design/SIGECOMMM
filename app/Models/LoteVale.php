<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoteVale extends Model
{
    protected $table = 'lotes_vales';

    protected $fillable = [
        'serie_vale_id',
        'serie_correlativa_id',
        'numero_inicial',
        'numero_final',
        'fecha_recepcion',
        'periodo_id',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_recepcion' => 'date',
        'estado' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | USUARIO QUE CREÓ EL LOTE
    |--------------------------------------------------------------------------
    */

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PERÍODO
    |--------------------------------------------------------------------------
    */

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(
            Periodo::class,
            'periodo_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SERIE DEL LOTE
    |--------------------------------------------------------------------------
    */

    public function serie(): BelongsTo
    {
        return $this->belongsTo(
            SerieVale::class,
            'serie_vale_id'
        );
    }

    public function serieCorrelativa(): BelongsTo
    {
        return $this->belongsTo(
            SerieCorrelativa::class,
            'serie_correlativa_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VALES DEL LOTE
    |--------------------------------------------------------------------------
    */

    public function valesCombustible(): HasMany
    {
        return $this->hasMany(
            ValeCombustible::class,
            'lote_vale_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SIGUIENTE NÚMERO DISPONIBLE
    |--------------------------------------------------------------------------
    */

    public function getSiguienteNumeroAttribute(): ?int
    {
        $ultimoNumero = (int) (
            $this->serieCorrelativa?->ultimo_numero
            ?? $this->serie?->ultimo_numero
            ?? 0
        );

        $siguiente = $ultimoNumero + 1;

        return $siguiente >= $this->numero_inicial
            && $siguiente <= $this->numero_final
                ? $siguiente
                : null;
    }

    /*
    |--------------------------------------------------------------------------
    | CANTIDAD DISPONIBLE
    |--------------------------------------------------------------------------
    */

    public function getDisponiblesAttribute(): int
    {
        $siguiente = ((int) (
            $this->serieCorrelativa?->ultimo_numero
            ?? $this->serie?->ultimo_numero
            ?? 0
        )) + 1;

        return max(
            0,
            $this->numero_final
            - max(
                $this->numero_inicial,
                $siguiente
            )
            + 1
        );
    }
}
