<?php

namespace App\Http\Controllers;

use App\Models\LoteVale;

use App\Models\NumeracionVale;

use App\Models\Periodo;

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

    public function index(Request $request)

    {

        $filtros = $request->validate([

            'periodo_id' => ['nullable', 'integer', 'exists:periodos,id'],

            'estado_lote' => ['nullable', 'in:disponible,entregado'],

            'numero_vale' => [

                'nullable',

                'string',

                'regex:/^\d{1,6}(?:\s*[-–]\s*\d{1,6})?$/u',

            ],

        ]);

        $consulta = LoteVale::with([

            'periodo',

            'serie',

            'usuario',

        ]);

        if (!empty($filtros['periodo_id'])) {

            $consulta->where('periodo_id', $filtros['periodo_id']);

        }

        if (($filtros['estado_lote'] ?? null) === 'entregado') {

            $consulta->whereExists(function ($subconsulta) {

                $subconsulta->selectRaw('1')

                    ->from('entregas_policia')

                    ->whereColumn('entregas_policia.lote_vale_id', 'lotes_vales.id');

            });

        } elseif (($filtros['estado_lote'] ?? null) === 'disponible') {

            $consulta->whereNotExists(function ($subconsulta) {

                $subconsulta->selectRaw('1')

                    ->from('entregas_policia')

                    ->whereColumn('entregas_policia.lote_vale_id', 'lotes_vales.id');

            });

        }

        if (!empty($filtros['numero_vale'])) {

            $rango = preg_split(

                '/\s*[-–]\s*/u',

                trim($filtros['numero_vale'])

            );

            $numeroInicial = (int) $rango[0];

            $numeroFinal = isset($rango[1])

                ? (int) $rango[1]

                : $numeroInicial;

            if ($numeroInicial > $numeroFinal) {

                throw ValidationException::withMessages([

                    'numero_vale' => 'El inicio del rango no puede ser mayor que el final.',

                ]);

            }

            $consulta->where('numero_inicial', '<=', $numeroFinal)

                ->where('numero_final', '>=', $numeroInicial);

        }

        $lotes = $consulta

            ->orderByDesc('id')

            ->get();

        $entregasPorLote = collect();

        if ($lotes->isNotEmpty()) {

            $entregasPorLote = DB::table('entregas_policia')

                ->leftJoin(

                    'usuarios',

                    'usuarios.id',

                    '=',

                    'entregas_policia.usuario_id'

                )

                ->whereIn('entregas_policia.lote_vale_id', $lotes->modelKeys())

                ->orderByDesc('entregas_policia.id')

                ->get([

                    'entregas_policia.lote_vale_id',

                    'usuarios.nombre as usuario_nombre',

                ])

                ->unique('lote_vale_id')

                ->keyBy('lote_vale_id');

        }

        $periodos = Periodo::orderByDesc('anio')

            ->orderByDesc('mes')

            ->get();

        return view(

            'lotes_vales.index',

            compact('lotes', 'periodos', 'entregasPorLote')

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

        $serieLegacy = DB::table('series_vales')

            ->where('estado', true)

            ->orderByDesc('id')

            ->first();

        if (!$serieLegacy) {

            throw new \RuntimeException('No existe una serie de vales activa.');

        }

        $errorSerie = null;

        try {

            // Comprobar que la serie existe y sus contadores están sincronizados.
            $ultimoNumero = NumeracionVale::siguiente((int) now()->year, 'POL-GAS') - 1;

            // El contador persistido es la fuente de verdad para POL-GAS.
            // No buscar huecos desde 000001: la secuencia nunca retrocede.
            $numeroSugerido = $ultimoNumero + 1;
            if ($numeroSugerido > 999999) {
                $numeroSugerido = null;
                $errorSerie = 'La numeración POL-GAS alcanzó el límite de seis dígitos.';
            }

            if (NumeracionVale::estado('POL-GAS', (int) now()->year) !== 'ACTIVA') {

                $errorSerie = 'La serie POL-GAS está EN_CONFIGURACION. TECNICO_I debe activarla antes de registrar lotes.';

            }

        } catch (\RuntimeException $exception) {

            $numeroSugerido = null;

            $ultimoNumero = null;

            $errorSerie = str_contains($exception->getMessage(), 'requiere configuración')

                || str_contains($exception->getMessage(), 'Falta la serie histórica')

                ? 'Las series correlativas del año seleccionado todavía no han sido configuradas. Solicite al TECNICO_I iniciar el nuevo año.'

                : $exception->getMessage();

        }

        return view(

            'lotes_vales.create',

            compact('periodos', 'ultimoNumero', 'numeroSugerido', 'errorSerie') + [

                'codigoSerie' => 'POL-GAS',

                'anioSerie' => (int) now()->year,

            ]

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

        'fecha_recepcion' => 'required|date',

        'periodo_id' => 'required|exists:periodos,id',

        'cantidad_vales' => 'required|integer|min:1|max:999999',
            'numero_inicial' => $lote ? 'sometimes' : 'required',

    ]);

    $cantidad = (int) $datos['cantidad_vales'];

    if (!$lote) {
        $datos['numero_inicial'] = $this->normalizarNumeroVale($datos['numero_inicial'], 'numero_inicial');
    }

    if ($lote) {

        // Mantener los números originales durante la edición.

        $numeroInicial = (int) $lote->numero_inicial;

        $numeroFinal = (int) $lote->numero_final;

        if ($cantidad !== ($numeroFinal - $numeroInicial + 1)) {

            throw ValidationException::withMessages([

                'cantidad_vales' =>

                    'No se puede cambiar la cantidad de un lote existente.',

            ]);

        }

        $datos['numero_inicial'] = $numeroInicial;

        $datos['numero_final'] = $numeroFinal;

    }

        /*

        |--------------------------------------------------------------------------

        | TODO LOTE NUEVO SE REGISTRA COMO DISPONIBLE

        |--------------------------------------------------------------------------

        */

        $datos['estado'] = true;

        return DB::transaction(function () use ($datos, $lote,$cantidad) {

            $serieLegacy = DB::table('series_vales')

                ->where('estado', true)

                ->orderByDesc('id')

                ->lockForUpdate()

                ->first();

            if (!$serieLegacy) {

                throw new \RuntimeException('No existe una serie de vales activa.');

            }

            /*

            |--------------------------------------------------------------------------

            | BLOQUEAR PERÍODO

            |--------------------------------------------------------------------------

            */

            $periodo = Periodo::lockForUpdate()

                ->findOrFail($datos['periodo_id']);

// Validar el período actual al registrar un lote nuevo.

if (!$lote) {

    if (

        (int) $periodo->anio !== (int) now()->year ||

        (int) $periodo->mes !== (int) now()->month

    ) {

        throw ValidationException::withMessages([

            'periodo_id' =>

                'Solo se permite registrar lotes de vales correspondientes al mes y año actuales.',

        ]);

    }

}

            /*

            |--------------------------------------------------------------------------

            | VALIDAR EDICIÓN DEL LOTE

            |--------------------------------------------------------------------------

            */

            if ($lote) {

                $lote = LoteVale::lockForUpdate()

                    ->findOrFail($lote->id);

                if ($lote->serie_correlativa_id) {

                    $serieCorrelativaActual = DB::table('series_correlativas')

                        ->where('id', $lote->serie_correlativa_id)

                        ->first();

                    if (

                        !$serieCorrelativaActual

                        || (int) $serieCorrelativaActual->anio !== (int) $periodo->anio

                    ) {

                        throw ValidationException::withMessages([

                            'periodo_id' => 'No se puede asociar el lote a una gestión distinta de su serie correlativa.',

                        ]);

                    }

                }

                $tieneEntrega = DB::table('entregas_policia')

                    ->where('lote_vale_id', $lote->id)

                    ->exists();

                if (

                    ($lote->valesCombustible()->exists() || $tieneEntrega)

                    && (

                        $datos['periodo_id'] != $lote->periodo_id

                        || $datos['numero_inicial'] != $lote->numero_inicial

                        || $datos['numero_final'] != $lote->numero_final

                    )

                ) {

                    throw ValidationException::withMessages([

                        'numero_inicial' =>

                            'No se puede cambiar el período ni el rango de un lote con vales asociados o entregas registradas.',

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

                : (int) $serieLegacy->id;

           $serieCorrelativa = null;

if (!$lote) {

    try {

        $serieCorrelativa = NumeracionVale::bloquear(

            (int) $periodo->anio,

            'POL-GAS'

        );

    } catch (\RuntimeException $exception) {

        if (

            str_contains($exception->getMessage(), 'no está configurada')

            || str_contains($exception->getMessage(), 'Falta la serie histórica')

            || str_contains($exception->getMessage(), 'EN_CONFIGURACION')

        ) {

            throw ValidationException::withMessages([

                'periodo_id' => 'Las series correlativas del año seleccionado todavía no han sido configuradas. Solicite al TECNICO_I iniciar el nuevo año.',

            ]);

        }

        throw $exception;

    }

    $numeroInicial = (int) $datos['numero_inicial'];

    // Solo el primer lote de una serie sin movimientos puede empezar
    // en un número inicial libre. Después, la numeración debe continuar.
    $ultimoRegistrado = (int) $serieCorrelativa->ultimo_numero;
    if ($ultimoRegistrado > 0 && $numeroInicial !== $ultimoRegistrado + 1) {
        throw ValidationException::withMessages([
            'numero_inicial' => 'El siguiente lote debe comenzar en el número '
                . str_pad((string) ($ultimoRegistrado + 1), 6, '0', STR_PAD_LEFT) . '.',
        ]);
    }

    $numeroFinal = $numeroInicial + $cantidad - 1;

    if ($numeroFinal > 999999) {

        throw ValidationException::withMessages([

            'cantidad_vales' =>

                'El rango supera el máximo permitido de seis dígitos.',

        ]);

    }

    $datos['numero_inicial'] = $numeroInicial;

    $datos['numero_final'] = $numeroFinal;

} elseif ($lote->serie_correlativa_id) {

    $serieCorrelativa = DB::table('series_correlativas')

        ->where('id', $lote->serie_correlativa_id)

        ->lockForUpdate()

        ->first();

}

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

                ->when(

                    $serieCorrelativa,

                    fn ($q) => $q->where('serie_correlativa_id', $serieCorrelativa->id),

                    fn ($q) => $q->where('serie_vale_id', $serieIdParaLote)

                        ->whereNull('serie_correlativa_id')

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

            $numeracionExistente = DB::table('vales_combustible')

                ->when(

                    $serieCorrelativa,

                    fn ($q) => $q->where('serie_correlativa_id', $serieCorrelativa->id),

                    fn ($q) => $q->where('serie_vale_id', $serieIdParaLote)

                        ->whereNull('serie_correlativa_id')

                )

                ->whereBetween('numero_vale', [

                    $datos['numero_inicial'],

                    $datos['numero_final'],

                ])

                ->when(

                    $lote,

                    fn ($q) => $q->where(

                        'lote_vale_id',

                        '!=',

                        $lote->id

                    )

                )

                ->exists();

            if ($numeracionExistente) {

                throw ValidationException::withMessages([

                    'numero_inicial' =>

                        'El rango incluye números de vale que ya están registrados en esta serie.',

                ]);

            }

            /*

            |--------------------------------------------------------------------------

            | ACTUALIZAR LOTE

            |--------------------------------------------------------------------------

            */

            if ($lote) {

                $lote->update($datos);

                if ($serieCorrelativa) {

                    NumeracionVale::avanzar(

                        $serieCorrelativa,

                        (int) $datos['numero_final']

                    );

                }

                return $lote;

            }

            /*

            |--------------------------------------------------------------------------

            | CREAR NUEVO LOTE

            |--------------------------------------------------------------------------

            */

           $datos['serie_vale_id'] = $serieIdParaLote;

           $datos['serie_correlativa_id'] = $serieCorrelativa->id;

// Guardar el usuario que creó el lote.

$datos['usuario_id'] = auth()->id();

// Registrar el lote.

$nuevoLote = LoteVale::create($datos);

            NumeracionVale::avanzar(

                $serieCorrelativa,

                (int) $datos['numero_final']

            );

            return $nuevoLote;

        }, 3);

    }

    private function normalizarNumeroVale(mixed $valor, string $campo): int

    {

        if (!is_string($valor) && !is_int($valor)) {

            throw ValidationException::withMessages([

                $campo => 'Ingrese un número entero entre 1 y 999999.',

            ]);

        }

        $texto = trim((string) $valor);

        if (!preg_match('/\A[0-9]+\z/', $texto)) {

            throw ValidationException::withMessages([

                $campo => 'Ingrese únicamente dígitos, sin letras ni decimales.',

            ]);

        }

        $numeroSinCeros = ltrim($texto, '0');

        $numeroSinCeros = $numeroSinCeros === '' ? '0' : $numeroSinCeros;

        if (

            strlen($numeroSinCeros) > 6

            || (int) $numeroSinCeros < 1

        ) {

            throw ValidationException::withMessages([

                $campo => 'El número debe estar entre 000001 y 999999.',

            ]);

        }

        return (int) $numeroSinCeros;

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
        if (DB::table('entregas_policia')
    ->where('lote_vale_id', $loteVale->id)
    ->exists()) {
    return redirect()
        ->route('lotes-vales.index')
        ->with('error', 'Este lote ya fue entregado a la Policía y no puede modificarse.');
}

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