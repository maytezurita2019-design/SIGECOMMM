<?php

namespace App\Http\Controllers;

use App\Models\{
    ValeCombustible,
    LoteVale,
    Vehiculo,
    AperturaProgramatica,
    TipoCombustible,
    EstacionServicio
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ValeCombustibleController extends Controller
{
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
        ]);

        $consulta = ValeCombustible::with([
            'periodo',
            'tipoCombustible',
            'estacionServicio',
            'loteVale',
            'vehiculo.unidad',
            'aperturaProgramatica',
            'usuario',
            'entregaPolicia'
        ]);

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

    public function create()
{
    $vehiculos = Vehiculo::with([
        'unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ])
        ->where('estado', true)
        ->orderBy('codigo')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | ÚLTIMO NÚMERO ENTREGADO A POLICÍA
    |--------------------------------------------------------------------------
    */

    $ultimoPolicia = \App\Models\EntregaPolicia::query()
        ->max('numero_final');

    /*
    |--------------------------------------------------------------------------
    | ÚLTIMO VALE GAMS
    |--------------------------------------------------------------------------
    */

    $ultimoGams = ValeCombustible::query()
        ->whereNull('entrega_policia_id')
        ->max('numero_vale');

    /*
    |--------------------------------------------------------------------------
    | SIGUIENTE NÚMERO
    |--------------------------------------------------------------------------
    */

    $ultimoNumero = max(
        (int) ($ultimoPolicia ?? 0),
        (int) ($ultimoGams ?? 0)
    );

    $numeroSugerido = sprintf(
        '%06d',
        $ultimoNumero + 1
    );

    return view(
        'vales_combustible.create',
        compact(
            'vehiculos',
            'numeroSugerido'
        )
    );
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

        'vehiculo_id' =>
            'required|exists:vehiculos,id,estado,1',

        'kilometraje' => [
    'nullable',
    'string',
    'max:50',
],
    ]);

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

    $vale = DB::transaction(function () use ($datos) {

        // AQUÍ CONTINÚA TODO TU CÓDIGO ACTUAL...

            /*
            |--------------------------------------------------------------------------
            | AÑO DEL VALE
            |--------------------------------------------------------------------------
            */

            $anio = (int) date(
                'Y',
                strtotime($datos['fecha'])
            );

            /*
            |--------------------------------------------------------------------------
            | BLOQUEAR NUMERACIÓN
            |--------------------------------------------------------------------------
            |
            | Esto evita que dos usuarios puedan emitir el mismo número
            | simultáneamente.
            |
            */

            $serie = \App\Models\NumeracionVale::bloquear(
                $anio
            );

            /*
            |--------------------------------------------------------------------------
            | ÚLTIMO NÚMERO DE POLICÍA
            |--------------------------------------------------------------------------
            |
            | NO filtramos por período.
            | Se toma el último número entregado registrado.
            |
            */

            $ultimoPolicia =
                \App\Models\EntregaPolicia::query()
                    ->max('numero_final');

            /*
            |--------------------------------------------------------------------------
            | ÚLTIMO VALE GAMS
            |--------------------------------------------------------------------------
            */

            $ultimoGams =
                ValeCombustible::query()
                    ->whereNull(
                        'entrega_policia_id'
                    )
                    ->max('numero_vale');

            /*
            |--------------------------------------------------------------------------
            | ÚLTIMO CORRELATIVO REAL
            |--------------------------------------------------------------------------
            */

            $ultimoNumero = max(
                (int) $serie->ultimo_numero,
                (int) ($ultimoPolicia ?? 0),
                (int) ($ultimoGams ?? 0)
            );

            /*
            |--------------------------------------------------------------------------
            | SIGUIENTE NÚMERO
            |--------------------------------------------------------------------------
            */

            $numero = $ultimoNumero + 1;

            $numeroFormateado = sprintf(
                '%06d',
                $numero
            );

            /*
            |--------------------------------------------------------------------------
            | VERIFICAR QUE EL NÚMERO NO EXISTA
            |--------------------------------------------------------------------------
            */

            $existe =
                ValeCombustible::query()
                    ->where(
                        'numero_vale',
                        $numeroFormateado
                    )
                    ->exists();

            if ($existe) {

                throw ValidationException::withMessages([
                    'numero_vale' =>
                        'El número de vale ' .
                        $numeroFormateado .
                        ' ya existe.'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR NUMERACIÓN
            |--------------------------------------------------------------------------
            */

            DB::table('series_vales')
                ->where('id', $serie->id)
                ->update([
                    'ultimo_numero' => $numero,
                    'updated_at' => now(),
                ]);

            /*
            |--------------------------------------------------------------------------
            | CREAR VALE GAMS
            |--------------------------------------------------------------------------
            |
            | GAMS ya NO utiliza lote_vale_id.
            |
            */

            return ValeCombustible::create([
                'serie_vale_id' => $serie->id,
                'numero_vale' => $numero,
                'anio' => $anio,
                'fecha' => $datos['fecha'],


                'vehiculo_id' => $datos['vehiculo_id'],

                'apertura_programatica_id' =>
                    $datos['apertura_programatica_id'],

                'kilometraje' =>
                    $datos['kilometraje'],

                'observacion' =>
                    $datos['observacion'] ?? null,

                'usuario_id' =>
                    auth()->id(),

                'estado' =>
                    'EMITIDO',
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
        sprintf('%06d', (int) $vale->numero_vale) .
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
            'estacionServicio',
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
            'estacionServicio'
        ]);

        return view(
            'vales_combustible.imprimir',
            compact('valeCombustible')
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
        'vales_combustible.edit',
        compact(
            'valeCombustible',
            'vehiculos',
            'tiposCombustible',
            'estacionesServicio'
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
]);


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

        'observacion' => $datos['observacion'] ?? null,

    'estado' =>
        'UTILIZADO',
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
];


/*
|--------------------------------------------------------------------------
| DATOS DE CARGA PARA VALES COMPLETADOS
|--------------------------------------------------------------------------
*/

if ($estaCompletado) {

    $reglas['tipo_combustible_id'] = [
        'required',
        'integer',
        'exists:tipos_combustible,id',
    ];

    $reglas['estacion_servicio_id'] = [
        'required',
        'integer',
        'exists:estaciones_servicio,id',
    ];

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
    ];


    if ($estaCompletado) {

        $actualizacion += [

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
        'tipoCombustible',
        'vehiculo.unidad',
        'vehiculo.aperturaProgramatica.fuenteFinanciamiento',
        'vehiculo.aperturaProgramatica.organismoFinanciador',
    ]);

    /*
    |--------------------------------------------------------------------------
    | TIPOS DE COMBUSTIBLE ACTIVOS
    |--------------------------------------------------------------------------
    */

    $tiposCombustible = TipoCombustible::where('estado', true)
        ->orderBy('nombre')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | ESTACIONES DE SERVICIO ACTIVAS
    |--------------------------------------------------------------------------
    */

    $estacionesServicio = EstacionServicio::where('estado', true)
        ->orderBy('nombre')
        ->get();


    return view(
        'vales_combustible.completar',
        compact(
            'valeCombustible',
            'tiposCombustible',
            'estacionesServicio'
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

    $datos = $request->validate([

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

        $vale->update([

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
    /*
    |--------------------------------------------------------------------------
    | SOLO VALES GAMS
    |--------------------------------------------------------------------------
    */

    abort_if(
        $valeCombustible->entrega_policia_id !== null,
        409,
        'Los vales de Policía no pueden eliminarse desde esta opción.'
    );

    /*
    |--------------------------------------------------------------------------
    | SOLO VALES PENDIENTES
    |--------------------------------------------------------------------------
    */

    abort_unless(
        $valeCombustible->estado === 'EMITIDO',
        409,
        'Solo se pueden eliminar vales pendientes que todavía no fueron cargados.'
    );

    /*
    |--------------------------------------------------------------------------
    | SOLO ADMINISTRADOR O AUXILIAR
    |--------------------------------------------------------------------------
    */

    $rol = mb_strtoupper(
        auth()->user()->rol->nombre ?? ''
    );

    abort_unless(
        str_contains($rol, 'ADMIN') ||
        str_contains($rol, 'AUX'),
        403,
        'No tiene permisos para eliminar vales.'
    );

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use ($valeCombustible) {

        $vale = ValeCombustible::lockForUpdate()
            ->findOrFail($valeCombustible->id);

        abort_unless(
            $vale->estado === 'EMITIDO',
            409,
            'El vale ya no está pendiente y no puede eliminarse.'
        );

        abort_if(
            $vale->entrega_policia_id !== null,
            409,
            'Los vales de Policía no pueden eliminarse desde esta opción.'
        );

        $vale->delete();
    });

    return redirect()
        ->route('vales-combustible.index', [
            'tipo' => 'GAMS'
        ])
        ->with(
            'success',
            'Vale eliminado correctamente. El número del vale no volverá a utilizarse.'
        );

    }
}