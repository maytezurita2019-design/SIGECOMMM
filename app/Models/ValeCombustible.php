<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\NumeracionVale;

class ValeCombustible extends Model
{
    protected $table = 'vales_combustible';

    protected $fillable = [
        'serie_vale_id',
        'serie_correlativa_id',
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
        'detalle_asignacion',
        'estacion_servicio_id',
        'lugar_entrega_id',
        'tipo_combustible_id',
        'periodo_id',
        'estado',
        'motivo_anulacion',
        'fecha_anulacion',
        'documento_respaldo',
        'uso_registrado_por',
'uso_registrado_en',
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

    public function getNumeroValeFormateadoAttribute(): string
    {
        $serie = $this->serieCorrelativa?->codigo_serie;
        if ($serie === null) {
            $serie = $this->entrega_policia_id
                ? 'POL-GAS'
                : NumeracionVale::codigoSerie(
                    null,
                    'GAMS',
                    $this->tipoCombustible?->nombre
                );
        }

        $anio = (int) ($this->anio ?? $this->serieCorrelativa?->anio);
        if ($serie === null || $anio < 2000 || $anio > 9999) {
            return (string) $this->numero_vale;
        }

        return NumeracionVale::formatearNumero(
            $serie,
            $anio,
            (int) $this->numero_vale
        );
    }

    protected $casts = [
        'fecha' => 'date',
        'cantidad_litros' => 'decimal:13',
        'precio_unitario' => 'decimal:4',
        'total' => 'decimal:2',
        'kilometraje' => 'string',
        'fecha_anulacion' => 'datetime',
        'uso_registrado_en' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auditoría de vales (un registro por operación)
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::created(function (ValeCombustible $vale): void {
            if (!\Illuminate\Support\Facades\Schema::hasTable('auditorias')) {
                return;
            }

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'EMITIR',
                'tabla' => 'vales_combustible',
                'registro_id' => $vale->getKey(),
                'datos_nuevos' => $vale->getAttributes(),
                'ip' => request()?->ip(),
                'descripcion' => 'Se emitió el vale N.º ' . sprintf('%06d', (int) $vale->numero_vale),
            ]);
        });

        static::updated(function (ValeCombustible $vale): void {
            if (!\Illuminate\Support\Facades\Schema::hasTable('auditorias')) {
                return;
            }

            $cambios = $vale->getChanges();
            unset($cambios['updated_at']);
            if (!$cambios) {
                return;
            }

            $anteriores = [];
            foreach (array_keys($cambios) as $campo) {
                $anteriores[$campo] = $vale->getRawOriginal($campo);
            }

            $estado = strtoupper((string) ($cambios['estado'] ?? ''));
            $accion = 'MODIFICAR';
            if ($estado === 'ANULADO') {
                $accion = 'ANULAR';
            } elseif (array_key_exists('uso_registrado_en', $cambios) && $cambios['uso_registrado_en']) {
                $accion = 'REGISTRAR USO';
            } elseif (in_array($estado, ['UTILIZADO', 'COMPLETADO'], true)) {
                $accion = 'REGISTRAR USO';
            }

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => $accion,
                'tabla' => 'vales_combustible',
                'registro_id' => $vale->getKey(),
                'datos_anteriores' => $anteriores,
                'datos_nuevos' => $cambios,
                'ip' => request()?->ip(),
                'descripcion' => $accion . ' - vale N.º ' . sprintf('%06d', (int) $vale->numero_vale),
            ]);
        });

        static::deleted(function (ValeCombustible $vale): void {
            if (!\Illuminate\Support\Facades\Schema::hasTable('auditorias')) {
                return;
            }

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => 'ELIMINAR',
                'tabla' => 'vales_combustible',
                'registro_id' => $vale->getKey(),
                'datos_anteriores' => $vale->getRawOriginal(),
                'ip' => request()?->ip(),
                'descripcion' => 'Se eliminó el vale N.º ' . sprintf('%06d', (int) $vale->numero_vale),
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

    public function serieCorrelativa(): BelongsTo
    {
        return $this->belongsTo(SerieCorrelativa::class, 'serie_correlativa_id');
    }

    public function entregaPolicia(): BelongsTo
    {
        return $this->belongsTo(EntregaPolicia::class);
    }

    public function scopeConConsumo($query, string $ambito = 'todos')
    {
        return $query->where(function ($consumos) use ($ambito) {
            if ($ambito === 'gams') {
                $consumos
                    ->whereNull('entrega_policia_id')
                    ->where('estado', 'COMPLETADO');

                return;
            }

            if ($ambito === 'policia') {
                $consumos
                    ->whereNotNull('entrega_policia_id')
                    ->where('estado', 'UTILIZADO');

                return;
            }

            $consumos
                ->where(function ($gams) {
                    $gams
                        ->whereNull('entrega_policia_id')
                        ->where('estado', 'COMPLETADO');
                })
                ->orWhere(function ($policia) {
                    $policia
                        ->whereNotNull('entrega_policia_id')
                        ->where('estado', 'UTILIZADO');
                });
        });
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
    public function usuarioRegistroUso(): BelongsTo
{
    return $this->belongsTo(
        Usuario::class,
        'uso_registrado_por'
    );
}

    public function estacionServicio(): BelongsTo
    {
        return $this->belongsTo(
            EstacionServicio::class,
            'estacion_servicio_id'
        );
    }

    public function lugarEntrega(): BelongsTo
    {
        return $this->belongsTo(LugarEntrega::class, 'lugar_entrega_id');
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