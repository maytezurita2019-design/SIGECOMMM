@extends('layouts.app')

@section('title', 'Reporte General de Consumo')

@section('content')

<div class="container-fluid py-3">

    {{-- ============================================================
        ENCABEZADO DE PANTALLA
    ============================================================ --}}
    <div class="d-flex gap-2 no-print">

    <button
        type="button"
        onclick="prepararImpresion()"
        class="btn btn-outline-success"
    >
        <i class="bi bi-printer me-1"></i>
        Imprimir
    </button>

    <button
        type="button"
        onclick="generarPdf()"
        class="btn btn-outline-danger"
    >
        <i class="bi bi-file-earmark-pdf me-1"></i>
        PDF
    </button>

    <a
        href="{{ route('reportes.consumo.excel', request()->query()) }}"
        class="btn btn-success"
    >
        <i class="bi bi-file-earmark-excel me-1"></i>
        Excel
    </a>

</div>


    {{-- ============================================================
        FILTROS
    ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4 no-print">

        <div class="card-header bg-white border-0 pt-3">

            <h6 class="fw-bold text-success mb-0">
                <i class="bi bi-funnel-fill me-2"></i>
                Filtros del reporte
            </h6>

        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('reportes.consumo') }}"
            >

                <div class="row g-3 align-items-end">

                    {{-- FECHA INICIAL --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Fecha inicial
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio"
                            class="form-control"
                            value="{{ request('fecha_inicio') }}"
                        >

                    </div>


                    {{-- FECHA FINAL --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Fecha final
                        </label>

                        <input
                            type="date"
                            name="fecha_fin"
                            class="form-control"
                            value="{{ request('fecha_fin') }}"
                        >

                    </div>


                    {{-- VEHÍCULO --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Vehículo
                        </label>

                        <select
                            name="vehiculo_id"
                            class="form-select"
                        >

                            <option value="">
                                Todos los vehículos
                            </option>

                            @foreach($vehiculos as $vehiculo)

                                <option
                                    value="{{ $vehiculo->id }}"
                                    @selected(
                                        (string) request('vehiculo_id')
                                        ===
                                        (string) $vehiculo->id
                                    )
                                >
                                    {{ $vehiculo->codigo }}
                                    -
                                    {{ $vehiculo->placa }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- UNIDAD --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Unidad
                        </label>

                        <select
                            name="unidad_id"
                            class="form-select"
                        >

                            <option value="">
                                Todas las unidades
                            </option>

                            @foreach($unidades as $unidad)

                                <option
                                    value="{{ $unidad->id }}"
                                    @selected(
                                        (string) request('unidad_id')
                                        ===
                                        (string) $unidad->id
                                    )
                                >
                                    {{ $unidad->nombre }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BOTONES --}}
                    <div class="col-12">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                <i class="bi bi-search me-1"></i>
                                Generar reporte
                            </button>

                            <a
                                href="{{ route('reportes.consumo') }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Limpiar
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ============================================================
        TEXTO PERSONALIZADO PARA LA IMPRESIÓN
    ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4 no-print">

        <div class="card-header bg-white border-0 pt-3">

            <h6 class="fw-bold text-success mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Texto para impresión
            </h6>

        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-12">

                    <label class="form-label">
                        Referencia o descripción del reporte
                    </label>

                    <textarea
                        id="textoReporte"
                        class="form-control"
                        rows="3"
                        placeholder="Escriba aquí la referencia, motivo o descripción que aparecerá antes del resumen..."
                    ></textarea>

                </div>


                <div class="col-md-12">

                    <label class="form-label">
                        Texto final u observación
                    </label>

                    <textarea
                        id="textoFinal"
                        class="form-control"
                        rows="2"
                        placeholder="Escriba aquí una observación o texto final si corresponde..."
                    ></textarea>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        ENCABEZADO EXCLUSIVO PARA IMPRESIÓN
    ============================================================ --}}
    <div class="print-header">

        <div class="encabezado-institucional">

            {{-- LOGO SACABA --}}
            <div class="logo-izquierdo">

                <img
                    src="{{ asset('images/logo-sacaba.jpg') }}"
                    alt="Gobierno Autónomo Municipal de Sacaba"
                >

            </div>


            {{-- ENCABEZADO CENTRAL --}}
            <div class="encabezado-centro">

                <div class="institucion">
                    SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN
                </div>

                <div class="direccion">
                    DIRECCIÓN ADMINISTRATIVA
                </div>

                <div class="titulo-reporte">
                    REPORTE GENERAL DE CONSUMO DE COMBUSTIBLE
                </div>

            </div>


            {{-- LOGO CHAPARE --}}
            <div class="logo-derecho">

                <img
                    src="{{ asset('images/logo-chapare.png') }}"
                    alt="Provincia Chapare"
                >

            </div>

        </div>


        {{-- DATOS DEL REPORTE --}}
        <div class="datos-reporte">

            <div>

                <strong>PERÍODO:</strong>

                @if(request('fecha_inicio'))

                    {{ \Carbon\Carbon::parse(
                        request('fecha_inicio')
                    )->format('d/m/Y') }}

                @else

                    INICIO

                @endif

                AL

                @if(request('fecha_fin'))

                    {{ \Carbon\Carbon::parse(
                        request('fecha_fin')
                    )->format('d/m/Y') }}

                @else

                    ACTUALIDAD

                @endif

            </div>


            <div>

                <strong>FECHA DE IMPRESIÓN:</strong>

                {{ now()->format('d/m/Y') }}

            </div>

        </div>

    </div>


    {{-- ============================================================
        TEXTO INICIAL PARA IMPRESIÓN
    ============================================================ --}}
    <div
        id="textoReporteImpresion"
        class="texto-reporte-impresion"
    ></div>


    {{-- ============================================================
        RESUMEN GENERAL
    ============================================================ --}}
    <h5 class="titulo">
        Resumen general
    </h5>


    <div class="row g-3 mb-4 resumen-indicadores">

        {{-- VALES --}}
        <div class="col-md-3">

            <div class="card indicador">

                <div class="card-body">

                    <small>
                        Vales utilizados
                    </small>

                    <h3>
                        {{ number_format(
                            $totalVales,
                            0,
                            ',',
                            '.'
                        ) }}
                    </h3>

                </div>

            </div>

        </div>


        {{-- VEHÍCULOS --}}
        <div class="col-md-3">

            <div class="card indicador">

                <div class="card-body">

                    <small>
                        Vehículos
                    </small>

                    <h3>
                        {{ number_format(
                            $totalVehiculos,
                            0,
                            ',',
                            '.'
                        ) }}
                    </h3>

                </div>

            </div>

        </div>


        {{-- LITROS --}}
        <div class="col-md-3">

            <div class="card indicador">

                <div class="card-body">

                    <small>
                        Combustible consumido
                    </small>

                    <h3>
                        {{ number_format(
                            $totalLitros,
                            2,
                            ',',
                            '.'
                        ) }}
                        L
                    </h3>

                </div>

            </div>

        </div>


        {{-- MONTO --}}
        <div class="col-md-3">

            <div class="card indicador">

                <div class="card-body">

                    <small>
                        Monto total
                    </small>

                    <h3>
                        Bs
                        {{ number_format(
                            $totalMonto,
                            2,
                            ',',
                            '.'
                        ) }}
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        CONSUMO CONSOLIDADO POR UNIDAD
    ============================================================ --}}
    <div class="card shadow-sm border-0 reporte-consolidado">

        <div class="card-header encabezado-verde">

            <strong>
                CONSUMO CONSOLIDADO POR UNIDAD
            </strong>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered mb-0 tabla-general">

                    <thead>

                        <tr>

                            <th>
                                Unidad
                            </th>

                            <th class="text-center">
                                Vehículos
                            </th>

                            <th class="text-center">
                                Vales
                            </th>

                            <th class="text-end">
                                Gasolina (L)
                            </th>

                            <th class="text-end">
                                Diésel (L)
                            </th>

                            <th class="text-end">
                                Otros (L)
                            </th>

                            <th class="text-end">
                                Total litros
                            </th>

                            <th class="text-end">
                                Monto total (Bs)
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($resumenUnidades as $item)

                            <tr>

                                <td class="fw-semibold">
                                    {{ $item->unidad }}
                                </td>

                                <td class="text-center">
                                    {{ $item->vehiculos }}
                                </td>

                                <td class="text-center">
                                    {{ $item->vales }}
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        $item->gasolina,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        $item->diesel,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        $item->otros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-end fw-semibold">
                                    {{ number_format(
                                        $item->litros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-end fw-semibold">
                                    {{ number_format(
                                        $item->total,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center text-muted py-4"
                                >
                                    No existen registros de consumo.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>


                    @if($resumenUnidades->count() > 0)

                        <tfoot>

                            <tr>

                                <th>
                                    TOTAL GENERAL
                                </th>

                                <th class="text-center">
                                    {{ $totalVehiculos }}
                                </th>

                                <th class="text-center">
                                    {{ $totalVales }}
                                </th>

                                <th class="text-end">
                                    {{ number_format(
                                        $totalGasolina,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </th>

                                <th class="text-end">
                                    {{ number_format(
                                        $totalDiesel,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </th>

                                <th class="text-end">
                                    {{ number_format(
                                        $totalOtros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </th>

                                <th class="text-end">
                                    {{ number_format(
                                        $totalLitros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </th>

                                <th class="text-end">
                                    {{ number_format(
                                        $totalMonto,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </th>

                            </tr>

                        </tfoot>

                    @endif

                </table>

            </div>

        </div>

    </div>


    {{-- ============================================================
        TEXTO FINAL PARA IMPRESIÓN
    ============================================================ --}}
    <div
        id="textoFinalImpresion"
        class="texto-final-impresion"
    ></div>

</div>

@endsection


{{-- ================================================================
    JAVASCRIPT
================================================================ --}}
@push('scripts')

<script>

function prepararImpresion()
{
    const textoReporte =
        document.getElementById('textoReporte').value.trim();

    const textoFinal =
        document.getElementById('textoFinal').value.trim();

    const textoReporteImpresion =
        document.getElementById('textoReporteImpresion');

    const textoFinalImpresion =
        document.getElementById('textoFinalImpresion');

    if (textoReporte !== '') {

        textoReporteImpresion.textContent =
            textoReporte;

        textoReporteImpresion.classList.add(
            'con-contenido'
        );

    } else {

        textoReporteImpresion.textContent = '';

        textoReporteImpresion.classList.remove(
            'con-contenido'
        );
    }


    if (textoFinal !== '') {

        textoFinalImpresion.textContent =
            textoFinal;

        textoFinalImpresion.classList.add(
            'con-contenido'
        );

    } else {

        textoFinalImpresion.textContent = '';

        textoFinalImpresion.classList.remove(
            'con-contenido'
        );
    }


    window.print();
}


function generarPdf()
{
    const textoSuperior =
        document.getElementById('textoReporte')?.value.trim() ?? '';

    const textoInferior =
        document.getElementById('textoFinal')?.value.trim() ?? '';

    const params = new URLSearchParams(
        window.location.search
    );


    if (textoSuperior !== '') {

        params.set(
            'texto_superior',
            textoSuperior
        );

    } else {

        params.delete(
            'texto_superior'
        );
    }


    if (textoInferior !== '') {

        params.set(
            'texto_inferior',
            textoInferior
        );

    } else {

        params.delete(
            'texto_inferior'
        );
    }


    const url =
        "{{ route('reportes.consumo.pdf') }}"
        + '?'
        + params.toString();


    window.open(
        url,
        '_blank'
    );
}

</script>

@endpush

{{-- ================================================================
    ESTILOS
================================================================ --}}
@push('styles')

<style>

    /* ============================================================
       PANTALLA
    ============================================================ */

    .titulo {
        color: #174d32;
        font-weight: 700;
        margin-bottom: 12px;
    }


    .indicador {
        border: 1px solid #dce7e0;
        border-left: 4px solid #198754;
        border-radius: 10px;
        height: 100%;
    }


    .indicador small {
        color: #6c757d;
    }


    .indicador h3 {
        color: #198754;
        font-weight: 700;
        margin-top: 6px;
        margin-bottom: 0;
    }


    .encabezado-verde {
        background: #198754;
        color: white;
    }


    .tabla-general thead th {
        background: #e9f5ee;
        color: #174d32;
        font-size: 13px;
        vertical-align: middle;
        white-space: nowrap;
    }


    .tabla-general td {
        font-size: 13px;
        vertical-align: middle;
    }


    .tabla-general tfoot th {
        background: #e9f5ee;
        color: #174d32;
        font-weight: bold;
    }


    /*
    |--------------------------------------------------------------------------
    | ELEMENTOS EXCLUSIVOS DE IMPRESIÓN
    |--------------------------------------------------------------------------
    */

    .print-header,
    .texto-reporte-impresion,
    .texto-final-impresion {
        display: none;
    }


    /*
    |--------------------------------------------------------------------------
    | ENCABEZADO INSTITUCIONAL
    |--------------------------------------------------------------------------
    */

    .encabezado-institucional {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }


    .logo-izquierdo {
        width: 20%;
        text-align: left;
    }


    .logo-izquierdo img {
        max-width: 170px;
        max-height: 65px;
        object-fit: contain;
    }


    .encabezado-centro {
        width: 60%;
        text-align: center;
    }


    .logo-derecho {
        width: 20%;
        text-align: right;
    }


    .logo-derecho img {
        max-width: 75px;
        max-height: 70px;
        object-fit: contain;
    }


    .institucion {
        font-size: 12px;
        font-weight: 700;
    }


    .direccion {
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
    }


    .titulo-reporte {
        font-size: 15px;
        font-weight: 700;
        color: #174d32;
        margin-top: 10px;
    }


    .datos-reporte {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-top: 12px;
        padding-bottom: 7px;

        border-bottom: 2px solid #174d32;

        font-size: 10px;
    }


    /* ============================================================
       IMPRESIÓN
    ============================================================ */

    @media print {

        @page {
            size: landscape;
            margin: 10mm;
        }


        html,
        body {
            background: white !important;
        }


        body {
            font-size: 9px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | OCULTAR ELEMENTOS DEL SISTEMA
        |--------------------------------------------------------------------------
        */

        .no-print,
        .main-sidebar,
        .main-header,
        .main-footer,
        nav,
        aside {
            display: none !important;
        }


        /*
        |--------------------------------------------------------------------------
        | UTILIZAR TODO EL ANCHO DE LA HOJA
        |--------------------------------------------------------------------------
        */

        .content-wrapper,
        .content,
        .container-fluid {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: 0 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | ENCABEZADO
        |--------------------------------------------------------------------------
        */

        .print-header {
            display: block !important;
            margin: 0 0 10px 0 !important;
        }


        .encabezado-institucional {
            display: flex !important;
            align-items: center !important;
        }


        .logo-izquierdo img,
        .logo-derecho img {
            display: inline-block !important;
        }


        .titulo-reporte {
            color: #174d32 !important;
        }


        .datos-reporte {
            border-bottom: 2px solid #174d32 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | TEXTO PERSONALIZADO
        |--------------------------------------------------------------------------
        */

        .texto-reporte-impresion.con-contenido {
            display: block !important;

            margin: 8px 0 10px 0;

            font-size: 9px;
            line-height: 1.4;

            text-align: justify;

            white-space: pre-line;
        }


        .texto-final-impresion.con-contenido {
            display: block !important;

            margin: 14px 0 0 0;

            font-size: 9px;
            line-height: 1.4;

            text-align: justify;

            white-space: pre-line;
        }


        /*
        |--------------------------------------------------------------------------
        | TÍTULO RESUMEN
        |--------------------------------------------------------------------------
        */

        .titulo {
            font-size: 11px !important;

            color: #174d32 !important;

            margin:
                6px
                0
                6px
                0 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | CUATRO INDICADORES EN UNA SOLA FILA
        |--------------------------------------------------------------------------
        */

        .resumen-indicadores {
            display: flex !important;

            flex-wrap: nowrap !important;

            width: 100% !important;

            gap: 6px !important;

            margin:
                0
                0
                10px
                0 !important;
        }


        .resumen-indicadores > .col-md-3 {
            width: 25% !important;
            max-width: 25% !important;

            flex:
                0
                0
                calc(25% - 5px) !important;

            padding: 0 !important;

            margin: 0 !important;
        }


        .indicador {
            height: 52px !important;
            min-height: 52px !important;

            border: 1px solid #666 !important;

            border-left:
                3px
                solid
                #174d32 !important;

            border-radius: 3px !important;

            box-shadow: none !important;
        }


        .indicador .card-body {
            padding: 6px 8px !important;
        }


        .indicador small {
            font-size: 7.5px !important;

            color: #333 !important;
        }


        .indicador h3 {
            font-size: 12px !important;

            color: #000 !important;

            margin:
                3px
                0
                0
                0 !important;

            line-height: 1.1 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLA
        |--------------------------------------------------------------------------
        */

        .reporte-consolidado {
            border: 1px solid #666 !important;

            box-shadow: none !important;

            break-inside: avoid;
        }


        .encabezado-verde {
            background: #e9f5ee !important;

            color: #000 !important;

            border-bottom:
                1px
                solid
                #666 !important;

            padding: 6px 8px !important;

            font-size: 9px !important;
        }


        .table-responsive {
            overflow: visible !important;
        }


        .tabla-general {
            width: 100% !important;

            border-collapse: collapse !important;

            margin: 0 !important;
        }


        .tabla-general th,
        .tabla-general td {
            border:
                1px
                solid
                #777 !important;

            padding:
                4px
                4px !important;

            font-size:
                7.5px !important;

            line-height:
                1.2 !important;

            vertical-align:
                middle !important;
        }


        .tabla-general thead th {
            background: #e9f5ee !important;

            color: #000 !important;

            font-weight: 700 !important;

            white-space: normal !important;
        }


        .tabla-general tfoot th {
            background: #e9f5ee !important;

            color: #000 !important;

            font-weight: 700 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | EVITAR ESPACIOS INNECESARIOS
        |--------------------------------------------------------------------------
        */

        .card {
            margin-bottom: 0 !important;
        }


        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p {
            orphans: 3;
            widows: 3;
        }


        /*
        |--------------------------------------------------------------------------
        | COLORES AL IMPRIMIR
        |--------------------------------------------------------------------------
        */

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

    }

</style>

@endpush