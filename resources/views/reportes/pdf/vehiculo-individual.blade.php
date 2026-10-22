
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Reporte Individual por Vehículo</title>

    <style>

        @page {
            size: letter landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #000;
        }

        /* ==========================================
           ENCABEZADO
        ========================================== */

        .encabezado {
            width: 100%;
            height: 70px;
            margin-bottom: 3px;
        }

        .encabezado td {
            border: none;
            vertical-align: middle;
        }

        .logo-izquierdo {
            width: 22%;
            text-align: left;
        }

        .institucion {
            width: 56%;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            line-height: 1.5;
        }

        .logo-derecho {
            width: 22%;
            text-align: right;
        }

        .logo-sacaba {
            max-width: 155px;
            max-height: 60px;
        }

        .logo-chapare {
            max-width: 65px;
            max-height: 60px;
        }

        .fecha {
            text-align: right;
            font-size: 8px;
            margin-bottom: 5px;
        }

        .referencia {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* ==========================================
           TEXTOS
        ========================================== */

        .texto-reporte {
            width: 100%;
            text-align: justify;
            line-height: 1.5;
            font-size: 8px;
            margin: 5px 0 8px 0;
            white-space: pre-line;
        }

        /* ==========================================
           TÍTULOS
        ========================================== */

        .titulo-seccion {
            background: #e7f4eb;
            border-left: 4px solid #08712f;
            padding: 4px 6px;
            margin: 6px 0 3px 0;
            font-size: 9px;
            font-weight: bold;
            color: #075326;
        }

        /* ==========================================
           DATOS DEL VEHÍCULO
        ========================================== */

        .tabla-vehiculo {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 7px;
        }

        .tabla-vehiculo th {
            background: #08712f;
            color: #ffffff;
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            font-weight: bold;
        }

        .tabla-vehiculo td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            font-weight: bold;
        }

        /* ==========================================
           TABLA DETALLE
        ========================================== */

        .tabla-detalle {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7px;
        }

        .tabla-detalle th {
            background: #08712f;
            color: #ffffff;
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            line-height: 1.2;
        }

        .tabla-detalle td {
            border: 1px solid #000;
            padding: 4px 2px;
            vertical-align: middle;
        }

        .centro {
            text-align: center;
        }

        .derecha {
            text-align: right;
        }

        .total-label {
            background: #08712f;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
        }

        .total-dato {
            background: #e7f4eb;
            font-weight: bold;
        }

        .firma {
            margin-top: 10px;
            font-size: 8px;
        }

    </style>

</head>


<body>

    {{-- ======================================================
         ENCABEZADO
    ======================================================= --}}

    <table class="encabezado">

        <tr>

            <td class="logo-izquierdo">

                @if($logoSacaba)

                    <img
                        src="{{ $logoSacaba }}"
                        class="logo-sacaba"
                        alt="Logo Sacaba">

                @endif

            </td>


            <td class="institucion">

                SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION

                <br>

                DIRECCION ADMINISTRATIVA

            </td>


            <td class="logo-derecho">

                @if($logoChapare)

                    <img
                        src="{{ $logoChapare }}"
                        class="logo-chapare"
                        alt="Logo Chapare">

                @endif

            </td>

        </tr>

    </table>


    {{-- FECHA --}}

    <div class="fecha">

        Sacaba,
        {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}

    </div>


    {{-- REFERENCIA --}}

    <div class="referencia">

        REF.: REPORTE ASIGNACIÓN DE COMBUSTIBLE

    </div>


    {{-- ======================================================
         TEXTO SUPERIOR
    ======================================================= --}}

    @if($textoSuperior !== '')

        <div class="texto-reporte">
            {{ $textoSuperior }}
        </div>

    @endif


    {{-- ======================================================
         DATOS DEL VEHÍCULO
    ======================================================= --}}

    <div class="titulo-seccion">

        DATOS RELATIVOS AL VEHÍCULO

    </div>


    <table class="tabla-vehiculo">

        <thead>

            <tr>

                <th>
                    TIPO
                </th>

                <th>
                    CÓD. ASIGNADO
                </th>

                <th>
                    INDUSTRIA
                </th>

                <th>
                    COLOR
                </th>

                <th>
                    PLACA
                </th>

                <th>
                    MARCA
                </th>

        
            </tr>

        </thead>


        <tbody>

            <tr>

                <td>
                    {{ $vehiculo->tipo ?: '-' }}
                </td>

                <td>
                    {{ $vehiculo->codigo ?: '-' }}
                </td>

                <td>
                    {{ $vehiculo->industria ?: '-' }}
                </td>

                <td>
                    {{ $vehiculo->color ?: '-' }}
                </td>

                <td>
                    {{ $vehiculo->placa ?: '-' }}
                </td>

                <td>
                    {{ $vehiculo->marca ?: '-' }}
                </td>

            
            </tr>

        </tbody>

    </table>


    {{-- ======================================================
         DETALLE
    ======================================================= --}}

    <div class="titulo-seccion">

        DETALLE DE ASIGNACIÓN:

    </div>


    <table class="tabla-detalle">

        <thead>

            <tr>

                <th style="width: 9%;">
                    FECHA
                </th>

                <th style="width: 8%;">
                    VALE
                </th>

                <th style="width: 10%;">
                    CANTIDAD<br>
                    (LT)
                </th>

                <th style="width: 9%;">
                    PRECIO<br>
                    (LT)
                </th>

                <th style="width: 10%;">
                    TOTAL<br>
                    (Bs)
                </th>

                <th style="width: 11%;">
                    N.º FACTURA
                </th>

                <th style="width: 11%;">
                    KILOMETRAJE
                </th>

                <th style="width: 21%;">
                    DETALLE DE ASIGNACIÓN
                </th>

                <th style="width: 11%;">
                    APERTURA<br>
                    PROGRAM.
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($vales as $vale)

                <tr>

                    {{-- FECHA --}}

                    <td class="centro">

                        {{ $vale->fecha
                            ? $vale->fecha->format('d/m/Y')
                            : '-' }}

                    </td>


                    {{-- VALE --}}

                    <td class="centro">

                        {{ sprintf(
                            '%06d',
                            (int) $vale->numero_vale
                        ) }}

                    </td>


                    {{-- LITROS --}}

                    <td class="derecha">

                        {{ number_format(
                            (float) $vale->cantidad_litros,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- PRECIO --}}

                    <td class="derecha">

                        {{ number_format(
                            (float) $vale->precio_unitario,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- TOTAL --}}

                    <td class="derecha">

                        {{ number_format(
                            (float) $vale->total,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- FACTURA --}}

                    <td class="centro">

                        {{ $vale->numero_factura ?: '-' }}

                    </td>


                    {{-- KILOMETRAJE --}}

                    <td class="derecha">

                        @if($vale->kilometraje !== null)

                            {{ number_format(
                                (int) $vale->kilometraje,
                                0,
                                ',',
                                '.'
                            ) }}

                        @else

                            -

                        @endif

                    </td>


                    {{-- DETALLE AUTOMÁTICO --}}

                    <td class="centro">

                        ASIGNADO A
                        {{ mb_strtoupper($vehiculo->tipo ?: 'VEHÍCULO') }}

                        @if($vehiculo->unidad)

                            DE
                            {{ mb_strtoupper(
                                $vehiculo->unidad->nombre ?? ''
                            ) }}

                        @endif

                    </td>


                    {{-- APERTURA --}}

                    <td class="centro">

                        {{ $vale->aperturaProgramatica->codigo ?? '-' }}

                    </td>

                </tr>


            @empty

                <tr>

                    <td colspan="9"
                        class="centro">

                        No existen registros de combustible
                        para este vehículo en el período seleccionado.

                    </td>

                </tr>

            @endforelse


            {{-- TOTAL --}}

            @if($vales->isNotEmpty())

                <tr>

                    <td colspan="2"
                        class="total-label">

                        TOTAL

                    </td>


                    <td class="derecha total-dato">

                        {{ number_format(
                            $totalLitros,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    {{-- PRECIO UNITARIO NO SE SUMA --}}

                    <td class="total-dato"></td>


                    <td class="derecha total-dato">

                        {{ number_format(
                            $totalMonto,
                            2,
                            ',',
                            '.'
                        ) }}

                    </td>


                    <td colspan="4"
                        class="total-dato">
                    </td>

                </tr>

            @endif

        </tbody>

    </table>


    {{-- ======================================================
         TEXTO INFERIOR
    ======================================================= --}}

    @if($textoInferior !== '')

        <div class="texto-reporte"
             style="margin-top: 10px;">

            {{ $textoInferior }}

        </div>

    @endif


    <div class="firma">

        Atentamente.

    </div>

</body>

</html>
