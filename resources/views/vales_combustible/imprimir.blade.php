@php
// El diseño especial se aplica únicamente a vales GAMS de diésel.
// Normalizamos espacios y tildes para reconocer nombres como "Diésel Oil".
$nombreCombustible = mb_strtoupper(trim((string) ($valeCombustible->tipoCombustible?->nombre ?? '')), 'UTF-8');
$nombreCombustible = preg_replace('/\s+/u', ' ', $nombreCombustible);
$nombreCombustible = str_replace('É', 'E', $nombreCombustible);


    $nombreCombustible = mb_strtoupper(
        trim((string) ($valeCombustible->tipoCombustible?->nombre ?? '')),
        'UTF-8'
    );

    $nombreCombustible = str_replace(
        ['É', 'È', 'Ê'],
        'E',
        $nombreCombustible
    );

    $nombreCombustible = preg_replace(
        '/\s+/u',
        ' ',
        $nombreCombustible
    );

    $esDiesel = !$valeCombustible->entrega_policia_id && in_array(
        $nombreCombustible,
        ['DIESEL', 'DIESEL OIL'],
        true
    );
@endphp

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Vale {{ $numeroValeFormateado }}
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

        .vale-diesel .direccion .numero {
            margin-top: 8px;
        }

        .diesel-contenido {
            padding: 14px 25px 0;
            font-size: 15px;
        }

        .diesel-fila {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            min-height: 23px;
            margin-bottom: 11px;
        }

        .diesel-etiqueta {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .diesel-linea {
            flex: 1;
            min-width: 0;
            min-height: 24px;
            padding: 0 6px 3px;
            border-bottom: 1px dotted #777;
        }

        .diesel-detalle {
            align-items: flex-start;
        }

        .diesel-detalle .diesel-etiqueta {
            padding-top: 3px;
        }

        .diesel-detalle-linea {
            flex: 1;
            min-width: 0;
            min-height: 34px;
            padding: 2px 6px 4px;
            border-bottom: 1px dotted #777;
            line-height: 1.35;
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }

        .diesel-cantidad {
            max-width: 45%;
        }

        .diesel-vehiculo .diesel-grupo {
            display: flex;
            align-items: flex-end;
            flex: 1;
            gap: 6px;
            min-width: 0;
        }

        .diesel-fecha {
            justify-content: center;
            margin-top: 14px;
        }

        .diesel-fecha .diesel-linea {
            flex: 0 1 270px;
            text-align: center;
        }

        .firmas-diesel {
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            padding: 38px 30px 16px;
        }

        .firmas-diesel .firma-subtitulo {
            font-size: 11px;
        }

        /* =====================================================
           FORMATO FÍSICO EXCLUSIVO DIÉSEL GAMS
           No modifica el diseño normal de gasolina / Policía.
        ===================================================== */
        .vale-diesel {
            display: flex;
            flex-direction: column;
        }

        .vale-diesel .diesel-detalle-linea {
            min-height: 2.8em;
            border-bottom: 0;
            background-image: linear-gradient(
                to bottom,
                transparent calc(1.4em - 1px),
                #777 1.4em,
                transparent calc(1.4em + 1px)
            );
            background-size: 100% 1.4em;
        }

        .vale-diesel .encabezado {
            grid-template-columns: 33% 42% 25%;
            min-height: 138px;
        }

        .vale-diesel .encabezado > div {
            padding: 12px 10px;
        }

        .vale-diesel .secretaria {
            font-size: 16px;
        }

        .vale-diesel .direccion {
            font-size: 13px;
        }

        .vale-diesel .direccion .numero {
            margin-top: 10px;
            font-size: 21px;
            color: #a56868;
            letter-spacing: 3px;
        }

        .vale-diesel .titulo {
            font-size: 25px;
            padding: 10px 12px;
        }

        .vale-diesel .diesel-contenido {
            padding: 22px 28px 0;
            font-size: 17px;
            flex: 1;
        }

        .vale-diesel .diesel-fila {
            min-height: 34px;
            margin-bottom: 17px;
        }

        .vale-diesel .diesel-detalle-linea {
            min-height: 56px;
        }

        .vale-diesel .diesel-cantidad {
            max-width: 52%;
        }

        .vale-diesel .diesel-fecha {
            margin-top: 22px;
        }

        .vale-diesel .firmas-diesel {
            padding: 12px 25px 18px;
            gap: 22px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .vale-diesel .firmas-diesel .firma {
            min-height: 74px;
        }

        .vale-diesel .firmas-diesel .firma-titulo {
            font-size: 12px;
        }

        .vale-diesel .firmas-diesel .firma-subtitulo {
            font-size: 10px;
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

        /* Página nombrada exclusiva de DIÉSEL GAMS: carta vertical.
           La regla @page sin nombre permanece igual para GASOLINA/POLICÍA. */
        @page diesel-gams {
            size: letter portrait;
            margin: 0;
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

            /* Solo el vale GAMS diésel utiliza la hoja carta vertical completa.
               El vale mide media carta y ocupa la mitad superior. */
            html:has(.vale-diesel),
            body:has(.vale-diesel) {
                width: 215.9mm;
                height: 279.4mm;
                min-height: 279.4mm;
                margin: 0;
                padding: 0;
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

            .vale-diesel {
                page: diesel-gams;
                position: relative;
                width: 215.9mm;
                height: 139.7mm;
                min-height: 0;
                margin: 0;
                border: 0;
                overflow: hidden;
                break-after: avoid-page;
                page-break-after: avoid;
            }

            .vale-diesel::before {
                content: "";
                position: absolute;
                z-index: 1;
                inset: 4mm;
                border: 1px solid #555;
                pointer-events: none;
            }

            .vale-diesel .encabezado {
                flex: 0 0 29mm;
                min-height: 29mm;
            }

            .vale-diesel .encabezado > div {
                padding: 4mm 5mm;
            }

            .vale-diesel .logo-sacaba {
                max-width: 55mm;
                max-height: 21mm;
            }

            .vale-diesel .secretaria {
                font-size: 11pt;
            }

            .vale-diesel .direccion {
                font-size: 9pt;
            }

            .vale-diesel .direccion .numero {
                font-size: 15pt;
            }

            .vale-diesel .hora {
                right: 5mm;
                bottom: 3mm;
                font-size: 8pt;
            }

            .vale-diesel .titulo {
                flex: 0 0 auto;
                padding: 2.5mm 4mm;
                font-size: 15pt;
            }

            .vale-diesel .diesel-contenido {
                display: flex;
                flex: 1 1 auto;
                flex-direction: column;
                justify-content: space-around;
                min-height: 0;
                padding: 3mm 9mm 0;
                font-size: 10pt;
            }

            .vale-diesel .diesel-fila {
                min-height: 7mm;
                margin: 0;
            }

            .vale-diesel .diesel-detalle-linea {
                min-height: 14mm;
                line-height: 7mm;
                background-size: 100% 7mm;
            }

            .vale-diesel .diesel-vehiculo {
                gap: 3mm;
                font-size: 8.5pt;
            }

            .vale-diesel .diesel-fecha {
                margin-top: 0;
            }

            .vale-diesel .firmas-diesel {
                flex: 0 0 29mm;
                padding: 3mm 9mm 5mm;
                gap: 7mm;
            }

            .vale-diesel .firmas-diesel .firma {
                min-height: 19mm;
            }

            .vale-diesel .firmas-diesel .firma-titulo {
                font-size: 9pt;
            }

            .vale-diesel .firmas-diesel .firma-subtitulo {
                font-size: 7.5pt;
            }

            .vale-diesel .firmas-diesel .firma-linea {
                margin-bottom: 2mm;
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

    <main class="vale {{ $esDiesel ? 'vale-diesel' : '' }}" data-print-format="{{ $esDiesel ? 'gams-diesel' : 'standard' }}">

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

                @unless($esDiesel)
                    <div class="numero">
                        N.º {{ $numeroValeFormateado }}
                    </div>
                @endunless

            </div>


            {{-- DIRECCIÓN --}}
            <div class="direccion">

                DIRECCIÓN ADMINISTRATIVA

                @if($esDiesel)
                    <div class="numero">
                        N.º {{ $numeroValeFormateado }}
                    </div>
                @endif

                <div class="hora">
                    Hora: {{ $valeCombustible->created_at?->format('H:i') ?? '—' }}
                </div>

            </div>

        </div>


        {{-- =================================================
             TÍTULO
        ================================================= --}}

        <div class="titulo">

            @if($esDiesel)
                VALE DE COMBUSTIBLE (DIESEL OIL)
            @else
                VALE DE COMBUSTIBLE

                @if($valeCombustible->tipoCombustible?->nombre)

                    ({{ strtoupper($valeCombustible->tipoCombustible->nombre) }})

                @else

                    (________________)

                @endif
            @endif

        </div>


        {{-- =================================================
             DATOS DEL VALE
        ================================================= --}}

             @if($esDiesel)

                 <div class="contenido diesel-contenido">

<div class="diesel-fila">
    <span class="diesel-etiqueta">Lugar de entrega:</span>
    <span class="diesel-linea">{{ $valeCombustible->lugarEntrega?->nombre ?? '' }}</span>
</div>

                     <div class="diesel-fila diesel-cantidad">
                         <span class="diesel-etiqueta">Cantidad:</span>
                         <span class="diesel-linea">{{ $valeCombustible->cantidad_litros ?? '' }}</span>
                         <span>Lts.</span>
                     </div>

                     <div class="diesel-fila diesel-detalle">
                         <span class="diesel-etiqueta">Detalle:</span>
                         <div class="diesel-detalle-linea">{{ $valeCombustible->detalle_asignacion ?? '' }}</div>
                     </div>

                     <div class="diesel-fila diesel-vehiculo">
                         <div class="diesel-grupo">
                             <span class="diesel-etiqueta">Nro. de Placa:</span>
                             <span class="diesel-linea">{{ $valeCombustible->vehiculo?->placa ?? '' }}</span>
                         </div>
                         <div class="diesel-grupo">
                             <span class="diesel-etiqueta">Código de vehículo:</span>
                             <span class="diesel-linea">{{ $valeCombustible->vehiculo?->codigo ?? '' }}</span>
                         </div>
                         <div class="diesel-grupo">
                             <span class="diesel-etiqueta">Kilometraje/Horómetro:</span>
                             <span class="diesel-linea">{{ $valeCombustible->kilometraje ?? '' }}</span>
                         </div>
                     </div>

                     <div class="diesel-fila diesel-fecha">
                         <span class="diesel-etiqueta">Sacaba:</span>
                         <span class="diesel-linea">{{ $valeCombustible->fecha ? \Illuminate\Support\Carbon::parse($valeCombustible->fecha)->translatedFormat('d \d\e F \d\e Y') : '' }}</span>
                     </div>

                 </div>

             @else

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
                        {{ $valeCombustible->detalle_asignacion ?? '' }}

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


            {{-- FECHA DEL VALE: elegida al emitir; hora de emisión: created_at --}}
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

        @endif


        {{-- =================================================
             FIRMAS
        ================================================= --}}

        @if($esDiesel)

            <div class="firmas firmas-diesel">

                <div class="firma">
                    <div class="firma-linea"></div>
                    <div class="firma-titulo">Recibí conforme</div>
                    <div class="firma-subtitulo">CONDUCTOR</div>
                </div>

                <div class="firma">
                    <div class="firma-linea"></div>
                    <div class="firma-titulo">Autorizado por:</div>
                    <div class="firma-subtitulo">
                        TEC. I ADMINISTRACIÓN DE<br>
                        COMBUSTIBLES Y LUBRICANTES
                    </div>
                </div>

                <div class="firma">
                    <div class="firma-linea"></div>
                    <div class="firma-titulo">Autorizado por:</div>
                    <div class="firma-subtitulo">DIRECTOR ADMINISTRATIVO</div>
                </div>

            </div>

        @else

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

        @endif

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