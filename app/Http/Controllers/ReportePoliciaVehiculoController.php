<?php
namespace App\Http\Controllers;

use App\Models\ValeCombustible;
use App\Models\Vehiculo;
use App\Models\TipoCombustible;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportePoliciaVehiculoController extends Controller
{
    private function datos(Request $request): array
    {
        $f = $request->validate([
            'mes' => 'required_if:periodicidad,mensual|nullable|integer|between:1,12',
            'anio' => 'required_if:periodicidad,mensual,anual|nullable|integer|between:2000,2100',
            'vehiculo_id' => 'nullable|integer|exists:vehiculos,id',
            'combustible' => 'nullable|in:todos,gasolina,diesel',
            'tipo_combustible_id' => 'nullable|integer|exists:tipos_combustible,id',
            'ambito' => 'nullable|in:todos,gams,policia',
            'observaciones' => 'nullable|string|max:2000',
            'periodicidad' => 'nullable|in:mensual,anual,personalizado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
        $periodicidad = $f['periodicidad'] ?? 'mensual';
        $mes = (int) ($f['mes'] ?? now()->month);
        $anio = (int) ($f['anio'] ?? now()->year);
        $combustible = $f['combustible'] ?? 'todos';
        $ambito = $f['ambito'] ?? 'policia';
        $inicio = null;
        $fin = null;

        if ($periodicidad === 'mensual') {
            $inicio = sprintf('%04d-%02d-01', $anio, $mes);
            $fin = \Carbon\Carbon::parse($inicio)->endOfMonth()->toDateString();
        } elseif ($periodicidad === 'anual') {
            $inicio = sprintf('%04d-01-01', $anio);
            $fin = sprintf('%04d-12-31', $anio);
        } else {
            $inicio = $f['fecha_inicio'] ?? null;
            $fin = $f['fecha_fin'] ?? null;
        }

        $consulta = ValeCombustible::conConsumo($ambito)
            ->with(['vehiculo', 'tipoCombustible', 'aperturaProgramatica'])
            ->when($inicio, fn ($query) => $query->whereDate('fecha', '>=', $inicio))
            ->when($fin, fn ($query) => $query->whereDate('fecha', '<=', $fin));

        if (!empty($f['vehiculo_id'])) {
            $consulta->where('vehiculo_id', $f['vehiculo_id']);
        }
        if (!empty($f['tipo_combustible_id'])) {
            $consulta->where('tipo_combustible_id', $f['tipo_combustible_id']);
        } elseif ($combustible !== 'todos') {
            $consulta->whereHas('tipoCombustible', function ($q) use ($combustible) {
                // Coincide con gasolina, diesel, diésel y DIÉSEL.
                $q->whereRaw('LOWER(nombre) LIKE ?', [$combustible === 'diesel' ? 'di_sel%' : 'gasolina%']);
            });
        }

        $vales = $consulta->orderBy('vehiculo_id')->orderBy('fecha')->orderBy('numero_vale')->get();
        $filas = $vales->map(function ($vale) {
            $litros = (float) ($vale->cantidad_litros ?? 0);
            $precio = $vale->precio_unitario === null ? null : (float) $vale->precio_unitario;
            $total = $vale->total === null
                ? ($precio === null ? null : round($litros * $precio, 2))
                : (float) $vale->total;
            return [
                'fecha' => $vale->fecha?->format('d/m/Y') ?? '-',
                'vale' => str_pad((string) $vale->numero_vale, 6, '0', STR_PAD_LEFT),
                'codigo' => $vale->vehiculo?->codigo ?? '-',
                'placa' => $vale->vehiculo?->placa ?? '-',
                'combustible' => $vale->tipoCombustible?->nombre ?? '-',
                'apertura' => $vale->aperturaProgramatica?->codigo ?? '-',
                'litros' => $litros,
                'precio' => $precio,
                'total' => $total,
                'numero_factura' => $vale->numero_factura ?? '',
                'kilometraje' => $vale->kilometraje,
                'detalle_asignacion' => $vale->detalle_asignacion ?? '',
            ];
        });

        $precios = $filas->pluck('precio')->filter(fn ($p) => $p !== null)->unique()->values();
        $vehiculoSeleccionado = !empty($f['vehiculo_id']) ? Vehiculo::find($f['vehiculo_id']) : null;
        $periodLabel = match ($periodicidad) {
            'anual' => (string) $anio,
            'personalizado' => $inicio || $fin
                ? (($inicio ? \Carbon\Carbon::parse($inicio)->format('d/m/Y') : 'Inicio abierto') . ' – ' . ($fin ? \Carbon\Carbon::parse($fin)->format('d/m/Y') : 'Sin fecha final'))
                : 'Todos los períodos',
            default => sprintf('%02d/%04d', $mes, $anio),
        };
        $tipoCombustible = !empty($f['tipo_combustible_id'])
            ? TipoCombustible::find($f['tipo_combustible_id'])
            : null;

        return [
            'filas' => $filas,
            'mes' => $mes,
            'anio' => $anio,
            'periodicidad' => $periodicidad,
            'periodLabel' => $periodLabel,
            'combustible' => $combustible,
            'combustibleLabel' => $tipoCombustible?->nombre ?? ($combustible === 'todos' ? 'Todos' : ucfirst($combustible)),
            'ambito' => $ambito,
            'ambitoLabel' => match ($ambito) {
                'gams' => 'GAMS',
                'policia' => 'Policía',
                default => 'Todos (GAMS y Policía)',
            },
            'vehiculo_id' => $f['vehiculo_id'] ?? null,
            'vehiculos' => Vehiculo::orderBy('codigo')->get(),
            'observaciones' => $f['observaciones'] ?? '',
            'totalVales' => $filas->count(),
            'totalVehiculos' => $vales->pluck('vehiculo_id')->filter()->unique()->count(),
            'totalLitros' => $filas->sum('litros'),
            'totalBs' => $filas->sum(fn ($fila) => $fila['total'] ?? 0),
            'sinPrecio' => $filas->whereNull('precio')->count(),
            'precioResumen' => $precios->count() === 1 && $filas->whereNull('precio')->isEmpty() ? $precios->first() : null,
            'vehiculoSeleccionado' => $vehiculoSeleccionado,
            'esReporteIndividual' => $vehiculoSeleccionado !== null,
        ];
    }

    public function index(Request $request)
    {
        return view('reportes.policia-vehiculos', $this->datos($request));
    }

    public function pdf(Request $request)
    {
        $datos = $this->datos($request);
        foreach ([
            'logoSacaba' => ['images/logo-sacaba.jpg', 'image/jpeg'],
            'logoChapare' => ['images/logo-chapare.png', 'image/png'],
        ] as $clave => [$ruta, $mime]) {
            $archivo = public_path($ruta);
            $datos[$clave] = is_file($archivo)
                ? 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($archivo))
                : null;
        }
        $nombre = $datos['esReporteIndividual'] ? 'policia_vehiculo_individual_' : 'policia_vehiculos_';
        return Pdf::loadView('reportes.pdf.policia-vehiculos', $datos)
            ->setPaper('letter', 'landscape')
            ->download($nombre . preg_replace('/[^0-9-]+/', '_', $datos['periodLabel']) . '.pdf');
    }

    public function excel(Request $request): StreamedResponse
    {
        $d = $this->datos($request);
        $individual = $d['esReporteIndividual'];
        $libro = new Spreadsheet();
        $hoja = $libro->getActiveSheet();
        $hoja->setTitle($individual ? 'Policia individual' : 'Policia vehiculos');
        $ultima = $individual ? 'J' : 'I';
        $hoja->getSheetView()->setShowGridLines(false);

        // Encabezado institucional, espacio reservado para ambos logotipos.
        foreach ([1, 2, 3, 4] as $fila) {
            $hoja->mergeCells('C' . $fila . ':' . ($individual ? 'H' : 'G') . $fila);
        }
        $hoja->setCellValue('C1', 'GOBIERNO AUTÓNOMO MUNICIPAL DE SACABA');
        $hoja->setCellValue('C2', 'SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN');
        $hoja->setCellValue('C3', 'DIRECCIÓN ADMINISTRATIVA');
        $tituloPeriodo = match ($d['periodicidad']) {
            'anual' => 'ANUAL',
            'personalizado' => 'POR RANGO',
            default => 'MENSUAL',
        };
        $hoja->setCellValue('C4', $individual
            ? 'REPORTE INDIVIDUAL DE CONSUMO DE COMBUSTIBLE · ' . strtoupper($d['ambitoLabel'])
            : 'REPORTE ' . $tituloPeriodo . ' DE CONSUMO DE COMBUSTIBLE POR VEHÍCULO · ' . strtoupper($d['ambitoLabel']));
        $hoja->mergeCells('A6:' . $ultima . '6');
        $hoja->setCellValue('A6', 'PERÍODO: ' . $d['periodLabel'] . '   |   COMBUSTIBLE: ' . $d['combustibleLabel'] . '   |   EMISIÓN: ' . now()->format('d/m/Y'));
        foreach ([1 => 22, 2 => 22, 3 => 22, 4 => 30, 5 => 10, 6 => 23] as $fila => $alto) {
            $hoja->getRowDimension($fila)->setRowHeight($alto);
        }
        $hoja->getStyle('C1:' . ($individual ? 'H4' : 'G4'))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hoja->getStyle('C1:' . ($individual ? 'H4' : 'G4'))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $hoja->getStyle('C1:' . ($individual ? 'H4' : 'G4'))->getFont()->setBold(true)->setSize(11);
        $hoja->getStyle('C4')->getAlignment()->setWrapText(true);
        $hoja->getStyle('A6')->getFont()->setBold(true)->setSize(10);

        // Inserta los logotipos reales cuando existen; no bloquea la exportación si faltan.
        foreach ([['images/logo-sacaba.jpg', 'A1', 72], ['images/logo-chapare.png', $individual ? 'I1' : 'H1', 68]] as [$ruta, $celda, $alto]) {
            $archivo = public_path($ruta);
            if (is_file($archivo)) {
                $imagen = new Drawing();
                $imagen->setName('Logotipo institucional');
                $imagen->setPath($archivo);
                $imagen->setHeight($alto);
                $imagen->setCoordinates($celda);
                $imagen->setWorksheet($hoja);
            }
        }

        if ($individual) {
            $v = $d['vehiculoSeleccionado'];
            $hoja->mergeCells('A8:J8');
            $hoja->setCellValue('A8', 'DATOS RELATIVOS AL VEHÍCULO');
            $hoja->fromArray(['TIPO', 'CÓD. ASIGNADO', 'INDUSTRIA', 'COLOR', 'PLACA', 'MARCA'], null, 'A9');
            $hoja->mergeCells('F9:J9');
            $hoja->fromArray([
                $v->tipo ?: '-', $v->codigo ?: '-', $v->industria ?: '-',
                $v->color ?: '-', $v->placa ?: '-', $v->marca ?: '-',
            ], null, 'A10');
            $hoja->mergeCells('F10:J10');
            $hoja->getStyle('A9:J9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('315B91');
            $hoja->getStyle('A9:J9')->getFont()->getColor()->setRGB('FFFFFF');
            $hoja->getStyle('A9:J10')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $hoja->mergeCells('A12:J12');
            $hoja->setCellValue('A12', 'DETALLE DE ASIGNACIÓN');
            $cabecera = 13;
            $datosInicio = 14;
            $columnas = ['FECHA', 'VALE', 'COMBUSTIBLE', 'CANTIDAD (LT)', 'PRECIO (LT)', 'TOTAL (Bs)', 'N.º DE FACTURA', 'KILOMETRAJE', 'DETALLE DE ASIGNACIÓN', 'APERTURA PROGRAM.'];
        } else {
            $hoja->mergeCells('A8:I8');
            $hoja->setCellValue('A8', 'CONSUMO DE COMBUSTIBLE POR VEHÍCULO');
            $cabecera = 9;
            $datosInicio = 10;
            $columnas = ['FECHA', 'VALE', 'CÓDIGO', 'PLACA', 'COMBUSTIBLE', 'CANTIDAD (LT)', 'PRECIO (LT)', 'TOTAL (Bs)', 'APERTURA PROGRAM.'];
        }

        $hoja->fromArray($columnas, null, 'A' . $cabecera);
        $hoja->getRowDimension($cabecera)->setRowHeight(42);
        $hoja->getStyle('A' . $cabecera . ':' . $ultima . $cabecera)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('315B91');
        $hoja->getStyle('A' . $cabecera . ':' . $ultima . $cabecera)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $hoja->getStyle('A' . $cabecera . ':' . $ultima . $cabecera)->getAlignment()->setWrapText(true);
        $hoja->getStyle('A' . $cabecera . ':' . $ultima . $cabecera)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $r = $datosInicio;
        foreach ($d['filas'] as $f) {
            // Orden explícito: evita desplazar las columnas al agregar campos al controlador.
            $valores = $individual
                ? [$f['fecha'], $f['vale'], $f['combustible'], $f['litros'], $f['precio'], $f['total'], $f['numero_factura'], $f['kilometraje'], $f['detalle_asignacion'], $f['apertura']]
                : [$f['fecha'], $f['vale'], $f['codigo'], $f['placa'], $f['combustible'], $f['litros'], $f['precio'], $f['total'], $f['apertura']];
            $hoja->fromArray($valores, null, 'A' . $r, true);
            $hoja->setCellValueExplicit('B' . $r, $f['vale'], DataType::TYPE_STRING);
            if ($individual && $f['numero_factura'] !== '') {
                $hoja->setCellValueExplicit('G' . $r, (string) $f['numero_factura'], DataType::TYPE_STRING);
            }
            $hoja->getRowDimension($r)->setRowHeight(25);
            $r++;
        }
        $colLitros = $individual ? 'D' : 'F';
        $colPrecio = $individual ? 'E' : 'G';
        $colTotal = $individual ? 'F' : 'H';
        $hoja->mergeCells('A' . $r . ':' . ($individual ? 'C' : 'E') . $r);
        $hoja->setCellValue('A' . $r, 'TOTAL (' . $d['totalVales'] . ' vales utilizados)');
        $hoja->setCellValue($colLitros . $r, $d['totalLitros']);
        $hoja->setCellValue($colPrecio . $r, $d['precioResumen'] ?? 'VARIABLE / SIN DATO');
        $hoja->setCellValue($colTotal . $r, $d['totalBs']);
        if ($individual) {
            $hoja->mergeCells('G' . $r . ':J' . $r);
        }
        $hoja->getStyle('A' . $r . ':' . $ultima . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F0F8');
        $hoja->getStyle('A' . $r . ':' . $ultima . $r)->getFont()->setBold(true);
        $hoja->getStyle('A' . $cabecera . ':' . $ultima . $r)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('333333');
        $hoja->getStyle($colLitros . $datosInicio . ':' . $colTotal . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $hoja->getStyle('A' . $cabecera . ':' . $ultima . $r)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $hoja->getStyle('A' . $datosInicio . ':' . $ultima . max($datosInicio, $r - 1))->getAlignment()->setWrapText(true);
        $hoja->getStyle('A8:' . $ultima . '8')->getFont()->setBold(true)->setSize(11);
        if ($individual) $hoja->getStyle('A12:J12')->getFont()->setBold(true)->setSize(11);

        $finImpresion = $r;
        if ($d['sinPrecio'] > 0) {
            $r += 2;
            $hoja->mergeCells('A' . $r . ':' . $ultima . $r);
            $hoja->setCellValue('A' . $r, 'ADVERTENCIA: ' . $d['sinPrecio'] . ' consumo(s) sin precio unitario histórico.');
            $hoja->getStyle('A' . $r)->getFont()->setBold(true);
            $finImpresion = $r;
        }
        if (trim($d['observaciones']) !== '') {
            $r += 2;
            $hoja->mergeCells('A' . $r . ':' . $ultima . $r);
            $hoja->setCellValue('A' . $r, 'OBSERVACIONES: ' . $d['observaciones']);
            $hoja->getStyle('A' . $r)->getAlignment()->setWrapText(true);
            $hoja->getRowDimension($r)->setRowHeight(65);
            $finImpresion = $r;
        }

        $anchos = $individual
            ? ['A'=>15,'B'=>14,'C'=>18,'D'=>18,'E'=>18,'F'=>19,'G'=>20,'H'=>18,'I'=>37,'J'=>24]
            : ['A'=>16,'B'=>14,'C'=>15,'D'=>18,'E'=>20,'F'=>18,'G'=>19,'H'=>20,'I'=>25];
        foreach ($anchos as $col => $ancho) $hoja->getColumnDimension($col)->setWidth($ancho);
        $hoja->freezePane('A' . $datosInicio);
        $hoja->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $hoja->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LETTER);
        $hoja->getPageSetup()->setFitToPage(true);
        $hoja->getPageSetup()->setFitToWidth(1);
        $hoja->getPageSetup()->setFitToHeight(0);
        $hoja->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($cabecera, $cabecera);
        $hoja->getPageSetup()->setPrintArea('A1:' . $ultima . $finImpresion);
        $hoja->getPageMargins()->setTop(0.35);
        $hoja->getPageMargins()->setBottom(0.35);
        $hoja->getPageMargins()->setLeft(0.25);
        $hoja->getPageMargins()->setRight(0.25);

        $nombre = ($individual ? 'policia_vehiculo_individual_' : 'policia_vehiculos_') . preg_replace('/[^0-9-]+/', '_', $d['periodLabel']) . '.xlsx';
        return response()->streamDownload(function () use ($libro) {
            (new Xlsx($libro))->save('php://output');
            $libro->disconnectWorksheets();
        }, $nombre, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
