<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte General de Consumo</title>

    <style>
        @page {
            margin: 10mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        /*
        |--------------------------------------------------------------------------
        | ENCABEZADO
        |--------------------------------------------------------------------------
        */

        .encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .encabezado td {
            vertical-align: middle;
            border: none;
        }

        .logo-izquierda {
            width: 20%;
            text-align: left;
        }

        .logo-centro {
            width: 60%;
            text-align: center;
        }

        .logo-derecha {
            width: 20%;
            text-align: right;
        }

        .logo-sacaba {
            max-width: 125px;
            max-height: 58px;
        }

        .logo-chapare {
            max-width: 70px;
            max-height: 65px;
        }

        .institucion {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.35;
        }

        .direccion {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }

        .titulo {
            font-size: 13px;
            font-weight: bold;
            margin-top: 6px;
            color: #174d32;
        }

        /*
        |--------------------------------------------------------------------------
        | FECHAS
        |--------------------------------------------------------------------------
        */

        .datos-reporte {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .datos-reporte td {
            border: none;
            font-size: 8.5px;
        }

        .periodo {
            text-align: left;
        }

        .fecha-impresion {
            text-align: right;
        }

        /*
        |--------------------------------------------------------------------------
        | TEXTO PERSONALIZADO
        |--------------------------------------------------------------------------
        */

        .texto-personalizado {
            font-size: 9px;
            line-height: 1.45;
            text-align: justify;
            margin-top: 7px;
            margin-bottom: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | INDICADORES
        |--------------------------------------------------------------------------
        */

        .indicadores {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin: 4px -5px 10px -5px;
        }

        .indicadores td {
            width: 25%;
            border: 1px solid #777;
            border-left: 4px solid #174d32;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .indicador-titulo {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #555;
            margin-bottom: 3px;
        }

        .indicador-valor {
            font-size: 12px;
            font-weight: bold;
            color: #174d32;
        }

        /*
        |--------------------------------------------------------------------------
        | TABLA
        |--------------------------------------------------------------------------
        */

        .titulo-seccion {
            font-size: 10px;
            font-weight: bold;
            color: #174d32;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #555;
            padding: 4px 3px;
            font-size: 7.5px;
        }

        .tabla th {
            background-color: #174d32;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
        }

        .tabla td {
            vertical-align: middle;
        }

        .tabla .unidad {
            text-align: left;
        }

        .tabla .numero {
            text-align: right;
        }

        .tabla .centro {
            text-align: center;
        }

        .tabla .total {
            background-color: #e7f4eb;
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | TEXTO FINAL
        |--------------------------------------------------------------------------
        */

        .texto-final {
            margin-top: 12px;
            font-size: 9px;
            line-height: 1.45;
            text-align: justify;
        }
    </style>
</head>

<body>

    {{-- ============================================================
         ENCABEZADO INSTITUCIONAL
    ============================================================ --}}

    <table class="encabezado">
        <tr>

            <td class="logo-izquierda">

                @if($logoSacaba)
                    <img
                        src="{{ $logoSacaba }}"
                        class="logo-sacaba"
                        alt="Sacaba"
                    >
                @endif

            </td>


            <td class="logo-centro">

                <div class="institucion">
                    SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN
                </div>

                <div class="direccion">
                    DIRECCIÓN ADMINISTRATIVA
                </div>

                <div class="titulo">
                    REPORTE GENERAL DE CONSUMO DE COMBUSTIBLE
                </div>

            </td>


            <td class="logo-derecha">

                @if($logoChapare)
                    <img
                        src="{{ $logoChapare }}"
                        class="logo-chapare"
                        alt="Provincia Chapare"
                    >
                @endif

            </td>

        </tr>
    </table>


    {{-- ============================================================
         PERÍODO Y FECHA
    ============================================================ --}}

    <table class="datos-reporte">

        <tr>

            <td class="periodo">

                <strong>PERÍODO:</strong>

                @if(request('fecha_inicio'))
                    {{ \Carbon\Carbon::parse(request('fecha_inicio'))->format('d/m/Y') }}
                @else
                    INICIO
                @endif

                AL

                @if(request('fecha_fin'))
                    {{ \Carbon\Carbon::parse(request('fecha_fin'))->format('d/m/Y') }}
                @else
                    ACTUALIDAD
                @endif

            </td>


            <td class="fecha-impresion">

                <strong>FECHA:</strong>
                {{ now()->format('d/m/Y') }}

            </td>

        </tr>

    </table>


    {{-- ============================================================
         TEXTO ESCRITO POR EL USUARIO
    ============================================================ --}}

    @if(!empty($textoSuperior))

        <div class="texto-personalizado">
            {!! nl2br(e($textoSuperior)) !!}
        </div>

    @endif


    {{-- ============================================================
         RESUMEN GENERAL
    ============================================================ --}}

    <table class="indicadores">

        <tr>

            <td>

                <div class="indicador-titulo">
                    Vales utilizados
                </div>

                <div class="indicador-valor">
                    {{ number_format($totalVales, 0, ',', '.') }}
                </div>

            </td>


            <td>

                <div class="indicador-titulo">
                    Vehículos
                </div>

                <div class="indicador-valor">
                    {{ number_format($totalVehiculos, 0, ',', '.') }}
                </div>

            </td>


            <td>

                <div class="indicador-titulo">
                    Combustible consumido
                </div>

                <div class="indicador-valor">
                    {{ number_format($totalLitros, 2, ',', '.') }} L
                </div>

            </td>


            <td>

                <div class="indicador-titulo">
                    Monto total
                </div>

                <div class="indicador-valor">
                    Bs {{ number_format($totalMonto, 2, ',', '.') }}
                </div>

            </td>

        </tr>

    </table>


    {{-- ============================================================
         CONSOLIDADO POR UNIDAD
    ============================================================ --}}

    <div class="titulo-seccion">
        CONSOLIDADO POR UNIDAD
    </div>


    <table class="tabla">

        <thead>

            <tr>

                <th style="width: 28%;">
                    UNIDAD
                </th>

                <th style="width: 8%;">
                    VEHÍCULOS
                </th>

                <th style="width: 7%;">
                    VALES
                </th>

                <th style="width: 10%;">
                    GASOLINA<br>(L)
                </th>

                <th style="width: 10%;">
                    DIÉSEL<br>(L)
                </th>

                <th style="width: 9%;">
                    OTROS<br>(L)
                </th>

                <th style="width: 12%;">
                    TOTAL<br>LITROS
                </th>

                <th style="width: 16%;">
                    MONTO TOTAL<br>(Bs)
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($resumenUnidades as $fila)

                <tr>

                    <td class="unidad">
                        {{ $fila->unidad }}
                    </td>

                    <td class="centro">
                        {{ $fila->vehiculos }}
                    </td>

                    <td class="centro">
                        {{ $fila->vales }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->gasolina, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->diesel, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->otros, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->litros, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($fila->total, 2, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        style="text-align:center; padding:15px;"
                    >
                        No existen registros para los filtros seleccionados.
                    </td>

                </tr>

            @endforelse


            @if($resumenUnidades->isNotEmpty())

                <tr class="total">

                    <td>
                        TOTAL GENERAL
                    </td>

                    <td class="centro">
                        {{ $totalVehiculos }}
                    </td>

                    <td class="centro">
                        {{ $totalVales }}
                    </td>

                    <td class="numero">
                        {{ number_format($totalGasolina, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($totalDiesel, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($totalOtros, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($totalLitros, 2, ',', '.') }}
                    </td>

                    <td class="numero">
                        {{ number_format($totalMonto, 2, ',', '.') }}
                    </td>

                </tr>

            @endif

        </tbody>

    </table>


    {{-- ============================================================
         TEXTO FINAL ESCRITO POR EL USUARIO
    ============================================================ --}}

    @if(!empty($textoInferior))

        <div class="texto-final">
            {!! nl2br(e($textoInferior)) !!}
        </div>

    @endif

</body>
</html>