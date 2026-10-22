```blade
@extends('layouts.app')

@section('title', 'Reporte por Vehículo')

@push('styles')
<style>
    .reporte-card {
        border: 1px solid #d6e2da;
        border-radius: 12px;
        background: #ffffff;
    }

    .reporte-titulo {
        color: #174c2b;
        font-weight: 700;
    }

    .resumen-card {
        border: 1px solid #d6e2da;
        border-radius: 10px;
        background: #f8fbf9;
        padding: 16px;
        height: 100%;
    }

    .resumen-card small {
        color: #6c757d;
    }

    .resumen-card h4 {
        color: #174c2b;
        margin: 5px 0 0;
        font-weight: 700;
    }

    .tabla-reporte {
        font-size: 13px;
    }

    .tabla-reporte th {
        background: #eaf7ee;
        color: #174c2b;
        font-weight: 700;
        border: 1px solid #d6e2da;
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
    }

    .tabla-reporte td {
        border: 1px solid #d6e2da;
        vertical-align: middle;
    }

    .tabla-reporte tbody tr:hover td {
        background: #f3faf5;
    }

    .btn-verde {
        background: #198754;
        border-color: #198754;
        color: white;
    }

    .btn-verde:hover {
        background: #157347;
        border-color: #146c43;
        color: white;
    }

    .parte-si {
        background: #198754;
    }

    .parte-no {
        background: #dc3545;
    }

    .parte-sin-registro {
        background: #6c757d;
    }

    /*
    |--------------------------------------------------------------------------
    | ENCABEZADO DE IMPRESIÓN
    |--------------------------------------------------------------------------
    | En pantalla permanece oculto.
    */

    .encabezado-impresion,
    .fecha-impresion,
    .titulo-impresion,
    .subtitulo-impresion {
        display: none;
    }

    /*
    |--------------------------------------------------------------------------
    | IMPRESIÓN
    |--------------------------------------------------------------------------
    */

    @media print {

        @page {
            size: letter landscape;
            margin: 8mm;
        }

        .no-print,
        .menu-lateral,
        .barra-superior {
            display: none !important;
        }

        html,
        body {
            background: #ffffff !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            font-size: 8px;
        }

        .pagina {
            margin-left: 0 !important;
        }

        .contenido,
        .container-fluid {
            width: 100% !important;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .card {
            box-shadow: none !important;
        }

        .reporte-card {
            border: 0 !important;
            border-radius: 0 !important;
        }

        .reporte-card .card-body {
            padding: 0 !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        /*
        |--------------------------------------------------------------------------
        | ENCABEZADO INSTITUCIONAL
        |--------------------------------------------------------------------------
        */

        .encabezado-impresion {
            display: flex !important;
            width: 100% !important;
            height: 3cm;
            align-items: center;
            justify-content: space-between;
            margin: 0 0 2px 0;
            padding: 0;
        }

        .logo-impresion {
            width: 20%;
        }

        .logo-impresion.izquierda {
            text-align: left;
        }

        .logo-impresion.derecha {
            text-align: right;
        }

        .logo-impresion img {
            display: inline-block !important;
            max-height: 65px;
            max-width: 145px;
            object-fit: contain;
        }

        .institucion-impresion {
            width: 60%;
            text-align: center;
            font-size: 11px;
            line-height: 1.5;
            color: #000000;
        }

        .fecha-impresion {
            display: block !important;
            width: 100%;
            text-align: right;
            font-size: 9px;
            margin: 0 0 8px 0;
            color: #000000;
        }

        .titulo-impresion {
            display: block !important;
            width: 100%;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #000000;
        }

        .subtitulo-impresion {
            display: block !important;
            width: 100%;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 12px 0;
            color: #000000;
        }

        /*
        |--------------------------------------------------------------------------
        | TABLA DE IMPRESIÓN
        |--------------------------------------------------------------------------
        */

        .tabla-reporte {
            width: 100% !important;
            font-size: 7px !important;
            border-collapse: collapse !important;
            table-layout: auto;
        }

        .tabla-reporte th {
            background: #eeeeee !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            font-weight: bold !important;
            text-align: center !important;
            vertical-align: middle !important;
            padding: 3px 2px !important;
            white-space: normal !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .tabla-reporte td {
            border: 1px solid #000000 !important;
            color: #000000 !important;
            padding: 3px 2px !important;
            vertical-align: middle !important;
            white-space: normal !important;
        }

        .tabla-reporte tbody tr:hover td {
            background: transparent !important;
        }

        .tabla-reporte .badge {
            color: #000000 !important;
            background: transparent !important;
            padding: 0 !important;
            font-size: 7px !important;
            font-weight: bold !important;
        }

        .tabla-reporte .table-light td {
            background: #eeeeee !important;
            font-weight: bold !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /*
        |--------------------------------------------------------------------------
        | EVITAR CORTES EXTRAÑOS
        |--------------------------------------------------------------------------
        */

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    }
</style>
@endpush


@section('content')

<div class="container-fluid">

    {{-- ============================================================
         BOTONES
    ============================================================ --}}

    <div class="d-flex justify-content-end gap-2 flex-wrap mb-3 no-print">

        <a href="{{ route('reportes.vehiculos.excel', request()->query()) }}"
           class="btn btn-outline-success">

            <i class="bi bi-file-earmark-excel me-1"></i>
            Excel

        </a>


        <a href="{{ route('reportes.vehiculos.pdf', request()->query()) }}"
           target="_blank"
           class="btn btn-outline-danger">

            <i class="bi bi-file-earmark-pdf me-1"></i>
            PDF

        </a>


        <button type="button"
                onclick="window.print()"
                class="btn btn-outline-success">

            <i class="bi bi-printer me-1"></i>
            Imprimir

        </button>

    </div>


    {{-- ============================================================
         ENCABEZADO SOLO PARA IMPRESIÓN
    ============================================================ --}}

    <div class="encabezado-impresion">

        <div class="logo-impresion izquierda">

            <img src="{{ asset('images/logo-sacaba.jpg') }}"
                 alt="Logo Sacaba">

        </div>


        <div class="institucion-impresion">

            <strong>
                SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION
            </strong>

            <br>

            <strong>
                DIRECCION ADMINISTRATIVA
            </strong>

        </div>


        <div class="logo-impresion derecha">

            <img src="{{ asset('images/logo-chapare.png') }}"
                 alt="Logo Chapare">

        </div>

    </div>


    <div class="fecha-impresion">

        Sacaba,
        {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}

    </div>


    <div class="titulo-impresion">

        REF.: REPORTE ASIGNACIÓN DE COMBUSTIBLE POR VEHÍCULO

    </div>


    <div class="subtitulo-impresion">

        DETALLE DE ASIGNACIÓN DE COMBUSTIBLE

    </div>


    {{-- ============================================================
         TÍTULO DE PANTALLA
    ============================================================ --}}

    <div class="mb-4 no-print">

        <h3 class="reporte-titulo mb-1">

            <i class="bi bi-car-front-fill me-2"></i>

            Reporte General por Vehículo

        </h3>

        <p class="text-muted mb-0">

            Reporte de asignación y consumo de combustible por vehículo.

        </p>

    </div>


    {{-- ============================================================
         FILTROS
    ============================================================ --}}

    <div class="card reporte-card shadow-sm mb-4 no-print">

        <div class="card-body">

            <h5 class="reporte-titulo mb-3">

                <i class="bi bi-funnel me-2"></i>
                Filtros

            </h5>


            <form method="GET"
                  action="{{ route('reportes.vehiculos') }}">

                <div class="row g-3">

                    {{-- VEHÍCULO --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Vehículo
                        </label>

                        <select name="vehiculo_id"
                                class="form-select">

                            <option value="">
                                Todos los vehículos
                            </option>

                            @foreach($vehiculos as $vehiculo)

                                <option
                                    value="{{ $vehiculo->id }}"
                                    {{ request('vehiculo_id') == $vehiculo->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $vehiculo->codigo }}
                                    -
                                    {{ $vehiculo->placa }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- FECHA INICIAL --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Fecha inicial
                        </label>

                        <input
                            type="date"
                            name="fecha_inicio"
                            class="form-control"
                            value="{{ request('fecha_inicio') }}">

                    </div>


                    {{-- FECHA FINAL --}}
                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Fecha final
                        </label>

                        <input
                            type="date"
                            name="fecha_fin"
                            class="form-control"
                            value="{{ request('fecha_fin') }}">

                    </div>


                    {{-- BOTÓN CONSULTAR --}}
                    <div class="col-md-2 d-flex align-items-end">

                        <button type="submit"
                                class="btn btn-verde w-100">

                            <i class="bi bi-search me-1"></i>
                            Consultar

                        </button>

                    </div>

                </div>


                <div class="mt-3">

                    <a href="{{ route('reportes.vehiculos') }}"
                       class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Limpiar filtros

                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- ============================================================
         RESUMEN
    ============================================================ --}}

    <div class="row g-3 mb-4 no-print">

        {{-- REGISTROS --}}
        <div class="col-md-3">

            <div class="resumen-card">

                <small>
                    Registros
                </small>

                <h4>

                    {{ number_format(
                        $totalVales,
                        0,
                        ',',
                        '.'
                    ) }}

                </h4>

            </div>

        </div>


        {{-- VEHÍCULOS --}}
        <div class="col-md-3">

            <div class="resumen-card">

                <small>
                    Vehículos
                </small>

                <h4>

                    {{ number_format(
                        $totalVehiculos,
                        0,
                        ',',
                        '.'
                    ) }}

                </h4>

            </div>

        </div>


        {{-- TOTAL LITROS --}}
        <div class="col-md-3">

            <div class="resumen-card">

                <small>
                    Total litros
                </small>

                <h4>

                    {{ number_format(
                        $totalLitros,
                        2,
                        ',',
                        '.'
                    ) }}
                    L

                </h4>

            </div>

        </div>


        {{-- MONTO TOTAL --}}
        <div class="col-md-3">

            <div class="resumen-card">

                <small>
                    Monto total
                </small>

                <h4>

                    Bs
                    {{ number_format(
                        $totalMonto,
                        2,
                        ',',
                        '.'
                    ) }}

                </h4>

            </div>

        </div>

    </div>


    {{-- ============================================================
         TABLA
    ============================================================ --}}

    <div class="card reporte-card shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 no-print">

                <h5 class="reporte-titulo mb-0">

                    Detalle de asignación de combustible

                </h5>

                <span class="text-muted">

                    {{ $totalVales }}
                    registro(s)

                </span>

            </div>


            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle tabla-reporte">

                    <thead>

                        <tr>

                            <th>
                                N.º
                            </th>

                            <th>
                                CÓDIGO DE<br>
                                VEHÍCULO
                            </th>

                            <th>
                                PLACA
                            </th>

                            <th>
                                PRESENTACIÓN<br>
                                PARTE DIARIO
                            </th>

                            <th>
                                TIPO DE<br>
                                VEHÍCULO
                            </th>

                            <th>
                                AP.<br>
                                PROGR.
                            </th>

                            <th>
                                FF
                            </th>

                            <th>
                                OF
                            </th>

                            <th>
                                CANTIDAD<br>
                                LITROS
                            </th>

                            <th>
                                P. U.<br>
                                (BS/LITRO)
                            </th>

                            <th>
                                PRECIO TOTAL<br>
                                (BS)
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($reporte as $indice => $fila)

                            <tr>

                                {{-- N.º --}}
                                <td class="text-center">

                                    {{ $indice + 1 }}

                                </td>


                                {{-- CÓDIGO --}}
                                <td>

                                    {{ $fila->codigo }}

                                </td>


                                {{-- PLACA --}}
                                <td>

                                    <strong>
                                        {{ $fila->placa }}
                                    </strong>

                                </td>


                                {{-- PARTE DIARIO --}}
                                <td class="text-center">

                                    @if($fila->parte_diario === 'SÍ')

                                        <span class="badge parte-si">

                                            SÍ

                                        </span>

                                    @elseif($fila->parte_diario === 'NO')

                                        <span class="badge parte-no">

                                            NO

                                        </span>

                                    @else

                                        <span class="badge parte-sin-registro">

                                            SIN REGISTRO

                                        </span>

                                    @endif

                                </td>


                                {{-- TIPO DE VEHÍCULO --}}
                                <td>

                                    {{ $fila->tipo_vehiculo }}

                                </td>


                                {{-- APERTURA --}}
                                <td>

                                    {{ $fila->apertura }}

                                </td>


                                {{-- FF --}}
                                <td class="text-center">

                                    {{ $fila->fuente }}

                                </td>


                                {{-- OF --}}
                                <td class="text-center">

                                    {{ $fila->organismo }}

                                </td>


                                {{-- LITROS --}}
                                <td class="text-end">

                                    {{ number_format(
                                        $fila->litros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </td>


                                {{-- PRECIO UNITARIO --}}
                                <td class="text-end">

                                    {{ number_format(
                                        $fila->precio_unitario,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </td>


                                {{-- TOTAL --}}
                                <td class="text-end fw-semibold">

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
                                    class="text-center text-muted py-4">

                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>

                                    No existen registros para los filtros seleccionados.

                                </td>

                            </tr>

                        @endforelse


                        {{-- ====================================================
                             TOTAL
                        ==================================================== --}}

                        @if($reporte->isNotEmpty())

                            <tr class="fw-bold table-light">

                                <td colspan="8"
                                    class="text-end">

                                    TOTAL

                                </td>


                                {{-- TOTAL LITROS --}}
                                <td class="text-end">

                                    {{ number_format(
                                        $totalLitros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}

                                </td>


                                {{-- PRECIO UNITARIO NO SE SUMA --}}
                                <td></td>


                                {{-- MONTO TOTAL --}}
                                <td class="text-end">

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

            </div>

        </div>

    </div>

</div>

@endsection
```