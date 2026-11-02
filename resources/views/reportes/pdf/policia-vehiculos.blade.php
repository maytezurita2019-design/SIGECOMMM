
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
@page { margin: 18px 20px; }
body { font-family: DejaVu Sans, sans-serif; font-size: 7.5px; color: #222; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #333; padding: 5px 4px; vertical-align: middle; }
.num { text-align: right; white-space: nowrap; }
.header td { border: 0; }
.titulo { text-align: center; font-weight: bold; font-size: 11px; margin: 15px 0 8px; }
.subtitulo { text-align: center; margin-bottom: 15px; }
.seccion { font-size: 10px; font-weight: bold; margin: 15px 0 7px; color: #172f4e; }
.datos-vehiculo th, .detalle thead th { background: #315b91; color: #fff; text-align: center; font-weight: bold; }
.datos-vehiculo td { text-align: center; }
.detalle td { overflow-wrap: break-word; }
.detalle .totales th { background: #e8f0f8; color: #182a3b; font-weight: bold; }
.obs { margin-top: 18px; white-space: pre-wrap; }
tr { page-break-inside: avoid; }
</style>
</head>
<body>
<table class="header"><tr>
    <td style="width:20%;text-align:left">@if($logoSacaba)<img src="{{ $logoSacaba }}" style="max-height:55px;max-width:110px">@endif</td>
    <td style="width:60%;text-align:center"><b>GOBIERNO AUTÓNOMO MUNICIPAL DE SACABA<br>SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN<br>DIRECCIÓN ADMINISTRATIVA</b></td>
    <td style="width:20%;text-align:right">@if($logoChapare)<img src="{{ $logoChapare }}" style="max-height:55px;max-width:80px">@endif</td>
</tr></table>

<p class="titulo">{{ !empty($esReporteIndividual) ? 'REPORTE INDIVIDUAL DE CONSUMO · ' . mb_strtoupper($ambitoLabel) : 'REPORTE DE CONSUMO POR VEHÍCULO · ' . mb_strtoupper($ambitoLabel) }}</p>
<p class="subtitulo">Período: {{ $periodLabel }} · Combustible: {{ $combustibleLabel }} · Fecha de emisión: {{ now()->format('d/m/Y') }}</p>

@if(!empty($esReporteIndividual) && !empty($vehiculoSeleccionado))
<div class="seccion">DATOS RELATIVOS AL VEHÍCULO</div>
<table class="datos-vehiculo">
    <thead><tr><th>TIPO</th><th>CÓD. ASIGNADO</th><th>INDUSTRIA</th><th>COLOR</th><th>PLACA</th><th>MARCA</th></tr></thead>
    <tbody><tr>
        <td>{{ $vehiculoSeleccionado->tipo ?: '-' }}</td>
        <td>{{ $vehiculoSeleccionado->codigo ?: '-' }}</td>
        <td>{{ $vehiculoSeleccionado->industria ?: '-' }}</td>
        <td>{{ $vehiculoSeleccionado->color ?: '-' }}</td>
        <td>{{ $vehiculoSeleccionado->placa ?: '-' }}</td>
        <td>{{ $vehiculoSeleccionado->marca ?: '-' }}</td>
    </tr></tbody>
</table>
<div class="seccion">DETALLE DE ASIGNACIÓN</div>
@else
<div class="seccion">CONSUMO DE COMBUSTIBLE POR VEHÍCULO</div>
@endif

<table class="detalle">
    <thead><tr>
        <th>FECHA</th><th>VALE</th>
        @if(empty($esReporteIndividual))<th>CÓDIGO</th><th>PLACA</th>@endif
        <th>COMBUSTIBLE</th><th>CANTIDAD (LT)</th><th>PRECIO (LT)</th><th>TOTAL (Bs)</th>
        @if(!empty($esReporteIndividual))<th>N.º DE FACTURA</th><th>KILOMETRAJE</th><th>DETALLE DE ASIGNACIÓN</th>@endif
        <th>APERTURA PROGRAM.</th>
    </tr></thead>
    <tbody>
    @forelse($filas as $f)
        <tr>
            <td>{{ $f['fecha'] }}</td><td>{{ $f['vale'] }}</td>
            @if(empty($esReporteIndividual))<td>{{ $f['codigo'] }}</td><td>{{ $f['placa'] }}</td>@endif
            <td>{{ $f['combustible'] }}</td>
            <td class="num">{{ number_format($f['litros'], 2, ',', '.') }}</td>
            <td class="num">{{ $f['precio'] === null ? 'SIN DATO' : number_format($f['precio'], 2, ',', '.') }}</td>
            <td class="num">{{ $f['total'] === null ? 'SIN DATO' : number_format($f['total'], 2, ',', '.') }}</td>
            @if(!empty($esReporteIndividual))
                <td>{{ $f['numero_factura'] !== '' ? $f['numero_factura'] : '-' }}</td>
                <td>{{ $f['kilometraje'] !== null ? $f['kilometraje'] : '-' }}</td>
                <td>{{ $f['detalle_asignacion'] !== '' ? $f['detalle_asignacion'] : '-' }}</td>
            @endif
            <td>{{ $f['apertura'] }}</td>
        </tr>
    @empty
        <tr><td colspan="{{ !empty($esReporteIndividual) ? 10 : 9 }}" style="text-align:center">No se encontraron consumos para los filtros seleccionados.</td></tr>
    @endforelse
        <tr class="totales">
            <th colspan="{{ !empty($esReporteIndividual) ? 3 : 5 }}" style="text-align:left">TOTAL ({{ $totalVales }} vales utilizados)</th>
            <th class="num">{{ number_format($totalLitros, 2, ',', '.') }}</th>
            <th class="num">{{ $precioResumen === null ? 'VARIABLE / SIN DATO' : number_format($precioResumen, 2, ',', '.') }}</th>
            <th class="num">{{ number_format($totalBs, 2, ',', '.') }}</th>
            <th colspan="{{ !empty($esReporteIndividual) ? 4 : 1 }}"></th>
        </tr>
    </tbody>
</table>
@if($sinPrecio > 0)<p><b>ADVERTENCIA:</b> {{ $sinPrecio }} consumo(s) carecen de precio unitario histórico.</p>@endif
@if(trim($observaciones) !== '')<div class="obs"><b>OBSERVACIONES:</b><p>{{ $observaciones }}</p></div>@endif
</body>
</html>
