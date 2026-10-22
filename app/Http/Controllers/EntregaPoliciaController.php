<?php

namespace App\Http\Controllers;

use App\Models\{
    EntregaPolicia,
    LoteVale,
    NumeracionVale,
    ValeCombustible,
    AperturaProgramatica
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EntregaPoliciaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'mes' => 'nullable|integer|between:1,12',
            'anio' => 'nullable|integer',
            'estado' => 'nullable|in:ENTREGADO,UTILIZADO,DEVUELTO,ANULADO',
            'por_pagina' => 'nullable|integer|in:10,30',
        ]);

        $porPagina = (int) $request->input('por_pagina', 30);

        $anios = LoteVale::query()
            ->join(
                'periodos',
                'periodos.id',
                '=',
                'lotes_vales.periodo_id'
            )
            ->select('periodos.anio')
            ->distinct()
            ->orderByDesc('periodos.anio')
            ->pluck('periodos.anio');

        $entregas = EntregaPolicia::with('loteVale.periodo')
            ->withCount([
                'vales as pendientes' => fn ($q) =>
                    $q->where('estado', 'ENTREGADO'),

                'vales as utilizados' => fn ($q) =>
                    $q->where('estado', 'UTILIZADO'),

                'vales as devueltos' => fn ($q) =>
                    $q->where('estado', 'DEVUELTO'),

                'vales as anulados' => fn ($q) =>
                    $q->where('estado', 'ANULADO'),
            ])

            ->when(
                $request->filled('mes'),
                function ($query) use ($request) {
                    $query->whereMonth(
                        'fecha_entrega',
                        $request->mes
                    );
                }
            )

            ->when(
                $request->filled('anio'),
                function ($query) use ($request) {
                    $query->whereHas(
                        'loteVale.periodo',
                        function ($q) use ($request) {
                            $q->where(
                                'anio',
                                $request->anio
                            );
                        }
                    );
                }
            )

            ->when(
                $request->filled('estado'),
                function ($query) use ($request) {
                    $query->whereHas(
                        'vales',
                        function ($q) use ($request) {
                            $q->where(
                                'estado',
                                $request->estado
                            );
                        }
                    );
                }
            )

            ->latest('fecha_entrega')
            ->paginate($porPagina)
            ->withQueryString();

        return view(
            'entregas_policia.index',
            compact(
                'entregas',
                'porPagina',
                'anios'
            )
        );
    }

    public function create()
    {
        $lotes = LoteVale::with('periodo')
            ->where('estado', true)
            ->whereHas('periodo', function ($q) {
                $q->where('estado', true);
            })
            ->whereDoesntHave('valesCombustible')
            ->orderBy('numero_inicial')
            ->get();

        $aperturas = AperturaProgramatica::where('estado', true)
            ->orderBy('codigo')
            ->get();

        $token = (string) Str::uuid();

        return view(
            'entregas_policia.create',
            compact('lotes', 'aperturas', 'token')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'lote_vale_id' =>
                'required|exists:lotes_vales,id',

            'contrato' =>
                'required|string|max:150',

            'dependencia' =>
                'required|string|max:150',

            'recibido_por' =>
                'required|string|max:150',

            'fecha_entrega' =>
                'required|date',

            'apertura_programatica_id' =>
                'required|exists:aperturas_programaticas,id',

            'solicitud_token' =>
                'required|uuid',
        ]);

        $entrega = DB::transaction(function () use ($datos) {

            $existente = EntregaPolicia::where(
                'solicitud_token',
                $datos['solicitud_token']
            )->first();

            if ($existente) {
                return $existente;
            }

            $lote = LoteVale::with('periodo')
                ->lockForUpdate()
                ->findOrFail($datos['lote_vale_id']);

            if (
                !$lote->estado ||
                !$lote->periodo ||
                !$lote->periodo->estado
            ) {
                throw ValidationException::withMessages([
                    'lote_vale_id' =>
                        'El lote seleccionado no está disponible.',
                ]);
            }

            $loteUtilizado = ValeCombustible::where(
                'lote_vale_id',
                $lote->id
            )->exists();

            if ($loteUtilizado) {
                throw ValidationException::withMessages([
                    'lote_vale_id' =>
                        'Este lote ya tiene vales registrados y no puede entregarse completo a la Policía.',
                ]);
            }

            $fechaInicio = $lote->periodo
                ->fecha_inicio
                ->format('Y-m-d');

            $fechaFin = $lote->periodo
                ->fecha_fin
                ->format('Y-m-d');

            if (
                $datos['fecha_entrega'] < $fechaInicio ||
                $datos['fecha_entrega'] > $fechaFin
            ) {
                throw ValidationException::withMessages([
                    'fecha_entrega' =>
                        'La fecha de entrega debe estar dentro del período del lote.',
                ]);
            }

            $inicio = (int) $lote->numero_inicial;
            $fin = (int) $lote->numero_final;

            $cantidad = $fin - $inicio + 1;

            if ($cantidad < 1) {
                throw ValidationException::withMessages([
                    'lote_vale_id' =>
                        'El rango del lote seleccionado no es válido.',
                ]);
            }

            $numeroDuplicado = ValeCombustible::whereBetween(
                'numero_vale',
                [$inicio, $fin]
            )->exists();

            if ($numeroDuplicado) {
                throw ValidationException::withMessages([
                    'lote_vale_id' =>
                        'Uno o más números de este lote ya están registrados.',
                ]);
            }

            $serie = NumeracionVale::bloquear(
                (int) $lote->periodo->anio
            );

            $entrega = EntregaPolicia::create([
                'lote_vale_id' => $lote->id,

                'cantidad' => $cantidad,

                'numero_inicial' =>
                    sprintf('%06d', $inicio),

                'numero_final' =>
                    sprintf('%06d', $fin),

                'contrato' =>
                    $datos['contrato'],

                'dependencia' =>
                    $datos['dependencia'],

                'recibido_por' =>
                    $datos['recibido_por'],

                'fecha_entrega' =>
                    $datos['fecha_entrega'],

                'solicitud_token' =>
                    $datos['solicitud_token'],

                'usuario_id' =>
                    auth()->id(),
            ]);

            for ($numero = $inicio; $numero <= $fin; $numero++) {

                ValeCombustible::create([
                    'numero_vale' =>
                        sprintf('%06d', $numero),

                    'anio' =>
                        $lote->periodo->anio,

                    'lote_vale_id' =>
                        $lote->id,

                    'periodo_id' =>
                        $lote->periodo_id,

                    'entrega_policia_id' =>
                        $entrega->id,

                    'usuario_id' =>
                        auth()->id(),

                    'apertura_programatica_id' =>
                        $datos['apertura_programatica_id'],

                    'estado' =>
                        'ENTREGADO',
                ]);
            }

          if ($fin > (int) $serie->ultimo_numero) {

    DB::table('series_vales')
        ->where('id', $serie->id)
        ->update([
            'ultimo_numero' => $fin,
            'updated_at' => now(),
        ]);
}

            return $entrega;

        }, 3);

        return redirect()
            ->route(
                'entregas-policia.show',
                $entrega
            )
            ->with(
                'success',
                'Entrega registrada correctamente. Se asignaron ' .
                $entrega->cantidad .
                ' vales a la Policía.'
            );
    }

    public function show(
        Request $request,
        EntregaPolicia $entregaPolicia
    ) {
        $request->validate([
            'numero' =>
                ['nullable', 'regex:/^[0-9]+$/'],

            'estado' =>
                'nullable|in:ENTREGADO,UTILIZADO,DEVUELTO,ANULADO',

            'por_pagina' =>
    'nullable|integer|in:10,30',
        ]);

        $porPagina = (int) $request->input(
            'por_pagina',
            30
        );

        $vales = $entregaPolicia
            ->vales()
            ->with('vehiculo')

            ->when(
                $request->filled('numero'),
                function ($q) use ($request) {

                    $numero = ltrim(
                        (string) $request->numero,
                        '0'
                    );

                    if ($numero === '') {
                        $numero = '0';
                    }

                    $q->whereRaw(
                        'CAST(numero_vale AS TEXT) LIKE ?',
                        ['%' . $numero . '%']
                    );
                }
            )

            ->when(
                $request->filled('estado'),
                fn ($q) =>
                    $q->where(
                        'estado',
                        $request->estado
                    )
            )

            ->orderBy('numero_vale')
            ->paginate($porPagina)
            ->withQueryString();

        return view(
            'entregas_policia.show',
            compact(
                'entregaPolicia',
                'vales',
                'porPagina'
            )
        );
    }

    public function imprimir(
        EntregaPolicia $entregaPolicia
    ) {
        $vales = $entregaPolicia
            ->vales()
            ->where('estado', 'ENTREGADO')
            ->with('periodo')
            ->orderBy('numero_vale')
            ->get();

        return view(
            'entregas_policia.imprimir',
            compact(
                'entregaPolicia',
                'vales'
            )
        );
    }

    public function cerrar(
        Request $request,
        ValeCombustible $valeCombustible
    ) {
        $datos = $request->validate([
            'estado' =>
                'required|in:DEVUELTO,ANULADO',

            'motivo' =>
                'required|string|max:1000',
        ]);

        DB::transaction(function () use (
            $valeCombustible,
            $datos
        ) {

            $vale = ValeCombustible::lockForUpdate()
                ->findOrFail(
                    $valeCombustible->id
                );

            abort_unless(
                $vale->entrega_policia_id &&
                $vale->estado === 'ENTREGADO',
                409,
                'Solo se pueden cerrar vales pendientes.'
            );

            $vale->update([
                'estado' =>
                    $datos['estado'],

                'motivo_anulacion' =>
                    $datos['motivo'],

                'fecha_anulacion' =>
                    now(),
            ]);
        });

        return back()->with(
            'success',
            'Estado actualizado. El número se conserva y no se reutiliza.'
        );
    }
}