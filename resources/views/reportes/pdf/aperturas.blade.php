<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        Reporte por Apertura Programática
    </title>

    <style>
        @page {
            size: letter landscape;
            margin: 0.8cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        .encabezado {
            position: relative;
            height: 3cm;
            border-bottom: 2px solid #174c2b;
            margin-bottom: 12px;
        }

        .logo-sacaba {
            position: absolute;
            left: 0;
            top: 0.35cm;
            width: 5cm;
            height: 2cm;
            object-fit: contain;
        }

        .logo-chapare {
            position: absolute;
            right: 0;
            top: 0.2cm;
            width: 2cm;
            height: 2.4cm;
            object-fit: contain;
        }

        .encabezado-centro {
            margin-left: 5.3cm;
            margin-right: 2.5cm;
            padding-top: 0.65cm;
            text-align: center;
        }

        .secretaria {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .direccion {
            margin-top: 5px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .fecha {
            text-align: right;
            margin-bottom: 12px;
        }

        .titulo {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .subtitulo {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .detalle {
            font-weight: bold;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #eaf7ee;
            color: #174c2b;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #777;
            padding: 6px 4px;
            font-size: 9px;
        }

        td {
            border: 1px solid #777;
            padding: 6px 4px;
            vertical-align: middle;
            font-size: 9px;
        }

        .centro {
            text-align: center;
        }

        .numero {
            text-align: right;
        }

        .total td {
            font-weight: bold;
            background: #f2f2f2;
        }

        .texto {
            margin-top: 20px;
            line-height: 1.6;
            text-align: justify;
        }

        .nota {
            margin-top: 12px;
            line-height: 1.6;
            text-align: justify;
        }

        .cierre {
            margin-top: 22px;
        }
    </style>
</head>

<body>

    <div class="encabezado">
@if($logoSacaba)
    <img
        src="{{ $logoSacaba }}"
        class="logo-sacaba">
@endif

@if($logoChapare)
    <img
        src="{{ $logoChapare }}"
        class="logo-chapare">
@endif

        <div class="encabezado-centro">

            <div class="secretaria">
                Secretaría Municipal de Finanzas y Administración
            </div>

            <div class="direccion">
                Dirección Administrativa
            </div>

        </div>

    </div>


    <div class="fecha">
        Sacaba,
        {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}
    </div>


    <div class="titulo">
        Ref.: Reporte asignación de combustible por apertura programática
    </div>

    <div class="subtitulo">
        Detalle de asignación de combustible
    </div>


    <div class="detalle">
        Detalle de asignación:
    </div>


    <table>

        <thead>
            <tr>

                <th style="width:15%">
                    APERTURA PROGRAMÁTICA
                </th>

                <th style="width:8%">
                    FF
                </th>

                <th style="width:8%">
                    OF
                </th>

                <th style="width:31%">
                    NOMBRE DE LA UNIDAD O PROYECTO
                </th>

                <th style="width:13%">
                    CANTIDAD LITROS
                </th>

                <th style="width:12%">
                    PRECIO UNITARIO<br>
                    (Bs./Litro)
                </th>

                <th style="width:13%">
                    PRECIO TOTAL<br>
                    (Bs.)
                </th>

            </tr>
        </thead>

        <tbody>

            @forelse($reporte as $fila)

                <tr>

                    <td class="centro">
                        {{ $fila->apertura }}
                    </td>

                    <td class="centro">
                        {{ $fila->fuente }}
                    </td>

                    <td class="centro">
                        {{ $fila->organismo }}
                    </td>

                    <td>
                        {{ $fila->unidad }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->litros, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->precio_unitario, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->total, 2, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="centro">
                        No existen registros para los filtros seleccionados.
                    </td>
                </tr>

            @endforelse


            @if($reporte->count() > 0)

                <tr class="total">

                    <td colspan="4" class="numero">
                        TOTAL
                    </td>

                    <td class="numero">
                        {{ number_format($totalLitros, 2, ',', '.') }}
                    </td>

                    {{-- PRECIO UNITARIO NO SE SUMA --}}
                    <td></td>

                    <td class="numero">
                        {{ number_format($totalMonto, 2, ',', '.') }}
                    </td>

                </tr>

            @endif

        </tbody>

    </table>


    @if($textoInforme !== '')
        <div class="texto">
            {{ $textoInforme }}
        </div>
    @endif


    @if($notaReporte !== '')
        <div class="nota">
            <strong>NOTA:</strong>
            {{ $notaReporte }}
        </div>
    @endif


    @if($cierreReporte !== '')
        <div class="cierre">
            {{ $cierreReporte }}
        </div>
    @endif

</body>
</html>