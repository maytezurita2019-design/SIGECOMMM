<?php

namespace App\Http\Controllers;

use App\Models\{
    ValeCombustible,
    LoteVale,
    Vehiculo,
    AperturaProgramatica,
    TipoCombustible,
    EstacionServicio,
    LugarEntrega,
    NumeracionVale
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValeCombustibleController extends Controller
{
    private function esDiesel(TipoCombustible $tipo): bool
    {
        $nombre = mb_strtoupper(trim($tipo->nombre), 'UTF-8');

        return str_contains($nombre, 'DIESEL') || str_contains($nombre, 'DIÉSEL');
    }

    private function validarUbicacionGams(
        TipoCombustible $tipo,
        array $datos,
        ?ValeCombustible $valeExistente = null,
        bool $emision = false
    ): array {
        $idLugar = $datos['lugar_entrega_id'] ?? $valeExistente?->lugar_entrega_id;
        $idEstacion = $datos['estacion_servicio_id'] ?? $valeExistente?->estacion_servicio_id;

        if ($this->esDiesel($tipo)) {
            if ($emision && !$idEstacion) {
                throw ValidationException::withMessages([
                    'estacion_servicio_id' => 'Seleccione una estación de servicio activa.',
                ]);
            }

            if ($emision && !$idLugar) {
                throw ValidationException::withMessages([
                    'lugar_entrega_id' => 'Seleccione un lugar de entrega activo.',
                ]);
            }

            if ($idEstacion) {
                $estacion = EstacionServicio::find($idEstacion);
                if (!$estacion) {
                    throw ValidationException::withMessages([
                        'estacion_servicio_id' => 'La estación de servicio seleccionada no existe.',
                    ]);
                }

                if (!$estacion->estado && (int) $idEstacion !== (int) $valeExistente?->estacion_servicio_id) {
                    throw ValidationException::withMessages([
                        'estacion_servicio_id' => 'Seleccione una estación activa.',
                    ]);
                }
            }

            if ($idLugar) {
                $lugar = LugarEntrega::find($idLugar);
                if (!$lugar) {
                    throw ValidationException::withMessages([
                        'lugar_entrega_id' => 'El lugar de entrega seleccionado no existe.',
                    ]);
                }

                if (!$lugar->estado && (int) $idLugar !== (int) $valeExistente?->lugar_entrega_id) {
                    throw ValidationException::withMessages([
                        'lugar_entrega_id' => 'Seleccione un lugar de entrega activo.',
                    ]);
                }
            }

            return ['estacion_servicio_id' => $idEstacion, 'lugar_entrega_id' => $idLugar];
        }

        $esGasolina = str_contains(mb_strtoupper($tipo->nombre, 'UTF-8'), 'GASOLINA');
        if (!$esGasolina) {
            throw ValidationException::withMessages([
                'tipo_combustible_id' => 'GAMS solo puede emitir vales de gasolina o diésel.',
            ]);
        }

        if ($emision && !$idEstacion) {
            throw ValidationException::withMessages([
                'estacion_servicio_id' => 'Seleccione una estación de servicio activa.',
            ]);
        }

        if ($idEstacion) {
            $estacion = EstacionServicio::find($idEstacion);
            if (!$estacion) {
                throw ValidationException::withMessages([
                    'estacion_servicio_id' => 'La estación de servicio seleccionada no existe.',
                ]);
            }

            if (!$estacion->estado && (int) $idEstacion !== (int) $valeExistente?->estacion_servicio_id) {
                throw ValidationException::withMessages([
                    'estacion_servicio_id' => 'Seleccione una estación activa.',
                ]);
            }
        }

        return ['estacion_servicio_id' => $idEstacion, 'lugar_entrega_id' => null];
    }

    private function normalizarDecimales(Request $request): void
    {
        foreach (['cantidad_litros', 'precio_unitario'] as $campo) {
            if (is_string($request->input($campo))) {
                $valor = trim($request->input($campo));

                $request->merge([
                    $campo => $valor === ''
                        ? null
                        : str_replace(',', '.', $valor)
                ]);
            }
        }
    }
        private function validarKilometrajeAnterior(
    int $vehiculoId,
    $kilometrajeNuevo,
    ?int $valeIgnorarId = null
): void {

    // Si no se ingresó kilometraje, no se realiza comparación.
    if ($kilometrajeNuevo === null || trim((string) $kilometrajeNuevo) === '') {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Normalizar kilometraje ingresado
    |--------------------------------------------------------------------------
    */

    $nuevo = str_replace(
        ['.', ',', ' '],
        '',
        trim((string) $kilometrajeNuevo)
    );

    if (!is_numeric($nuevo)) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'kilometraje' =>
                'El kilometraje debe contener solamente números.',
        ]);
    }

    $nuevo = (int) $nuevo;


    /*
    |--------------------------------------------------------------------------
    | Buscar último kilometraje DEL MISMO VEHÍCULO
    |--------------------------------------------------------------------------
    */

    $consulta = ValeCombustible::query()
        ->where('vehiculo_id', $vehiculoId)
        ->whereNotNull('kilometraje')
        ->where('kilometraje', '<>', '');

    /*
     * Al editar un vale no debe compararse contra sí mismo.
     */
    if ($valeIgnorarId !== null) {
        $consulta->where('id', '<>', $valeIgnorarId);
    }

    $ultimoVale = $consulta
        ->orderByDesc('fecha')
        ->orderByDesc('id')
        ->first();


    /*
    |--------------------------------------------------------------------------
    | Si es el primer registro del vehículo
    |--------------------------------------------------------------------------
    */

    if (!$ultimoVale) {
        return;
    }

    $anterior = str_replace(
        ['.', ',', ' '],
        '',
        trim((string) $ultimoVale->kilometraje)
    );

    if (!is_numeric($anterior)) {
        return;
    }

    $anterior = (int) $anterior;


    /*
    |--------------------------------------------------------------------------
    | Comparar
    |--------------------------------------------------------------------------
    */

    if ($nuevo < $anterior) {

        throw \Illuminate\Validation\ValidationException::withMessages([
            'kilometraje' =>
                'El kilometraje ingresado (' .
                number_format($nuevo, 0, ',', '.') .
                ' km) es menor al último kilometraje registrado para este vehículo (' .
                number_format($anterior, 0, ',', '.') .
                ' km). Verifique el dato antes de continuar.',
        ]);
    }
}

    private function calcularTotal(array $datos): ?string
    {
        if (!isset(
            $datos['cantidad_litros'],
            $datos['precio_unitario']
        )) {
            return null;
        }

        $total = \Brick\Math\BigDecimal::of(
            (string) $datos['cantidad_litros']
        )
            ->multipliedBy(
                (string) $datos['precio_unitario']
            )
            ->toScale(
                2,
                \Brick\Math\RoundingMode::HALF_UP
            );

        if ($total->isGreaterThan('999999999999.99')) {
            throw ValidationException::withMessages([
                'cantidad_litros' =>
                    'El costo total supera el máximo permitido.'
            ]);
        }

        return (string) $total;
    }

    /*
    |--------------------------------------------------------------------------
    | LISTADO DE VALES
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $request->validate([
            'estado' =>
                'nullable|in:EMITIDO,COMPLETADO,ANULADO,ENTREGADO,UTILIZADO,DEVUELTO',

            'tipo' =>
                'nullable|in:GAMS,POLICIA',

            'por_pagina' =>
                'nullable|integer|in:10,30,50,100',
                'combustible' =>
    'nullable|in:gasolina,diesel',
        ]);

        $consulta = ValeCombustible::with([
            'periodo',
            'tipoCombustible',
            'serieCorrelativa',
            'estacionServicio',
            'lugarEntrega',
            'loteVale',
            'vehiculo.unidad',
            'aperturaProgramatica',
            'usuario',
            'entregaPolicia'
        ]);
        
$combustible = $request->input('combustible');

if ($combustible === 'gasolina') {
    $consulta->whereHas('tipoCombustible', function ($q) {
        $q->whereRaw('UPPER(nombre) LIKE ?', ['%GASOLINA%']);
    });
} elseif ($combustible === 'diesel') {
    $consulta->whereHas('tipoCombustible', function ($q) {
        $q->where(function ($sub) {
            $sub->whereRaw('UPPER(nombre) LIKE ?', ['%DIESEL%'])
                ->orWhereRaw('UPPER(nombre) LIKE ?', ['%DIÉSEL%']);
        });
    });
}


        if ($request->filled('buscar')) {

            $buscar = trim($request->buscar);

            $consulta->where(function ($q) use ($buscar) {

                $q->where(
                    'numero_vale',
                    'like',
                    "%{$buscar}%"
                )
                    ->orWhere(
                        'numero_factura',
                        'like',
                        "%{$buscar}%"
                    )
                    ->orWhereHas(
                        'vehiculo',
                        function ($v) use ($buscar) {

                            $v->where(
                                'codigo',
                                'like',
                                "%{$buscar}%"
                            )
                                ->orWhere(
                                    'placa',
                                    'like',
                                    "%{$buscar}%"
                                );
                        }
                    );
            });
        }

        if ($request->filled('estado')) {
            $consulta->where(
                'estado',
                $request->estado
            );
        }

        $tipo = $request->input(
            'tipo',
            'GAMS'
        );

        if ($tipo === 'GAMS') {

            $consulta->whereNull(
                'entrega_policia_id'
            );

        } elseif ($tipo === 'POLICIA') {

            $consulta->whereNotNull(
                'entrega_policia_id'
            );
        }

        $totalFiltrado = (clone $consulta)
            ->sum('total');

        $porPagina = (int) $request->input(
            'por_pagina',
            30
        );

        $vales = $consulta
            ->orderByDesc('fecha')
            ->orderByDesc('numero_vale')
            ->paginate($porPagina)
            ->withQueryString();

        return view(
            'vales_combustible.index',
            compact(
                'vales',
                'totalFiltrado',
                'porPagina'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO PARA EMITIR VALE GAMS
    |--------------------------------------------------------------------------
    */

   public function create(Request $request)
{

$request->validate([
    'combustible' => 'nullable|in:gasolina,diesel',
]);

$combustibleSeleccionado = $request->query('combustible');
    $vehiculos = Vehiculo::with([
        'unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ])
        ->where('estado', true)
        ->orderBy('codigo')
        ->get();

    $tiposCombustible = TipoCombustible::where('estado', true)
        ->orderBy('nombre')
        ->get();

    $anio = (int) date('Y');
    $numerosSugeridos = [];
    $numerosFormateadosSugeridos = [];
    $erroresSugeridos = [];
    $codigosSerieSugeridos = [];
    $estadosSerieSugeridos = [];
    foreach ($tiposCombustible as $tipo) {
        $codigo = NumeracionVale::codigoSerie(null, 'GAMS', $tipo->nombre);
        if ($codigo !== null) {
            $codigosSerieSugeridos[$tipo->id] = $codigo;
            try {
                $numero = NumeracionVale::siguiente($anio, $codigo);
                $estadoSerie = NumeracionVale::estado($codigo, $anio) ?? 'ACTIVA';
                $numerosSugeridos[$tipo->id] = $numero;
                $estadosSerieSugeridos[$tipo->id] = $estadoSerie;
                $numerosFormateadosSugeridos[$tipo->id] = NumeracionVale::formatearNumero(
                    $codigo,
                    $anio,
                    $numero
                );
                if ($estadoSerie !== 'ACTIVA') {
                    $erroresSugeridos[$codigo] = "La serie {$codigo} del año {$anio} está EN_CONFIGURACION. TECNICO_I debe activarla antes de emitir.";
                }
            } catch (\RuntimeException $exception) {
                $erroresSugeridos[$codigo] = $this->mensajeSerieNoConfigurada($exception);
            }
        }
    }

    $tipoGasolinaId = array_search('GAMS-GAS', $codigosSerieSugeridos, true);
    $numeroSugerido = $tipoGasolinaId === false
        ? ''
        : ($numerosFormateadosSugeridos[$tipoGasolinaId] ?? '');

    $estacionesServicio = EstacionServicio::where('estado', true)
        ->orderBy('nombre')
        ->get();

    $lugaresEntrega = LugarEntrega::where('estado', true)
        ->orderBy('nombre')
        ->get();

    return view(
        'vales_combustible.create',
        compact(
            'vehiculos',
            'anio',
            'numeroSugerido',
            'tiposCombustible',
            'estacionesServicio',
            'lugaresEntrega',
            'numerosSugeridos',
            'numerosFormateadosSugeridos',
            'erroresSugeridos',
            'estadosSerieSugeridos',
            'combustibleSeleccionado',
            'codigosSerieSugeridos'
        )
    );
}

    public function previsualizarSerie(Request $request)
    {
        $datos = $request->validate([
            'anio' => ['required', 'integer', 'between:2026,' . ((int) now()->year + 1)],
        ]);
        $anio = (int) $datos['anio'];
        $series = [];

        foreach (['GAMS-GAS', 'GAMS-DIE'] as $codigo) {
            try {
                $numero = NumeracionVale::siguiente($anio, $codigo);
                $series[$codigo] = [
                    'disponible' => true,
                    'numero' => $numero,
                    'formateado' => NumeracionVale::formatearNumero($codigo, $anio, $numero),
                    'estado' => NumeracionVale::estado($codigo, $anio) ?? 'ACTIVA',
                    'activa' => NumeracionVale::estado($codigo, $anio) === 'ACTIVA',
                ];
                if (!$series[$codigo]['activa']) {
                    $series[$codigo]['mensaje'] = "La serie {$codigo} del año {$anio} está EN_CONFIGURACION. TECNICO_I debe activarla antes de emitir.";
                }
            } catch (\RuntimeException $exception) {
                $series[$codigo] = [
                    'disponible' => false,
                    'mensaje' => $this->mensajeSerieNoConfigurada($exception),
                ];
            }
        }

        return response()->json([
            'anio' => $anio,
            'series' => $series,
        ]);
    }

    private function mensajeSerieNoConfigurada(\RuntimeException $exception): string
    {
        if (
            str_contains($exception->getMessage(), 'requiere configuración')
            || str_contains($exception->getMessage(), 'Falta la serie histórica')
        ) {
            return 'Las series correlativas del año seleccionado todavía no han sido configuradas. Solicite al TECNICO_I iniciar el nuevo año.';
        }
        if (str_contains($exception->getMessage(), 'EN_CONFIGURACION')) {
            return $exception->getMessage();
        }

        return $exception->getMessage();
    }
    /*
    |--------------------------------------------------------------------------
    | GUARDAR / EMITIR VALE GAMS
    |--------------------------------------------------------------------------
    */

   public function store(Request $request)
{
    $this->normalizarDecimales($request);

    $datos = $request->validate([
        'fecha' => 'required|date|before_or_equal:today',
        'tipo_combustible_id' => 'required|integer|exists:tipos_combustible,id',
        'estacion_servicio_id' => 'nullable|integer|exists:estaciones_servicio,id',
        'lugar_entrega_id' => 'nullable|integer|exists:lugares_entrega,id',

        'vehiculo_id' =>
            'required|exists:vehiculos,id,estado,1',

        'detalle_asignacion' => [
            'required',
            'string',
            'max:1000',
        ],

        'kilometraje' => [
    'nullable',
    'string',
    'max:50',
],
    ]);

    if (!TipoCombustible::whereKey($datos['tipo_combustible_id'])->where('estado', true)->exists()) {
        throw ValidationException::withMessages(['tipo_combustible_id' => 'Seleccione un combustible activo.']);
    }

    $this->validarKilometrajeAnterior(
    (int) $datos['vehiculo_id'],
    $datos['kilometraje'] ?? null
);

    $tipoCombustible = TipoCombustible::findOrFail(
        $datos['tipo_combustible_id']
    );
    if (
        !str_contains(mb_strtoupper($tipoCombustible->nombre), 'GASOLINA')
        && !str_contains(mb_strtoupper($tipoCombustible->nombre), 'DIESEL')
        && !str_contains(mb_strtoupper($tipoCombustible->nombre), 'DIÉSEL')
    ) {
        throw ValidationException::withMessages([
            'tipo_combustible_id' => 'GAMS solo puede emitir vales de gasolina o diésel.',
        ]);
    }

    $datos = array_merge(
        $datos,
        $this->validarUbicacionGams($tipoCombustible, $datos, emision: true)
    );

    $vehiculo = Vehiculo::with([
        'unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ])
        ->where('estado', true)
        ->findOrFail($datos['vehiculo_id']);

    if (!$vehiculo->apertura_programatica_id) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'vehiculo_id' =>
                'El vehículo seleccionado no tiene una Apertura Programática asignada. Edite el vehículo y asigne AP, FF y OF antes de emitir el vale.',
        ]);
    }

    if (!$vehiculo->aperturaProgramatica) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'vehiculo_id' =>
                'La Apertura Programática asignada al vehículo no existe.',
        ]);
    }

    if (!$vehiculo->aperturaProgramatica->estado) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'vehiculo_id' =>
                'La Apertura Programática asignada al vehículo está inactiva.',
        ]);
    }

    $datos['apertura_programatica_id'] =
        $vehiculo->apertura_programatica_id;

    $datos['observacion'] = null;

    $vale = DB::transaction(function () use ($datos, $tipoCombustible) {


            $anio = (int) date(
                'Y',
                strtotime($datos['fecha'])
            );

/*
|--------------------------------------------------------------------------
| BLOQUEAR SERIE ACTIVA
|--------------------------------------------------------------------------
|
| Esto evita que dos usuarios puedan emitir el mismo número
| simultáneamente.
|
*/

$codigoSerie = NumeracionVale::codigoSerie(
    null,
    'GAMS',
    $tipoCombustible->nombre
);

try {
    $serie = NumeracionVale::bloquear($anio, $codigoSerie);
} catch (\RuntimeException $exception) {
    if (
        str_contains($exception->getMessage(), 'no está configurada')
        || str_contains($exception->getMessage(), 'Falta la serie histórica')
        || str_contains($exception->getMessage(), 'EN_CONFIGURACION')
    ) {
        throw ValidationException::withMessages([
            'fecha' => $this->mensajeSerieNoConfigurada($exception),
        ]);
    }

    throw $exception;
}

/*
|--------------------------------------------------------------------------
| SIGUIENTE NÚMERO DE LA SERIE ACTIVA
|--------------------------------------------------------------------------
|
| Cada serie mantiene su propio correlativo.
| No se consideran números pertenecientes a series anteriores.
|
*/

$numero = ((int) $serie->ultimo_numero) + 1;

/*
|--------------------------------------------------------------------------
| VERIFICAR QUE EL NÚMERO NO EXISTA EN LA SERIE ACTIVA
|--------------------------------------------------------------------------
|
| El mismo número puede existir en una serie anterior.
| Lo que no puede repetirse es dentro de la misma serie.
|
*/

$existe = ValeCombustible::query()
    ->where('serie_correlativa_id', $serie->id)
    ->where('numero_vale', $numero)
    ->exists();

if ($existe) {
    throw ValidationException::withMessages([
        'numero_vale' =>
            'El número de vale ' .
            NumeracionVale::formatear(
    $codigoSerie,
    $anio,
    $numero
) .
            ' ya existe en la serie activa.',
    ]);
}

/*
|--------------------------------------------------------------------------
| ACTUALIZAR NUMERACIÓN DE LA SERIE
|--------------------------------------------------------------------------
*/

NumeracionVale::avanzar(
    $serie,
    $numero
);

/*
|--------------------------------------------------------------------------
| CREAR VALE GAMS
|--------------------------------------------------------------------------
|
| El vale se asocia a la serie correlativa nueva. La FK histórica
| serie_vale_id se deja intacta para los registros anteriores.
|
*/

return ValeCombustible::create([
    'serie_vale_id' => null,
    'serie_correlativa_id' => $serie->id,
    'numero_vale' => $numero,
    'anio' => $anio,
    'fecha' => $datos['fecha'],
    'tipo_combustible_id' => $datos['tipo_combustible_id'],
    'estacion_servicio_id' => $datos['estacion_servicio_id'],
    'lugar_entrega_id' => $datos['lugar_entrega_id'],

    'vehiculo_id' => $datos['vehiculo_id'],

    'apertura_programatica_id' =>
        $datos['apertura_programatica_id'],

    'kilometraje' =>
        $datos['kilometraje'] ?? null,

    'observacion' =>
        $datos['observacion'] ?? null,

    'detalle_asignacion' =>
        $datos['detalle_asignacion'],

    'usuario_id' =>
        auth()->id(),

    'estado' => 'EMITIDO',
]);
        }, 3);

        /*
        |--------------------------------------------------------------------------
        | EMITIR E IMPRIMIR
        |--------------------------------------------------------------------------
        */

        if ($request->boolean('imprimir')) {

            return redirect()->route(
                'vales-combustible.imprimir',
                [
                    'valeCombustible' => $vale,
                    'auto' => 1
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EMITIR SIN IMPRIMIR
        |--------------------------------------------------------------------------
        */


return redirect()
    ->route('vales-combustible.index', [
        'tipo' => 'GAMS',
        'estado' => 'EMITIDO',
    ])
    ->with(
        'success',
        'Vale GAMS N.º ' .
NumeracionVale::formatear(
    $vale->tipoCombustible &&
    str_contains(mb_strtoupper($vale->tipoCombustible->nombre), 'GASOLINA')
        ? 'GAMS-GAS'
        : 'GAMS-DIE',
    (int) $vale->anio,
    (int) $vale->numero_vale
).
        ' emitido correctamente.'
    );
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR VALE
    |--------------------------------------------------------------------------
    */

    public function show(
        ValeCombustible $valeCombustible
    ) {
        $valeCombustible->load([
            'loteVale',
            'periodo',
            'tipoCombustible',
            'serieCorrelativa',
            'estacionServicio',
            'lugarEntrega',
            'vehiculo.unidad',
            'aperturaProgramatica',
            'usuario'
        ]);

        return view(
            'vales_combustible.show',
            compact('valeCombustible')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | IMPRIMIR VALE
    |--------------------------------------------------------------------------
    */

    public function imprimir(
        ValeCombustible $valeCombustible
    ) {
        abort_if(
            !$valeCombustible->entrega_policia_id &&
            $valeCombustible->estado === 'ANULADO',
            409,
            'No se pueden imprimir vales anulados.'
        );

        if (
            $valeCombustible->entrega_policia_id &&
            $valeCombustible->estado === 'ENTREGADO'
        ) {

            return view(
                'entregas_policia.imprimir',
                [
                    'entregaPolicia' =>
                        $valeCombustible->entregaPolicia,

                    'vales' =>
                        collect([$valeCombustible])
                ]
            );
        }

        $valeCombustible->load([
            'periodo',
            'vehiculo.unidad',
            'aperturaProgramatica',
            'usuario',
            'tipoCombustible',
            'serieCorrelativa',
            'estacionServicio',
            'lugarEntrega'
        ]);

        $numeroValeFormateado = $valeCombustible->numero_vale_formateado;

        return view(
            'vales_combustible.imprimir',
            compact('valeCombustible', 'numeroValeFormateado')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR VALE POLICÍA
    |--------------------------------------------------------------------------
    */

  public function edit(ValeCombustible $valeCombustible)
{
    /*
    |--------------------------------------------------------------------------
    | POLICÍA
    |--------------------------------------------------------------------------
    */

    if ($valeCombustible->entrega_policia_id !== null) {

        abort_unless(
            in_array($valeCombustible->estado, ['ENTREGADO', 'UTILIZADO']),
            409,
            'Este vale de Policía no puede modificarse.'
        );

        $valeCombustible->load([
            'entregaPolicia',
            'loteVale.periodo',
            'vehiculo.unidad',
            'vehiculo.aperturaProgramatica.fuenteFinanciamiento',
            'vehiculo.aperturaProgramatica.organismoFinanciador',
            'tipoCombustible',
            'estacionServicio',
        ]);

        $vehiculos = Vehiculo::with([
            'unidad',
            'aperturaProgramatica.fuenteFinanciamiento',
            'aperturaProgramatica.organismoFinanciador',
        ])
            ->where(function ($query) use ($valeCombustible) {

                $query->where('estado', true);

                if ($valeCombustible->vehiculo_id) {
                    $query->orWhere(
                        'id',
                        $valeCombustible->vehiculo_id
                    );
                }
            })
            ->orderBy('codigo')
            ->get();

        $tiposCombustible = TipoCombustible::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $estacionesServicio = EstacionServicio::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'vales_combustible.registrar_uso_policia',
            compact(
                'valeCombustible',
                'vehiculos',
                'tiposCombustible',
                'estacionesServicio'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GAMS
    |--------------------------------------------------------------------------
    */

    abort_unless(
        in_array(
            $valeCombustible->estado,
            ['EMITIDO', 'COMPLETADO']
        ),
        409,
        'Este vale GAMS no puede modificarse.'
    );

    $valeCombustible->load([
        'vehiculo.unidad',
        'vehiculo.aperturaProgramatica.fuenteFinanciamiento',
        'vehiculo.aperturaProgramatica.organismoFinanciador',
        'tipoCombustible',
        'serieCorrelativa',
        'estacionServicio',
        'lugarEntrega',
    ]);

    $vehiculos = Vehiculo::with([
        'unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ])
        ->where(function ($query) use ($valeCombustible) {

            $query->where('estado', true);

            if ($valeCombustible->vehiculo_id) {
                $query->orWhere(
                    'id',
                    $valeCombustible->vehiculo_id
                );
            }
        })
        ->orderBy('codigo')
        ->get();

    $tiposCombustible = TipoCombustible::where('estado', true)
        ->orderBy('nombre')
        ->get();

    $lugaresEntrega = LugarEntrega::where('estado', true)
        ->when($valeCombustible->lugar_entrega_id, function ($query) use ($valeCombustible) {
            $query->orWhere('id', $valeCombustible->lugar_entrega_id);
        })
        ->orderBy('nombre')
        ->get();

    $estacionesServicio = EstacionServicio::where('estado', true)
        ->when($valeCombustible->estacion_servicio_id, function ($query) use ($valeCombustible) {
            $query->orWhere('id', $valeCombustible->estacion_servicio_id);
        })
        ->orderBy('nombre')
        ->get();

    return view(
        'vales_combustible.edit',
        compact(
            'valeCombustible',
            'vehiculos',
            'tiposCombustible',
            'estacionesServicio',
            'lugaresEntrega'
        )
    );
}    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR VALE POLICÍA
    |--------------------------------------------------------------------------
    */

   public function update(
    Request $request,
    ValeCombustible $valeCombustible
) {
    /*
    |--------------------------------------------------------------------------
    | POLICÍA
    |--------------------------------------------------------------------------
    */

    if ($valeCombustible->entrega_policia_id !== null) {

        abort_unless(
    in_array($valeCombustible->estado, ['ENTREGADO', 'UTILIZADO']),
    409,
    'Este vale de Policía no puede modificarse.'
);
        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR DECIMALES
        |--------------------------------------------------------------------------
        */

        $this->normalizarDecimales($request);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR TODOS LOS DATOS
        |--------------------------------------------------------------------------
        */

        $datos = $request->validate([

    'fecha' => [
    'required',
    'date',
],
    'vehiculo_id' => [
        'required',
        'integer',
        'exists:vehiculos,id',
    ],

   'kilometraje' => [
    'nullable',
    'string',
    'max:50',
],

    'tipo_combustible_id' => [
        'required',
        'integer',
        'exists:tipos_combustible,id',
    ],

    'estacion_servicio_id' => [
        'required',
        'integer',
        'exists:estaciones_servicio,id',
    ],

    'numero_factura' => [
        'required',
        'string',
        'max:50',
    ],

    'cantidad_litros' => [
        'required',
        'numeric',
        'gt:0',
        'regex:/^\d{1,8}(?:\.\d{1,13})?$/',
    ],

    'precio_unitario' => [
        'required',
        'numeric',
        'gt:0',
        'regex:/^\d{1,8}(?:\.\d{1,4})?$/',
    ],
    'observacion' => [
    'nullable',
    'string',
    'max:1000',
],
    'detalle_asignacion' => [
        'nullable',
        'string',
        'max:1000',
    ],
]);
if (
    $valeCombustible->estado === 'ENTREGADO'
    && trim((string) ($datos['detalle_asignacion'] ?? '')) === ''
) {
    throw ValidationException::withMessages([
        'detalle_asignacion' => 'El detalle de asignación es obligatorio para registrar el uso del vale.',
    ]);
}
$this->validarKilometrajeAnterior(
    (int) $datos['vehiculo_id'],
    $datos['kilometraje'] ?? null,
    $valeCombustible->id
);


        /*
        |--------------------------------------------------------------------------
        | BUSCAR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $vehiculo = Vehiculo::with([
            'unidad',
            'aperturaProgramatica.fuenteFinanciamiento',
            'aperturaProgramatica.organismoFinanciador',
        ])
            ->where('estado', true)
            ->find($datos['vehiculo_id']);


        if (!$vehiculo) {

            throw ValidationException::withMessages([
                'vehiculo_id' =>
                    'El vehículo seleccionado no existe o está inactivo.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR AP DEL VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (!$vehiculo->apertura_programatica_id) {

            throw ValidationException::withMessages([
                'vehiculo_id' =>
                    'El vehículo seleccionado no tiene una Apertura Programática asignada.',
            ]);
        }


        if (!$vehiculo->aperturaProgramatica) {

            throw ValidationException::withMessages([
                'vehiculo_id' =>
                    'La Apertura Programática asignada al vehículo no existe.',
            ]);
        }


        if (!$vehiculo->aperturaProgramatica->estado) {

            throw ValidationException::withMessages([
                'vehiculo_id' =>
                    'La Apertura Programática asignada al vehículo está inactiva.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CALCULAR TOTAL
        |--------------------------------------------------------------------------
        */

        $datos['total'] = $this->calcularTotal($datos);


        /*
        |--------------------------------------------------------------------------
        | GUARDAR TODO EN UNA SOLA OPERACIÓN
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $valeCombustible,
            $datos,
            $vehiculo
        ) {

            $vale = ValeCombustible::lockForUpdate()
                ->findOrFail($valeCombustible->id);


            abort_unless(
    $vale->entrega_policia_id !== null &&
    in_array($vale->estado, ['ENTREGADO', 'UTILIZADO']),
    409,
    'Este vale de Policía no puede modificarse.'
);


          $vale->update([

    'fecha' =>
        $datos['fecha'],

    'vehiculo_id' =>
        $vehiculo->id,

    'apertura_programatica_id' =>
        $vehiculo->apertura_programatica_id,

    'kilometraje' =>
        $datos['kilometraje'],

    'tipo_combustible_id' =>
        $datos['tipo_combustible_id'],

    'estacion_servicio_id' =>
        $datos['estacion_servicio_id'],

    'numero_factura' =>
        $datos['numero_factura'],

    'cantidad_litros' =>
        $datos['cantidad_litros'],

    'precio_unitario' =>
        $datos['precio_unitario'],

    'total' =>
        $datos['total'],

    'detalle_asignacion' =>
        trim((string) ($datos['detalle_asignacion'] ?? '')) !== ''
            ? $datos['detalle_asignacion']
            : $vale->detalle_asignacion,

'observacion' => $datos['observacion'] ?? null,

'estado' => 'UTILIZADO',

'uso_registrado_por' => $vale->uso_registrado_por ?? auth()->id(),

'uso_registrado_en' => $vale->uso_registrado_en ?? now(),
]);

        }, 3);


        return redirect()
            ->route(
                'entregas-policia.show',
                $valeCombustible->entrega_policia_id
            )
            ->with(
                'success',
                'Uso del vale de Policía registrado correctamente.'
            );
    }


   /*
|--------------------------------------------------------------------------
| GAMS
|--------------------------------------------------------------------------
*/

abort_unless(
    $valeCombustible->entrega_policia_id === null &&
    in_array(
        $valeCombustible->estado,
        ['EMITIDO', 'COMPLETADO']
    ),
    409,
    'Este vale GAMS no puede modificarse.'
);

$this->normalizarDecimales($request);

$estaCompletado =
    $valeCombustible->estado === 'COMPLETADO';


$reglas = [

    'fecha' => [
        'required',
        'date',
        'before_or_equal:today',
    ],

    'vehiculo_id' => [
        'required',
        'integer',
        'exists:vehiculos,id',
    ],

    'kilometraje' => [
        'nullable',
        'string',
        'max:50',
    ],

    'estacion_servicio_id' => [
        'nullable',
        'integer',
        'exists:estaciones_servicio,id',
    ],

    'lugar_entrega_id' => [
        'nullable',
        'integer',
        'exists:lugares_entrega,id',
    ],
];


/*
|--------------------------------------------------------------------------
| DATOS DE CARGA PARA VALES COMPLETADOS
|--------------------------------------------------------------------------
*/

if ($estaCompletado) {

    $reglas['tipo_combustible_id'] = $valeCombustible->tipo_combustible_id === null
        ? ['required', 'integer', 'exists:tipos_combustible,id']
        : ['required', 'integer', 'in:' . $valeCombustible->tipo_combustible_id];

    $reglas['numero_factura'] = [
        'required',
        'string',
        'max:50',
    ];

    $reglas['cantidad_litros'] = [
        'required',
        'numeric',
        'gt:0',
        'regex:/^\d{1,8}(?:\.\d{1,13})?$/',
    ];

    $reglas['precio_unitario'] = [
        'required',
        'numeric',
        'gt:0',
        'regex:/^\d{1,8}(?:\.\d{1,4})?$/',
    ];

    $reglas['observacion'] = [
        'nullable',
        'string',
        'max:1000',
    ];
}


$datos = $request->validate($reglas);

$tipoVale = null;
if ($estaCompletado) {
    $tipoVale = TipoCombustible::findOrFail($datos['tipo_combustible_id']);
    if (!$tipoVale->estado && (int) $tipoVale->id !== (int) $valeCombustible->tipo_combustible_id) {
        throw ValidationException::withMessages([
            'tipo_combustible_id' => 'Seleccione un combustible activo.',
        ]);
    }

    if ($valeCombustible->tipo_combustible_id === null) {
        $codigoCombustible = NumeracionVale::codigoSerie(null, 'GAMS', $tipoVale->nombre);
        $serie = $valeCombustible->serieCorrelativa()->first();
        if ($codigoCombustible === null || !$serie || $serie->codigo_serie !== $codigoCombustible) {
            throw ValidationException::withMessages([
                'tipo_combustible_id' => 'No se puede asignar ese combustible porque no coincide con la serie correlativa del vale.',
            ]);
        }
    }
} elseif ($valeCombustible->tipo_combustible_id !== null) {
    $tipoVale = TipoCombustible::findOrFail($valeCombustible->tipo_combustible_id);
}

if ($tipoVale !== null) {
    $datos = array_merge(
        $datos,
        $this->validarUbicacionGams($tipoVale, $datos, $valeCombustible)
    );
}

$this->validarKilometrajeAnterior(
    (int) $datos['vehiculo_id'],
    $datos['kilometraje'] ?? null,
    $valeCombustible->id
);

/*
|--------------------------------------------------------------------------
| VEHÍCULO
|--------------------------------------------------------------------------
*/

$vehiculo = Vehiculo::with([
    'unidad',
    'aperturaProgramatica.fuenteFinanciamiento',
    'aperturaProgramatica.organismoFinanciador',
])
    ->findOrFail($datos['vehiculo_id']);


if (!$vehiculo->estado) {

    throw ValidationException::withMessages([
        'vehiculo_id' =>
            'El vehículo seleccionado está inactivo.',
    ]);
}


if (!$vehiculo->apertura_programatica_id) {

    throw ValidationException::withMessages([
        'vehiculo_id' =>
            'El vehículo seleccionado no tiene una Apertura Programática asignada.',
    ]);
}


if (!$vehiculo->aperturaProgramatica) {

    throw ValidationException::withMessages([
        'vehiculo_id' =>
            'La Apertura Programática asignada al vehículo no existe.',
    ]);
}


if (!$vehiculo->aperturaProgramatica->estado) {

    throw ValidationException::withMessages([
        'vehiculo_id' =>
            'La Apertura Programática asignada al vehículo está inactiva.',
    ]);
}


$datos['apertura_programatica_id'] =
    $vehiculo->apertura_programatica_id;


/*
|--------------------------------------------------------------------------
| RECALCULAR TOTAL SI ESTÁ COMPLETADO
|--------------------------------------------------------------------------
*/

if ($estaCompletado) {

    $datos['total'] =
        $this->calcularTotal($datos);
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR
|--------------------------------------------------------------------------
*/

DB::transaction(function () use (
    $valeCombustible,
    $datos,
    $estaCompletado
) {

    $vale = ValeCombustible::lockForUpdate()
        ->findOrFail($valeCombustible->id);


    abort_unless(
        $vale->entrega_policia_id === null &&
        in_array(
            $vale->estado,
            ['EMITIDO', 'COMPLETADO']
        ),
        409,
        'Este vale GAMS no puede modificarse.'
    );


    $actualizacion = [

        'fecha' =>
            $datos['fecha'],

        'vehiculo_id' =>
            $datos['vehiculo_id'],

        'kilometraje' =>
            $datos['kilometraje'] ?? null,

        'apertura_programatica_id' =>
            $datos['apertura_programatica_id'],

        'estacion_servicio_id' =>
            $datos['estacion_servicio_id'] ?? $valeCombustible->estacion_servicio_id,

        'lugar_entrega_id' =>
            $datos['lugar_entrega_id'] ?? $valeCombustible->lugar_entrega_id,
    ];


    if ($estaCompletado) {

        $actualizacion += [

            'tipo_combustible_id' =>
                $datos['tipo_combustible_id'],

            'numero_factura' =>
                $datos['numero_factura'],

            'cantidad_litros' =>
                $datos['cantidad_litros'],

            'precio_unitario' =>
                $datos['precio_unitario'],

            'total' =>
                $datos['total'],

            'observacion' =>
                $datos['observacion'] ?? null,

            'estado' =>
                'COMPLETADO',
        ];
    }


    $vale->update($actualizacion);

}, 3);


return redirect()
    ->route(
        'vales-combustible.show',
        $valeCombustible
    )
    ->with(
        'success',
        $estaCompletado
            ? 'Vale completado actualizado correctamente.'
            : 'Vale actualizado correctamente.'
    );}
    /*
    |--------------------------------------------------------------------------
    | FORMULARIO COMPLETAR GAMS
    |--------------------------------------------------------------------------
    */

    public function completarFormulario(
    ValeCombustible $valeCombustible
) {
    abort_unless(
        !$valeCombustible->entrega_policia_id &&
        $valeCombustible->estado === 'EMITIDO',
        409,
        'Solo se pueden completar vales normales en estado EMITIDO.'
    );

    $valeCombustible->load([
        'periodo',
        'loteVale',
        'estacionServicio',
        'lugarEntrega',
        'tipoCombustible',
        'serieCorrelativa',
        'vehiculo.unidad',
        'vehiculo.aperturaProgramatica.fuenteFinanciamiento',
        'vehiculo.aperturaProgramatica.organismoFinanciador',
    ]);

    $tiposCombustible = TipoCombustible::where(function ($query) use ($valeCombustible) {
        $query->where('estado', true);

        if ($valeCombustible->tipo_combustible_id !== null) {
            $query->orWhere('id', $valeCombustible->tipo_combustible_id);
        }
    })
        ->orderBy('nombre')
        ->get();

    $estacionesServicio = EstacionServicio::where(function ($query) use ($valeCombustible) {
        $query->where('estado', true);

        if ($valeCombustible->estacion_servicio_id !== null) {
            $query->orWhere('id', $valeCombustible->estacion_servicio_id);
        }
    })
        ->orderBy('nombre')
        ->get();

    $lugaresEntrega = LugarEntrega::where('estado', true)
        ->when($valeCombustible->lugar_entrega_id, function ($query) use ($valeCombustible) {
            $query->orWhere('id', $valeCombustible->lugar_entrega_id);
        })
        ->orderBy('nombre')
        ->get();


    return view(
        'vales_combustible.completar',
        compact(
            'valeCombustible',
            'tiposCombustible',
            'estacionesServicio',
            'lugaresEntrega'
        )
    );

        
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETAR VALE GAMS
    |--------------------------------------------------------------------------
    */

    public function completar(
    Request $request,
    ValeCombustible $valeCombustible
) {
    $this->normalizarDecimales($request);

    $tipoCombustibleRules = $valeCombustible->tipo_combustible_id === null
        ? ['required', 'integer', 'exists:tipos_combustible,id']
        : ['sometimes', 'nullable', 'integer', 'in:' . $valeCombustible->tipo_combustible_id];

    $datos = $request->validate([

        'tipo_combustible_id' => $tipoCombustibleRules,
        'estacion_servicio_id' => ['nullable', 'integer', 'exists:estaciones_servicio,id'],
        'lugar_entrega_id' => ['nullable', 'integer', 'exists:lugares_entrega,id'],

        'numero_factura' => [
            'required',
            'string',
            'max:50',
        ],

        'cantidad_litros' => [
            'required',
            'numeric',
            'gt:0',
            'regex:/^\d{1,8}(?:\.\d{1,13})?$/',
        ],

        'precio_unitario' => [
            'required',
            'numeric',
            'gt:0',
            'regex:/^\d{1,8}(?:\.\d{1,4})?$/',
        ],
    ]);

    $datos['total'] =
        $this->calcularTotal($datos);

    DB::transaction(function () use (
        $valeCombustible,
        $datos
    ) {

        $vale = ValeCombustible::lockForUpdate()
            ->findOrFail($valeCombustible->id);

        abort_unless(
            !$vale->entrega_policia_id &&
            $vale->estado === 'EMITIDO',
            409,
            'Solo se pueden completar vales normales en estado EMITIDO.'
        );

        $tipoCombustibleId = $vale->tipo_combustible_id
            ?? $datos['tipo_combustible_id']
            ?? null;
        if ($tipoCombustibleId === null) {
            throw ValidationException::withMessages([
                'tipo_combustible_id' => 'Seleccione el tipo de combustible.',
            ]);
        }

        $tipo = TipoCombustible::findOrFail($tipoCombustibleId);
        if (!$tipo->estado && (int) $tipoCombustibleId !== (int) $vale->tipo_combustible_id) {
            throw ValidationException::withMessages([
                'tipo_combustible_id' => 'Seleccione un combustible activo.',
            ]);
        }

        if ($vale->serie_correlativa_id) {
            $codigoCombustible = NumeracionVale::codigoSerie(null, 'GAMS', $tipo->nombre);
            $serie = $vale->serieCorrelativa()->first();
            if ($codigoCombustible === null || !$serie || $serie->codigo_serie !== $codigoCombustible) {
                throw ValidationException::withMessages([
                    'tipo_combustible_id' => 'El combustible no corresponde a la serie correlativa de este vale.',
                ]);
            }
        }

        $ubicacion = $this->validarUbicacionGams($tipo, $datos, $vale);

        $vale->update([

            'tipo_combustible_id' =>
                $tipoCombustibleId,

            'estacion_servicio_id' =>
                $ubicacion['estacion_servicio_id'],

            'lugar_entrega_id' =>
                $ubicacion['lugar_entrega_id'],

            'numero_factura' =>
                $datos['numero_factura'],

            'cantidad_litros' =>
                $datos['cantidad_litros'],

            'precio_unitario' =>
                $datos['precio_unitario'],

            'total' =>
                $datos['total'],

            'estado' =>
                'COMPLETADO',
        ]);

    }, 3);

    return redirect()
        ->route(
            'vales-combustible.show',
            $valeCombustible
        )
        ->with(
            'success',
            'Vale completado correctamente.'
        );
}
    /*
    |--------------------------------------------------------------------------
    | ANULAR VALE GAMS
    |--------------------------------------------------------------------------
    */

    public function anular(
        Request $request,
        ValeCombustible $valeCombustible
    ) {
        $datos = $request->validate([
            'motivo_anulacion' =>
                'required|string|max:1000',
        ]);

        DB::transaction(
            function () use (
                $valeCombustible,
                $datos
            ) {

                $vale =
                    ValeCombustible::lockForUpdate()
                        ->findOrFail(
                            $valeCombustible->id
                        );

                abort_unless(
                    !$vale->entrega_policia_id &&
                    $vale->estado === 'EMITIDO',
                    409,
                    'Solo se pueden anular vales normales en estado EMITIDO.'
                );

                $vale->update([
                    'estado' =>
                        'ANULADO',

                    'motivo_anulacion' =>
                        $datos['motivo_anulacion'],

                    'fecha_anulacion' =>
                        now(),
                ]);
            },
            3
        );

        return redirect()
            ->route(
                'vales-combustible.show',
                $valeCombustible
            )
            ->with(
                'success',
                'Vale anulado. El número queda conservado y no podrá reutilizarse.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

   public function destroy(ValeCombustible $valeCombustible)
{
    abort(403, 'Los vales emitidos no pueden eliminarse. Utilice la opción Anular para conservar el historial y la numeración.');
}}