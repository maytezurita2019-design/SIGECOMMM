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
use App\Models\TipoCombustible;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $reportHtml = null;
        $dashboard = null;

        if ($request->filled('tipo_reporte')) {
            $filtros = $request->validate([
                'tipo_reporte' => 'required|in:general,vehiculo,individual,apertura,policia',
                'ambito' => 'required|in:todos,gams,policia',
                'formato' => 'required|in:html,pdf,excel,imprimir',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'mes' => 'required_if:periodicidad,mensual|nullable|integer|between:1,12',
                'anio' => 'required_if:periodicidad,mensual,anual|nullable|integer|between:2000,2100',
                'periodicidad' => 'nullable|in:mensual,anual,personalizado',
                'vehiculo_id' => 'required_if:tipo_reporte,individual|nullable|integer|exists:vehiculos,id',
                'unidad_id' => 'nullable|integer|exists:unidades,id',
                'apertura_programatica_id' => 'nullable|integer|exists:aperturas_programaticas,id',
                'periodo_id' => 'nullable|integer|exists:periodos,id',
                'tipo_combustible_id' => 'nullable|integer|exists:tipos_combustible,id',
                'observaciones' => 'nullable|string|max:2000',
            ]);

            $periodicidad = $filtros['periodicidad'] ?? (
                $filtros['tipo_reporte'] === 'policia' ? 'mensual' : 'personalizado'
            );
            $filtros['periodicidad'] = $periodicidad;
            $request->merge($filtros);

            if ($periodicidad === 'mensual' && !empty($filtros['mes']) && !empty($filtros['anio'])) {
                $inicio = \Carbon\Carbon::createFromDate($filtros['anio'], $filtros['mes'], 1);
                $request->merge([
                    'fecha_inicio' => $inicio->toDateString(),
                    'fecha_fin' => $inicio->copy()->endOfMonth()->toDateString(),
                ]);
                $filtros['fecha_inicio'] = $inicio->toDateString();
                $filtros['fecha_fin'] = $inicio->copy()->endOfMonth()->toDateString();
            } elseif ($periodicidad === 'anual' && !empty($filtros['anio'])) {
                $request->merge([
                    'fecha_inicio' => $filtros['anio'] . '-01-01',
                    'fecha_fin' => $filtros['anio'] . '-12-31',
                ]);
                $filtros['fecha_inicio'] = $filtros['anio'] . '-01-01';
                $filtros['fecha_fin'] = $filtros['anio'] . '-12-31';
            }

            $rutas = [
                'general' => [
                    'html' => 'reportes.consumo',
                    'pdf' => 'reportes.consumo.pdf',
                    'excel' => 'reportes.consumo.excel',
                    'imprimir' => 'reportes.consumo',
                ],
                'vehiculo' => [
                    'html' => 'reportes.vehiculos',
                    'pdf' => 'reportes.vehiculos.pdf',
                    'excel' => 'reportes.vehiculos.excel',
                    'imprimir' => 'reportes.vehiculos',
                ],
                'individual' => [
                    'html' => 'reportes.vehiculo-individual',
                    'pdf' => 'reportes.vehiculo-individual.pdf',
                    'excel' => 'reportes.vehiculo-individual.excel',
                    'imprimir' => 'reportes.vehiculo-individual',
                ],
                'apertura' => [
                    'html' => 'reportes.aperturas',
                    'pdf' => 'reportes.aperturas.pdf',
                    'excel' => 'reportes.aperturas.excel',
                    'imprimir' => 'reportes.aperturas',
                ],
                'policia' => [
                    'html' => 'reportes.policia-vehiculos',
                    'pdf' => 'reportes.policia-vehiculos.pdf',
                    'excel' => 'reportes.policia-vehiculos.excel',
                    'imprimir' => 'reportes.policia-vehiculos',
                ],
            ];

            $formato = $filtros['formato'];
            unset($filtros['tipo_reporte'], $filtros['formato']);

            if ($formato === 'html') {
                $request->merge(['fragment' => true]);
                $dashboard = $this->datosDashboard($request);
                $vistaReporte = match ($request->input('tipo_reporte')) {
                    'general' => $this->consumo($request),
                    'vehiculo' => $this->porVehiculo($request),
                    'individual' => $this->vehiculoIndividual($request),
                    'apertura' => $this->porApertura($request),
                    'policia' => app(ReportePoliciaVehiculoController::class)->index($request),
                };

                if (!$vistaReporte instanceof \Illuminate\Contracts\View\View) {
                    throw new \LogicException('El controlador del reporte no devolvió una vista HTML.');
                }

                $reportHtml = $vistaReporte->render();

                if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'dashboardHtml' => view('reportes.partials.dashboard', compact('dashboard'))->render(),
                        'reportHtml' => view('reportes.partials.resultado', compact('reportHtml'))->render(),
                    ]);
                }
            }

            if ($formato === 'imprimir') {
                $filtros['imprimir'] = 1;
            }

            if ($formato !== 'html') {
                return redirect()->route(
                    $rutas[$request->input('tipo_reporte')][$formato],
                    $filtros
                );
            }
        }

        return view('reportes.index', [
            'vehiculos' => Vehiculo::orderBy('codigo')->get(),
            'unidades' => Unidad::orderBy('nombre')->get(),
            'aperturas' => AperturaProgramatica::where('estado', true)->orderBy('codigo')->get(),
            'periodos' => Periodo::orderByDesc('anio')->orderByDesc('mes')->get(),
            'tiposCombustible' => TipoCombustible::orderBy('nombre')->get(),
            'reportHtml' => $reportHtml,
            'dashboard' => $dashboard,
        ]);
    }

    private function consultaConsumo(Request $request)
    {
        $filtros = $request->validate([
            'ambito' => 'nullable|in:todos,gams,policia',
            'tipo_combustible_id' => 'nullable|integer|exists:tipos_combustible,id',
        ]);

        $consulta = ValeCombustible::conConsumo($filtros['ambito'] ?? 'todos');

        if (!empty($filtros['tipo_combustible_id'])) {
            $consulta->where('tipo_combustible_id', $filtros['tipo_combustible_id']);
        }

        return $consulta;
    }

    private function etiquetasReporteVehiculos(Request $request): array
    {
        $periodicidad = $request->input('periodicidad', 'personalizado');
        $mes = (int) $request->input('mes', now()->month);
        $anio = (int) $request->input('anio', now()->year);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $periodo = match ($periodicidad) {
            'mensual' => \Carbon\Carbon::createFromDate($anio, $mes, 1)
                ->locale('es')
                ->translatedFormat('F Y'),
            'anual' => (string) $anio,
            default => $fechaInicio || $fechaFin
                ? (($fechaInicio ? \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') : 'Inicio abierto')
                    . ' – '
                    . ($fechaFin ? \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') : 'Sin fecha final'))
                : 'Todos los períodos',
        };

        $ambito = match ($request->input('ambito', 'todos')) {
            'gams' => 'GAMS',
            'policia' => 'Policía',
            default => 'Todos (GAMS y Policía)',
        };
        $combustible = $request->filled('tipo_combustible_id')
            ? TipoCombustible::find($request->input('tipo_combustible_id'))?->nombre
            : 'Todos';

        return compact('periodo', 'ambito', 'combustible');
    }

    private function datosDashboard(Request $request): array
    {
        $consultaConsumos = $this->consultaConsumo($request)
            ->with(['vehiculo', 'tipoCombustible']);
        $this->aplicarFiltrosDashboard($consultaConsumos, $request);
        $consumos = $consultaConsumos->get();

        $porVehiculo = $consumos
            ->groupBy(fn ($vale) => $vale->vehiculo_id ?? 0)
            ->map(function ($vales) {
                $vehiculo = $vales->first()->vehiculo;

                return [
                    'label' => trim(($vehiculo?->codigo ?? 'SIN CÓDIGO') . ' · ' . ($vehiculo?->placa ?? 'SIN PLACA')),
                    'litros' => (float) $vales->sum(fn ($vale) => (float) ($vale->cantidad_litros ?? 0)),
                ];
            })
            ->sortByDesc('litros')
            ->values();

        $porMes = $consumos
            ->groupBy(fn ($vale) => $vale->fecha?->format('m') ?? '00')
            ->map(fn ($vales) => (float) $vales->sum(fn ($vale) => (float) ($vale->cantidad_litros ?? 0)));

        $porCombustible = $consumos
            ->groupBy(fn ($vale) => $vale->tipoCombustible?->nombre ?? 'Sin tipo')
            ->map(fn ($vales, $nombre) => [
                'label' => $nombre,
                'litros' => (float) $vales->sum(fn ($vale) => (float) ($vale->cantidad_litros ?? 0)),
            ])
            ->sortByDesc('litros')
            ->values();

        $entregados = 0;
        if (($request->input('ambito', 'todos')) !== 'gams') {
            $entregados = ValeCombustible::query()
                ->whereNotNull('entrega_policia_id')
                ->where('estado', 'ENTREGADO')
                ->count();
        }

        $pendientes = 0;
        if (($request->input('ambito', 'todos')) !== 'policia') {
            $pendientes = ValeCombustible::query()
                ->whereNull('entrega_policia_id')
                ->where('estado', 'EMITIDO')
                ->count();
        }

        $periodicidad = $request->input('periodicidad', 'personalizado');

        return [
            'totalVales' => $consumos->count(),
            'totalLitros' => (float) $consumos->sum(fn ($vale) => (float) ($vale->cantidad_litros ?? 0)),
            'entregados' => $entregados,
            'pendientes' => $pendientes,
            'mostrarEntregados' => ($request->input('ambito', 'todos')) !== 'gams',
            'mostrarPendientes' => ($request->input('ambito', 'todos')) !== 'policia',
            'vehiculos' => $porVehiculo,
            'meses' => collect(range(1, 12))->map(fn ($mes) => [
                'label' => \Carbon\Carbon::create(now()->year, $mes, 1)->locale('es')->translatedFormat('M'),
                'litros' => (float) $porMes->get(sprintf('%02d', $mes), 0),
            ]),
            'combustibles' => $porCombustible,
            'mostrarMeses' => $periodicidad === 'anual',
        ];
    }

    private function aplicarFiltrosDashboard($consulta, Request $request): void
    {
        $this->aplicarFiltrosFecha($consulta, $request);

        if ($request->filled('vehiculo_id')) {
            $consulta->where('vehiculo_id', $request->input('vehiculo_id'));
        }
        if ($request->filled('unidad_id')) {
            $consulta->whereHas('vehiculo', fn ($query) => $query->where('unidad_id', $request->input('unidad_id')));
        }
        if ($request->filled('apertura_programatica_id')) {
            $consulta->where('apertura_programatica_id', $request->input('apertura_programatica_id'));
        }
        if ($request->filled('periodo_id')) {
            $consulta->where('periodo_id', $request->input('periodo_id'));
        }
        if ($request->filled('tipo_combustible_id')) {
            $consulta->where('tipo_combustible_id', $request->input('tipo_combustible_id'));
        }
    }


    public function consumo(Request $request)
{
    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'estacionServicio',
        'aperturaProgramatica',
        'usuario',
    ]);

$this->aplicarFiltrosFecha($consulta, $request);
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

    $totalVales = $vales->count();

    $totalVehiculos = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->count();

    $totalLitros = (float) $vales->sum('cantidad_litros');

    $totalMonto = (float) $vales->sum('total');

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

    $totalGasolina = (float) $resumenUnidades->sum('gasolina');

    $totalDiesel = (float) $resumenUnidades->sum('diesel');

    $totalOtros = (float) $resumenUnidades->sum('otros');

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

    $request->validate([
        'fecha_inicio' => ['nullable', 'date'],
        'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        'vehiculo_id' => ['nullable', 'integer', 'exists:vehiculos,id'],
        'unidad_id' => ['nullable', 'integer', 'exists:unidades,id'],
        'texto_superior' => ['nullable', 'string'],
        'texto_inferior' => ['nullable', 'string'],
    ]);

    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'estacionServicio',
        'aperturaProgramatica',
        'usuario',
    ]);

    $this->aplicarFiltrosFecha($consulta, $request);

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

    $vales = $consulta
        ->orderByDesc('fecha')
        ->orderByDesc('numero_vale')
        ->get();

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

    $resumenUnidades = $vales
        ->groupBy(function ($vale) {

            return $vale
                ->vehiculo
                ?->unidad
                ?->nombre
                ?? 'SIN UNIDAD';

        })
        ->map(function ($grupo, $unidad) {

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

    $totalGasolina = (float) $resumenUnidades
        ->sum('gasolina');

    $totalDiesel = (float) $resumenUnidades
        ->sum('diesel');

    $totalOtros = (float) $resumenUnidades
        ->sum('otros');

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

    $pdf->setPaper(
        'letter',
        'landscape'
    );

    return $pdf->download(
        'Reporte_general_consumo_' .
        now()->format('Y-m-d')  .
        '.pdf'
    );
}

private function aplicarFiltrosFecha(
    $consulta,
    Request $request
) {
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

    return $consulta;
}

    public function porVehiculo(Request $request)
{

    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
        'estacionServicio',
    ]);

    if ($request->filled('vehiculo_id')) {
        $consulta->where(
            'vehiculo_id',
            $request->vehiculo_id
        );
    }

$this->aplicarFiltrosFecha($consulta, $request);

    $vales = $consulta
        ->orderBy('vehiculo_id')
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();

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

            return (object) [

                'fecha' =>
                    $vale->fecha,

                'numero_vale' =>
                    $vale->numero_vale,

                'combustible' =>
                    $vale->tipoCombustible?->nombre ?? '-',

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

    $vehiculos = Vehiculo::where(
            'estado',
            true
        )
        ->orderBy('codigo')
        ->get();

    $observaciones = trim((string) $request->input('observaciones', ''));

    return view(
        'reportes.vehiculos',
        compact(
            'vales',
            'reporte',
            'vehiculos',
            'totalVales',
            'totalVehiculos',
            'totalLitros',
            'totalMonto',
            'observaciones'
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

        $consulta = $this->consultaConsumo($request)
            ->with([
                'aperturaProgramatica',
                'tipoCombustible',
                'estacionServicio',
            ])
            ->where('vehiculo_id', $vehiculo->id);

$this->aplicarFiltrosFecha($consulta, $request);

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

    $consulta = $this->consultaConsumo($request)
        ->with([
            'aperturaProgramatica',
            'tipoCombustible',
            'estacionServicio',
        ])
        ->where('vehiculo_id', $vehiculo->id);

    $this->aplicarFiltrosFecha($consulta, $request);

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

    return $pdf->download(
        'Reporte_individual_' .
        $vehiculo->codigo .
        '_' .
        now()->format('Y-m-d')  .
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

    $vehiculo = Vehiculo::with('unidad')->findOrFail($request->vehiculo_id);

    $consulta = $this->consultaConsumo($request)
        ->with('aperturaProgramatica')
        ->where('vehiculo_id', $vehiculo->id);

    $this->aplicarFiltrosFecha($consulta, $request);

    $vales = $consulta
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Vehiculo Individual');

    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA');
    $sheet->mergeCells('A2:I2');
    $sheet->setCellValue('A2', 'REPORTE ASIGNACION DE COMBUSTIBLE');

    $sheet->mergeCells('A4:F4');
    $sheet->setCellValue('A4', 'DATOS RELATIVOS AL VEHICULO');

    $sheet->fromArray([
        'TIPO', 'COD. ASIGNADO', 'INDUSTRIA', 'COLOR', 'PLACA', 'MARCA'
    ], null, 'A5');

    $sheet->fromArray([
        $vehiculo->tipo ?: '-',
        $vehiculo->codigo ?: '-',
        $vehiculo->industria ?: '-',
        $vehiculo->color ?: '-',
        $vehiculo->placa ?: '-',
        $vehiculo->marca ?: '-',
    ], null, 'A6');

    $sheet->mergeCells('A8:I8');
    $sheet->setCellValue('A8', 'DETALLE DE ASIGNACION');

    $sheet->fromArray([
        'FECHA',
        'VALE',
        'CANTIDAD (LT)',
        'PRECIO (LT)',
        'TOTAL (Bs)',
        'N.º FACTURA',
        'KILOMETRAJE',
        'DETALLE DE ASIGNACIÓN',
        'APERTURA PROGRAM.'
    ], null, 'A9');

    $fila = 10;

    foreach ($vales as $vale) {
        $sheet->setCellValue('A' . $fila, $vale->fecha ? $vale->fecha->format('d/m/Y') : '-');
        $sheet->setCellValueExplicit(
            'B' . $fila,
            sprintf('%06d', (int) $vale->numero_vale),
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
        );
        $sheet->setCellValue('C' . $fila, (float) $vale->cantidad_litros);
        $sheet->setCellValue('D' . $fila, (float) $vale->precio_unitario);
        $sheet->setCellValue('E' . $fila, (float) $vale->total);
        $sheet->setCellValue('F' . $fila, $vale->numero_factura ?: '-');
        $sheet->setCellValue('G' . $fila, $vale->kilometraje ?? '-');
        $sheet->setCellValue('H' . $fila, $vale->detalle_asignacion ?? '');
        $sheet->setCellValue('I' . $fila, $vale->aperturaProgramatica?->codigo ?? '-');
        $fila++;
    }

    $filaTotal = $fila;
    $sheet->setCellValue('A' . $filaTotal, 'TOTAL');
    $sheet->setCellValue('C' . $filaTotal, (float) $vales->sum('cantidad_litros'));
    $sheet->setCellValue('E' . $filaTotal, (float) $vales->sum('total'));

    $estiloEncabezado = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '08712F']],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    ];

    $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A2:I2')->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('A1:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4:F4')->getFont()->setBold(true);
    $sheet->getStyle('A8:I8')->getFont()->setBold(true);
    $sheet->getStyle('A5:F5')->applyFromArray($estiloEncabezado);
    $sheet->getStyle('A9:I9')->applyFromArray($estiloEncabezado);
    $sheet->getStyle('A5:F6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A9:I' . $filaTotal)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A' . $filaTotal . ':I' . $filaTotal)->getFont()->setBold(true);
    $sheet->getStyle('A' . $filaTotal . ':I' . $filaTotal)->getFill()
        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E7F4EB');

    if ($filaTotal > 10) {
        $sheet->getStyle('C10:E' . ($filaTotal - 1))
            ->getNumberFormat()->setFormatCode('#,##0.00');
    }
    $sheet->getStyle('C' . $filaTotal)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('E' . $filaTotal)->getNumberFormat()->setFormatCode('#,##0.00');

    foreach ([
        'A' => 14, 'B' => 14, 'C' => 17, 'D' => 17, 'E' => 17,
        'F' => 18, 'G' => 18, 'H' => 48, 'I' => 22,
    ] as $columna => $ancho) {
        $sheet->getColumnDimension($columna)->setWidth($ancho);
    }

    $sheet->freezePane('A10');
    $sheet->setAutoFilter('A9:I' . max(9, $filaTotal - 1));

    $nombre = 'SIGECOM_Reporte_individual_' .
        preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $vehiculo->codigo) . '_' .
        now()->format('Ymd_His') . '.xlsx';

    return $this->descargarExcel($spreadsheet, $nombre, 'sigecom_individual_');
}

    public function porApertura(Request $request)
{
    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'periodo',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
        'estacionServicio',
    ]);
    if ($request->filled('apertura_programatica_id')) {
        $consulta->where(
            'apertura_programatica_id',
            $request->apertura_programatica_id
        );
    }
    if ($request->filled('periodo_id')) {
        $consulta->where(
            'periodo_id',
            $request->periodo_id
        );
    }

    $this->aplicarFiltrosFecha($consulta, $request);

    $vales = $consulta
        ->orderBy('apertura_programatica_id')
        ->orderBy('fecha')
        ->get();

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
            'litros' =>
                (float) $grupo->sum('cantidad_litros'),
            'precio_unitario' =>
                (float) $primerVale->precio_unitario,
            'total' =>
                (float) $grupo->sum('total'),

            'cantidad_vales' =>
                $grupo->count(),
        ];
    })
    ->values();

    $totalVales = $vales->count();

    $totalVehiculos = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->count();

    $totalLitros = (float) $vales->sum('cantidad_litros');

    $totalMonto = (float) $vales->sum('total');

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
    public function consumoExcel(Request $request)
    {
        $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'vehiculo_id' => ['nullable', 'integer', 'exists:vehiculos,id'],
            'unidad_id' => ['nullable', 'integer', 'exists:unidades,id'],
        ]);

        $consulta = $this->consultaConsumo($request)->with([
            'vehiculo.unidad',
            'tipoCombustible',
        ]);

    $this->aplicarFiltrosFecha($consulta, $request);

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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Reporte General');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue(
            'A1',
            'SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN - DIRECCIÓN ADMINISTRATIVA'
        );

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue(
            'A2',
            'REPORTE GENERAL DE CONSUMO DE COMBUSTIBLE'
        );

        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(14);

        $sheet->getStyle('A2')->getFont()
            ->setBold(true)
            ->setSize(12);

        $sheet->getStyle('A1:H2')
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'VALES UTILIZADOS');
        $sheet->setCellValue('B4', 'VEHÍCULOS');
        $sheet->setCellValue('C4', 'COMBUSTIBLE CONSUMIDO (L)');
        $sheet->setCellValue('D4', 'MONTO TOTAL (Bs)');

        $sheet->setCellValue('A5', $totalVales);
        $sheet->setCellValue('B5', $totalVehiculos);
        $sheet->setCellValue('C5', $totalLitros);
        $sheet->setCellValue('D5', $totalMonto);

        $filaCabecera = 7;

        $encabezados = [
            'A' => 'UNIDAD',
            'B' => 'VEHÍCULOS',
            'C' => 'VALES',
            'D' => 'GASOLINA (L)',
            'E' => 'DIÉSEL (L)',
            'F' => 'OTROS (L)',
            'G' => 'TOTAL LITROS',
            'H' => 'MONTO TOTAL (Bs)',
        ];

        foreach ($encabezados as $columna => $titulo) {
            $sheet->setCellValue(
                $columna . $filaCabecera,
                $titulo
            );
        }

        $fila = 8;

        foreach ($resumenUnidades as $registro) {

            $sheet->setCellValue(
                'A' . $fila,
                $registro->unidad
            );

            $sheet->setCellValue(
                'B' . $fila,
                $registro->vehiculos
            );

            $sheet->setCellValue(
                'C' . $fila,
                $registro->vales
            );

            $sheet->setCellValue(
                'D' . $fila,
                $registro->gasolina
            );

            $sheet->setCellValue(
                'E' . $fila,
                $registro->diesel
            );

            $sheet->setCellValue(
                'F' . $fila,
                $registro->otros
            );

            $sheet->setCellValue(
                'G' . $fila,
                $registro->litros
            );

            $sheet->setCellValue(
                'H' . $fila,
                $registro->total
            );

            $fila++;
        }

        $sheet->setCellValue('A' . $fila, 'TOTAL GENERAL');
        $sheet->setCellValue('B' . $fila, $totalVehiculos);
        $sheet->setCellValue('C' . $fila, $totalVales);
        $sheet->setCellValue('D' . $fila, $totalGasolina);
        $sheet->setCellValue('E' . $fila, $totalDiesel);
        $sheet->setCellValue('F' . $fila, $totalOtros);
        $sheet->setCellValue('G' . $fila, $totalLitros);
        $sheet->setCellValue('H' . $fila, $totalMonto);

        $estiloEncabezado = [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '08712F',
                ],
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A4:D4')
            ->applyFromArray($estiloEncabezado);

        $sheet->getStyle('A7:H7')
            ->applyFromArray($estiloEncabezado);

        $sheet->getStyle('A5:D5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('A8:H' . $fila)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('A' . $fila . ':H' . $fila)
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A' . $fila . ':H' . $fila)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('E7F4EB');

        $sheet->getStyle('C5:D5')
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        if ($fila >= 8) {
            $sheet->getStyle('D8:H' . $fila)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(22);

        $sheet->freezePane('A8');

        if ($fila > 8) {
            $sheet->setAutoFilter(
                'A7:H' . ($fila - 1)
            );
        }

        $nombre =
            'SIGECOM_Reporte_general_consumo_' .
            now()->format('Ymd_His') .
            '.xlsx';

        $rutaTemporal =
            tempnam(
                sys_get_temp_dir(),
                'sigecom_excel_'
            );

        $writer = new Xlsx($spreadsheet);

        $writer->save($rutaTemporal);

        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        return response()
            ->download(
                $rutaTemporal,
                $nombre,
                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            )
            ->deleteFileAfterSend(true);    }

    public function vehiculosExcel(Request $request)
{
        $request->validate([
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ]);

        $consulta = $this->consultaConsumo($request)->with([
            'vehiculo.unidad',
            'tipoCombustible',
            'aperturaProgramatica.fuenteFinanciamiento',
            'aperturaProgramatica.organismoFinanciador',
    ]);

    $consulta
        ->when(
            $request->filled('vehiculo_id'),
            fn ($q) => $q->where('vehiculo_id', $request->vehiculo_id)
        )
        ->when(
            $request->filled('fecha_inicio'),
            fn ($q) => $q->whereDate('fecha', '>=', $request->fecha_inicio)
        )
        ->when(
            $request->filled('fecha_fin'),
            fn ($q) => $q->whereDate('fecha', '<=', $request->fecha_fin)
        );

    $vales = $consulta
        ->orderBy('vehiculo_id')
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();
    $vehiculoIds = $vales
        ->pluck('vehiculo_id')
        ->filter()
        ->unique()
        ->values();

    $partesDiarios = collect();

    if ($vehiculoIds->isNotEmpty() && $vales->isNotEmpty()) {
        $fechaMinima = $vales->pluck('fecha')->filter()->min();
        $fechaMaxima = $vales->pluck('fecha')->filter()->max();

        $partesDiarios = ParteDiario::whereIn('vehiculo_id', $vehiculoIds)
            ->when(
                $fechaMinima,
                fn ($q) => $q->whereDate('fecha', '>=', $fechaMinima)
            )
            ->when(
                $fechaMaxima,
                fn ($q) => $q->whereDate('fecha', '<=', $fechaMaxima)
            )
            ->get()
            ->keyBy(
                fn ($parte) =>
                    $parte->vehiculo_id . '|' . $parte->fecha->format('Y-m-d')
            );
    }
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Vehiculos');
    $sheet->mergeCells('A1:N1');
    $sheet->setCellValue(
        'A1',
        'SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA'
    );

    $sheet->mergeCells('A2:N2');
    $sheet->setCellValue(
        'A2',
        'REPORTE ASIGNACION DE COMBUSTIBLE POR VEHICULO'
    );

    $sheet->mergeCells('A3:N3');
    $sheet->setCellValue(
        'A3',
        'DETALLE DE ASIGNACION DE COMBUSTIBLE'
    );
    $etiquetas = $this->etiquetasReporteVehiculos($request);
    $sheet->mergeCells('A4:N4');
    $sheet->setCellValue(
        'A4',
        'PERÍODO: ' . $etiquetas['periodo']
            . ' | ÁMBITO: ' . $etiquetas['ambito']
            . ' | COMBUSTIBLE: ' . $etiquetas['combustible']
    );
    $encabezados = [
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
        'FECHA',
        'N.º VALE',
        'COMBUSTIBLE',
    ];

    $sheet->fromArray(
        $encabezados,
        null,
        'A5'
    );
    $fila = 6;

    foreach ($vales as $indice => $vale) {
        $vehiculo = $vale->vehiculo;
        $apertura = $vale->aperturaProgramatica;

        $fecha = $vale->fecha
            ? $vale->fecha->format('Y-m-d')
            : null;

        $parte = $partesDiarios->get(
            $vale->vehiculo_id . '|' . $fecha
        );

        $presentacionParte = !$parte
            ? 'SIN REGISTRO'
            : ($parte->presentado ? 'SÍ' : 'NO');

        $sheet->fromArray([
            $indice + 1,
            $vehiculo?->codigo ?? '-',
            $vehiculo?->placa ?? '-',
            $presentacionParte,
            $vehiculo?->tipo
                ?? $vehiculo?->tipo_vehiculo
                ?? '-',
            $apertura?->codigo ?? '-',
            $apertura?->fuenteFinanciamiento?->codigo ?? '-',
            $apertura?->organismoFinanciador?->codigo ?? '-',
            (float) ($vale->cantidad_litros ?? 0),
            (float) ($vale->precio_unitario ?? 0),
            (float) ($vale->total ?? 0),
            $vale->fecha?->format('d/m/Y') ?? '-',
            sprintf('%06d', (int) $vale->numero_vale),
            $vale->tipoCombustible?->nombre ?? '-',
        ], null, 'A' . $fila);

        $fila++;
    }
    $filaTotal = $fila;

    $sheet->setCellValue(
        'H' . $filaTotal,
        'TOTAL'
    );

    $sheet->setCellValue(
        'I' . $filaTotal,
        (float) $vales->sum('cantidad_litros')
    );

    $sheet->setCellValue(
        'K' . $filaTotal,
        (float) $vales->sum('total')
    );
    $sheet->getStyle('A1:N1')->getFont()
        ->setBold(true)
        ->setSize(14);

    $sheet->getStyle('A2:N3')->getFont()
        ->setBold(true)
        ->setSize(12);

    $sheet->getStyle('A1:N3')
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A5:N5')->getFont()
        ->setBold(true);

    $sheet->getStyle('A5:N5')
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $sheet->getStyle('A5:N5')
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FFEAF4EF');
    $sheet->getStyle(
        'A5:N' . $filaTotal
    )->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle(
        'H' . $filaTotal . ':K' . $filaTotal
    )->getFont()->setBold(true);

    $sheet->getStyle(
        'H' . $filaTotal . ':K' . $filaTotal
    )->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FFEAF4EF');
    if ($filaTotal > 6) {
        $sheet->getStyle(
            'I6:K' . ($filaTotal - 1)
        )->getNumberFormat()->setFormatCode('#,##0.00');
    }

    $sheet->getStyle(
        'I' . $filaTotal
    )->getNumberFormat()->setFormatCode('#,##0.00');

    $sheet->getStyle(
        'K' . $filaTotal
    )->getNumberFormat()->setFormatCode('#,##0.00');
    $anchos = [
        'A' => 8,
        'B' => 18,
        'C' => 18,
        'D' => 28,
        'E' => 24,
        'F' => 16,
        'G' => 16,
        'H' => 16,
        'I' => 20,
        'J' => 20,
        'K' => 20,
        'L' => 16,
        'M' => 14,
        'N' => 20,
    ];

    foreach ($anchos as $columna => $ancho) {
        $sheet->getColumnDimension($columna)
            ->setWidth($ancho);
    }

    $sheet->freezePane('A6');

    $ultimaFilaDatos = max(5, $filaTotal - 1);

    $sheet->setAutoFilter(
        'A5:N' . $ultimaFilaDatos
    );
    $observaciones = trim((string) $request->input('observaciones', ''));
    if ($observaciones !== '') {
        $filaObservaciones = $filaTotal + 2;
        $sheet->mergeCells('A' . $filaObservaciones . ':N' . $filaObservaciones);
        $sheet->setCellValue('A' . $filaObservaciones, 'OBSERVACIONES: ' . $observaciones);
        $sheet->getStyle('A' . $filaObservaciones)->getAlignment()->setWrapText(true);
    }
    $nombre =
        'SIGECOM_Reporte_por_vehiculo_' .
        now()->format('Ymd_His') .
        '.xlsx';

    $tmp = tempnam(
        sys_get_temp_dir(),
        'sigecom_vehiculos_'
    );

    $writer = new Xlsx($spreadsheet);
    $writer->save($tmp);

    return response()
        ->download(
            $tmp,
            $nombre,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        )
        ->deleteFileAfterSend(true);
}

public function vehiculosPdf(Request $request)
{
    $request->validate([
        'observaciones' => ['nullable', 'string', 'max:2000'],
    ]);

    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ]);

    if ($request->filled('vehiculo_id')) {
        $consulta->where(
            'vehiculo_id',
            $request->vehiculo_id
        );
    }

    $this->aplicarFiltrosFecha($consulta, $request);

    $vales = $consulta
        ->orderBy('vehiculo_id')
        ->orderBy('fecha')
        ->orderBy('numero_vale')
        ->get();

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
                'fecha' =>
                    $vale->fecha,

                'numero_vale' =>
                    $vale->numero_vale,

                'combustible' =>
                    $vale->tipoCombustible?->nombre ?? '-',

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

    $totalLitros = (float) $reporte->sum('litros');
    $totalMonto = (float) $reporte->sum('total');
    $etiquetas = $this->etiquetasReporteVehiculos($request);

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

    $observaciones = trim((string) $request->input('observaciones', ''));

    $pdf = Pdf::loadView(
        'reportes.pdf.vehiculos',
        compact(
            'reporte',
            'totalLitros',
            'totalMonto',
            'logoSacaba',
            'logoChapare',
            'observaciones',
            'etiquetas'
        )
    );

    $pdf->setPaper('letter', 'landscape');

    return $pdf->download(
        'Reporte_por_vehiculo_' .
        now()->format('Y-m-d')  .
        '.pdf'
    );
}
public function aperturasExcel(Request $request)
{
    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
    ]);

    $consulta
        ->when(
            $request->filled('apertura_programatica_id'),
            fn ($q) => $q->where(
                'apertura_programatica_id',
                $request->apertura_programatica_id
            )
        )
        ->when(
            $request->filled('periodo_id'),
            fn ($q) => $q->where(
                'periodo_id',
                $request->periodo_id
            )
        )
        ->when(
            $request->filled('fecha_inicio'),
            fn ($q) => $q->whereDate(
                'fecha',
                '>=',
                $request->fecha_inicio
            )
        )
        ->when(
            $request->filled('fecha_fin'),
            fn ($q) => $q->whereDate(
                'fecha',
                '<=',
                $request->fecha_fin
            )
        );

    $vales = $consulta
        ->orderBy('apertura_programatica_id')
        ->orderBy('fecha')
        ->get();
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
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Aperturas');
    $sheet->mergeCells('A1:G1');
    $sheet->setCellValue(
        'A1',
        'SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION - DIRECCION ADMINISTRATIVA'
    );

    $sheet->mergeCells('A2:G2');
    $sheet->setCellValue(
        'A2',
        'REPORTE ASIGNACIÓN DE COMBUSTIBLE POR APERTURA PROGRAMÁTICA'
    );

    $sheet->mergeCells('A3:G3');
    $sheet->setCellValue(
        'A3',
        'DETALLE DE ASIGNACIÓN DE COMBUSTIBLE'
    );
    $encabezados = [
        'APERTURA PROGRAMÁTICA',
        'FF',
        'OF',
        'NOMBRE DE LA UNIDAD O PROYECTO',
        'CANTIDAD GASOLINA LITROS',
        'PRECIO UNITARIO (Bs./Litro)',
        'PRECIO TOTAL (Bs.)',
    ];

    $sheet->fromArray(
        $encabezados,
        null,
        'A5'
    );
    $fila = 6;

    foreach ($reporte as $dato) {
        $sheet->fromArray([
            $dato['apertura'],
            $dato['fuente'],
            $dato['organismo'],
            $dato['unidad'],
            $dato['litros'],
            $dato['precio_unitario'],
            $dato['total'],
        ], null, 'A' . $fila);

        $fila++;
    }
    $filaTotal = $fila;

    $sheet->setCellValue(
        'D' . $filaTotal,
        'TOTAL'
    );

    $sheet->setCellValue(
        'E' . $filaTotal,
        (float) $reporte->sum('litros')
    );

    $sheet->setCellValue(
        'G' . $filaTotal,
        (float) $reporte->sum('total')
    );
    $sheet->getStyle('A1:G1')
        ->getFont()
        ->setBold(true)
        ->setSize(14);

    $sheet->getStyle('A2:G3')
        ->getFont()
        ->setBold(true)
        ->setSize(12);

    $sheet->getStyle('A1:G3')
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A5:G5')
        ->getFont()
        ->setBold(true);

    $sheet->getStyle('A5:G5')
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $sheet->getStyle('A5:G5')
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FFEAF4EF');
    $sheet->getStyle(
        'A5:G' . $filaTotal
    )
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle(
        'D' . $filaTotal . ':G' . $filaTotal
    )
        ->getFont()
        ->setBold(true);

    $sheet->getStyle(
        'D' . $filaTotal . ':G' . $filaTotal
    )
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('FFEAF4EF');
    if ($filaTotal > 6) {
        $sheet->getStyle(
            'E6:G' . ($filaTotal - 1)
        )
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }

    $sheet->getStyle(
        'E' . $filaTotal
    )
        ->getNumberFormat()
        ->setFormatCode('#,##0.00');

    $sheet->getStyle(
        'G' . $filaTotal
    )
        ->getNumberFormat()
        ->setFormatCode('#,##0.00');
    $anchos = [
        'A' => 28,
        'B' => 12,
        'C' => 12,
        'D' => 45,
        'E' => 22,
        'F' => 25,
        'G' => 22,
    ];

    foreach ($anchos as $columna => $ancho) {
        $sheet->getColumnDimension($columna)
            ->setWidth($ancho);
    }
    $sheet->freezePane('A6');

    $ultimaFilaDatos = max(
        5,
        $filaTotal - 1
    );

    $sheet->setAutoFilter(
        'A5:G' . $ultimaFilaDatos
    );
    $nombre =
        'SIGECOM_Reporte_por_apertura_' .
        now()->format('Ymd_His') .
        '.xlsx';

    $tmp = tempnam(
        sys_get_temp_dir(),
        'sigecom_aperturas_'
    );

    $writer = new Xlsx($spreadsheet);
    $writer->save($tmp);

    return response()
        ->download(
            $tmp,
            $nombre,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        )
        ->deleteFileAfterSend(true);
}
    public function aperturasPdf(Request $request)
{
    $consulta = $this->consultaConsumo($request)->with([
        'vehiculo.unidad',
        'tipoCombustible',
        'periodo',
        'aperturaProgramatica.fuenteFinanciamiento',
        'aperturaProgramatica.organismoFinanciador',
        'estacionServicio',
    ]);
    if ($request->filled('apertura_programatica_id')) {
        $consulta->where(
            'apertura_programatica_id',
            $request->apertura_programatica_id
        );
    }
    if ($request->filled('periodo_id')) {
        $consulta->where(
            'periodo_id',
            $request->periodo_id
        );
    }

    $this->aplicarFiltrosFecha($consulta, $request);

    $vales = $consulta
        ->orderBy('apertura_programatica_id')
        ->orderBy('fecha')
        ->get();

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
                'litros' =>
                    (float) $grupo->sum('cantidad_litros'),
                'precio_unitario' =>
                    (float) $primerVale->precio_unitario,
                'total' =>
                    (float) $grupo->sum('total'),
            ];
        })
        ->values();

    $totalLitros = (float) $reporte->sum('litros');
    $totalMonto = (float) $reporte->sum('total');
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
    $pdf->setPaper('letter', 'landscape');

    return $pdf->download(
        'reporte_aperturas_' . now()->format('Y-m-d') . '.pdf'
    );
}


    private function descargarExcel(Spreadsheet $spreadsheet, string $nombre, string $prefijo)
    {
        $rutaTemporal = tempnam(sys_get_temp_dir(), $prefijo);
        $writer = new Xlsx($spreadsheet);
        $writer->save($rutaTemporal);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return response()
            ->download($rutaTemporal, $nombre, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend(true);
    }

}