<?php

namespace App\Http\Controllers;

use App\Models\ValeCombustible;
use App\Models\Vehiculo;
use App\Models\Unidad;
use App\Models\Periodo;
use Illuminate\Http\Request;
use App\Models\AperturaProgramatica;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ParteDiario;
class ReporteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REPORTE GENERAL DE CONSUMO
    |--------------------------------------------------------------------------
    */
    public function consumo(Request $request)
{
    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'estacionServicio',
        'aperturaProgramatica',
        'usuario',
    ]);

    // Fecha inicial
    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate('fecha', '>=', $request->fecha_inicio);
    }

    // Fecha final
    if ($request->filled('fecha_fin')) {
        $consulta->whereDate('fecha', '<=', $request->fecha_fin);
    }

    // Vehículo
    if ($request->filled('vehiculo_id')) {
        $consulta->where('vehiculo_id', $request->vehiculo_id);
    }

    // Unidad
    if ($request->filled('unidad_id')) {
        $consulta->whereHas('vehiculo', function ($query) use ($request) {
            $query->where('unidad_id', $request->unidad_id);
        });
    }

    // Obtener vales
    $vales = $consulta
        ->orderByDesc('fecha')
        ->orderByDesc('numero_vale')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | TOTALES GENERALES
    |--------------------------------------------------------------------------
    */

    $totalVales = $vales->count();

    $totalVehiculos = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->count();

    $totalLitros = (float) $vales->sum('cantidad_litros');

    $totalMonto = (float) $vales->sum('total');


    /*
    |--------------------------------------------------------------------------
    | REPORTE CONSOLIDADO POR UNIDAD
    |--------------------------------------------------------------------------
    */

    $resumenUnidades = $vales
        ->groupBy(function ($vale) {
            return $vale->vehiculo?->unidad?->nombre ?? 'SIN UNIDAD';
        })
        ->map(function ($grupo, $unidad) {

            $gasolina = $grupo
                ->filter(function ($vale) {
                    return str_contains(
                        strtoupper($vale->tipoCombustible?->nombre ?? ''),
                        'GASOLINA'
                    );
                })
                ->sum('cantidad_litros');

            $diesel = $grupo
                ->filter(function ($vale) {
                    return str_contains(
                        strtoupper($vale->tipoCombustible?->nombre ?? ''),
                        'DIESEL'
                    );
                })
                ->sum('cantidad_litros');

            $otros = $grupo
                ->filter(function ($vale) {
                    $combustible = strtoupper(
                        $vale->tipoCombustible?->nombre ?? ''
                    );

                    return !str_contains($combustible, 'GASOLINA')
                        && !str_contains($combustible, 'DIESEL');
                })
                ->sum('cantidad_litros');

            return (object) [
                'unidad' => $unidad,

                'vehiculos' => $grupo
                    ->pluck('vehiculo_id')
                    ->filter()
                    ->unique()
                    ->count(),

                'vales' => $grupo->count(),

                'gasolina' => (float) $gasolina,

                'diesel' => (float) $diesel,

                'otros' => (float) $otros,

                'litros' => (float) $grupo->sum('cantidad_litros'),

                'total' => (float) $grupo->sum('total'),
            ];
        })
        ->sortBy('unidad')
        ->values();


    /*
    |--------------------------------------------------------------------------
    | TOTALES POR TIPO
    |--------------------------------------------------------------------------
    */

    $totalGasolina = (float) $resumenUnidades->sum('gasolina');

    $totalDiesel = (float) $resumenUnidades->sum('diesel');

    $totalOtros = (float) $resumenUnidades->sum('otros');


    /*
    |--------------------------------------------------------------------------
    | DATOS DE LOS FILTROS
    |--------------------------------------------------------------------------
    */

    $vehiculos = Vehiculo::orderBy('codigo')->get();

    $unidades = Unidad::orderBy('nombre')->get();


    return view('reportes.consumo', compact(
        'vales',
        'vehiculos',
        'unidades',
        'totalVales',
        'totalVehiculos',
        'totalLitros',
        'totalMonto',
        'totalGasolina',
        'totalDiesel',
        'totalOtros',
        'resumenUnidades'
    ));
}

public function consumoPdf(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    $request->validate([
        'fecha_inicio' => ['nullable', 'date'],
        'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        'vehiculo_id' => ['nullable', 'integer', 'exists:vehiculos,id'],
        'unidad_id' => ['nullable', 'integer', 'exists:unidades,id'],
        'texto_superior' => ['nullable', 'string'],
        'texto_inferior' => ['nullable', 'string'],
    ]);


    /*
    |--------------------------------------------------------------------------
    | CONSULTA
    |--------------------------------------------------------------------------
    */

    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'estacionServicio',
        'aperturaProgramatica',
        'usuario',
    ]);


    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }


    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }


    if ($request->filled('vehiculo_id')) {
        $consulta->where(
            'vehiculo_id',
            $request->vehiculo_id
        );
    }


    if ($request->filled('unidad_id')) {

        $consulta->whereHas(
            'vehiculo',
            function ($query) use ($request) {

                $query->where(
                    'unidad_id',
                    $request->unidad_id
                );

            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALES
    |--------------------------------------------------------------------------
    */

    $vales = $consulta
        ->orderByDesc('fecha')
        ->orderByDesc('numero_vale')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | TOTALES GENERALES
    |--------------------------------------------------------------------------
    */

    $totalVales = $vales->count();


    $totalVehiculos = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->count();


    $totalLitros = (float) $vales
        ->sum('cantidad_litros');


    $totalMonto = (float) $vales
        ->sum('total');


    /*
    |--------------------------------------------------------------------------
    | CONSOLIDADO POR UNIDAD
    |--------------------------------------------------------------------------
    */

    $resumenUnidades = $vales
        ->groupBy(function ($vale) {

            return $vale
                ->vehiculo
                ?->unidad
                ?->nombre
                ?? 'SIN UNIDAD';

        })
        ->map(function ($grupo, $unidad) {

            /*
            |--------------------------------------------------------------------------
            | GASOLINA
            |--------------------------------------------------------------------------
            */

            $gasolina = $grupo
                ->filter(function ($vale) {

                    $combustible = strtoupper(
                        $vale->tipoCombustible?->nombre ?? ''
                    );

                    return str_contains(
                        $combustible,
                        'GASOLINA'
                    );

                })
                ->sum('cantidad_litros');


            /*
            |--------------------------------------------------------------------------
            | DIÉSEL
            |--------------------------------------------------------------------------
            */

            $diesel = $grupo
                ->filter(function ($vale) {

                    $combustible = strtoupper(
                        $vale->tipoCombustible?->nombre ?? ''
                    );

                    return str_contains(
                        $combustible,
                        'DIESEL'
                    );

                })
                ->sum('cantidad_litros');


            /*
            |--------------------------------------------------------------------------
            | OTROS
            |--------------------------------------------------------------------------
            */

            $otros = $grupo
                ->filter(function ($vale) {

                    $combustible = strtoupper(
                        $vale->tipoCombustible?->nombre ?? ''
                    );

                    return
                        !str_contains(
                            $combustible,
                            'GASOLINA'
                        )
                        &&
                        !str_contains(
                            $combustible,
                            'DIESEL'
                        );

                })
                ->sum('cantidad_litros');


            /*
            |--------------------------------------------------------------------------
            | FILA DEL REPORTE
            |--------------------------------------------------------------------------
            */

            return (object) [

                'unidad' => $unidad,

                'vehiculos' => $grupo
                    ->pluck('vehiculo_id')
                    ->filter()
                    ->unique()
                    ->count(),

                'vales' => $grupo->count(),

                'gasolina' => (float) $gasolina,

                'diesel' => (float) $diesel,

                'otros' => (float) $otros,

                'litros' => (float) $grupo
                    ->sum('cantidad_litros'),

                'total' => (float) $grupo
                    ->sum('total'),

            ];

        })
        ->sortBy('unidad')
        ->values();


    /*
    |--------------------------------------------------------------------------
    | TOTALES POR COMBUSTIBLE
    |--------------------------------------------------------------------------
    */

    $totalGasolina = (float) $resumenUnidades
        ->sum('gasolina');


    $totalDiesel = (float) $resumenUnidades
        ->sum('diesel');


    $totalOtros = (float) $resumenUnidades
        ->sum('otros');


    /*
    |--------------------------------------------------------------------------
    | TEXTOS PERSONALIZADOS
    |--------------------------------------------------------------------------
    */

    $textoSuperior = trim(
        (string) $request->input(
            'texto_superior',
            ''
        )
    );


    $textoInferior = trim(
        (string) $request->input(
            'texto_inferior',
            ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | LOGOS
    |--------------------------------------------------------------------------
    */

    $logoSacabaPath =
        public_path('images/logo-sacaba.jpg');


    $logoChaparePath =
        public_path('images/logo-chapare.png');


    $logoSacaba = null;

    $logoChapare = null;


    if (file_exists($logoSacabaPath)) {

        $logoSacaba =
            'data:image/jpeg;base64,' .
            base64_encode(
                file_get_contents(
                    $logoSacabaPath
                )
            );

    }


    if (file_exists($logoChaparePath)) {

        $logoChapare =
            'data:image/png;base64,' .
            base64_encode(
                file_get_contents(
                    $logoChaparePath
                )
            );

    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR PDF
    |--------------------------------------------------------------------------
    */

    $pdf = Pdf::loadView(
        'reportes.pdf.consumo',
        compact(
            'vales',
            'totalVales',
            'totalVehiculos',
            'totalLitros',
            'totalMonto',
            'totalGasolina',
            'totalDiesel',
            'totalOtros',
            'resumenUnidades',
            'textoSuperior',
            'textoInferior',
            'logoSacaba',
            'logoChapare'
        )
    );


    /*
    |--------------------------------------------------------------------------
    | HOJA CARTA HORIZONTAL
    |--------------------------------------------------------------------------
    */

    $pdf->setPaper(
        'letter',
        'landscape'
    );


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR PDF
    |--------------------------------------------------------------------------
    */

    return $pdf->stream(
        'Reporte_general_consumo_' .
        now()->format('Ymd_His') .
        '.pdf'
    );
}


    /*
    |--------------------------------------------------------------------------
    | REPORTE POR VEHÍCULO
    |--------------------------------------------------------------------------
    */
    public function porVehiculo(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | CONSULTA DE CONSUMO
    |--------------------------------------------------------------------------
    */

    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
        'estacionServicio',
    ]);

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR VEHÍCULO
    |--------------------------------------------------------------------------
    */

    if ($request->filled('vehiculo_id')) {
        $consulta->where(
            'vehiculo_id',
            $request->vehiculo_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR FECHA INICIAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FILTRO POR FECHA FINAL
    |--------------------------------------------------------------------------
    */

    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER VALES
    |--------------------------------------------------------------------------
    */

    $vales = $consulta
        ->orderBy('vehiculo_id')
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | OBTENER PARTES DIARIOS
    |--------------------------------------------------------------------------
    | Se buscan únicamente los partes correspondientes a los vehículos y
    | fechas que aparecen dentro del reporte.
    |--------------------------------------------------------------------------
    */

    $vehiculoIds = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->values();

    $partesDiarios = collect();

    if ($vehiculoIds->isNotEmpty() && $vales->isNotEmpty()) {

        $fechaMinima = $vales
            ->pluck('fecha')
            ->filter()
            ->min();

        $fechaMaxima = $vales
            ->pluck('fecha')
            ->filter()
            ->max();

        $partesDiarios = ParteDiario::whereIn(
                'vehiculo_id',
                $vehiculoIds
            )
            ->when(
                $fechaMinima,
                fn ($query) =>
                    $query->whereDate(
                        'fecha',
                        '>=',
                        $fechaMinima
                    )
            )
            ->when(
                $fechaMaxima,
                fn ($query) =>
                    $query->whereDate(
                        'fecha',
                        '<=',
                        $fechaMaxima
                    )
            )
            ->get()
            ->keyBy(function ($parte) {

                return $parte->vehiculo_id
                    . '|'
                    . $parte->fecha->format('Y-m-d');

            });
    }

    /*
    |--------------------------------------------------------------------------
    | PREPARAR REPORTE
    |--------------------------------------------------------------------------
    */

    $reporte = $vales
        ->map(function ($vale) use ($partesDiarios) {

            $vehiculo = $vale->vehiculo;

            $apertura = $vale->aperturaProgramatica;

            $fecha = $vale->fecha
                ? $vale->fecha->format('Y-m-d')
                : null;

            $claveParte = $vale->vehiculo_id
                . '|'
                . $fecha;

            $parte = $partesDiarios->get(
                $claveParte
            );

            /*
            |--------------------------------------------------------------------------
            | ESTADO DEL PARTE DIARIO
            |--------------------------------------------------------------------------
            */

            if (!$parte) {

                $presentacionParte = 'SIN REGISTRO';

            } elseif ($parte->presentado) {

                $presentacionParte = 'SÍ';

            } else {

                $presentacionParte = 'NO';
            }

            return (object) [

                'fecha' =>
                    $vale->fecha,

                'numero_vale' =>
                    $vale->numero_vale,

                'vehiculo_id' =>
                    $vale->vehiculo_id,

                'codigo' =>
                    $vehiculo?->codigo ?? '-',

                'placa' =>
                    $vehiculo?->placa ?? '-',

                'parte_diario' =>
                    $presentacionParte,

                'tipo_vehiculo' =>
                    $vehiculo?->tipo
                    ?? $vehiculo?->tipo_vehiculo
                    ?? '-',

                'apertura' =>
                    $apertura?->codigo ?? '-',

                'fuente' =>
                    $apertura?->fuenteFinanciamiento?->codigo
                    ?? '-',

                'organismo' =>
                    $apertura?->organismoFinanciador?->codigo
                    ?? '-',

                'litros' =>
                    (float) ($vale->cantidad_litros ?? 0),

                'precio_unitario' =>
                    (float) ($vale->precio_unitario ?? 0),

                'total' =>
                    (float) ($vale->total ?? 0),
            ];

        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | TOTALES
    |--------------------------------------------------------------------------
    */

    $totalVales = $reporte->count();

    $totalVehiculos = $reporte
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->count();

    $totalLitros = (float) $reporte
        ->sum('litros');

    $totalMonto = (float) $reporte
        ->sum('total');

    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS PARA EL FILTRO
    |--------------------------------------------------------------------------
    */

    $vehiculos = Vehiculo::where(
            'estado',
            true
        )
        ->orderBy('codigo')
        ->get();

    return view(
        'reportes.vehiculos',
        compact(
            'vales',
            'reporte',
            'vehiculos',
            'totalVales',
            'totalVehiculos',
            'totalLitros',
            'totalMonto'
        )
    );
}

public function vehiculoIndividual(Request $request)
{
    $vehiculos = Vehiculo::with('unidad')
        ->orderBy('codigo')
        ->get();

    $vehiculo = null;
    $vales = collect();

    $totalVales = 0;
    $totalLitros = 0;
    $totalMonto = 0;

    if ($request->filled('vehiculo_id')) {

        $vehiculo = Vehiculo::with([
            'unidad',
            'aperturaProgramatica.fuenteFinanciamiento',
            'aperturaProgramatica.organismoFinanciador',
        ])->findOrFail($request->vehiculo_id);

        $consulta = ValeCombustible::conConsumo()
            ->with([
                'aperturaProgramatica',
                'tipoCombustible',
                'estacionServicio',
            ])
            ->where('vehiculo_id', $vehiculo->id);

        if ($request->filled('fecha_inicio')) {
            $consulta->whereDate(
                'fecha',
                '>=',
                $request->fecha_inicio
            );
        }

        if ($request->filled('fecha_fin')) {
            $consulta->whereDate(
                'fecha',
                '<=',
                $request->fecha_fin
            );
        }

        $vales = $consulta
            ->orderBy('fecha')
            ->orderBy('numero_vale')
            ->get();

        $totalVales = $vales->count();

        $totalLitros = (float) $vales
            ->sum('cantidad_litros');

        $totalMonto = (float) $vales
            ->sum('total');
    }

    return view(
        'reportes.vehiculo-individual',
        compact(
            'vehiculos',
            'vehiculo',
            'vales',
            'totalVales',
            'totalLitros',
            'totalMonto'
        )
    );
}
public function vehiculoIndividualPdf(Request $request)
{
    $request->validate([
        'vehiculo_id' => ['required', 'integer', 'exists:vehiculos,id'],
        'fecha_inicio' => ['nullable', 'date'],
        'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        'texto_superior' => ['nullable', 'string'],
        'texto_inferior' => ['nullable', 'string'],
    ]);

    $vehiculo = Vehiculo::with([
        'unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ])->findOrFail($request->vehiculo_id);

    $consulta = ValeCombustible::conConsumo()
        ->with([
            'aperturaProgramatica',
            'tipoCombustible',
            'estacionServicio',
        ])
        ->where('vehiculo_id', $vehiculo->id);

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    $vales = $consulta
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();

    $totalVales = $vales->count();

    $totalLitros = (float) $vales->sum('cantidad_litros');

    $totalMonto = (float) $vales->sum('total');

    $textoSuperior = trim(
        (string) $request->input('texto_superior', '')
    );

    $textoInferior = trim(
        (string) $request->input('texto_inferior', '')
    );

    /*
    |--------------------------------------------------------------------------
    | LOGOS
    |--------------------------------------------------------------------------
    */

    $logoSacabaPath = public_path('images/logo-sacaba.jpg');
    $logoChaparePath = public_path('images/logo-chapare.png');

    $logoSacaba = null;
    $logoChapare = null;

    if (file_exists($logoSacabaPath)) {
        $logoSacaba =
            'data:image/jpeg;base64,' .
            base64_encode(file_get_contents($logoSacabaPath));
    }

    if (file_exists($logoChaparePath)) {
        $logoChapare =
            'data:image/png;base64,' .
            base64_encode(file_get_contents($logoChaparePath));
    }

    $pdf = Pdf::loadView(
        'reportes.pdf.vehiculo-individual',
        compact(
            'vehiculo',
            'vales',
            'totalVales',
            'totalLitros',
            'totalMonto',
            'textoSuperior',
            'textoInferior',
            'logoSacaba',
            'logoChapare'
        )
    );

    $pdf->setPaper('letter', 'landscape');

    return $pdf->stream(
        'Reporte_individual_' .
        $vehiculo->codigo .
        '_' .
        now()->format('Ymd_His') .
        '.pdf'
    );
}
public function vehiculoIndividualExcel(Request $request)
{
    $request->validate([
        'vehiculo_id' => ['required', 'integer', 'exists:vehiculos,id'],
        'fecha_inicio' => ['nullable', 'date'],
        'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | VEHÍCULO
    |--------------------------------------------------------------------------
    */

    $vehiculo = Vehiculo::with([
        'unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ])->findOrFail($request->vehiculo_id);


    /*
    |--------------------------------------------------------------------------
    | VALES
    |--------------------------------------------------------------------------
    */

    $consulta = ValeCombustible::conConsumo()
        ->with([
            'aperturaProgramatica',
            'tipoCombustible',
            'estacionServicio',
        ])
        ->where('vehiculo_id', $vehiculo->id);


    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }


    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }


    $vales = $consulta
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | SI ZIPARCHIVE NO ESTÁ DISPONIBLE -> CSV
    |--------------------------------------------------------------------------
    */

    if (!class_exists(\ZipArchive::class)) {

        $nombre =
            'SIGECOM_Reporte_individual_' .
            $vehiculo->codigo .
            '_' .
            now()->format('Ymd_His') .
            '.csv';


        return response()->streamDownload(
            function () use ($vehiculo, $vales) {

                $out = fopen('php://output', 'w');

                fwrite($out, "\xEF\xBB\xBF");


                fputcsv($out, [
                    'REPORTE INDIVIDUAL POR VEHÍCULO'
                ], ';');


                fputcsv($out, [
                    'Código',
                    $vehiculo->codigo
                ], ';');


                fputcsv($out, [
                    'Placa',
                    $vehiculo->placa
                ], ';');


                fputcsv($out, [
                    'Tipo',
                    $vehiculo->tipo
                ], ';');


                fputcsv($out, [
                    'Industria',
                    $vehiculo->industria
                ], ';');


                fputcsv($out, [
                    'Color',
                    $vehiculo->color
                ], ';');


                fputcsv($out, [
                    'Marca',
                    $vehiculo->marca
                ], ';');


                fputcsv($out, [
                    'Unidad',
                    $vehiculo->unidad?->nombre ?? '-'
                ], ';');


                fputcsv($out, [], ';');


                fputcsv($out, [
                    'FECHA',
                    'VALE',
                    'CANTIDAD (LT)',
                    'PRECIO (LT)',
                    'TOTAL (Bs)',
                    'N.º FACTURA',
                    'KILOMETRAJE',
                    'DETALLE DE ASIGNACIÓN',
                    'APERTURA PROGRAM.'
                ], ';');


                foreach ($vales as $vale) {

                    $detalle =
                        'ASIGNADO A ' .
                        mb_strtoupper(
                            $vehiculo->tipo ?: 'VEHÍCULO'
                        );

                    if ($vehiculo->unidad) {
                        $detalle .=
                            ' DE ' .
                            mb_strtoupper(
                                $vehiculo->unidad->nombre ?? ''
                            );
                    }


                    fputcsv($out, [

                        $vale->fecha
                            ? $vale->fecha->format('d/m/Y')
                            : '-',

                        sprintf(
                            '%06d',
                            (int) $vale->numero_vale
                        ),

                        number_format(
                            (float) $vale->cantidad_litros,
                            2,
                            ',',
                            ''
                        ),

                        number_format(
                            (float) $vale->precio_unitario,
                            2,
                            ',',
                            ''
                        ),

                        number_format(
                            (float) $vale->total,
                            2,
                            ',',
                            ''
                        ),

                        $vale->numero_factura ?: '-',

                        $vale->kilometraje ?? '-',

                        $detalle,

                        $vale->aperturaProgramatica?->codigo ?? '-',

                    ], ';');
                }


                fputcsv($out, [

                    'TOTAL',
                    '',

                    number_format(
                        (float) $vales->sum('cantidad_litros'),
                        2,
                        ',',
                        ''
                    ),

                    '',

                    number_format(
                        (float) $vales->sum('total'),
                        2,
                        ',',
                        ''
                    ),

                    '',
                    '',
                    '',
                    ''

                ], ';');


                fclose($out);

            },
            $nombre,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8'
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FUNCIONES AUXILIARES
    |--------------------------------------------------------------------------
    */

    $esc = static fn ($valor) =>
        htmlspecialchars(
            (string) $valor,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );


    $col = static function (int $numero): string {

        $resultado = '';

        while ($numero > 0) {

            $numero--;

            $resultado =
                chr(65 + ($numero % 26)) .
                $resultado;

            $numero = intdiv(
                $numero,
                26
            );
        }

        return $resultado;
    };


    /*
    |--------------------------------------------------------------------------
    | FILAS DEL EXCEL
    |--------------------------------------------------------------------------
    */

    $sheetRows = [];


    /*
    |--------------------------------------------------------------------------
    | ENCABEZADO INSTITUCIONAL
    |--------------------------------------------------------------------------
    */

    $sheetRows[] =
        '<row r="1" ht="22" customHeight="1">' .
            '<c r="A1" t="inlineStr" s="1">' .
                '<is><t>' .
                    $esc(
                        'SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA'
                    ) .
                '</t></is>' .
            '</c>' .
        '</row>';


    $sheetRows[] =
        '<row r="2" ht="22" customHeight="1">' .
            '<c r="A2" t="inlineStr" s="2">' .
                '<is><t>' .
                    $esc(
                        'REPORTE ASIGNACION DE COMBUSTIBLE'
                    ) .
                '</t></is>' .
            '</c>' .
        '</row>';


    /*
    |--------------------------------------------------------------------------
    | DATOS RELATIVOS AL VEHÍCULO
    |--------------------------------------------------------------------------
    */

    $sheetRows[] =
        '<row r="4">' .
            '<c r="A4" t="inlineStr" s="6">' .
                '<is><t>DATOS RELATIVOS AL VEHICULO</t></is>' .
            '</c>' .
        '</row>';


    $datosVehiculoHeaders = [
        'TIPO',
        'COD. ASIGNADO',
        'INDUSTRIA',
        'COLOR',
        'PLACA',
        'MARCA',
        
    ];


    $cells = [];

    foreach ($datosVehiculoHeaders as $indice => $header) {

        $referencia = $col($indice + 1) . '5';

        $cells[] =
            '<c r="' . $referencia .
            '" t="inlineStr" s="3">' .
                '<is><t>' .
                    $esc($header) .
                '</t></is>' .
            '</c>';
    }


    $sheetRows[] =
        '<row r="5">' .
            implode('', $cells) .
        '</row>';


    $datosVehiculo = [
        $vehiculo->tipo ?: '-',
        $vehiculo->codigo ?: '-',
        $vehiculo->industria ?: '-',
        $vehiculo->color ?: '-',
        $vehiculo->placa ?: '-',
        $vehiculo->marca ?: '-',
       
    ];


    $cells = [];

    foreach ($datosVehiculo as $indice => $dato) {

        $referencia = $col($indice + 1) . '6';

        $cells[] =
            '<c r="' .
            $referencia .
            '" t="inlineStr" s="7">' .

                '<is><t>' .
                    $esc($dato) .
                '</t></is>' .

            '</c>';
    }


    $sheetRows[] =
        '<row r="6">' .
            implode('', $cells) .
        '</row>';


    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    $sheetRows[] =
        '<row r="8">' .
            '<c r="A8" t="inlineStr" s="6">' .
                '<is><t>DETALLE DE ASIGNACION</t></is>' .
            '</c>' .
        '</row>';


    $headers = [
        'FECHA',
        'VALE',
        'CANTIDAD (LT)',
        'PRECIO (LT)',
        'TOTAL (Bs)',
        'N.º FACTURA',
        'KILOMETRAJE',
        'DETALLE DE ASIGNACIÓN',
        'APERTURA PROGRAM.'
    ];


    $r = 9;

    $cells = [];


    foreach ($headers as $indice => $header) {

        $referencia =
            $col($indice + 1) .
            $r;

        $cells[] =
            '<c r="' .
            $referencia .
            '" t="inlineStr" s="3">' .

                '<is><t>' .
                    $esc($header) .
                '</t></is>' .

            '</c>';
    }


    $sheetRows[] =
        '<row r="' .
        $r .
        '">' .
            implode('', $cells) .
        '</row>';


    /*
    |--------------------------------------------------------------------------
    | REGISTROS
    |--------------------------------------------------------------------------
    */

    foreach ($vales as $vale) {

        $r++;


        $detalle =
            'ASIGNADO A ' .
            mb_strtoupper(
                $vehiculo->tipo ?: 'VEHÍCULO'
            );


        if ($vehiculo->unidad) {

            $detalle .=
                ' DE ' .
                mb_strtoupper(
                    $vehiculo->unidad->nombre ?? ''
                );
        }


        $fila = [

            [
                's',
                $vale->fecha
                    ? $vale->fecha->format('d/m/Y')
                    : '-'
            ],

            [
                's',
                sprintf(
                    '%06d',
                    (int) $vale->numero_vale
                )
            ],

            [
                'n',
                round(
                    (float) $vale->cantidad_litros,
                    2
                )
            ],

            [
                'n',
                round(
                    (float) $vale->precio_unitario,
                    2
                )
            ],

            [
                'n',
                round(
                    (float) $vale->total,
                    2
                )
            ],

            [
                's',
                $vale->numero_factura ?: '-'
            ],

            [
                's',
                $vale->kilometraje !== null
                    ? (string) $vale->kilometraje
                    : '-'
            ],

            [
                's',
                $detalle
            ],

            [
                's',
                $vale->aperturaProgramatica?->codigo
                    ?? '-'
            ],

        ];


        $cells = [];


        foreach ($fila as $indice => $cell) {

            [$tipo, $valor] = $cell;

            $referencia =
                $col($indice + 1) .
                $r;


            if ($tipo === 'n') {

                $cells[] =
                    '<c r="' .
                    $referencia .
                    '" s="5">' .

                        '<v>' .
                            ((float) $valor) .
                        '</v>' .

                    '</c>';

            } else {

                $cells[] =
                    '<c r="' .
                    $referencia .
                    '" t="inlineStr" s="7">' .

                        '<is><t>' .
                            $esc($valor) .
                        '</t></is>' .

                    '</c>';
            }
        }


        $sheetRows[] =
            '<row r="' .
            $r .
            '">' .
                implode('', $cells) .
            '</row>';
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    $r++;


    $totalLitros =
        (float) $vales->sum(
            'cantidad_litros'
        );


    $totalMonto =
        (float) $vales->sum(
            'total'
        );


    $sheetRows[] =
        '<row r="' . $r . '">' .

            '<c r="A' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t>TOTAL</t></is>' .
            '</c>' .

            '<c r="B' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t></t></is>' .
            '</c>' .

            '<c r="C' . $r .
            '" s="8">' .
                '<v>' .
                    round($totalLitros, 2) .
                '</v>' .
            '</c>' .

            '<c r="D' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t></t></is>' .
            '</c>' .

            '<c r="E' . $r .
            '" s="8">' .
                '<v>' .
                    round($totalMonto, 2) .
                '</v>' .
            '</c>' .

            '<c r="F' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t></t></is>' .
            '</c>' .

            '<c r="G' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t></t></is>' .
            '</c>' .

            '<c r="H' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t></t></is>' .
            '</c>' .

            '<c r="I' . $r .
            '" t="inlineStr" s="4">' .
                '<is><t></t></is>' .
            '</c>' .

        '</row>';


    /*
    |--------------------------------------------------------------------------
    | HOJA
    |--------------------------------------------------------------------------
    */

    $sheet =
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .

        '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .

            '<sheetViews>' .

                '<sheetView workbookViewId="0">' .

                    '<pane ' .
                        'ySplit="9" ' .
                        'topLeftCell="A10" ' .
                        'activePane="bottomLeft" ' .
                        'state="frozen"/>' .

                '</sheetView>' .

            '</sheetViews>' .


            '<cols>' .

                '<col min="1" max="1" width="14" customWidth="1"/>' .

                '<col min="2" max="2" width="14" customWidth="1"/>' .

                '<col min="3" max="5" width="17" customWidth="1"/>' .

                '<col min="6" max="6" width="18" customWidth="1"/>' .

                '<col min="7" max="7" width="18" customWidth="1"/>' .

                '<col min="8" max="8" width="48" customWidth="1"/>' .

                '<col min="9" max="9" width="22" customWidth="1"/>' .

            '</cols>' .


            '<sheetData>' .

                implode(
                    '',
                    $sheetRows
                ) .

            '</sheetData>' .


            '<autoFilter ref="A9:I' .
                max(
                    9,
                    $r - 1
                ) .
            '"/>' .

        '</worksheet>';


    /*
    |--------------------------------------------------------------------------
    | ESTILOS
    |--------------------------------------------------------------------------
    */

    $styles =
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .

        '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .

            '<fonts count="4">' .

                '<font>' .
                    '<sz val="11"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

                '<font>' .
                    '<b/>' .
                    '<sz val="14"/>' .
                    '<color rgb="FF075326"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

                '<font>' .
                    '<b/>' .
                    '<sz val="12"/>' .
                    '<color rgb="FF08712F"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

                '<font>' .
                    '<b/>' .
                    '<color rgb="FFFFFFFF"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

            '</fonts>' .


            '<fills count="4">' .

                '<fill>' .
                    '<patternFill patternType="none"/>' .
                '</fill>' .

                '<fill>' .
                    '<patternFill patternType="gray125"/>' .
                '</fill>' .

                '<fill>' .
                    '<patternFill patternType="solid">' .
                        '<fgColor rgb="FF08712F"/>' .
                        '<bgColor indexed="64"/>' .
                    '</patternFill>' .
                '</fill>' .

                '<fill>' .
                    '<patternFill patternType="solid">' .
                        '<fgColor rgb="FFE7F4EB"/>' .
                        '<bgColor indexed="64"/>' .
                    '</patternFill>' .
                '</fill>' .

            '</fills>' .


            '<borders count="2">' .

                '<border/>' .

                '<border>' .
                    '<left style="thin"/>' .
                    '<right style="thin"/>' .
                    '<top style="thin"/>' .
                    '<bottom style="thin"/>' .
                '</border>' .

            '</borders>' .


            '<cellXfs count="9">' .

                '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .

                '<xf numFmtId="0" fontId="1" fillId="0" borderId="0"/>' .

                '<xf numFmtId="0" fontId="2" fillId="0" borderId="0"/>' .

                '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" applyFill="1" applyBorder="1"/>' .

                '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" applyFill="1" applyBorder="1"/>' .

                '<xf numFmtId="4" fontId="0" fillId="0" borderId="1" applyNumberFormat="1" applyBorder="1"/>' .

                '<xf numFmtId="0" fontId="2" fillId="3" borderId="1" applyFill="1" applyBorder="1"/>' .

                '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyBorder="1"/>' .

                '<xf numFmtId="4" fontId="0" fillId="3" borderId="1" applyFill="1" applyNumberFormat="1" applyBorder="1"/>' .

            '</cellXfs>' .

        '</styleSheet>';


    /*
    |--------------------------------------------------------------------------
    | CREAR XLSX
    |--------------------------------------------------------------------------
    */

    $tmp = tempnam(
        sys_get_temp_dir(),
        'sigecom_individual_'
    );


    $zip = new \ZipArchive();


    $zip->open(
        $tmp,
        \ZipArchive::OVERWRITE
    );


    $zip->addFromString(
        '[Content_Types].xml',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
        '</Types>'
    );


    $zip->addFromString(
        '_rels/.rels',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
        '</Relationships>'
    );


    $zip->addFromString(
        'xl/workbook.xml',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ' .
        'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .

            '<sheets>' .
                '<sheet name="Vehiculo Individual" sheetId="1" r:id="rId1"/>' .
            '</sheets>' .

        '</workbook>'
    );


    $zip->addFromString(
        'xl/_rels/workbook.xml.rels',
        '<?xml version="1.0" encoding="UTF-8"?>' .

        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .

            '<Relationship Id="rId1" ' .
                'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" ' .
                'Target="worksheets/sheet1.xml"/>' .

            '<Relationship Id="rId2" ' .
                'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" ' .
                'Target="styles.xml"/>' .

        '</Relationships>'
    );


    $zip->addFromString(
        'xl/worksheets/sheet1.xml',
        $sheet
    );


    $zip->addFromString(
        'xl/styles.xml',
        $styles
    );


    $zip->close();


    /*
    |--------------------------------------------------------------------------
    | DESCARGAR
    |--------------------------------------------------------------------------
    */

    $nombre =
        'SIGECOM_Reporte_individual_' .
        preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '_',
            (string) $vehiculo->codigo
        ) .
        '_' .
        now()->format('Ymd_His') .
        '.xlsx';


    return response()
        ->download(
            $tmp,
            $nombre,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        )
        ->deleteFileAfterSend(true);
}
    /*
|--------------------------------------------------------------------------
| REPORTE POR APERTURA PROGRAMÁTICA
|--------------------------------------------------------------------------
*/
public function porApertura(Request $request)
{
    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'periodo',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
        'estacionServicio',
    ]);

    // APERTURA PROGRAMÁTICA
    if ($request->filled('apertura_programatica_id')) {
        $consulta->where(
            'apertura_programatica_id',
            $request->apertura_programatica_id
        );
    }

    // PERÍODO
    if ($request->filled('periodo_id')) {
        $consulta->where(
            'periodo_id',
            $request->periodo_id
        );
    }

    // FECHA INICIAL
    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    // FECHA FINAL
    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    $vales = $consulta
        ->orderBy('apertura_programatica_id')
        ->orderBy('fecha')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | AGRUPAR POR AP + FF + OF + UNIDAD
    |--------------------------------------------------------------------------
    */

   $reporte = $vales
    ->groupBy(function ($vale) {

        $apertura = $vale->aperturaProgramatica;
        $unidad = $vale->vehiculo?->unidad;

        return implode('|', [
            $apertura?->codigo ?? '',
            $apertura?->fuenteFinanciamiento?->codigo ?? '',
            $apertura?->organismoFinanciador?->codigo ?? '',
            $unidad?->nombre ?? '',
            (string) $vale->precio_unitario,
        ]);
    })
    ->map(function ($grupo) {

        $primerVale = $grupo->first();

        $apertura = $primerVale->aperturaProgramatica;
        $unidad = $primerVale->vehiculo?->unidad;

        return (object) [

            'apertura' =>
                $apertura?->codigo ?? '-',

            'fuente' =>
                $apertura?->fuenteFinanciamiento?->codigo ?? '-',

            'organismo' =>
                $apertura?->organismoFinanciador?->codigo ?? '-',

            'unidad' =>
                $unidad?->nombre
                ?? $apertura?->descripcion
                ?? '-',

            // SE SUMAN LOS LITROS
            'litros' =>
                (float) $grupo->sum('cantidad_litros'),

            // NO SE SUMA EL PRECIO
            // SE MUESTRA EL PRECIO REGISTRADO EN EL VALE
            'precio_unitario' =>
                (float) $primerVale->precio_unitario,

            // SE SUMAN LOS TOTALES
            'total' =>
                (float) $grupo->sum('total'),

            'cantidad_vales' =>
                $grupo->count(),
        ];
    })
    ->values();
    /*
    |--------------------------------------------------------------------------
    | TOTALES
    |--------------------------------------------------------------------------
    */

    $totalVales = $vales->count();

    $totalVehiculos = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->count();

    $totalLitros = (float) $vales->sum('cantidad_litros');

    $totalMonto = (float) $vales->sum('total');

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    $aperturas = AperturaProgramatica::where('estado', true)
        ->with([
            'fuenteFinanciamiento',
            'organismoFinanciador',
        ])
        ->orderBy('codigo')
        ->get();

    $periodos = Periodo::orderByDesc('anio')
        ->orderByDesc('mes')
        ->get();

    return view('reportes.aperturas', compact(
        'vales',
        'reporte',
        'aperturas',
        'periodos',
        'totalVales',
        'totalVehiculos',
        'totalLitros',
        'totalMonto'
    ));
}

    /**
     * Genera un XLSX real sin depender de HTML interpretado por Excel.
     * Los identificadores se escriben como texto (conserva 001006) y los
     * importes/litros como números reales, evitando que Excel en configuración
     * regional española convierta 53.017... en 5.301.7... por interpretar el
     * punto como separador de miles.
     */
    private function respuestaExcel($vales, string $titulo)
    {
        if (!class_exists(\ZipArchive::class)) {
            // Fallback seguro: CSV UTF-8 separado por punto y coma, compatible con Excel ES.
            $nombre = 'SIGECOM_'.preg_replace('/[^A-Za-z0-9_-]+/', '_', $titulo).'_'.now()->format('Ymd_His').'.csv';
            return response()->streamDownload(function () use ($vales) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Fecha','Vale','Código','Placa','Unidad','Combustible','Apertura','Factura','Litros','Precio unitario','Total Bs'], ';');
                foreach ($vales as $v) {
                    fputcsv($out, [
                        optional($v->fecha)->format('d/m/Y'), (string)$v->numero_vale,
                        optional($v->vehiculo)->codigo, optional($v->vehiculo)->placa,
                        optional(optional($v->vehiculo)->unidad)->nombre,
                        optional($v->tipoCombustible)->nombre,
                        optional($v->aperturaProgramatica)->codigo, (string)$v->numero_factura,
                        number_format((float)$v->cantidad_litros, 2, ',', ''),
                        number_format((float)$v->precio_unitario, 4, ',', ''),
                        number_format((float)$v->total, 2, ',', ''),
                    ], ';');
                }
                fclose($out);
            }, $nombre, ['Content-Type'=>'text/csv; charset=UTF-8']);
        }

        $headers = ['Fecha','Vale','Código','Placa','Unidad','Combustible','Apertura','Factura','Litros','Precio unitario','Total Bs'];
        $rows = [];
        foreach ($vales as $v) {
            $rows[] = [
                ['s', optional($v->fecha)->format('d/m/Y')],
                ['s', (string)$v->numero_vale],
                ['s', (string)optional($v->vehiculo)->codigo],
                ['s', (string)optional($v->vehiculo)->placa],
                ['s', (string)optional(optional($v->vehiculo)->unidad)->nombre],
                ['s', (string)optional($v->tipoCombustible)->nombre],
                ['s', (string)optional($v->aperturaProgramatica)->codigo],
                ['s', (string)$v->numero_factura],
                ['n', round((float)$v->cantidad_litros, 2)],
                ['n', round((float)$v->precio_unitario, 4)],
                ['n', round((float)$v->total, 2)],
            ];
        }
        $rows[] = [
            ['s','TOTALES'],['s',''],['s',''],['s',''],['s',''],['s',''],['s',''],['s',''],
            ['n', round((float)$vales->sum('cantidad_litros'), 2)], ['s',''],
            ['n', round((float)$vales->sum('total'), 2)],
        ];

        $esc = static fn($v) => htmlspecialchars((string)$v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $col = static function (int $n): string { $r=''; while($n>0){$n--; $r=chr(65+($n%26)).$r; $n=intdiv($n,26);} return $r; };
        $sheetRows = [];
        $r = 1;
        $title = $esc(str_replace('_',' ', $titulo));
        $sheetRows[] = '<row r="1"><c r="A1" t="inlineStr" s="1"><is><t>Gobierno Autónomo Municipal de Sacaba - SIGECOM</t></is></c></row>';
        $sheetRows[] = '<row r="2"><c r="A2" t="inlineStr" s="2"><is><t>'.$title.'</t></is></c></row>';
        $r=4; $cells=[];
        foreach($headers as $i=>$h){$ref=$col($i+1).$r; $cells[]='<c r="'.$ref.'" t="inlineStr" s="3"><is><t>'.$esc($h).'</t></is></c>';}
        $sheetRows[]='<row r="'.$r.'">'.implode('',$cells).'</row>';
        foreach($rows as $ri=>$row){
            $r++; $cells=[]; $isTotal=$ri===count($rows)-1;
            foreach($row as $i=>$cell){ [$type,$value]=$cell; $ref=$col($i+1).$r; $style=$isTotal?4:($type==='n'?5:0);
                if($type==='n') $cells[]='<c r="'.$ref.'" s="'.$style.'"><v>'.((float)$value).'</v></c>';
                else $cells[]='<c r="'.$ref.'" t="inlineStr" s="'.$style.'"><is><t>'.$esc($value).'</t></is></c>';
            }
            $sheetRows[]='<row r="'.$r.'">'.implode('',$cells).'</row>';
        }
        $sheet='<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><cols><col min="1" max="1" width="13" customWidth="1"/><col min="2" max="4" width="14" customWidth="1"/><col min="5" max="5" width="38" customWidth="1"/><col min="6" max="8" width="18" customWidth="1"/><col min="9" max="11" width="16" customWidth="1"/></cols><sheetData>'.implode('',$sheetRows).'</sheetData><autoFilter ref="A4:K'.max(4,$r-1).'"/><sheetViews><sheetView workbookViewId="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews></worksheet>';
        $styles='<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="3"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="14"/><color rgb="FF173F32"/><name val="Calibri"/></font><font><b/><sz val="12"/><color rgb="FF198754"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFEAF4EF"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border/><border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border></borders><cellXfs count="6"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0"/><xf numFmtId="0" fontId="2" fillId="0" borderId="0"/><xf numFmtId="0" fontId="0" fillId="2" borderId="1" applyFill="1" applyBorder="1"/><xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFill="1" applyBorder="1"/><xf numFmtId="4" fontId="0" fillId="0" borderId="1" applyNumberFormat="1" applyBorder="1"/></cellXfs></styleSheet>';
        $tmp=tempnam(sys_get_temp_dir(),'sigecom_xlsx_'); $zip=new \ZipArchive(); $zip->open($tmp, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml','<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('_rels/.rels','<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml','<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Reporte" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels','<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml',$sheet); $zip->addFromString('xl/styles.xml',$styles); $zip->close();
        $nombre='SIGECOM_'.preg_replace('/[^A-Za-z0-9_-]+/','_',$titulo).'_'.now()->format('Ymd_His').'.xlsx';
        return response()->download($tmp,$nombre,['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend(true);
    }

    private function consultaExportacion(Request $request)
    {
        return ValeCombustible::conConsumo()->with(['vehiculo.unidad','tipoCombustible','periodo','aperturaProgramatica','estacionServicio'])
            ->when($request->filled('fecha_inicio'), fn($q)=>$q->whereDate('fecha','>=',$request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn($q)=>$q->whereDate('fecha','<=',$request->fecha_fin))
            ->when($request->filled('vehiculo_id'), fn($q)=>$q->where('vehiculo_id',$request->vehiculo_id))
            ->when($request->filled('unidad_id'), fn($q)=>$q->whereHas('vehiculo',fn($v)=>$v->where('unidad_id',$request->unidad_id)))
            ->when($request->filled('periodo_id'), fn($q)=>$q->where('periodo_id',$request->periodo_id))
            ->when($request->filled('apertura_programatica_id'), fn($q)=>$q->where('apertura_programatica_id',$request->apertura_programatica_id));
    }


    public function consumoExcel(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'vehiculo_id' => ['nullable', 'integer', 'exists:vehiculos,id'],
            'unidad_id' => ['nullable', 'integer', 'exists:unidades,id'],
        ]);

        $consulta = ValeCombustible::conConsumo()->with([
            'vehiculo.unidad',
            'tipoCombustible',
        ]);

        if ($request->filled('fecha_inicio')) {
            $consulta->whereDate('fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $consulta->whereDate('fecha', '<=', $request->fecha_fin);
        }

        if ($request->filled('vehiculo_id')) {
            $consulta->where('vehiculo_id', $request->vehiculo_id);
        }

        if ($request->filled('unidad_id')) {
            $consulta->whereHas('vehiculo', function ($query) use ($request) {
                $query->where('unidad_id', $request->unidad_id);
            });
        }

        $vales = $consulta
            ->orderByDesc('fecha')
            ->orderByDesc('numero_vale')
            ->get();

        $resumenUnidades = $vales
            ->groupBy(fn ($vale) => $vale->vehiculo?->unidad?->nombre ?? 'SIN UNIDAD')
            ->map(function ($grupo, $unidad) {
                $gasolina = $grupo->filter(function ($vale) {
                    return str_contains(
                        strtoupper($vale->tipoCombustible?->nombre ?? ''),
                        'GASOLINA'
                    );
                })->sum('cantidad_litros');

                $diesel = $grupo->filter(function ($vale) {
                    return str_contains(
                        strtoupper($vale->tipoCombustible?->nombre ?? ''),
                        'DIESEL'
                    );
                })->sum('cantidad_litros');

                $otros = $grupo->filter(function ($vale) {
                    $combustible = strtoupper($vale->tipoCombustible?->nombre ?? '');

                    return !str_contains($combustible, 'GASOLINA')
                        && !str_contains($combustible, 'DIESEL');
                })->sum('cantidad_litros');

                return (object) [
                    'unidad' => $unidad,
                    'vehiculos' => $grupo->pluck('vehiculo_id')->filter()->unique()->count(),
                    'vales' => $grupo->count(),
                    'gasolina' => (float) $gasolina,
                    'diesel' => (float) $diesel,
                    'otros' => (float) $otros,
                    'litros' => (float) $grupo->sum('cantidad_litros'),
                    'total' => (float) $grupo->sum('total'),
                ];
            })
            ->sortBy('unidad')
            ->values();

        $totalVales = $vales->count();
        $totalVehiculos = $vales->pluck('vehiculo_id')->filter()->unique()->count();
        $totalLitros = (float) $vales->sum('cantidad_litros');
        $totalMonto = (float) $vales->sum('total');
        $totalGasolina = (float) $resumenUnidades->sum('gasolina');
        $totalDiesel = (float) $resumenUnidades->sum('diesel');
        $totalOtros = (float) $resumenUnidades->sum('otros');

        if (!class_exists(\ZipArchive::class)) {
            $nombre = 'SIGECOM_Reporte_general_consumo_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use (
                $resumenUnidades,
                $totalVales,
                $totalVehiculos,
                $totalLitros,
                $totalMonto,
                $totalGasolina,
                $totalDiesel,
                $totalOtros
            ) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");

                fputcsv($out, ['REPORTE GENERAL DE CONSUMO DE COMBUSTIBLE'], ';');
                fputcsv($out, [], ';');
                fputcsv($out, ['Vales utilizados', $totalVales], ';');
                fputcsv($out, ['Vehículos', $totalVehiculos], ';');
                fputcsv($out, ['Combustible consumido (L)', number_format($totalLitros, 2, ',', '')], ';');
                fputcsv($out, ['Monto total (Bs)', number_format($totalMonto, 2, ',', '')], ';');
                fputcsv($out, [], ';');
                fputcsv($out, ['Unidad', 'Vehículos', 'Vales', 'Gasolina (L)', 'Diésel (L)', 'Otros (L)', 'Total litros', 'Monto total (Bs)'], ';');

                foreach ($resumenUnidades as $fila) {
                    fputcsv($out, [
                        $fila->unidad,
                        $fila->vehiculos,
                        $fila->vales,
                        number_format($fila->gasolina, 2, ',', ''),
                        number_format($fila->diesel, 2, ',', ''),
                        number_format($fila->otros, 2, ',', ''),
                        number_format($fila->litros, 2, ',', ''),
                        number_format($fila->total, 2, ',', ''),
                    ], ';');
                }

                fputcsv($out, [
                    'TOTAL',
                    $totalVehiculos,
                    $totalVales,
                    number_format($totalGasolina, 2, ',', ''),
                    number_format($totalDiesel, 2, ',', ''),
                    number_format($totalOtros, 2, ',', ''),
                    number_format($totalLitros, 2, ',', ''),
                    number_format($totalMonto, 2, ',', ''),
                ], ';');

                fclose($out);
            }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $esc = static fn ($valor) => htmlspecialchars(
            (string) $valor,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );

        $texto = static function (string $celda, $valor, int $estilo = 0) use ($esc): string {
            return '<c r="' . $celda . '" t="inlineStr" s="' . $estilo . '"><is><t>' .
                $esc($valor) . '</t></is></c>';
        };

        $numero = static function (string $celda, $valor, int $estilo): string {
            return '<c r="' . $celda . '" s="' . $estilo . '"><v>' .
                ((float) $valor) . '</v></c>';
        };

        $sheetRows = [];
        $sheetRows[] = '<row r="1" ht="24" customHeight="1">' .
            $texto('A1', 'SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA', 1) .
            '</row>';
        $sheetRows[] = '<row r="2" ht="22" customHeight="1">' .
            $texto('A2', 'REPORTE GENERAL DE CONSUMO DE COMBUSTIBLE', 2) .
            '</row>';

        $sheetRows[] = '<row r="4">' .
            $texto('A4', 'VALES UTILIZADOS', 3) .
            $texto('B4', 'VEHICULOS', 3) .
            $texto('C4', 'COMBUSTIBLE CONSUMIDO (L)', 3) .
            $texto('D4', 'MONTO TOTAL (Bs)', 3) .
            '</row>';
        $sheetRows[] = '<row r="5">' .
            $numero('A5', $totalVales, 6) .
            $numero('B5', $totalVehiculos, 6) .
            $numero('C5', round($totalLitros, 2), 5) .
            $numero('D5', round($totalMonto, 2), 5) .
            '</row>';

        $headers = ['UNIDAD', 'VEHICULOS', 'VALES', 'GASOLINA (L)', 'DIESEL (L)', 'OTROS (L)', 'TOTAL LITROS', 'MONTO TOTAL (Bs)'];
        $headerCells = [];
        foreach ($headers as $i => $header) {
            $headerCells[] = $texto(chr(65 + $i) . '7', $header, 3);
        }
        $sheetRows[] = '<row r="7">' . implode('', $headerCells) . '</row>';

        $r = 7;
        foreach ($resumenUnidades as $fila) {
            $r++;
            $sheetRows[] = '<row r="' . $r . '">' .
                $texto('A' . $r, $fila->unidad, 4) .
                $numero('B' . $r, $fila->vehiculos, 6) .
                $numero('C' . $r, $fila->vales, 6) .
                $numero('D' . $r, round($fila->gasolina, 2), 5) .
                $numero('E' . $r, round($fila->diesel, 2), 5) .
                $numero('F' . $r, round($fila->otros, 2), 5) .
                $numero('G' . $r, round($fila->litros, 2), 5) .
                $numero('H' . $r, round($fila->total, 2), 5) .
                '</row>';
        }

        $r++;
        $sheetRows[] = '<row r="' . $r . '">' .
            $texto('A' . $r, 'TOTAL', 7) .
            $numero('B' . $r, $totalVehiculos, 8) .
            $numero('C' . $r, $totalVales, 8) .
            $numero('D' . $r, round($totalGasolina, 2), 9) .
            $numero('E' . $r, round($totalDiesel, 2), 9) .
            $numero('F' . $r, round($totalOtros, 2), 9) .
            $numero('G' . $r, round($totalLitros, 2), 9) .
            $numero('H' . $r, round($totalMonto, 2), 9) .
            '</row>';

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<sheetViews><sheetView workbookViewId="0"><pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>' .
            '<cols>' .
                '<col min="1" max="1" width="48" customWidth="1"/>' .
                '<col min="2" max="3" width="14" customWidth="1"/>' .
                '<col min="4" max="8" width="18" customWidth="1"/>' .
            '</cols>' .
            '<sheetData>' . implode('', $sheetRows) . '</sheetData>' .
            '<mergeCells count="2"><mergeCell ref="A1:H1"/><mergeCell ref="A2:H2"/></mergeCells>' .
            '<autoFilter ref="A7:H' . max(7, $r - 1) . '"/>' .
            '</worksheet>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
            '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0.00"/></numFmts>' .
            '<fonts count="4">' .
                '<font><sz val="11"/><name val="Calibri"/></font>' .
                '<font><b/><sz val="14"/><color rgb="FF075326"/><name val="Calibri"/></font>' .
                '<font><b/><sz val="12"/><color rgb="FF08712F"/><name val="Calibri"/></font>' .
                '<font><b/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>' .
            '</fonts>' .
            '<fills count="4">' .
                '<fill><patternFill patternType="none"/></fill>' .
                '<fill><patternFill patternType="gray125"/></fill>' .
                '<fill><patternFill patternType="solid"><fgColor rgb="FF08712F"/><bgColor indexed="64"/></patternFill></fill>' .
                '<fill><patternFill patternType="solid"><fgColor rgb="FFE7F4EB"/><bgColor indexed="64"/></patternFill></fill>' .
            '</fills>' .
            '<borders count="2"><border/><border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border></borders>' .
            '<cellXfs count="10">' .
                '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
                '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" applyAlignment="1"><alignment horizontal="center"/></xf>' .
                '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyAlignment="1"><alignment horizontal="center"/></xf>' .
                '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" wrapText="1"/></xf>' .
                '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyBorder="1"/>' .
                '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" applyNumberFormat="1" applyBorder="1"/>' .
                '<xf numFmtId="1" fontId="0" fillId="0" borderId="1" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center"/></xf>' .
                '<xf numFmtId="0" fontId="3" fillId="2" borderId="1" applyFill="1" applyBorder="1"/>' .
                '<xf numFmtId="1" fontId="3" fillId="2" borderId="1" applyFill="1" applyNumberFormat="1" applyBorder="1"/>' .
                '<xf numFmtId="164" fontId="3" fillId="2" borderId="1" applyFill="1" applyNumberFormat="1" applyBorder="1"/>' .
            '</cellXfs>' .
            '</styleSheet>';

        $tmp = tempnam(sys_get_temp_dir(), 'sigecom_consumo_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Reporte General" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->close();

        $nombre = 'SIGECOM_Reporte_general_consumo_' . now()->format('Ymd_His') . '.xlsx';

        return response()->download(
            $tmp,
            $nombre,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    public function vehiculosExcel(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | CONSULTA
    |--------------------------------------------------------------------------
    */

    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ]);

    if ($request->filled('vehiculo_id')) {
        $consulta->where(
            'vehiculo_id',
            $request->vehiculo_id
        );
    }

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    $vales = $consulta
        ->orderBy('vehiculo_id')
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | PARTES DIARIOS
    |--------------------------------------------------------------------------
    */

    $vehiculoIds = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->values();

    $partesDiarios = collect();

    if ($vehiculoIds->isNotEmpty() && $vales->isNotEmpty()) {

        $fechaMinima = $vales
            ->pluck('fecha')
            ->filter()
            ->min();

        $fechaMaxima = $vales
            ->pluck('fecha')
            ->filter()
            ->max();

        $partesDiarios = ParteDiario::whereIn(
                'vehiculo_id',
                $vehiculoIds
            )
            ->when(
                $fechaMinima,
                fn ($query) =>
                    $query->whereDate(
                        'fecha',
                        '>=',
                        $fechaMinima
                    )
            )
            ->when(
                $fechaMaxima,
                fn ($query) =>
                    $query->whereDate(
                        'fecha',
                        '<=',
                        $fechaMaxima
                    )
            )
            ->get()
            ->keyBy(function ($parte) {

                return $parte->vehiculo_id
                    . '|'
                    . $parte->fecha->format('Y-m-d');

            });
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR REPORTE
    |--------------------------------------------------------------------------
    */

    $reporte = $vales
        ->map(function ($vale) use ($partesDiarios) {

            $vehiculo = $vale->vehiculo;

            $apertura = $vale->aperturaProgramatica;

            $fecha = $vale->fecha
                ? $vale->fecha->format('Y-m-d')
                : null;

            $claveParte = $vale->vehiculo_id
                . '|'
                . $fecha;

            $parte = $partesDiarios->get(
                $claveParte
            );

            if (!$parte) {

                $presentacionParte = 'SIN REGISTRO';

            } elseif ($parte->presentado) {

                $presentacionParte = 'SÍ';

            } else {

                $presentacionParte = 'NO';
            }

            return [
                'codigo' =>
                    $vehiculo?->codigo ?? '-',

                'placa' =>
                    $vehiculo?->placa ?? '-',

                'parte_diario' =>
                    $presentacionParte,

                'tipo_vehiculo' =>
                    $vehiculo?->tipo
                    ?? $vehiculo?->tipo_vehiculo
                    ?? '-',

                'apertura' =>
                    $apertura?->codigo ?? '-',

                'fuente' =>
                    $apertura?->fuenteFinanciamiento?->codigo
                    ?? '-',

                'organismo' =>
                    $apertura?->organismoFinanciador?->codigo
                    ?? '-',

                'litros' =>
                    (float) ($vale->cantidad_litros ?? 0),

                'precio_unitario' =>
                    (float) ($vale->precio_unitario ?? 0),

                'total' =>
                    (float) ($vale->total ?? 0),
            ];

        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | CSV SI ZIPARCHIVE NO ESTÁ DISPONIBLE
    |--------------------------------------------------------------------------
    */

    if (!class_exists(\ZipArchive::class)) {

        $nombre =
            'SIGECOM_Reporte_por_vehiculo_' .
            now()->format('Ymd_His') .
            '.csv';

        return response()->streamDownload(
            function () use ($reporte) {

                $out = fopen(
                    'php://output',
                    'w'
                );

                fwrite(
                    $out,
                    "\xEF\xBB\xBF"
                );

                fputcsv($out, [
                    'N°',
                    'CODIGO DE VEHICULO',
                    'PLACA',
                    'PRESENTACION PARTE DIARIO',
                    'TIPO DE VEHICULO',
                    'AP. PROGR.',
                    'FF',
                    'OF',
                    'CANTIDAD LITROS',
                    'P. U. (BS/LITRO)',
                    'PRECIO TOTAL (BS)',
                ], ';');


                foreach ($reporte as $indice => $fila) {

                    fputcsv($out, [
                        $indice + 1,
                        $fila['codigo'],
                        $fila['placa'],
                        $fila['parte_diario'],
                        $fila['tipo_vehiculo'],
                        $fila['apertura'],
                        $fila['fuente'],
                        $fila['organismo'],

                        number_format(
                            $fila['litros'],
                            2,
                            ',',
                            ''
                        ),

                        number_format(
                            $fila['precio_unitario'],
                            2,
                            ',',
                            ''
                        ),

                        number_format(
                            $fila['total'],
                            2,
                            ',',
                            ''
                        ),

                    ], ';');
                }


                fputcsv($out, [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'TOTAL',

                    number_format(
                        $reporte->sum('litros'),
                        2,
                        ',',
                        ''
                    ),

                    '',

                    number_format(
                        $reporte->sum('total'),
                        2,
                        ',',
                        ''
                    ),

                ], ';');


                fclose($out);

            },
            $nombre,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8'
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ENCABEZADOS
    |--------------------------------------------------------------------------
    */

    $headers = [
        'N°',
        'CODIGO DE VEHICULO',
        'PLACA',
        'PRESENTACION PARTE DIARIO',
        'TIPO DE VEHICULO',
        'AP. PROGR.',
        'FF',
        'OF',
        'CANTIDAD LITROS',
        'P. U. (BS/LITRO)',
        'PRECIO TOTAL (BS)',
    ];


    /*
    |--------------------------------------------------------------------------
    | FILAS
    |--------------------------------------------------------------------------
    */

    $rows = [];

    foreach ($reporte as $indice => $fila) {

        $rows[] = [

            ['n', $indice + 1],

            ['s', (string) $fila['codigo']],

            ['s', (string) $fila['placa']],

            ['s', (string) $fila['parte_diario']],

            ['s', (string) $fila['tipo_vehiculo']],

            ['s', (string) $fila['apertura']],

            ['s', (string) $fila['fuente']],

            ['s', (string) $fila['organismo']],

            [
                'n',
                round(
                    (float) $fila['litros'],
                    2
                )
            ],

            [
                'n',
                round(
                    (float) $fila['precio_unitario'],
                    2
                )
            ],

            [
                'n',
                round(
                    (float) $fila['total'],
                    2
                )
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    $rows[] = [

        ['s', ''],
        ['s', ''],
        ['s', ''],
        ['s', ''],
        ['s', ''],
        ['s', ''],
        ['s', ''],

        ['s', 'TOTAL'],

        [
            'n',
            round(
                (float) $reporte->sum('litros'),
                2
            )
        ],

        // P.U. NO SE SUMA
        ['s', ''],

        [
            'n',
            round(
                (float) $reporte->sum('total'),
                2
            )
        ],
    ];


    /*
    |--------------------------------------------------------------------------
    | FUNCIONES AUXILIARES
    |--------------------------------------------------------------------------
    */

    $esc = static fn ($valor) =>
        htmlspecialchars(
            (string) $valor,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );


    $col = static function (int $numero): string {

        $resultado = '';

        while ($numero > 0) {

            $numero--;

            $resultado =
                chr(
                    65 + ($numero % 26)
                )
                . $resultado;

            $numero = intdiv(
                $numero,
                26
            );
        }

        return $resultado;
    };


    /*
    |--------------------------------------------------------------------------
    | CONTENIDO DEL EXCEL
    |--------------------------------------------------------------------------
    */

    $sheetRows = [];


    // Institución
    $sheetRows[] =
        '<row r="1">' .
            '<c r="A1" t="inlineStr" s="1">' .
                '<is><t>' .
                    $esc(
                        'SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA'
                    ) .
                '</t></is>' .
            '</c>' .
        '</row>';


    // Título
    $sheetRows[] =
        '<row r="2">' .
            '<c r="A2" t="inlineStr" s="2">' .
                '<is><t>' .
                    $esc(
                        'REPORTE ASIGNACION DE COMBUSTIBLE POR VEHICULO'
                    ) .
                '</t></is>' .
            '</c>' .
        '</row>';


    // Detalle
    $sheetRows[] =
        '<row r="3">' .
            '<c r="A3" t="inlineStr" s="2">' .
                '<is><t>' .
                    $esc(
                        'DETALLE DE ASIGNACION DE COMBUSTIBLE'
                    ) .
                '</t></is>' .
            '</c>' .
        '</row>';


    /*
    |--------------------------------------------------------------------------
    | ENCABEZADOS TABLA
    |--------------------------------------------------------------------------
    */

    $r = 5;

    $cells = [];

    foreach ($headers as $indice => $header) {

        $referencia =
            $col($indice + 1) . $r;

        $cells[] =
            '<c r="' .
            $referencia .
            '" t="inlineStr" s="3">' .

                '<is><t>' .
                    $esc($header) .
                '</t></is>' .

            '</c>';
    }

    $sheetRows[] =
        '<row r="' . $r . '">' .
            implode('', $cells) .
        '</row>';


    /*
    |--------------------------------------------------------------------------
    | DATOS
    |--------------------------------------------------------------------------
    */

    foreach ($rows as $indiceFila => $row) {

        $r++;

        $cells = [];

        $esTotal =
            $indiceFila === count($rows) - 1;

        foreach ($row as $indice => $cell) {

            [$tipo, $valor] = $cell;

            $referencia =
                $col($indice + 1) . $r;

            $estilo = $esTotal
                ? 4
                : ($tipo === 'n' ? 5 : 0);


            if ($tipo === 'n') {

                $cells[] =
                    '<c r="' .
                    $referencia .
                    '" s="' .
                    $estilo .
                    '">' .

                        '<v>' .
                            ((float) $valor) .
                        '</v>' .

                    '</c>';

            } else {

                $cells[] =
                    '<c r="' .
                    $referencia .
                    '" t="inlineStr" s="' .
                    $estilo .
                    '">' .

                        '<is><t>' .
                            $esc($valor) .
                        '</t></is>' .

                    '</c>';
            }
        }

        $sheetRows[] =
            '<row r="' . $r . '">' .
                implode('', $cells) .
            '</row>';
    }


    /*
    |--------------------------------------------------------------------------
    | HOJA
    |--------------------------------------------------------------------------
    */

    $sheet =
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .

        '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .

            '<sheetViews>' .

                '<sheetView workbookViewId="0">' .

                    '<pane ' .
                        'ySplit="5" ' .
                        'topLeftCell="A6" ' .
                        'activePane="bottomLeft" ' .
                        'state="frozen"/>' .

                '</sheetView>' .

            '</sheetViews>' .


            '<cols>' .

                '<col min="1" max="1" width="8" customWidth="1"/>' .

                '<col min="2" max="3" width="18" customWidth="1"/>' .

                '<col min="4" max="4" width="28" customWidth="1"/>' .

                '<col min="5" max="5" width="24" customWidth="1"/>' .

                '<col min="6" max="8" width="16" customWidth="1"/>' .

                '<col min="9" max="11" width="20" customWidth="1"/>' .

            '</cols>' .


            '<sheetData>' .

                implode(
                    '',
                    $sheetRows
                ) .

            '</sheetData>' .


            '<autoFilter ref="A5:K' .
                max(
                    5,
                    $r - 1
                ) .
            '"/>' .

        '</worksheet>';


    /*
    |--------------------------------------------------------------------------
    | ESTILOS
    |--------------------------------------------------------------------------
    */

    $styles =
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .

        '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .

            '<fonts count="3">' .

                '<font>' .
                    '<sz val="11"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

                '<font>' .
                    '<b/>' .
                    '<sz val="14"/>' .
                    '<color rgb="FF173F32"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

                '<font>' .
                    '<b/>' .
                    '<sz val="12"/>' .
                    '<color rgb="FF198754"/>' .
                    '<name val="Calibri"/>' .
                '</font>' .

            '</fonts>' .


            '<fills count="3">' .

                '<fill>' .
                    '<patternFill patternType="none"/>' .
                '</fill>' .

                '<fill>' .
                    '<patternFill patternType="gray125"/>' .
                '</fill>' .

                '<fill>' .
                    '<patternFill patternType="solid">' .
                        '<fgColor rgb="FFEAF4EF"/>' .
                        '<bgColor indexed="64"/>' .
                    '</patternFill>' .
                '</fill>' .

            '</fills>' .


            '<borders count="2">' .

                '<border/>' .

                '<border>' .
                    '<left style="thin"/>' .
                    '<right style="thin"/>' .
                    '<top style="thin"/>' .
                    '<bottom style="thin"/>' .
                '</border>' .

            '</borders>' .


            '<cellXfs count="6">' .

                '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .

                '<xf numFmtId="0" fontId="1" fillId="0" borderId="0"/>' .

                '<xf numFmtId="0" fontId="2" fillId="0" borderId="0"/>' .

                '<xf numFmtId="0" fontId="0" fillId="2" borderId="1"
                    applyFill="1" applyBorder="1"/>' .

                '<xf numFmtId="0" fontId="1" fillId="2" borderId="1"
                    applyFill="1" applyBorder="1"/>' .

                '<xf numFmtId="4" fontId="0" fillId="0" borderId="1"
                    applyNumberFormat="1" applyBorder="1"/>' .

            '</cellXfs>' .

        '</styleSheet>';


    /*
    |--------------------------------------------------------------------------
    | CREAR XLSX
    |--------------------------------------------------------------------------
    */

    $tmp = tempnam(
        sys_get_temp_dir(),
        'sigecom_vehiculos_'
    );

    $zip = new \ZipArchive();

    $zip->open(
        $tmp,
        \ZipArchive::OVERWRITE
    );


    $zip->addFromString(
        '[Content_Types].xml',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
        '</Types>'
    );


    $zip->addFromString(
        '_rels/.rels',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
        '</Relationships>'
    );


    $zip->addFromString(
        'xl/workbook.xml',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ' .
        'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .

            '<sheets>' .
                '<sheet name="Vehiculos" sheetId="1" r:id="rId1"/>' .
            '</sheets>' .

        '</workbook>'
    );


    $zip->addFromString(
        'xl/_rels/workbook.xml.rels',
        '<?xml version="1.0" encoding="UTF-8"?>' .

        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .

            '<Relationship Id="rId1" ' .
                'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" ' .
                'Target="worksheets/sheet1.xml"/>' .

            '<Relationship Id="rId2" ' .
                'Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" ' .
                'Target="styles.xml"/>' .

        '</Relationships>'
    );


    $zip->addFromString(
        'xl/worksheets/sheet1.xml',
        $sheet
    );

    $zip->addFromString(
        'xl/styles.xml',
        $styles
    );

    $zip->close();


    /*
    |--------------------------------------------------------------------------
    | DESCARGAR
    |--------------------------------------------------------------------------
    */

    $nombre =
        'SIGECOM_Reporte_por_vehiculo_' .
        now()->format('Ymd_His') .
        '.xlsx';


    return response()
        ->download(
            $tmp,
            $nombre,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        )
        ->deleteFileAfterSend(true);
}

public function vehiculosPdf(Request $request)
{
    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ]);

    if ($request->filled('vehiculo_id')) {
        $consulta->where(
            'vehiculo_id',
            $request->vehiculo_id
        );
    }

    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    $vales = $consulta
        ->orderBy('vehiculo_id')
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | PARTES DIARIOS
    |--------------------------------------------------------------------------
    */

    $vehiculoIds = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->values();

    $partesDiarios = collect();

    if ($vehiculoIds->isNotEmpty() && $vales->isNotEmpty()) {

        $fechaMinima = $vales
            ->pluck('fecha')
            ->filter()
            ->min();

        $fechaMaxima = $vales
            ->pluck('fecha')
            ->filter()
            ->max();

        $partesDiarios = ParteDiario::whereIn(
                'vehiculo_id',
                $vehiculoIds
            )
            ->when(
                $fechaMinima,
                fn ($query) => $query->whereDate(
                    'fecha',
                    '>=',
                    $fechaMinima
                )
            )
            ->when(
                $fechaMaxima,
                fn ($query) => $query->whereDate(
                    'fecha',
                    '<=',
                    $fechaMaxima
                )
            )
            ->get()
            ->keyBy(function ($parte) {
                return $parte->vehiculo_id
                    . '|'
                    . $parte->fecha->format('Y-m-d');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | PREPARAR REPORTE
    |--------------------------------------------------------------------------
    */

    $reporte = $vales
        ->map(function ($vale) use ($partesDiarios) {

            $vehiculo = $vale->vehiculo;
            $apertura = $vale->aperturaProgramatica;

            $fecha = $vale->fecha
                ? $vale->fecha->format('Y-m-d')
                : null;

            $claveParte = $vale->vehiculo_id
                . '|'
                . $fecha;

            $parte = $partesDiarios->get($claveParte);

            if (!$parte) {
                $presentacionParte = 'SIN REGISTRO';
            } elseif ($parte->presentado) {
                $presentacionParte = 'SÍ';
            } else {
                $presentacionParte = 'NO';
            }

            return (object) [
                'codigo' =>
                    $vehiculo?->codigo ?? '-',

                'placa' =>
                    $vehiculo?->placa ?? '-',

                'parte_diario' =>
                    $presentacionParte,

                'tipo_vehiculo' =>
                    $vehiculo?->tipo
                    ?? $vehiculo?->tipo_vehiculo
                    ?? '-',

                'apertura' =>
                    $apertura?->codigo ?? '-',

                'fuente' =>
                    $apertura?->fuenteFinanciamiento?->codigo
                    ?? '-',

                'organismo' =>
                    $apertura?->organismoFinanciador?->codigo
                    ?? '-',

                'litros' =>
                    (float) ($vale->cantidad_litros ?? 0),

                'precio_unitario' =>
                    (float) ($vale->precio_unitario ?? 0),

                'total' =>
                    (float) ($vale->total ?? 0),
            ];
        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | TOTALES
    |--------------------------------------------------------------------------
    */

    $totalLitros = (float) $reporte->sum('litros');
    $totalMonto = (float) $reporte->sum('total');

    /*
    |--------------------------------------------------------------------------
    | LOGOS
    |--------------------------------------------------------------------------
    */

    $logoSacaba = null;
    $logoChapare = null;

    $logoSacabaPath = public_path('images/logo-sacaba.jpg');
    $logoChaparePath = public_path('images/logo-chapare.png');

    if (file_exists($logoSacabaPath)) {
        $logoSacaba =
            'data:image/jpeg;base64,' .
            base64_encode(
                file_get_contents($logoSacabaPath)
            );
    }

    if (file_exists($logoChaparePath)) {
        $logoChapare =
            'data:image/png;base64,' .
            base64_encode(
                file_get_contents($logoChaparePath)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR PDF
    |--------------------------------------------------------------------------
    */

    $pdf = Pdf::loadView(
        'reportes.pdf.vehiculos',
        compact(
            'reporte',
            'totalLitros',
            'totalMonto',
            'logoSacaba',
            'logoChapare'
        )
    );

    $pdf->setPaper('letter', 'landscape');

    return $pdf->stream(
        'Reporte_por_vehiculo_' .
        now()->format('Ymd_His') .
        '.pdf'
    );
}
 public function aperturasExcel(Request $request)
{
    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ]);

    // APERTURA PROGRAMÁTICA
    if ($request->filled('apertura_programatica_id')) {
        $consulta->where(
            'apertura_programatica_id',
            $request->apertura_programatica_id
        );
    }

    // PERÍODO
    if ($request->filled('periodo_id')) {
        $consulta->where(
            'periodo_id',
            $request->periodo_id
        );
    }

    // FECHA INICIAL
    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    // FECHA FINAL
    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    $vales = $consulta
        ->orderBy('apertura_programatica_id')
        ->orderBy('fecha')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | AGRUPAR AP + FF + OF + UNIDAD + PRECIO UNITARIO
    |--------------------------------------------------------------------------
    */

    $reporte = $vales
        ->groupBy(function ($vale) {

            $apertura = $vale->aperturaProgramatica;
            $unidad = $vale->vehiculo?->unidad;

            return implode('|', [
                $apertura?->codigo ?? '',
                $apertura?->fuenteFinanciamiento?->codigo ?? '',
                $apertura?->organismoFinanciador?->codigo ?? '',
                $unidad?->nombre ?? '',
                (string) $vale->precio_unitario,
            ]);
        })
        ->map(function ($grupo) {

            $primerVale = $grupo->first();

            $apertura = $primerVale->aperturaProgramatica;
            $unidad = $primerVale->vehiculo?->unidad;

            return [
                'apertura' =>
                    $apertura?->codigo ?? '-',

                'fuente' =>
                    $apertura?->fuenteFinanciamiento?->codigo ?? '-',

                'organismo' =>
                    $apertura?->organismoFinanciador?->codigo ?? '-',

                'unidad' =>
                    $unidad?->nombre
                    ?? $apertura?->descripcion
                    ?? '-',

                'litros' =>
                    (float) $grupo->sum('cantidad_litros'),

                'precio_unitario' =>
                    (float) $primerVale->precio_unitario,

                'total' =>
                    (float) $grupo->sum('total'),
            ];
        })
        ->values();

    /*
    |--------------------------------------------------------------------------
    | SI ZIPARCHIVE NO ESTÁ DISPONIBLE → CSV
    |--------------------------------------------------------------------------
    */

    if (!class_exists(\ZipArchive::class)) {

        $nombre = 'SIGECOM_Reporte_por_apertura_' .
            now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($reporte) {

            $out = fopen('php://output', 'w');

            // UTF-8 BOM para Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'APERTURA PROGRAMÁTICA',
                'FF',
                'OF',
                'NOMBRE DE LA UNIDAD O PROYECTO',
                'CANTIDAD GASOLINA LITROS',
                'PRECIO UNITARIO (Bs./Litro)',
                'PRECIO TOTAL (Bs.)',
            ], ';');

            foreach ($reporte as $fila) {

                fputcsv($out, [
                    $fila['apertura'],
                    $fila['fuente'],
                    $fila['organismo'],
                    $fila['unidad'],
                    number_format($fila['litros'], 2, ',', ''),
                    number_format($fila['precio_unitario'], 2, ',', ''),
                    number_format($fila['total'], 2, ',', ''),
                ], ';');
            }

            // TOTAL
            fputcsv($out, [
                '',
                '',
                '',
                'TOTAL',
                number_format($reporte->sum('litros'), 2, ',', ''),
                '',
                number_format($reporte->sum('total'), 2, ',', ''),
            ], ';');

            fclose($out);

        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR XLSX
    |--------------------------------------------------------------------------
    */

    $headers = [
        'APERTURA PROGRAMÁTICA',
        'FF',
        'OF',
        'NOMBRE DE LA UNIDAD O PROYECTO',
        'CANTIDAD GASOLINA LITROS',
        'PRECIO UNITARIO (Bs./Litro)',
        'PRECIO TOTAL (Bs.)',
    ];

    $rows = [];

    foreach ($reporte as $fila) {

        $rows[] = [
            ['s', (string) $fila['apertura']],
            ['s', (string) $fila['fuente']],
            ['s', (string) $fila['organismo']],
            ['s', (string) $fila['unidad']],
            ['n', round((float) $fila['litros'], 2)],
            ['n', round((float) $fila['precio_unitario'], 2)],
            ['n', round((float) $fila['total'], 2)],
        ];
    }

    // FILA TOTAL
    $rows[] = [
        ['s', ''],
        ['s', ''],
        ['s', ''],
        ['s', 'TOTAL'],
        ['n', round((float) $reporte->sum('litros'), 2)],

        // Precio unitario NO se suma
        ['s', ''],

        ['n', round((float) $reporte->sum('total'), 2)],
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCIONES AUXILIARES
    |--------------------------------------------------------------------------
    */

    $esc = static fn($v) =>
        htmlspecialchars(
            (string) $v,
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );

    $col = static function (int $n): string {

        $r = '';

        while ($n > 0) {
            $n--;
            $r = chr(65 + ($n % 26)) . $r;
            $n = intdiv($n, 26);
        }

        return $r;
    };

    /*
    |--------------------------------------------------------------------------
    | CONTENIDO DEL EXCEL
    |--------------------------------------------------------------------------
    */

    $sheetRows = [];

    // Título institucional
    $sheetRows[] =
        '<row r="1">' .
            '<c r="A1" t="inlineStr" s="1">' .
                '<is><t>SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA</t></is>' .
            '</c>' .
        '</row>';

    // Título reporte
    $sheetRows[] =
        '<row r="2">' .
            '<c r="A2" t="inlineStr" s="2">' .
                '<is><t>REPORTE ASIGNACIÓN DE COMBUSTIBLE POR APERTURA PROGRAMÁTICA</t></is>' .
            '</c>' .
        '</row>';

    // Subtítulo
    $sheetRows[] =
        '<row r="3">' .
            '<c r="A3" t="inlineStr" s="2">' .
                '<is><t>DETALLE DE ASIGNACIÓN DE COMBUSTIBLE</t></is>' .
            '</c>' .
        '</row>';

    /*
    |--------------------------------------------------------------------------
    | ENCABEZADOS DE TABLA
    |--------------------------------------------------------------------------
    */

    $r = 5;

    $cells = [];

    foreach ($headers as $i => $h) {

        $ref = $col($i + 1) . $r;

        $cells[] =
            '<c r="' . $ref . '" t="inlineStr" s="3">' .
                '<is><t>' . $esc($h) . '</t></is>' .
            '</c>';
    }

    $sheetRows[] =
        '<row r="' . $r . '">' .
            implode('', $cells) .
        '</row>';

    /*
    |--------------------------------------------------------------------------
    | FILAS
    |--------------------------------------------------------------------------
    */

    foreach ($rows as $ri => $row) {

        $r++;

        $cells = [];

        $isTotal = $ri === count($rows) - 1;

        foreach ($row as $i => $cell) {

            [$type, $value] = $cell;

            $ref = $col($i + 1) . $r;

            $style = $isTotal
                ? 4
                : ($type === 'n' ? 5 : 0);

            if ($type === 'n') {

                $cells[] =
                    '<c r="' . $ref . '" s="' . $style . '">' .
                        '<v>' . ((float) $value) . '</v>' .
                    '</c>';

            } else {

                $cells[] =
                    '<c r="' . $ref . '" t="inlineStr" s="' . $style . '">' .
                        '<is><t>' . $esc($value) . '</t></is>' .
                    '</c>';
            }
        }

        $sheetRows[] =
            '<row r="' . $r . '">' .
                implode('', $cells) .
            '</row>';
    }

    /*
    |--------------------------------------------------------------------------
    | HOJA
    |--------------------------------------------------------------------------
    */

    $sheet =
    '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
    '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .

    '<sheetViews>' .
        '<sheetView workbookViewId="0">' .
            '<pane ySplit="5" topLeftCell="A6" activePane="bottomLeft" state="frozen"/>' .
        '</sheetView>' .
    '</sheetViews>' .

    '<cols>' .
        '<col min="1" max="1" width="28" customWidth="1"/>' .
        '<col min="2" max="3" width="12" customWidth="1"/>' .
        '<col min="4" max="4" width="45" customWidth="1"/>' .
        '<col min="5" max="5" width="22" customWidth="1"/>' .
        '<col min="6" max="6" width="25" customWidth="1"/>' .
        '<col min="7" max="7" width="22" customWidth="1"/>' .
    '</cols>' .

    '<sheetData>' .
        implode('', $sheetRows) .
    '</sheetData>' .

    '<autoFilter ref="A5:G' . max(5, $r - 1) . '"/>' .

    '</worksheet>';

    /*
    |--------------------------------------------------------------------------
    | ESTILOS
    |--------------------------------------------------------------------------
    */

    $styles =
        '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .

        '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .

        '<fonts count="3">' .

            '<font>' .
                '<sz val="11"/>' .
                '<name val="Calibri"/>' .
            '</font>' .

            '<font>' .
                '<b/>' .
                '<sz val="14"/>' .
                '<color rgb="FF173F32"/>' .
                '<name val="Calibri"/>' .
            '</font>' .

            '<font>' .
                '<b/>' .
                '<sz val="12"/>' .
                '<color rgb="FF198754"/>' .
                '<name val="Calibri"/>' .
            '</font>' .

        '</fonts>' .

        '<fills count="3">' .
            '<fill><patternFill patternType="none"/></fill>' .
            '<fill><patternFill patternType="gray125"/></fill>' .
            '<fill>' .
                '<patternFill patternType="solid">' .
                    '<fgColor rgb="FFEAF4EF"/>' .
                    '<bgColor indexed="64"/>' .
                '</patternFill>' .
            '</fill>' .
        '</fills>' .

        '<borders count="2">' .
            '<border/>' .
            '<border>' .
                '<left style="thin"/>' .
                '<right style="thin"/>' .
                '<top style="thin"/>' .
                '<bottom style="thin"/>' .
            '</border>' .
        '</borders>' .

        '<cellXfs count="6">' .

            '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .

            '<xf numFmtId="0" fontId="1" fillId="0" borderId="0"/>' .

            '<xf numFmtId="0" fontId="2" fillId="0" borderId="0"/>' .

            '<xf numFmtId="0" fontId="0" fillId="2" borderId="1"
                applyFill="1" applyBorder="1"/>' .

            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1"
                applyFill="1" applyBorder="1"/>' .

            '<xf numFmtId="4" fontId="0" fillId="0" borderId="1"
                applyNumberFormat="1" applyBorder="1"/>' .

        '</cellXfs>' .

        '</styleSheet>';

    /*
    |--------------------------------------------------------------------------
    | CREAR ARCHIVO XLSX
    |--------------------------------------------------------------------------
    */

    $tmp = tempnam(
        sys_get_temp_dir(),
        'sigecom_aperturas_'
    );

    $zip = new \ZipArchive();

    $zip->open(
        $tmp,
        \ZipArchive::OVERWRITE
    );

    $zip->addFromString(
        '[Content_Types].xml',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
        '</Types>'
    );

    $zip->addFromString(
        '_rels/.rels',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
        '</Relationships>'
    );

    $zip->addFromString(
        'xl/workbook.xml',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ' .
        'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
            '<sheets>' .
                '<sheet name="Aperturas" sheetId="1" r:id="rId1"/>' .
            '</sheets>' .
        '</workbook>'
    );

    $zip->addFromString(
        'xl/_rels/workbook.xml.rels',
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
        '</Relationships>'
    );

    $zip->addFromString(
        'xl/worksheets/sheet1.xml',
        $sheet
    );

    $zip->addFromString(
        'xl/styles.xml',
        $styles
    );

    $zip->close();

    /*
    |--------------------------------------------------------------------------
    | DESCARGAR
    |--------------------------------------------------------------------------
    */

    $nombre =
        'SIGECOM_Reporte_por_apertura_' .
        now()->format('Ymd_His') .
        '.xlsx';

    return response()
        ->download(
            $tmp,
            $nombre,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        )
        ->deleteFileAfterSend(true);
}
    public function aperturasPdf(Request $request)
{
    $consulta = ValeCombustible::conConsumo()->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'periodo',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
        'estacionServicio',
    ]);

    // Apertura programática
    if ($request->filled('apertura_programatica_id')) {
        $consulta->where(
            'apertura_programatica_id',
            $request->apertura_programatica_id
        );
    }


    // Fecha inicial
    if ($request->filled('fecha_inicio')) {
        $consulta->whereDate(
            'fecha',
            '>=',
            $request->fecha_inicio
        );
    }

    // Fecha final
    if ($request->filled('fecha_fin')) {
        $consulta->whereDate(
            'fecha',
            '<=',
            $request->fecha_fin
        );
    }

    $vales = $consulta
        ->orderBy('apertura_programatica_id')
        ->orderBy('fecha')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | AGRUPACIÓN
    |--------------------------------------------------------------------------
    | Se agrupa también por precio_unitario porque el precio puede cambiar.
    | El precio NO se suma.
    */

    $reporte = $vales
        ->groupBy(function ($vale) {

            $apertura = $vale->aperturaProgramatica;
            $unidad = $vale->vehiculo?->unidad;

            return implode('|', [
                $apertura?->codigo ?? '',
                $apertura?->fuenteFinanciamiento?->codigo ?? '',
                $apertura?->organismoFinanciador?->codigo ?? '',
                $unidad?->nombre ?? '',
                (string) $vale->precio_unitario,
            ]);
        })
        ->map(function ($grupo) {

            $primerVale = $grupo->first();

            $apertura = $primerVale->aperturaProgramatica;
            $unidad = $primerVale->vehiculo?->unidad;

            return (object) [
                'apertura' =>
                    $apertura?->codigo ?? '-',

                'fuente' =>
                    $apertura?->fuenteFinanciamiento?->codigo ?? '-',

                'organismo' =>
                    $apertura?->organismoFinanciador?->codigo ?? '-',

                'unidad' =>
                    $unidad?->nombre
                    ?? $apertura?->descripcion
                    ?? '-',

                // Litros sí se suman
                'litros' =>
                    (float) $grupo->sum('cantidad_litros'),

                // Precio unitario NO se suma
                'precio_unitario' =>
                    (float) $primerVale->precio_unitario,

                // Total sí se suma
                'total' =>
                    (float) $grupo->sum('total'),
            ];
        })
        ->values();

    $totalLitros = (float) $reporte->sum('litros');
    $totalMonto = (float) $reporte->sum('total');

    // Textos escritos por el usuario
    $textoInforme = trim((string) $request->input('texto_informe', ''));
    $notaReporte = trim((string) $request->input('nota_reporte', ''));
    $cierreReporte = trim((string) $request->input('cierre_reporte', 'Atentamente.'));

    $logoSacabaPath = public_path('images/logo-sacaba.jpg');
$logoChaparePath = public_path('images/logo-chapare.png');

$logoSacaba = file_exists($logoSacabaPath)
    ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoSacabaPath))
    : null;

$logoChapare = file_exists($logoChaparePath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoChaparePath))
    : null;

    $pdf = Pdf::loadView('reportes.pdf.aperturas', compact(
    'reporte',
    'totalLitros',
    'totalMonto',
    'textoInforme',
    'notaReporte',
    'cierreReporte',
    'logoSacaba',
    'logoChapare'
));
    // Carta horizontal
    $pdf->setPaper('letter', 'landscape');

    return $pdf->download(
        'reporte_aperturas_' . now()->format('Y-m-d') . '.pdf'
    );
}

}