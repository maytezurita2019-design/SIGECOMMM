<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Vales para Policía</title>

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

        .acciones p {
            margin-top: 12px;
            color: #555;
        }

        /* =====================================================
           VALE
        ===================================================== */

        .vale {
            width: 100%;
            max-width: 1050px;
            min-height: 610px;
            margin: 0 auto 25px;
            background: #ffffff;
            border: 1.5px solid #555;
            break-inside: avoid;
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
            border-right: 1px solid #aaa;
        }

        .logo-sacaba {
            display: block;
            max-width: 230px;
            max-height: 75px;
            width: auto;
            height: auto;
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
            letter-spacing: 2px;
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
           TÍTULO VERDE
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
           INFORMACIÓN POLICÍA
        ===================================================== */

        .datos-policia {
            display: flex;
            gap: 25px;
            padding: 12px 25px 0;
            font-size: 14px;
        }

        .datos-policia strong {
            color: #333;
        }

        /* =====================================================
           CONTENIDO
        ===================================================== */

        .contenido {
            padding: 15px 25px 5px;
            font-size: 17px;
        }

        .fila {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 18px;
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
            margin-top: 25px;
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
                page-break-after: always;
            }

            .vale:last-child {
                page-break-after: auto;
            }

            .titulo {
                background: #08712f !important;
                color: #ffffff !important;

                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Formato físico exclusivo para vales DIÉSEL de Policía. */
        .vale-diesel { display: flex; flex-direction: column; }
        .vale-diesel .encabezado { grid-template-columns: 33% 42% 25%; min-height: 115px; }
        .vale-diesel .direccion .numero { margin-top: 12px; color: #a56868; font-size: 20px; }
        .vale-diesel .diesel-contenido { flex: 1; padding: 20px 28px 0; font-size: 16px; }
        .vale-diesel .diesel-fila { display: flex; align-items: flex-end; gap: 8px; margin-bottom: 15px; min-height: 26px; }
        .vale-diesel .diesel-etiqueta { white-space: nowrap; flex-shrink: 0; }
        .vale-diesel .diesel-linea { flex: 1; min-width: 0; border-bottom: 1px dotted #777; min-height: 24px; padding: 0 6px 3px; }
        .vale-diesel .diesel-cantidad { max-width: 48%; }
        .vale-diesel .diesel-detalle { align-items: flex-start; }
        .vale-diesel .diesel-detalle-linea { flex: 1; min-width: 0; min-height: 50px; line-height: 25px; background: repeating-linear-gradient(to bottom, transparent 0, transparent 24px, #999 24px, #999 25px); overflow-wrap: anywhere; white-space: pre-wrap; padding: 0 5px; }
        .vale-diesel .diesel-vehiculo { gap: 8px; }
        .vale-diesel .diesel-grupo { display: flex; align-items: flex-end; flex: 1; min-width: 0; gap: 5px; }
        .vale-diesel .diesel-fecha { justify-content: center; margin-top: 10px; }
        .vale-diesel .diesel-fecha .diesel-linea { flex: 0 1 310px; text-align: center; }
        .vale-diesel .firmas-diesel { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; padding: 20px 28px 16px; }
        .vale-diesel .firmas-diesel .firma-subtitulo { font-size: 10px; }

        /* Carta vertical, con un único vale diésel en su mitad superior. */
        @page diesel-policia { size: letter portrait; margin: 0; }
        @media print {
            .vale-diesel {
                page: diesel-policia;
                width: 215.9mm;
                height: 139.7mm;
                min-height: 0;
                max-width: none;
                margin: 0;
                border: 1px solid #555;
                overflow: hidden;
                break-after: page;
                page-break-after: always;
            }
            .vale-diesel:last-child { break-after: auto; page-break-after: auto; }
            .vale-diesel .encabezado { flex: 0 0 29mm; min-height: 29mm; }
            .vale-diesel .encabezado > div { padding: 4mm 5mm; }
            .vale-diesel .logo-sacaba { max-width: 55mm; max-height: 21mm; }
            .vale-diesel .secretaria { font-size: 11pt; }
            .vale-diesel .direccion { font-size: 9pt; }
            .vale-diesel .direccion .numero { font-size: 15pt; }
            .vale-diesel .hora { font-size: 8pt; bottom: 3mm; right: 5mm; }
            .vale-diesel .titulo { flex: 0 0 auto; font-size: 15pt; padding: 2.5mm 4mm; }
            .vale-diesel .diesel-contenido { display: flex; flex: 1 1 auto; flex-direction: column; justify-content: space-around; min-height: 0; padding: 3mm 9mm 0; font-size: 10pt; }
            .vale-diesel .diesel-fila { min-height: 7mm; margin: 0; }
            .vale-diesel .diesel-detalle-linea { min-height: 14mm; line-height: 7mm; background-size: 100% 7mm; }
            .vale-diesel .diesel-vehiculo { gap: 3mm; font-size: 8.5pt; }
            .vale-diesel .diesel-fecha { margin-top: 0; }
            .vale-diesel .firmas-diesel { flex: 0 0 29mm; padding: 3mm 9mm 5mm; gap: 7mm; }
            .vale-diesel .firmas-diesel .firma { min-height: 19mm; }
            .vale-diesel .firmas-diesel .firma-titulo { font-size: 9pt; }
            .vale-diesel .firmas-diesel .firma-subtitulo { font-size: 7.5pt; }
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

        <a href="{{ route('entregas-policia.show', $entregaPolicia) }}">
            Volver
        </a>

        <p>
            Un vale por hoja. Solo se imprimen los pendientes de devolución.
        </p>

    </div>


    {{-- =====================================================
         VALES
    ===================================================== --}}

    @forelse($vales as $vale)
        @php
            $nombreCombustible = mb_strtoupper(trim((string) ($vale->tipoCombustible?->nombre ?? '')), 'UTF-8');
            $nombreCombustible = str_replace(['É', 'È', 'Ê'], 'E', $nombreCombustible);
            $nombreCombustible = preg_replace('/\s+/u', ' ', $nombreCombustible);
            $esDiesel = in_array($nombreCombustible, ['DIESEL', 'DIESEL OIL'], true);
        @endphp

        <main class="vale {{ $esDiesel ? 'vale-diesel' : '' }}">

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
                        N.º {{ sprintf('%06d', (int) $vale->numero_vale) }}
                    </div>
                    @endunless

                </div>


                {{-- DIRECCIÓN --}}
                <div class="direccion">

                    DIRECCIÓN ADMINISTRATIVA

                    @if($esDiesel)
                    <div class="numero">N.º {{ sprintf('%06d', (int) $vale->numero_vale) }}</div>
                    @endif

                    <div class="hora">
                        Hora: __________________
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
                    VALE DE COMBUSTIBLE ({{ mb_strtoupper($vale->tipoCombustible?->nombre ?? 'SIN ASIGNAR') }})
                @endif
            </div>


            {{-- =================================================
                 INFORMACIÓN DE POLICÍA
            ================================================= --}}

            @unless($esDiesel)
            <div class="datos-policia">

                <div>
                    <strong>Dependencia:</strong>
                    {{ $entregaPolicia->dependencia }}
                </div>

                <div>
                    <strong>Contrato:</strong>
                    {{ $entregaPolicia->contrato }}
                </div>

            </div>


            {{-- =================================================
                 DATOS DEL VALE
            ================================================= --}}

            @endunless

            @if($esDiesel)
            <div class="contenido diesel-contenido">
                <div class="diesel-fila">
                    <span class="diesel-etiqueta">Lugar de entrega:</span>
                    <span class="diesel-linea">&nbsp;</span>
                </div>
                <div class="diesel-fila diesel-cantidad">
                    <span class="diesel-etiqueta">Cantidad:</span>
                    <span class="diesel-linea">{{ $vale->cantidad_litros ?? '' }}</span>
                    <span>Lts.</span>
                </div>
                <div class="diesel-fila diesel-detalle">
                    <span class="diesel-etiqueta">Detalle:</span>
                    <div class="diesel-detalle-linea">{{ $vale->detalle_asignacion ?? '' }}</div>
                </div>
                <div class="diesel-fila diesel-vehiculo">
                    <div class="diesel-grupo"><span class="diesel-etiqueta">Nro. de Placa:</span><span class="diesel-linea">{{ $vale->vehiculo?->placa ?? '' }}</span></div>
                    <div class="diesel-grupo"><span class="diesel-etiqueta">Código de vehículo:</span><span class="diesel-linea">{{ $vale->vehiculo?->codigo ?? '' }}</span></div>
                    <div class="diesel-grupo"><span class="diesel-etiqueta">Kilometraje/Horómetro:</span><span class="diesel-linea">{{ $vale->kilometraje ?? '' }}</span></div>
                </div>
                <div class="diesel-fila diesel-fecha">
                    <span class="diesel-etiqueta">Sacaba:</span>
                    <span class="diesel-linea">{{ $entregaPolicia->fecha_entrega ? \Carbon\Carbon::parse($entregaPolicia->fecha_entrega)->translatedFormat('d \d\e F \d\e Y') : ($vale->anio ?? '') }}</span>
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

                        <span class="linea"></span>

                    </div>

                </div>


                {{-- CANTIDAD / PRECIO / TOTAL --}}
                <div class="fila">

                    <div class="grupo cantidad">

                        <span class="etiqueta">
                            Cantidad:
                        </span>

                        <span class="linea"></span>

                        <span>
                            &nbsp;Lts.
                        </span>

                    </div>


                    <div class="grupo precio">

                        <span class="etiqueta">
                            P/Unit. Bs/Lts:
                        </span>

                        <span class="linea"></span>

                    </div>


                    <div class="grupo total">

                        <span class="etiqueta">
                            Costo total:
                        </span>

                        <span class="linea"></span>

                    </div>

                </div>


                {{-- DETALLE --}}
                <div class="fila">

                    <div class="grupo">

                        <span class="etiqueta">
                            Detalle: asignado a
                        </span>

                        <span class="linea"></span>

                    </div>

                </div>


                {{-- VEHÍCULO --}}
                <div class="fila">

                    <div class="grupo placa">

                        <span class="etiqueta">
                            Nro. de Placa:
                        </span>

                        <span class="linea"></span>

                    </div>


                    <div class="grupo codigo">

                        <span class="etiqueta">
                            Código de vehículo:
                        </span>

                        <span class="linea"></span>

                    </div>


                    <div class="grupo kilometraje">

                        <span class="etiqueta">
                            Kilometraje/Horómetro:
                        </span>

                        <span class="linea"></span>

                    </div>

                </div>


                {{-- FECHA --}}
                <div class="fila fecha">

                    <div class="fecha-texto">
                        Sacaba: ______ de ______________ de {{ $entregaPolicia->fecha_entrega ? \Carbon\Carbon::parse($entregaPolicia->fecha_entrega)->format('Y') : $vale->anio }}
                    </div>

                </div>

            </div>


            {{-- =================================================
                 FIRMAS
            ================================================= --}}

            @endif

            @if($esDiesel)
            <div class="firmas firmas-diesel">
                <div class="firma"><div class="firma-linea"></div><div class="firma-titulo">Recibí conforme</div><div class="firma-subtitulo">CONDUCTOR</div></div>
                <div class="firma"><div class="firma-linea"></div><div class="firma-titulo">Autorizado por:</div><div class="firma-subtitulo">TEC. I ADMINISTRACIÓN DE<br>COMBUSTIBLES Y LUBRICANTES</div></div>
                <div class="firma"><div class="firma-linea"></div><div class="firma-titulo">Autorizado por:</div><div class="firma-subtitulo">DIRECTOR ADMINISTRATIVO</div></div>
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

    @empty

        <p style="text-align:center;">
            No hay vales pendientes para imprimir.
        </p>

    @endforelse

</body>

</html>