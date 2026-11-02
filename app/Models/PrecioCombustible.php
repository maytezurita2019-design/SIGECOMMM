<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrecioCombustible extends Model
{
    protected $table = 'precios_combustible';

    protected $fillable = [
        'tipo_combustible_id',
        'precio_unitario',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'usuario_id',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:4',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function tipoCombustible(): BelongsTo
    {
        return $this->belongsTo(TipoCombustible::class, 'tipo_combustible_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function scopeVigente($query, $fecha = null)
    {
        $fecha = Carbon::parse($fecha ?? now())->toDateString();

        return $query->whereDate('fecha_inicio', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', $fecha);
            });
    }

    public static function vigenteEnFecha(int $tipoCombustibleId, $fecha = null): ?self
    {
        return self::where('tipo_combustible_id', $tipoCombustibleId)
            ->vigente($fecha)
            ->orderByDesc('fecha_inicio')
            ->first();
    }

    public static function registrar(array $datos): self
    {
        return DB::transaction(function () use ($datos) {
            $tipoCombustibleId = (int) ($datos['tipo_combustible_id'] ?? 0);
            $fechaInicio = Carbon::parse($datos['fecha_inicio'] ?? now())->startOfDay();

            if ($tipoCombustibleId <= 0) {
                throw ValidationException::withMessages([
                    'tipo_combustible_id' => 'Debe seleccionar un tipo de combustible.',
                ]);
            }

            $vigentes = self::where('tipo_combustible_id', $tipoCombustibleId)
                ->whereDate('fecha_inicio', '<=', $fechaInicio->toDateString())
                ->where(function ($query) use ($fechaInicio) {
                    $query->whereNull('fecha_fin')
                        ->orWhereDate('fecha_fin', '>=', $fechaInicio->toDateString());
                })
                ->orderByDesc('fecha_inicio')
                ->get();

            if ($vigentes->isNotEmpty()) {
                $vigenteMasReciente = $vigentes->first();

                if ($vigenteMasReciente->fecha_inicio->equalTo($fechaInicio)) {
                    throw ValidationException::withMessages([
                        'fecha_inicio' => 'No se permiten vigencias superpuestas para el mismo combustible.',
                    ]);
                }

                if ($vigenteMasReciente->fecha_inicio->lessThan($fechaInicio) && $vigenteMasReciente->fecha_fin === null) {
                    // Caso permitido: se cierra automáticamente el precio vigente al registrar uno nuevo posterior.
                } else {
                    throw ValidationException::withMessages([
                        'fecha_inicio' => 'No se permiten vigencias superpuestas para el mismo combustible.',
                    ]);
                }
            }

            $nuevoRegistro = new self($datos);
            $nuevoRegistro->tipo_combustible_id = $tipoCombustibleId;
            $nuevoRegistro->fecha_inicio = $fechaInicio->toDateString();
            $nuevoRegistro->usuario_id = $datos['usuario_id'] ?? auth()->id();

            $registros = self::where('tipo_combustible_id', $tipoCombustibleId)
                ->orderBy('fecha_inicio')
                ->get()
                ->all();

            $ultimoRegistro = collect($registros)->last();

            foreach ($registros as $registro) {
                $inicio = Carbon::parse($registro->fecha_inicio)->startOfDay();
                $fin = $registro->fecha_fin ? Carbon::parse($registro->fecha_fin)->startOfDay() : null;

                $solapa = $inicio->lessThanOrEqualTo($fechaInicio)
                    && ($fin === null || $fin->greaterThanOrEqualTo($fechaInicio));

                if ($solapa) {
                    $esCierreVigente = $registro->is($ultimoRegistro)
                        && $inicio->lessThan($fechaInicio)
                        && $fin === null;

                    if (!$esCierreVigente) {
                        throw ValidationException::withMessages([
                            'fecha_inicio' => 'No se permiten vigencias superpuestas para el mismo combustible.',
                        ]);
                    }
                }
            }

            $registros[] = $nuevoRegistro;

            usort($registros, function ($a, $b) {
                $fechaA = Carbon::parse($a->fecha_inicio)->startOfDay();
                $fechaB = Carbon::parse($b->fecha_inicio)->startOfDay();

                return $fechaA->lessThan($fechaB) ? -1 : ($fechaA->greaterThan($fechaB) ? 1 : 0);
            });

            foreach ($registros as $indice => $registro) {
                $siguiente = $registros[$indice + 1] ?? null;
                $registro->fecha_fin = $siguiente
                    ? Carbon::parse($siguiente->fecha_inicio)->subDay()->toDateString()
                    : null;
            }

            foreach ($registros as $registro) {
                if ($registro->exists) {
                    $registro->save();
                    continue;
                }

                $registro->save();
            }

            return $nuevoRegistro->fresh();
        });
    }
}
