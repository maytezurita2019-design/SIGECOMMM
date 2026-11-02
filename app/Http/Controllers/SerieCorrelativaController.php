<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\NumeracionVale;
use App\Models\SerieCorrelativa;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SerieCorrelativaController extends Controller
{
    private const SERIES = ['POL-GAS', 'GAMS-GAS', 'GAMS-DIE'];

    public function index(Request $request)
    {
        $datos = $request->validate([
'anio' => ['nullable', 'integer', 'min:2026', 'max:9999'],
        ]);

        $anio = (int) ($datos['anio'] ?? now()->year);

        $series = SerieCorrelativa::query()
            ->where('anio', $anio)
            ->orderByRaw("CASE codigo_serie WHEN 'POL-GAS' THEN 1 WHEN 'GAMS-GAS' THEN 2 ELSE 3 END")
            ->get();

        foreach ($series as $serie) {
            if (!in_array($serie->codigo_serie, self::SERIES, true)) {
                $serie->ultimo_formateado = (string) $serie->ultimo_numero;
                $serie->proximo_formateado = (string) ((int) $serie->ultimo_numero + 1);
                continue;
            }

            $limite = $serie->codigo_serie === 'GAMS-DIE' ? 9999999 : 999999;
            $serie->ultimo_formateado = \App\Models\NumeracionVale::formatearNumero(
                $serie->codigo_serie,
                (int) $serie->anio,
                (int) $serie->ultimo_numero
            );
            $serie->proximo_formateado = (int) $serie->ultimo_numero < $limite
                ? NumeracionVale::formatearNumero(
                    $serie->codigo_serie,
                    (int) $serie->anio,
                    (int) $serie->ultimo_numero + 1
                )
                : null;
            $serie->estado = $serie->estado ?: 'ACTIVA';
        }

        $puedeModificarSeries = strtoupper(trim(auth()->user()->rol->nombre ?? '')) === 'TECNICO_II';
        $seriesAnioActual = SerieCorrelativa::query()
            ->where('anio', (int) now()->year)
            ->whereIn('codigo_serie', self::SERIES)
            ->count();
        $anioInicioSugerido = (int) now()->year >= 2027 && $seriesAnioActual < count(self::SERIES)
            ? (int) now()->year
            : max(2027, (int) now()->year + 1);

        return view('series_correlativas.index', compact(
            'anio',
            'series',
            'puedeModificarSeries',
            'seriesAnioActual',
            'anioInicioSugerido'
        ));
    }

    public function update(Request $request, string $codigo)
    {
        abort(410, 'La modificación manual anterior fue reemplazada por configuración y activación de series.');
    }

    public function configurar(Request $request, string $codigo)
    {
        abort_unless(in_array($codigo, self::SERIES, true), 404);
        abort_unless(strtoupper(trim(auth()->user()->rol->nombre ?? '')) === 'TECNICO_II', 403);

        $maximo = $codigo === 'GAMS-DIE' ? 9999999 : 999999;
        $datos = $request->validate([
            'anio' => ['required', 'integer', 'between:2026,' . (int) now()->year],
            'primer_numero' => ['required', 'string', 'regex:/\A[0-9]{1,' . ($codigo === 'GAMS-DIE' ? 7 : 6) . '}\z/'],
            'motivo' => ['required', 'string', 'min:10', 'max:1000'],
            'confirmacion' => ['required', 'string', 'max:64'],
        ]);
        $anio = (int) $datos['anio'];
        $primerNumero = (int) $datos['primer_numero'];

        if ($primerNumero < 1 || $primerNumero > $maximo) {
            throw ValidationException::withMessages([
                'primer_numero' => "El primer número debe estar entre 1 y {$maximo}.",
            ]);
        }
        if (!hash_equals("CONFIGURAR {$codigo} {$anio}", trim($datos['confirmacion']))) {
            throw ValidationException::withMessages([
                'confirmacion' => "Escriba exactamente: CONFIGURAR {$codigo} {$anio}",
            ]);
        }

        DB::transaction(function () use ($codigo, $anio, $primerNumero, $datos): void {
            $contador = DB::table('series_contadores')
                ->where('codigo_serie', $codigo)
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();
            $serie = SerieCorrelativa::query()
                ->where('codigo_serie', $codigo)
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();

            if (!$contador || !$serie) {
                throw ValidationException::withMessages([
                    'anio' => "No existe la serie completa {$codigo} para {$anio}.",
                ]);
            }
            if ($serie->estado !== 'EN_CONFIGURACION') {
                throw ValidationException::withMessages([
                    'primer_numero' => 'Una serie ACTIVA no permite modificar su numeración.',
                ]);
            }
            if ((int) $contador->ultimo_numero !== (int) $serie->ultimo_numero) {
                throw ValidationException::withMessages([
                    'primer_numero' => 'Las tablas de numeración no están sincronizadas. No se realizó ningún cambio.',
                ]);
            }
            if ($this->tieneMovimientos($codigo, $anio, (int) $serie->id)) {
                throw ValidationException::withMessages([
                    'primer_numero' => 'La serie tiene vales o lotes asociados y no puede reconfigurarse.',
                ]);
            }

            $ultimoNumero = $primerNumero - 1;
            $anterior = [
                'estado' => $serie->estado,
                'ultimo_numero' => (int) $serie->ultimo_numero,
                'primer_numero' => (int) $serie->ultimo_numero + 1,
            ];
            DB::table('series_contadores')->where('id', $contador->id)->update([
                'ultimo_numero' => $ultimoNumero,
                'updated_at' => now(),
            ]);
            $serie->update(['ultimo_numero' => $ultimoNumero]);

            Auditoria::create([
                'usuario_id' => auth()->id(),
                'accion' => 'CONFIGURAR SERIE CORRELATIVA',
                'tabla' => 'series_correlativas',
                'registro_id' => $serie->id,
                'datos_anteriores' => $anterior,
                'datos_nuevos' => [
                    'rol' => 'TECNICO_II',
                    'anio' => $anio,
                    'codigo_serie' => $codigo,
                    'primer_numero' => $primerNumero,
                    'ultimo_numero' => $ultimoNumero,
                    'motivo' => $datos['motivo'],
                    'fecha' => now()->toISOString(),
                ],
                'ip' => request()->ip(),
                'descripcion' => "Configuración inicial de {$codigo} para {$anio}. Motivo: {$datos['motivo']}",
            ]);
        }, 3);

        return redirect()
            ->route('series-correlativas.index', ['anio' => $anio])
            ->with('success', "Se configuró {$codigo} para iniciar en " . NumeracionVale::formatearNumero($codigo, $anio, $primerNumero) . '.');
    }

    public function activar(Request $request, string $codigo)
    {
        abort_unless(in_array($codigo, self::SERIES, true), 404);
        abort_unless(strtoupper(trim(auth()->user()->rol->nombre ?? '')) === 'TECNICO_II', 403);

        $datos = $request->validate([
            'anio' => ['required', 'integer', 'between:2026,' . (int) now()->year],
            'motivo' => ['required', 'string', 'min:10', 'max:1000'],
            'confirmacion' => ['required', 'string', 'max:64'],
        ]);
        $anio = (int) $datos['anio'];
        if (!hash_equals("ACTIVAR {$codigo} {$anio}", trim($datos['confirmacion']))) {
            throw ValidationException::withMessages([
                'confirmacion' => "Escriba exactamente: ACTIVAR {$codigo} {$anio}",
            ]);
        }

        DB::transaction(function () use ($codigo, $anio, $datos): void {
            $contador = DB::table('series_contadores')
                ->where('codigo_serie', $codigo)
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();
            $serie = SerieCorrelativa::query()
                ->where('codigo_serie', $codigo)
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();

            if (!$contador || !$serie) {
                throw ValidationException::withMessages([
                    'anio' => "No existe la serie completa {$codigo} para {$anio}.",
                ]);
            }
            if ($serie->estado !== 'EN_CONFIGURACION') {
                throw ValidationException::withMessages([
                    'confirmacion' => 'Solo se pueden activar series EN_CONFIGURACION.',
                ]);
            }
            if ((int) $contador->ultimo_numero !== (int) $serie->ultimo_numero) {
                throw ValidationException::withMessages([
                    'anio' => 'Las tablas de numeración no están sincronizadas. No se activó la serie.',
                ]);
            }
            if ($this->tieneMovimientos($codigo, $anio, (int) $serie->id)) {
                throw ValidationException::withMessages([
                    'anio' => 'La serie tiene movimientos asociados. Revise su integridad antes de activarla.',
                ]);
            }

            $primerNumero = (int) $serie->ultimo_numero + 1;
            $anterior = $serie->getAttributes();
            $serie->update(['estado' => 'ACTIVA']);

            Auditoria::create([
                'usuario_id' => auth()->id(),
                'accion' => 'ACTIVAR SERIE CORRELATIVA',
                'tabla' => 'series_correlativas',
                'registro_id' => $serie->id,
                'datos_anteriores' => $anterior,
                'datos_nuevos' => [
                    'rol' => 'TECNICO_II',
                    'anio' => $anio,
                    'codigo_serie' => $codigo,
                    'estado' => 'ACTIVA',
                    'primer_numero' => $primerNumero,
                    'motivo' => $datos['motivo'],
                    'fecha' => now()->toISOString(),
                ],
                'ip' => request()->ip(),
                'descripcion' => "Activación de {$codigo} para {$anio}. Motivo: {$datos['motivo']}",
            ]);
        }, 3);

        return redirect()
            ->route('series-correlativas.index', ['anio' => $anio])
            ->with('success', "La serie {$codigo} quedó ACTIVA para {$anio}.");
    }

    private function tieneMovimientos(string $codigo, int $anio, int $serieId): bool
    {
        $vales = DB::table('vales_combustible')
            ->where('serie_correlativa_id', $serieId)
            ->exists();
        $lotes = DB::table('lotes_vales')
            ->where('serie_correlativa_id', $serieId)
            ->exists();

        if ($vales || $lotes) {
            return true;
        }

        $valesHistoricos = DB::table('vales_combustible as vales')
            ->leftJoin('tipos_combustible as tipos', 'tipos.id', '=', 'vales.tipo_combustible_id')
            ->where('vales.anio', $anio)
            ->whereNull('vales.serie_correlativa_id')
            ->when(
                $codigo === 'POL-GAS',
                fn ($query) => $query->whereNotNull('vales.entrega_policia_id'),
                fn ($query) => $query
                    ->whereNull('vales.entrega_policia_id')
                    ->whereRaw(
                        $codigo === 'GAMS-DIE'
                            ? "(UPPER(tipos.nombre) LIKE '%DIESEL%' OR UPPER(tipos.nombre) LIKE '%DIÉSEL%')"
                            : "UPPER(tipos.nombre) LIKE '%GASOLINA%'"
                    )
            )
            ->exists();
        if ($valesHistoricos) {
            return true;
        }

        return $codigo === 'POL-GAS'
            && DB::table('lotes_vales')
                ->join('periodos', 'periodos.id', '=', 'lotes_vales.periodo_id')
                ->where('periodos.anio', $anio)
                ->whereNull('lotes_vales.serie_correlativa_id')
                ->exists();
    }

    public function iniciarAnio(Request $request)
    {
        abort_unless(
            strtoupper(trim(auth()->user()->rol->nombre ?? '')) === 'TECNICO_II',
            403
        );

        $limiteAnio = (int) now()->year + 1;
        $datos = $request->validate([
            'anio' => ['required', 'integer', 'between:2026,' . (int) now()->year],
            'primer_gams_gas' => ['required', 'string', 'regex:/\A[0-9]{1,6}\z/'],
            'primer_pol_gas' => ['required', 'string', 'regex:/\A[0-9]{1,6}\z/'],
            'primer_gams_die' => ['required', 'string', 'regex:/\A[0-9]{1,7}\z/'],
            'motivo' => ['required', 'string', 'min:10', 'max:1000'],
            'confirmacion' => ['required', 'string', 'max:32'],
        ]);

        $anio = (int) $datos['anio'];
        $primerosNumeros = [
            'GAMS-GAS' => (int) $datos['primer_gams_gas'],
            'POL-GAS' => (int) $datos['primer_pol_gas'],
            'GAMS-DIE' => (int) $datos['primer_gams_die'],
        ];
        // Desde la gestión 2027 toda serie nueva comienza en 1.
        // La continuación de talonarios físicos en una instalación inicial
        // requiere una operación distinta, previa a cualquier emisión.
        if ($anio >= 2027) {
            foreach ($primerosNumeros as $codigo => $primerNumero) {
                if ($primerNumero !== 1) {
                    throw ValidationException::withMessages([
                        'anio' => "En una nueva gestión, {$codigo} debe comenzar desde 1. No se permite continuar ni alterar el inicio anual.",
                    ]);
                }
            }
        }

        $limites = [
            'GAMS-GAS' => 999999,
            'POL-GAS' => 999999,
            'GAMS-DIE' => 9999999,
        ];

        foreach ($primerosNumeros as $codigo => $primerNumero) {
            if ($primerNumero < 1 || $primerNumero > $limites[$codigo]) {
                throw ValidationException::withMessages([
                    match ($codigo) {
                        'GAMS-GAS' => 'primer_gams_gas',
                        'POL-GAS' => 'primer_pol_gas',
                        default => 'primer_gams_die',
                    } => "El primer número de {$codigo} debe estar entre 1 y {$limites[$codigo]}.",
                ]);
            }
        }

        $confirmacionEsperada = "INICIAR {$anio}";
        if (!hash_equals($confirmacionEsperada, trim($datos['confirmacion']))) {
            throw ValidationException::withMessages([
                'confirmacion' => "Escriba exactamente: {$confirmacionEsperada}",
            ]);
        }

        try {
            DB::transaction(function () use ($anio, $primerosNumeros, $datos): void {
                if (DB::connection()->getDriverName() === 'pgsql') {
                    DB::select(
                        'SELECT pg_advisory_xact_lock(hashtext(?), ?)',
                        ['sigecom_series_correlativas_anio', $anio]
                    );
                }

                $codigos = array_keys($primerosNumeros);
                $seriesExistentes = DB::table('series_correlativas')
                    ->where('anio', $anio)
                    ->whereIn('codigo_serie', $codigos)
                    ->pluck('codigo_serie')
                    ->all();
                $contadoresExistentes = DB::table('series_contadores')
                    ->where('anio', $anio)
                    ->whereIn('codigo_serie', $codigos)
                    ->pluck('codigo_serie')
                    ->all();

                if ($seriesExistentes || $contadoresExistentes) {
                    $existentes = array_values(array_unique(array_merge(
                        $seriesExistentes,
                        $contadoresExistentes
                    )));
                    throw ValidationException::withMessages([
                        'anio' => 'No se puede iniciar el año porque ya hay series o contadores configurados: '
                            . implode(', ', $existentes) . '.',
                    ]);
                }

                $hayVales = DB::table('vales_combustible')
                    ->where('anio', $anio)
                    ->exists();
                $hayLotes = DB::table('lotes_vales')
                    ->join('periodos', 'periodos.id', '=', 'lotes_vales.periodo_id')
                    ->where('periodos.anio', $anio)
                    ->exists();

                if ($hayVales || $hayLotes) {
                    throw ValidationException::withMessages([
                        'anio' => 'No se puede iniciar el año porque ya existen vales o lotes de esa gestión.',
                    ]);
                }

                $ahora = now();
                $creadas = [];
                foreach ($primerosNumeros as $codigo => $primerNumero) {
                    $configuracion = match ($codigo) {
                        'POL-GAS' => ['institucion' => 'POLICIA', 'combustible' => 'GASOLINA'],
                        'GAMS-GAS' => ['institucion' => 'GAMS', 'combustible' => 'GASOLINA'],
                        default => ['institucion' => 'GAMS', 'combustible' => 'DIESEL'],
                    };
                    $ultimoNumero = $primerNumero - 1;

                    $serieId = DB::table('series_correlativas')->insertGetId([
                        'codigo_serie' => $codigo,
                        'institucion' => $configuracion['institucion'],
                        'combustible' => $configuracion['combustible'],
                        'ultimo_numero' => $ultimoNumero,
                        'anio' => $anio,
                        'estado' => 'EN_CONFIGURACION',
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);

                    DB::table('series_contadores')->insert([
                        'codigo_serie' => $codigo,
                        'anio' => $anio,
                        'ultimo_numero' => $ultimoNumero,
                        'prefijo' => $codigo . '-' . $anio,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ]);

                    $creadas[$codigo] = [
                        'serie_id' => $serieId,
                        'primer_numero' => $primerNumero,
                        'ultimo_numero' => $ultimoNumero,
                    ];

                    Auditoria::create([
                        'usuario_id' => auth()->id(),
                        'accion' => 'CREAR SERIE EN CONFIGURACION',
                        'tabla' => 'series_correlativas',
                        'registro_id' => $serieId,
                        'datos_nuevos' => [
                            'rol' => strtoupper(trim(auth()->user()->rol->nombre ?? '')),
                            'anio' => $anio,
                            'codigo_serie' => $codigo,
                            'estado' => 'EN_CONFIGURACION',
                            'primer_numero' => $primerNumero,
                            'ultimo_numero' => $ultimoNumero,
                            'motivo' => $datos['motivo'],
                            'fecha' => $ahora->toISOString(),
                        ],
                        'ip' => request()->ip(),
                        'descripcion' => "Creación de {$codigo} en configuración para {$anio}. Motivo: {$datos['motivo']}",
                    ]);
                }

                Auditoria::create([
                    'usuario_id' => auth()->id(),
                    'accion' => 'INICIAR SERIES NUEVO AÑO',
                    'tabla' => 'series_correlativas',
                    'registro_id' => $creadas['GAMS-GAS']['serie_id'],
                    'datos_nuevos' => [
                        'anio' => $anio,
                        'rol' => strtoupper(trim(auth()->user()->rol->nombre ?? '')),
                        'series' => $creadas,
                        'motivo' => $datos['motivo'],
                        'registrado_en' => $ahora->toISOString(),
                    ],
                    'ip' => request()->ip(),
                    'descripcion' => "Inicio de las tres series correlativas para {$anio}. Motivo: {$datos['motivo']}",
                ]);
            }, 3);
        } catch (QueryException $exception) {
            $codigoError = (string) $exception->getCode();
            $mensajeError = $exception->getMessage();
            $violacionUnica = $codigoError === '23505'
                || (
                    $codigoError === '23000'
                    && (
                        str_contains($mensajeError, 'UNIQUE constraint failed')
                        || str_contains(mb_strtolower($mensajeError), 'unique violation')
                    )
                );
            if (!$violacionUnica) {
                throw $exception;
            }

            $existentes = DB::table('series_correlativas')
                ->where('anio', $anio)
                ->whereIn('codigo_serie', array_keys($primerosNumeros))
                ->pluck('codigo_serie')
                ->all();
            throw ValidationException::withMessages([
                'anio' => 'Otra solicitud ya configuró parte de este año. No se modificó ninguna serie existente.'
                    . ($existentes ? ' Configuradas: ' . implode(', ', $existentes) . '.' : ''),
            ]);
        }

        return redirect()
            ->route('series-correlativas.index', ['anio' => $anio])
            ->with('success', "Se iniciaron las tres series correlativas de {$anio}.");
    }
}
