<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18px 20px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #222; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #aaa; padding: 3px; vertical-align: middle; }
        th { background: #e8f5ec; }
        .num { text-align: right; white-space: nowrap; }
        .header td { border: 0; }
        .titulo { text-align: center; font-weight: bold; font-size: 11px; }
        .obs { margin-top: 16px; white-space: pre-wrap; }
    </style>
</head>
<body>
<table class="header">
    <tr>
        <td style="width:20%">
            @if($logoSacaba)<img src="{{ $logoSacaba }}" style="max-height:55px;max-width:110px">@endif
        </td>
        <td style="width:60%;text-align:center">
            <b>SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN<br>DIRECCIÓN ADMINISTRATIVA</b>
        </td>
        <td style="width:20%;text-align:right">
            @if($logoChapare)<img src="{{ $logoChapare }}" style="max-height:55px;max-width:80px">@endif
        </td>
    </tr>
</table>
<p class="titulo">REPORTE DE CONSUMO DE COMBUSTIBLE POR VEHÍCULO</p>
<p style="text-align:center">
    Período: {{ $etiquetas['periodo'] }} · Ámbito: {{ $etiquetas['ambito'] }} ·
    Combustible: {{ $etiquetas['combustible'] }} · Emisión: {{ now()->format('d/m/Y') }}
</p>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>N.º vale</th>
            <th>N.º</th>
            <th>Combustible</th>
            <th>Código</th>
            <th>Placa</th>
            <th>Parte diario</th>
            <th>Tipo de vehículo</th>
            <th>Apertura</th>
            <th>FF</th>
            <th>OF</th>
            <th>Litros</th>
            <th>P.U. Bs/L</th>
            <th>Total Bs</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reporte as $indice => $fila)
            <tr>
                <td>{{ $fila->fecha?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ str_pad((string) $fila->numero_vale, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $indice + 1 }}</td>
                <td>{{ $fila->combustible }}</td>
                <td>{{ $fila->codigo }}</td>
                <td>{{ $fila->placa }}</td>
                <td>{{ $fila->parte_diario }}</td>
                <td>{{ $fila->tipo_vehiculo }}</td>
                <td>{{ $fila->apertura }}</td>
                <td>{{ $fila->fuente }}</td>
                <td>{{ $fila->organismo }}</td>
                <td class="num">{{ number_format($fila->litros, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($fila->precio_unitario, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($fila->total, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="11">TOTAL</th>
            <th class="num">{{ number_format($totalLitros, 2, ',', '.') }}</th>
            <th></th>
            <th class="num">{{ number_format($totalMonto, 2, ',', '.') }}</th>
        </tr>
    </tbody>
</table>
@if(trim($observaciones) !== '')
    <div class="obs"><b>OBSERVACIONES:</b><p>{{ $observaciones }}</p></div>
@endif
</body>
</html>
