<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ValeCombustible extends Model
{
    protected $table = 'vales_combustible';

    protected $fillable = [
        'serie_vale_id',
        'entrega_policia_id',
        'numero_vale',
        'anio',
        'fecha',
        'lote_vale_id',
        'vehiculo_id',
        'apertura_programatica_id',
        'usuario_id',
        'numero_factura',
        'cantidad_litros',
        'precio_unitario',
        'total',
        'kilometraje',
        'observacion',
        'estacion_servicio_id',
        'tipo_combustible_id',
        'periodo_id',
        'estado',
        'motivo_anulacion',
        'fecha_anulacion',
        'documento_respaldo',
    ];

    public function setNumeroValeAttribute($value): void
    {
        $numero = (int) $value;

        if ($numero < 1) {
            throw new \InvalidArgumentException(
                'El número de vale debe ser mayor a cero.'
            );
        }

        $this->attributes['numero_vale'] = $numero;
    }

    protected $casts = [
        'fecha' => 'date',
        'cantidad_litros' => 'decimal:13',
        'precio_unitario' => 'decimal:4',
        'total' => 'decimal:2',
        'kilometraje' => 'string',
        'fecha_anulacion' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auditoría automática
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        // Registrar creación del vale
        static::created(function (ValeCombustible $vale) {
            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'CREAR',
                'tabla' => 'vales_combustible',
                'registro_id' => $vale->id,
                'datos_anteriores' => null,
                'datos_nuevos' => $vale->getAttributes(),
                'ip' => request()->ip(),
                'descripcion' =>
                    'Se creó el vale de combustible N.º ' . $vale->numero_vale,
            ]);
        });

        // Registrar modificaciones
        static::updated(function (ValeCombustible $vale) {
            $cambios = $vale->getChanges();

            // No registrar updated_at como un cambio importante.
            unset($cambios['updated_at']);

            if (empty($cambios)) {
                return;
            }

            $anteriores = [];

            foreach ($cambios as $campo => $valorNuevo) {
                $anteriores[$campo] = $vale->getOriginal($campo);
            }

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'MODIFICAR',
                'tabla' => 'vales_combustible',
                'registro_id' => $vale->id,
                'datos_anteriores' => $anteriores,
                'datos_nuevos' => $cambios,
                'ip' => request()->ip(),
                'descripcion' =>
                    'Se modificó el vale de combustible N.º ' . $vale->numero_vale,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function loteVale(): BelongsTo
    {
        return $this->belongsTo(LoteVale::class, 'lote_vale_id');
    }

    public function entregaPolicia(): BelongsTo
    {
        return $this->belongsTo(EntregaPolicia::class);
    }

    public function scopeConConsumo($query)
    {
        return $query->where(
            fn ($q) =>
                $q->where(
                    fn ($normal) =>
                        $normal
                            ->whereNull('entrega_policia_id')
                            ->where('estado', 'COMPLETADO')
                )
                ->orWhere(
                    fn ($policia) =>
                        $policia
                            ->whereNotNull('entrega_policia_id')
                            ->where('estado', 'UTILIZADO')
                )
        );
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function aperturaProgramatica(): BelongsTo
    {
        return $this->belongsTo(
            AperturaProgramatica::class,
            'apertura_programatica_id'
        );
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function estacionServicio(): BelongsTo
    {
        return $this->belongsTo(
            EstacionServicio::class,
            'estacion_servicio_id'
        );
    }

    public function tipoCombustible(): BelongsTo
    {
        return $this->belongsTo(
            TipoCombustible::class,
            'tipo_combustible_id'
        );
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(
            Periodo::class,
            'periodo_id'
        );
    }
}