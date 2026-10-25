<?php

namespace App\Http\Controllers;

use App\Models\LoteVale;
use App\Models\Periodo;

use App\Models\NumeracionVale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class LoteValeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE LOTES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $lotes = LoteVale::with([
            'periodo',
            'serie',
        ])
            ->orderByDesc('id')
            ->get();

        $lotesEntregados = DB::table('entregas_policia')
            ->pluck('lote_vale_id')
            ->filter()
            ->unique()
            ->flip();

        return view(
            'lotes_vales.index',
            compact(
                'lotes',
                'lotesEntregados'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO NUEVO LOTE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $periodos = Periodo::where('estado', true)
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SERIE ACTIVA
        |--------------------------------------------------------------------------
        |
        | La sugerencia debe salir de la serie activa.
        | Esto permite que, cuando exista una nueva serie, vuelva a sugerir
        | 000001 sin tomar los números históricos de series anteriores.
        |
        */

        $serie = DB::table('series_vales')
            ->where('estado', true)
            ->orderByDesc('id')
            ->first();

        if (!$serie) {
            throw new \RuntimeException(
                'No existe una serie de vales activa.'
            );
        }

        $ultimoNumero = (int) $serie->ultimo_numero;
        $numeroSugerido = $ultimoNumero + 1;

        return view(
            'lotes_vales.create',
            compact(
                'periodos',
                'ultimoNumero',
                'numeroSugerido'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR / ACTUALIZAR LOTE
    |--------------------------------------------------------------------------
    */

    private function guardar(
        Request $request,
        ?LoteVale $lote = null
    ): LoteVale {

        $datos = $request->validate([
            'numero_inicial' => 'required|integer|min:1',
            'numero_final' => 'required|integer|gte:numero_inicial',
            'fecha_recepcion' => 'required|date',
            'periodo_id' => 'required|exists:periodos,id',
            'iniciar_nueva_serie' => 'nullable|boolean',
        ]);

        /*
        |--------------------------------------------------------------------------
        | TODO LOTE NUEVO SE REGISTRA COMO DISPONIBLE
        |--------------------------------------------------------------------------
        */

        $datos['estado'] = true;

        return DB::transaction(function () use ($datos, $lote) {

            $referencia = Periodo::findOrFail(
                $datos['periodo_id']
            );

            /*
            |--------------------------------------------------------------------------
            | BLOQUEAR SERIE ACTIVA
            |--------------------------------------------------------------------------
            */

            $serieActiva = NumeracionVale::bloquear(
                (int) $referencia->anio
            );

            /*
            |--------------------------------------------------------------------------
            | DETERMINAR SI SE SOLICITÓ UNA NUEVA SERIE
            |--------------------------------------------------------------------------
            */

            $iniciarNuevaSerie = (bool) (
                $datos['iniciar_nueva_serie'] ?? false
            );

            /*
            |--------------------------------------------------------------------------
            | INICIAR NUEVA SERIE
            |--------------------------------------------------------------------------
            |
            | Solamente se permite al registrar un lote nuevo.
            |
            | Ejemplo:
            |
            | Serie actual:
            | último número = 002472
            |
            | Usuario registra:
            | número inicial = 1
            |
            | Resultado:
            |
            | Serie anterior -> cerrada
            | Nueva serie    -> comienza desde 000001
            |
            */

            if ($iniciarNuevaSerie && !$lote) {

                $siguienteActual =
                    ((int) $serieActiva->ultimo_numero) + 1;

                if (
                    (int) $datos['numero_inicial']
                    >= $siguienteActual
                ) {
                    throw ValidationException::withMessages([
                        'numero_inicial' =>
                            'El número indicado no requiere iniciar una nueva serie.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CERRAR SERIE ANTERIOR
                |--------------------------------------------------------------------------
                */

                DB::table('series_vales')
                    ->where('id', $serieActiva->id)
                    ->update([
                        'estado' => false,
                        'updated_at' => now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | CREAR NUEVA SERIE
                |--------------------------------------------------------------------------
                */

                $numeroInicialNuevaSerie =
                    (int) $datos['numero_inicial'];

                $nuevaSerieId = DB::table('series_vales')
                    ->insertGetId([
                        'nombre' =>
                            'Serie iniciada ' .
                            now()->format('d/m/Y H:i'),

                        'numero_inicial' =>
                            $numeroInicialNuevaSerie,

                        /*
                         * Todavía no se utilizó ningún vale.
                         *
                         * Si inicia en 1:
                         * ultimo_numero = 0
                         *
                         * El siguiente será 1.
                         */
                        'ultimo_numero' =>
                            $numeroInicialNuevaSerie - 1,

                        'estado' => true,

                        'motivo' =>
                            'Nueva serie iniciada manualmente desde el registro de un lote de vales.',

                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                $serieActiva = DB::table('series_vales')
                    ->where('id', $nuevaSerieId)
                    ->first();

                if (!$serieActiva) {
                    throw new \RuntimeException(
                        'No fue posible crear la nueva serie de vales.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | BLOQUEAR PERÍODO
            |--------------------------------------------------------------------------
            */

            $periodo = Periodo::lockForUpdate()
                ->findOrFail($datos['periodo_id']);

            /*
            |--------------------------------------------------------------------------
            | VALIDAR EDICIÓN DEL LOTE
            |--------------------------------------------------------------------------
            */

            if ($lote) {

                $lote = LoteVale::lockForUpdate()
                    ->findOrFail($lote->id);

                if (
                    $lote->valesCombustible()->exists()
                    && (
                        $datos['periodo_id'] != $lote->periodo_id
                        || $datos['numero_inicial'] != $lote->numero_inicial
                        || $datos['numero_final'] != $lote->numero_final
                    )
                ) {
                    throw ValidationException::withMessages([
                        'numero_inicial' =>
                            'No se puede cambiar el período ni el rango de un lote con vales emitidos.',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDAR PERÍODO
            |--------------------------------------------------------------------------
            */

            if (
                !$periodo->estado
                && (
                    !$lote
                    || $lote->periodo_id != $periodo->id
                    || $datos['estado']
                )
            ) {
                throw ValidationException::withMessages([
                    'periodo_id' =>
                        'Seleccione un período activo.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | DETERMINAR SERIE DEL LOTE
            |--------------------------------------------------------------------------
            |
            | Si estamos editando, se conserva la serie original del lote.
            | Si estamos creando, se utiliza la serie activa.
            |
            */

            $serieIdParaLote = $lote
                ? (int) $lote->serie_vale_id
                : (int) $serieActiva->id;

            /*
            |--------------------------------------------------------------------------
            | VALIDAR RANGO DENTRO DE LA MISMA SERIE
            |--------------------------------------------------------------------------
            |
            | El mismo número puede volver a existir en otra serie.
            |
            | Ejemplo:
            |
            | Serie 1 -> 000001
            | Serie 2 -> 000001
            |
            | Pero dentro de una misma serie los rangos no pueden
            | superponerse.
            |
            */

            $solapado = LoteVale::query()
                ->where(
                    'serie_vale_id',
                    $serieIdParaLote
                )
                ->when(
                    $lote,
                    fn ($q) =>
                        $q->where(
                            'id',
                            '!=',
                            $lote->id
                        )
                )
                ->where(
                    'numero_inicial',
                    '<=',
                    $datos['numero_final']
                )
                ->where(
                    'numero_final',
                    '>=',
                    $datos['numero_inicial']
                )
                ->exists();

            if ($solapado) {
                throw ValidationException::withMessages([
                    'numero_inicial' =>
                        'El rango se superpone con otro lote de la misma serie.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ELIMINAR CAMPO AUXILIAR
            |--------------------------------------------------------------------------
            |
            | iniciar_nueva_serie pertenece al formulario.
            | No existe como columna en lotes_vales.
            |
            */

            unset($datos['iniciar_nueva_serie']);

            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR LOTE
            |--------------------------------------------------------------------------
            */

            if ($lote) {

                $lote->update($datos);

                return $lote;
            }

            /*
            |--------------------------------------------------------------------------
            | CREAR NUEVO LOTE
            |--------------------------------------------------------------------------
            */

            $datos['serie_vale_id'] =
                $serieIdParaLote;

            return LoteVale::create($datos);

        }, 3);
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRAR LOTE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->guardar($request);

        return redirect()
            ->route('lotes-vales.index')
            ->with(
                'success',
                'Lote registrado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VER LOTE
    |--------------------------------------------------------------------------
    */

    public function show(LoteVale $loteVale)
    {
        $loteVale->load([
            'periodo',
            'serie',
        ]);

        $total =
            $loteVale->numero_final
            - $loteVale->numero_inicial
            + 1;

        $cantidadVales =
            $loteVale
                ->valesCombustible()
                ->count();

        $disponibles =
            $loteVale->disponibles;

        $entregaPolicia = DB::table('entregas_policia')
            ->where(
                'lote_vale_id',
                $loteVale->id
            )
            ->latest('id')
            ->first();

        return view(
            'lotes_vales.show',
            compact(
                'loteVale',
                'total',
                'cantidadVales',
                'disponibles',
                'entregaPolicia'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR LOTE
    |--------------------------------------------------------------------------
    */

    public function edit(LoteVale $loteVale)
    {
        $periodos = Periodo::where(
            'estado',
            true
        )
            ->orWhere(
                'id',
                $loteVale->periodo_id
            )
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->get();

        return view(
            'lotes_vales.edit',
            compact(
                'loteVale',
                'periodos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR LOTE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        LoteVale $loteVale
    ) {
        $this->guardar(
            $request,
            $loteVale
        );

        return redirect()
            ->route(
                'lotes-vales.show',
                $loteVale
            )
            ->with(
                'success',
                'Lote actualizado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR LOTE
    |--------------------------------------------------------------------------
    */

    public function destroy(LoteVale $loteVale)
{
    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ENTREGA A POLICÍA
    |--------------------------------------------------------------------------
    */

    $tieneEntregaPolicia = DB::table('entregas_policia')
        ->where('lote_vale_id', $loteVale->id)
        ->exists();

    if ($tieneEntregaPolicia) {
        return redirect()
            ->route('lotes-vales.index')
            ->with(
                'error',
                'No se puede eliminar este lote porque ya fue entregado a la Policía.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR VALES RELACIONADOS
    |--------------------------------------------------------------------------
    */

    $tieneVales = $loteVale
        ->valesCombustible()
        ->exists();

    if ($tieneVales) {
        return redirect()
            ->route('lotes-vales.index')
            ->with(
                'error',
                'No se puede eliminar este lote porque ya tiene vales registrados.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR LOTE
    |--------------------------------------------------------------------------
    */

    $loteVale->delete();

    return redirect()
        ->route('lotes-vales.index')
        ->with(
            'success',
            'Lote eliminado correctamente.'
        );
}
        
    
}