```blade
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Vale {{ $valeCombustible->numero_vale }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, Helvetica, sans-serif;
            color: #444;
            background: #eeeeee;
        }

        /* =====================================================
           BOTONES
        ===================================================== */

        .acciones {
            text-align: center;
            margin-bottom: 18px;
        }

        .acciones button,
        .acciones a {
            display: inline-block;
            padding: 10px 18px;
            margin: 0 4px;
            border-radius: 5px;
            border: 1px solid #08712f;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .acciones button {
            background: #08712f;
            color: #ffffff;
        }

        .acciones button:hover {
            background: #065b27;
        }

        .acciones a {
            background: #ffffff;
            color: #08712f;
        }

        /* =====================================================
           VALE
        ===================================================== */

        .vale {
            width: 100%;
            max-width: 1050px;
            min-height: 610px;
            margin: 0 auto;
            background: #ffffff;
            border: 1.5px solid #555;
        }

        /* =====================================================
           ENCABEZADO
        ===================================================== */

        .encabezado {
            display: grid;
            grid-template-columns: 31% 39% 30%;
            min-height: 115px;
            border-bottom: 1px solid #777;
        }

        .encabezado > div {
            padding: 15px;
        }

        /* LOGO */

        .institucion {
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #aaa;
        }

        .logo-sacaba {
            display: block;
            width: auto;
            height: auto;
            max-width: 230px;
            max-height: 75px;
            object-fit: contain;
        }

        /* SECRETARÍA */

        .secretaria {
            border-right: 1px solid #aaa;
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            line-height: 1.3;
        }

        .numero {
            margin-top: 20px;
            font-size: 18px;
            letter-spacing: 3px;
        }

        /* DIRECCIÓN */

        .direccion {
            position: relative;
            text-align: center;
            font-size: 14px;
        }

        .hora {
            position: absolute;
            right: 15px;
            bottom: 10px;
            font-size: 13px;
        }

        /* =====================================================
           TÍTULO
        ===================================================== */

        .titulo {
            padding: 8px 15px;
            background: #08712f;
            color: #ffffff;
            border-bottom: 1px solid #065b27;
            text-align: center;
            font-size: 27px;
            font-weight: bold;
            letter-spacing: .5px;
        }

        /* =====================================================
           CONTENIDO
        ===================================================== */

        .contenido {
            padding: 18px 25px 10px;
            font-size: 17px;
        }

        .fila {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 20px;
        }

        .grupo {
            display: flex;
            align-items: flex-end;
            flex: 1;
            min-width: 0;
        }

        .etiqueta {
            white-space: nowrap;
            margin-right: 8px;
        }

        .linea {
            flex: 1;
            min-height: 25px;
            padding: 0 6px 3px;
            border-bottom: 1px dotted #777;
            font-weight: 500;
        }

        .cantidad {
            flex: 1.05;
        }

        .precio {
            flex: 1.05;
        }

        .total {
            flex: 1;
        }

        .placa,
        .codigo,
        .kilometraje {
            flex: 1;
        }

        /* =====================================================
           FECHA
        ===================================================== */

        .fecha {
            justify-content: center;
            margin-top: 28px;
        }

        .fecha-texto {
            min-width: 420px;
            text-align: center;
            padding-bottom: 5px;
            border-bottom: 1px dotted #777;
        }

        /* =====================================================
           FIRMAS
        ===================================================== */

        .firmas {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 35px;
            padding: 55px 25px 20px;
            text-align: center;
        }

        .firma {
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .firma-linea {
            border-top: 1px dotted #555;
            margin-bottom: 6px;
        }

        .firma-titulo {
            font-size: 14px;
        }

        .firma-subtitulo {
            margin-top: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        /* =====================================================
           IMPRESIÓN
        ===================================================== */

        @page {
            size: 215.9mm 139.7mm;
            margin: 5mm;
        }

        @media print {

            html,
            body {
                width: 215.9mm;
                height: 139.7mm;
                margin: 0;
                padding: 0;
                background: #ffffff;
            }

            .acciones {
                display: none !important;
            }

            .vale {
                width: 205.9mm;
                height: 129.7mm;
                min-height: 0;
                max-width: none;
                margin: 0;
                overflow: hidden;
                border: 1px solid #555;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .titulo {
                background: #08712f !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

</head>

<body>

    {{-- =====================================================
         ACCIONES
    ===================================================== --}}

    <div class="acciones">

        <button
            type="button"
            onclick="window.print()"
        >
            Imprimir / Guardar PDF
        </button>

        <a href="{{ route('vales-combustible.show', $valeCombustible) }}">
            Volver al vale
        </a>

    </div>


    {{-- =====================================================
         VALE
    ===================================================== --}}

    <main class="vale">

        {{-- =================================================
             ENCABEZADO
        ================================================= --}}

        <div class="encabezado">

            {{-- LOGO SACABA --}}
            <div class="institucion">

                <img
                    src="{{ asset('images/logo-sacaba.jpg') }}"
                    alt="Gobierno Autónomo Municipal de Sacaba"
                    class="logo-sacaba"
                >

            </div>


            {{-- SECRETARÍA --}}
            <div class="secretaria">

                SECRETARÍA MUNICIPAL<br>
                DE FINANZAS Y ADMINISTRACIÓN

                <div class="numero">
                    N.º {{ $valeCombustible->numero_vale }}
                </div>

            </div>


            {{-- DIRECCIÓN --}}
            <div class="direccion">

                DIRECCIÓN ADMINISTRATIVA

                <div class="hora">
                    Hora: __________________
                </div>

            </div>

        </div>


        {{-- =================================================
             TÍTULO
        ================================================= --}}

        <div class="titulo">

            VALE DE COMBUSTIBLE

            @if($valeCombustible->tipoCombustible?->nombre)

                ({{ strtoupper($valeCombustible->tipoCombustible->nombre) }})

            @else

                (________________)

            @endif

        </div>


        {{-- =================================================
             DATOS DEL VALE
        ================================================= --}}

        <div class="contenido">

            {{-- ESTACIÓN DE SERVICIO --}}
            <div class="fila">

                <div class="grupo">

                    <span class="etiqueta">
                        Estación de servicio:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->estacionServicio?->nombre ?? '' }}
                    </span>

                </div>

            </div>


            {{-- CANTIDAD / PRECIO / TOTAL --}}
            <div class="fila">

                <div class="grupo cantidad">

                    <span class="etiqueta">
                        Cantidad:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->cantidad_litros ?? '' }}
                    </span>

                    <span>
                        &nbsp;Lts.
                    </span>

                </div>


                <div class="grupo precio">

                    <span class="etiqueta">
                        P/Unit. Bs/Lts:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->precio_unitario ?? '' }}
                    </span>

                </div>


                <div class="grupo total">

                    <span class="etiqueta">
                        Costo total:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->total ?? '' }}
                    </span>

                </div>

            </div>


            {{-- DETALLE --}}
            <div class="fila">

                <div class="grupo">

                    <span class="etiqueta">
                        Detalle:
                    </span>

                    <span class="linea">

                        @php
                            $tipoVehiculo = mb_strtoupper(
                                trim($valeCombustible->vehiculo?->tipo ?? '')
                            );

                            $unidadVehiculo = mb_strtoupper(
                                trim($valeCombustible->vehiculo?->unidad?->nombre ?? '')
                            );

                            $detalle = 'ASIGNADO A';

                            if ($tipoVehiculo !== '') {
                                $detalle .= ' ' . $tipoVehiculo;
                            }

                            if ($unidadVehiculo !== '') {
                                $detalle .= ' DE ' . $unidadVehiculo;
                            }
                        @endphp

                        {{ $detalle }}

                    </span>

                </div>

            </div>


            {{-- VEHÍCULO --}}
            <div class="fila">

                <div class="grupo placa">

                    <span class="etiqueta">
                        Nro. de Placa:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->vehiculo?->placa ?? '' }}
                    </span>

                </div>


                <div class="grupo codigo">

                    <span class="etiqueta">
                        Código de vehículo:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->vehiculo?->codigo ?? '' }}
                    </span>

                </div>


                <div class="grupo kilometraje">

                    <span class="etiqueta">
                        Kilometraje/Horómetro:
                    </span>

                    <span class="linea">
                        {{ $valeCombustible->kilometraje ?? '' }}
                    </span>

                </div>

            </div>


            {{-- FECHA --}}
            <div class="fila fecha">

                <div class="fecha-texto">

                    Sacaba:

                    @if($valeCombustible->fecha)

                        {{ $valeCombustible->fecha->format('d') }}

                        de

                        @php
                            $meses = [
                                1 => 'enero',
                                2 => 'febrero',
                                3 => 'marzo',
                                4 => 'abril',
                                5 => 'mayo',
                                6 => 'junio',
                                7 => 'julio',
                                8 => 'agosto',
                                9 => 'septiembre',
                                10 => 'octubre',
                                11 => 'noviembre',
                                12 => 'diciembre',
                            ];
                        @endphp

                        {{ $meses[(int) $valeCombustible->fecha->format('m')] }}

                        de

                        {{ $valeCombustible->fecha->format('Y') }}

                    @else

                        ______ de ______________ de 20____

                    @endif

                </div>

            </div>

        </div>


        {{-- =================================================
             FIRMAS
        ================================================= --}}

        <div class="firmas">

            <div class="firma">

                <div class="firma-linea"></div>

                <div class="firma-titulo">
                    Autorizado por:
                </div>

                <div class="firma-subtitulo">
                    ADMINISTRACIÓN DE<br>
                    COMBUSTIBLES Y LUBRICANTES
                </div>

            </div>


            <div class="firma">

                <div class="firma-linea"></div>

                <div class="firma-titulo">
                    Recibí conforme
                </div>

                <div class="firma-subtitulo">
                    CONDUCTOR
                </div>

            </div>


            <div class="firma">

                <div class="firma-linea"></div>

                <div class="firma-titulo">
                    Entregué conforme
                </div>

                <div class="firma-subtitulo">
                    PROVEEDOR
                </div>

            </div>


            <div class="firma">

                <div class="firma-linea"></div>

                <div class="firma-titulo">
                    Autorizado por:
                </div>

                <div class="firma-subtitulo">
                    DIRECCIÓN ADMINISTRATIVA
                </div>

            </div>

        </div>

    </main>


    {{-- =====================================================
         IMPRESIÓN AUTOMÁTICA
    ===================================================== --}}

    @if(request('auto'))

        <script>
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 250);
            });
        </script>

    @endif

</body>

</html>
