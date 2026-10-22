<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte por Vehículo</title>

    <style>
        @page {
            margin: 18px 22px 22px 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #000000;
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
            margin-bottom: 8px;
        }

        .encabezado td {
            border: none;
            vertical-align: middle;
        }

        .logo-izquierdo {
            width: 20%;
            text-align: left;
        }

        .logo-centro {
            width: 60%;
            text-align: center;
        }

        .logo-derecho {
            width: 20%;
            text-align: right;
        }

        .logo-sacaba {
            max-width: 125px;
            max-height: 55px;
        }

        .logo-chapare {
            max-width: 60px;
            max-height: 60px;
        }

        .institucion {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            line-height: 1.4;
        }

        /*
        |--------------------------------------------------------------------------
        | FECHA
        |--------------------------------------------------------------------------
        */

        .fecha {
            width: 100%;
            text-align: right;
            font-size: 8px;
            margin-bottom: 8px;
        }

        /*
        |--------------------------------------------------------------------------
        | TÍTULOS
        |--------------------------------------------------------------------------
        */

        .titulo {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 5px;
        }

        .subtitulo {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        /*
        |--------------------------------------------------------------------------
        | TABLA
        |--------------------------------------------------------------------------
        */

        .tabla {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tabla th {
            border: 1px solid #000000;
            background-color: #eeeeee;
            font-size: 6.5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
        }

        .tabla td {
            border: 1px solid #000000;
            font-size: 6.5px;
            padding: 4px 2px;
            vertical-align: middle;
        }

        .centrado {
            text-align: center;
        }

        .derecha {
            text-align: right;
        }

        .negrita {
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | ANCHOS
        |--------------------------------------------------------------------------
        */

        .col-numero {
            width: 3%;
        }

        .col-codigo {
            width: 9%;
        }

        .col-placa {
            width: 8%;
        }

        .col-parte {
            width: 12%;
        }

        .col-tipo {
            width: 12%;
        }

        .col-ap {
            width: 12%;
        }

        .col-ff {
            width: 5%;
        }

        .col-of {
            width: 5%;
        }

        .col-litros {
            width: 10%;
        }

        .col-precio {
            width: 11%;
        }

        .col-total {
            width: 13%;
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        .fila-total td {
            background-color: #eeeeee;
            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | PARTE DIARIO
        |--------------------------------------------------------------------------
        */

        .parte-si {
            font-weight: bold;
        }

        .parte-no {
            font-weight: bold;
        }

        .sin-registro {
            font-size: 6px;
        }

        /*
        |--------------------------------------------------------------------------
        | PIE
        |--------------------------------------------------------------------------
        */

        .pie {
            margin-top: 15px;
            font-size: 7px;
        }
    </style>
</head>

<body>

    {{-- ============================================================
         ENCABEZADO
    ============================================================ --}}

    <table class="encabezado">

        <tr>

            <td class="logo-izquierdo">

                @if($logoSacaba)

                    <img
                        src="{{ $logoSacaba }}"
                        class="logo-sacaba">

                @endif

            </td>


            <td class="logo-centro">

                <div class="institucion">

                    SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION
                    <br>

                    DIRECCION ADMINISTRATIVA

                </div>

            </td>


            <td class="logo-derecho">

                @if($logoChapare)

                    <img
                        src="{{ $logoChapare }}"
                        class="logo-chapare">

                @endif

            </td>

        </tr>

    </table>


    {{-- ============================================================
         FECHA
    ============================================================ --}}

    <div class="fecha">

        Sacaba,
        {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}

    </div>


    {{-- ============================================================
         TÍTULO
    ============================================================ --}}

    <div class="titulo">

        REF.: REPORTE ASIGNACIÓN DE COMBUSTIBLE POR VEHÍCULO

    </div>


    <div class="subtitulo">

        DETALLE DE ASIGNACIÓN DE COMBUSTIBLE

    </div>


    {{-- ============================================================
         TABLA
    ============================================================ --}}

    <table class="tabla">

        <thead>

            <tr>

                <th class="col-numero">
                    N°
                </th>

                <th class="col-codigo">
                    CODIGO DE<br>
                    VEHICULO
                </th>

                <th class="col-placa">
                    PLACA
                </th>

                <th class="col-parte">
                    PRESENTACION<br>
                    PARTE DIARIO
                </th>

                <th class="col-tipo">
                    TIPO DE<br>
                    VEHICULO
                </th>

                <th class="col-ap">
                    AP.<br>
                    PROGR.
                </th>

                <th class="col-ff">
                    FF
                </th>

                <th class="col-of">
                    OF
                </th>

                <th class="col-litros">
                    CANTIDAD<br>
                    LITROS
                </th>

                <th class="col-precio">
                    P. U.<br>
                    (BS/LITRO)
                </th>

                <th class="col-total">
                    PRECIO TOTAL<br>
                    (BS)
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($reporte as $indice => $fila)

                <tr>

                    {{-- N° --}}
                    <td class="centrado">

                        {{ $indice + 1 }}

                    </td>


                    {{-- CÓDIGO --}}
                    <td class="centrado">

                        {{ $fila->codigo }}

                    </td>


                    {{-- PLACA --}}
                    <td class="centrado negrita">

                        {{ $fila->placa }}

                    </td>


                    {{-- PARTE DIARIO --}}
                    <td class="centrado">

                        @if($fila->parte_diario === 'SÍ')

                            <span class="parte-si">
                                SÍ
                            </span>

                        @elseif($fila->parte_diario === 'NO')

                            <span class="parte-no">
                                NO
                            </span>

                        @else

                            <span class="sin-registro">
                                SIN REGISTRO
                            </span>

                        @endif

                    </td>


                    {{-- TIPO VEHÍCULO --}}
                    <td>

                        {{ $fila->tipo_vehiculo }}

                    </td>


                    {{-- AP --}}
                    <td class="centrado">

                        {{ $fila->apertura }}

                    </td>


                    {{-- FF --}}
                    <td class="centrado">

                        {{ $fila->fuente }}

                    </td>


                    {{-- OF --}}
                    <td class="centrado">

                        {{ $fila->organismo }}

                    </td>


                    {{-- LITROS --}}
                    <td class="derecha">

                        {{ number_format(
                            $fila->litros,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- PRECIO UNITARIO --}}
                    <td class="derecha">

                        {{ number_format(
                            $fila->precio_unitario,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- TOTAL --}}
                    <td class="derecha negrita">

                        {{ number_format(
                            $fila->total,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="11"
                        class="centrado">

                        No existen registros para los filtros seleccionados.

                    </td>

                </tr>

            @endforelse


            {{-- ====================================================
                 TOTAL
            ==================================================== --}}

            @if($reporte->isNotEmpty())

                <tr class="fila-total">

                    <td colspan="8"
                        class="derecha">

                        TOTAL

                    </td>


                    <td class="derecha">

                        {{ number_format(
                            $totalLitros,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- P.U. NO SE SUMA --}}
                    <td></td>


                    <td class="derecha">

                        {{ number_format(
                            $totalMonto,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>

                </tr>

            @endif

        </tbody>

    </table>


    {{-- ============================================================
         PIE
    ============================================================ --}}

    <div class="pie">

        <strong>Nota:</strong>
        Reporte generado por el Sistema de Gestión y Control de Combustible - SIGECOM.

    </div>

</body>
</html>