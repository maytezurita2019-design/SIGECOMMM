@extends('layouts.app')

@section('title', 'Reporte por apertura programática')

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

    .btn-verde {
        background: #198754;
        border-color: #198754;
        color: white;
    }

    .btn-verde:hover {
        background: #157347;
        color: white;
    }

    /* REPORTE INSTITUCIONAL */
    .documento-reporte {
        background: #ffffff;
        border: 1px solid #d6e2da;
        border-radius: 12px;
        padding: 25px;
    }

    .encabezado-reporte {
        min-height: 3cm;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 2px solid #174c2b;
        margin-bottom: 18px;
        padding: 0 2.8cm;
    }

    .logo-sacaba {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);

    width: 5.3cm;
    height: 2.2cm;

    object-fit: contain;
    object-position: left center;
}

.logo-chapare {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);

    width: 2.2cm;
    height: 2.4cm;

    object-fit: contain;
}

.encabezado-reporte {
    height: 3cm;
    min-height: 3cm;

    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    border-bottom: 2px solid #174c2b;

    margin-bottom: 18px;

    padding-left: 5.6cm;
    padding-right: 2.6cm;
}

.encabezado-centro {
    width: 100%;
    text-align: center;
    color: #1f2937;
    line-height: 1.3;
}

.encabezado-centro .secretaria {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
}

.encabezado-centro .direccion {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 4px;
}

    .fecha-reporte {
        text-align: right;
        font-size: 12px;
        margin-bottom: 15px;
    }

    .titulo-documento {
        text-align: center;
        font-weight: 700;
        font-size: 15px;
        text-transform: uppercase;
        margin: 12px 0 5px;
    }

    .subtitulo-documento {
        text-align: center;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .detalle-asignacion {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .tabla-reporte {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .tabla-reporte th {
        background: #eaf7ee;
        color: #174c2b;
        font-weight: 700;
        border: 1px solid #9ca3af;
        vertical-align: middle;
        text-align: center;
        padding: 8px 6px;
        font-size: 12px;
    }

    .tabla-reporte td {
        border: 1px solid #9ca3af;
        vertical-align: middle;
        padding: 8px 6px;
        font-size: 12px;
    }

    .tabla-reporte tbody tr:hover td {
        background: #f3faf5;
    }

    .tabla-reporte .numero {
        text-align: right;
        white-space: nowrap;
    }

    .tabla-reporte .centro {
        text-align: center;
    }

    .fila-total td {
        font-weight: 700;
        background: #f1f5f2;
    }

    .texto-inferior {
        margin-top: 22px;
        font-size: 12px;
        line-height: 1.6;
        text-align: justify;
    }

    .nota-reporte {
        margin-top: 14px;
        font-size: 12px;
    }

    .atentamente {
        margin-top: 25px;
        font-size: 12px;
    }

    @media print {

        @page {
            size: Letter landscape;
            margin: 0.8cm;
        }

        .no-print,
        .menu-lateral,
        .barra-superior {
            display: none !important;
        }

        .pagina {
            margin-left: 0 !important;
        }

        .contenido,
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            max-width: none !important;
            width: 100% !important;
        }

        body {
            background: #ffffff !important;
            font-size: 10px;
        }

        .documento-reporte {
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .encabezado-reporte {
            min-height: 3cm;
        }

        .tabla-reporte {
            width: 100% !important;
            table-layout: fixed;
        }

        .tabla-reporte th,
        .tabla-reporte td {
            padding: 5px 4px !important;
            font-size: 9px !important;
            white-space: normal !important;
            word-break: normal;
        }

        .tabla-reporte thead {
            display: table-header-group;
        }

        .tabla-reporte tr {
            page-break-inside: avoid;
        }

        .texto-inferior,
        .nota-reporte,
        .atentamente {
            font-size: 10px;
        }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-end gap-2 flex-wrap mb-3 no-print">


    <a href="{{ route('reportes.aperturas.excel', request()->query()) }}"
       class="btn btn-outline-success">
        <i class="bi bi-file-earmark-excel me-1"></i>
        Excel
    </a>

    <button type="button"
            onclick="window.print()"
            class="btn btn-outline-success">
        <i class="bi bi-printer me-1"></i>
        Imprimir
    </button>

    <button type="button"
        class="btn btn-danger"
        onclick="exportarPdf()">
    <i class="bi bi-file-earmark-pdf me-1"></i>
    Exportar PDF
</button>

</div>

<div class="container-fluid">

    {{-- TÍTULO DE LA PANTALLA --}}
    <div class="mb-4 no-print">

        <h3 class="reporte-titulo mb-1">
            <i class="bi bi-diagram-3 me-2"></i>
            Reporte por apertura programática
        </h3>

        <p class="text-muted mb-0">
            Consulta el consumo de combustible registrado por apertura programática.
        </p>

    </div>


    {{-- FILTROS --}}
    <div class="card reporte-card shadow-sm mb-4 no-print">

        <div class="card-body">

            <h5 class="reporte-titulo mb-3">
                <i class="bi bi-funnel me-2"></i>
                Filtros
            </h5>

            <form method="GET"
                  action="{{ route('reportes.aperturas') }}">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Apertura programática
                        </label>

                        <select name="apertura_programatica_id"
                                class="form-select">

                            <option value="">
                                Todas las aperturas
                            </option>

                            @foreach($aperturas as $apertura)

                                <option value="{{ $apertura->id }}"
                                    {{ request('apertura_programatica_id') == $apertura->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $apertura->codigo }}

                                    @if($apertura->descripcion)
                                        - {{ $apertura->descripcion }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            Período
                        </label>

                        <select name="periodo_id"
                                class="form-select">

                            <option value="">
                                Todos los períodos
                            </option>

                            @php
                                $nombresMeses = [
                                    1 => 'Enero',
                                    2 => 'Febrero',
                                    3 => 'Marzo',
                                    4 => 'Abril',
                                    5 => 'Mayo',
                                    6 => 'Junio',
                                    7 => 'Julio',
                                    8 => 'Agosto',
                                    9 => 'Septiembre',
                                    10 => 'Octubre',
                                    11 => 'Noviembre',
                                    12 => 'Diciembre',
                                ];
                            @endphp

                            @foreach($periodos as $periodo)

                                <option value="{{ $periodo->id }}"
                                    {{ request('periodo_id') == $periodo->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $nombresMeses[$periodo->mes] ?? $periodo->mes }}
                                    {{ $periodo->anio }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Fecha inicial
                        </label>

                        <input type="date"
                               name="fecha_inicio"
                               class="form-control"
                               value="{{ request('fecha_inicio') }}">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label fw-semibold">
                            Fecha final
                        </label>

                        <input type="date"
                               name="fecha_fin"
                               class="form-control"
                               value="{{ request('fecha_fin') }}">

                    </div>

                </div>


                <div class="mt-3 d-flex gap-2">

                    <button type="submit"
                            class="btn btn-verde">

                        <i class="bi bi-search me-1"></i>
                        Consultar

                    </button>

                    <a href="{{ route('reportes.aperturas') }}"
                       class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Limpiar

                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- RESUMEN EN PANTALLA --}}
    <div class="row g-3 mb-4 no-print">

        <div class="col-md-3">
            <div class="resumen-card">
                <small>Vehículos atendidos</small>
                <h4>
                    {{ number_format($totalVehiculos, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="resumen-card">
                <small>Vales utilizados</small>
                <h4>
                    {{ number_format($totalVales, 0, ',', '.') }}
                </h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="resumen-card">
                <small>Total de litros</small>
                <h4>
                    {{ number_format($totalLitros, 2, ',', '.') }} L
                </h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="resumen-card">
                <small>Monto total</small>
                <h4>
                    Bs {{ number_format($totalMonto, 2, ',', '.') }}
                </h4>
            </div>
        </div>

    </div>

   {{-- TEXTOS PERSONALIZABLES DEL REPORTE --}}
<div class="card reporte-card shadow-sm mb-4 no-print">
    <div class="card-body">

        <h5 class="reporte-titulo mb-3">
            <i class="bi bi-pencil-square me-2"></i>
            Texto del documento
        </h5>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Texto del informe
            </label>

            <textarea
                id="textoInformeInput"
                class="form-control"
                rows="3"
                placeholder="Escriba el texto que acompañará al reporte..."
                oninput="actualizarTextosReporte()"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">
                Nota
            </label>

            <textarea
                id="notaReporteInput"
                class="form-control"
                rows="2"
                placeholder="Escriba una nota si corresponde..."
                oninput="actualizarTextosReporte()"></textarea>

            <small class="text-muted">
                Opcional. Si se deja vacío, no aparecerá en el documento.
            </small>
        </div>

        <div>
            <label class="form-label fw-semibold">
                Cierre
            </label>

            <input
                type="text"
                id="cierreReporteInput"
                class="form-control"
                value="Atentamente."
                oninput="actualizarTextosReporte()">
        </div>

    </div>
</div>

        {{-- ENCABEZADO INSTITUCIONAL --}}
        <div class="encabezado-reporte">

           <img
    src="{{ asset('images/logo-sacaba.jpg') }}"
    class="logo-sacaba"
    alt="Gobierno Autónomo Municipal de Sacaba"
>

<img
    src="{{ asset('images/logo-chapare.png') }}"
    class="logo-chapare"
    alt="Provincia Chapare"
>

            <div class="encabezado-centro">

                <div class="secretaria">
                    Secretaría Municipal de Finanzas y Administración
                </div>

                <div class="direccion">
                    Dirección Administrativa
                </div>

            </div>

        </div>


        {{-- FECHA --}}
        <div class="fecha-reporte">

            Sacaba,
            {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}

        </div>


        {{-- TÍTULO --}}
        <div class="titulo-documento">
            Ref.: Reporte asignación de combustible por apertura programática
        </div>

       <div class="subtitulo-documento">
    Detalle de asignación de combustible
</div>


        <div class="detalle-asignacion">
            Detalle de asignación:
        </div>


        {{-- TABLA OFICIAL --}}
        <div class="table-responsive">

            <table class="tabla-reporte">

                <thead>

                    <tr>

                        <th style="width: 15%;">
                            APERTURA<br>
                            PROGRAMÁTICA
                        </th>

                        <th style="width: 8%;">
                            FF
                        </th>

                        <th style="width: 8%;">
                            OF
                        </th>

                        <th style="width: 31%;">
                            NOMBRE DE LA UNIDAD O PROYECTO
                        </th>

                        <th style="width: 13%;">
                            CANTIDAD<br>
                            LITROS
                        </th>

                        <th style="width: 12%;">
                            PRECIO UNITARIO<br>
                            (Bs./Litro)
                        </th>

                        <th style="width: 13%;">
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
                                {{ number_format(
                                    $fila->litros,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td class="numero">
                                {{ number_format(
                                    $fila->precio_unitario,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td class="numero fw-semibold">
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

                            <td colspan="7"
                                class="text-center text-muted py-4">

                                No existen registros para los filtros seleccionados.

                            </td>

                        </tr>

                    @endforelse


                    @if($reporte->count() > 0)

                        <tr class="fila-total">

                            <td colspan="4"
                                class="text-end">

                                TOTAL

                            </td>

                            <td class="numero">

                                {{ number_format(
                                    $totalLitros,
                                    2,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td></td>

                            <td class="numero">

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


      {{-- TEXTO PERSONALIZADO DEL DOCUMENTO --}}
<div
    id="textoInformeDocumento"
    class="texto-inferior"
    style="display: none;">
</div>

<div
    id="notaReporteDocumento"
    class="nota-reporte"
    style="display: none;">

    <strong>NOTA:</strong>
    <span id="notaReporteTexto"></span>

</div>

<div
    id="cierreReporteDocumento"
    class="atentamente">

    Atentamente.

</div>

    </div>

</div>

<script>
function actualizarTextosReporte() {

    const texto = document
        .getElementById('textoInformeInput')
        .value
        .trim();

    const nota = document
        .getElementById('notaReporteInput')
        .value
        .trim();

    const cierre = document
        .getElementById('cierreReporteInput')
        .value
        .trim();


    // TEXTO DEL INFORME
    const textoDocumento =
        document.getElementById('textoInformeDocumento');

    if (texto !== '') {

        textoDocumento.textContent = texto;
        textoDocumento.style.display = 'block';

    } else {

        textoDocumento.textContent = '';
        textoDocumento.style.display = 'none';

    }


    // NOTA
    const notaDocumento =
        document.getElementById('notaReporteDocumento');

    const notaTexto =
        document.getElementById('notaReporteTexto');

    if (nota !== '') {

        notaTexto.textContent = nota;
        notaDocumento.style.display = 'block';

    } else {

        notaTexto.textContent = '';
        notaDocumento.style.display = 'none';

    }


    // CIERRE
    const cierreDocumento =
        document.getElementById('cierreReporteDocumento');

    if (cierre !== '') {

        cierreDocumento.textContent = cierre;
        cierreDocumento.style.display = 'block';

    } else {

        cierreDocumento.textContent = '';
        cierreDocumento.style.display = 'none';

    }
}

document.addEventListener('DOMContentLoaded', function () {
    actualizarTextosReporte();
});
</script>

<script>
function exportarPdf() {

    const params = new URLSearchParams();

    // FILTROS ACTUALES
    const apertura = document.querySelector(
        '[name="apertura_programatica_id"]'
    )?.value;

    const periodo = document.querySelector(
        '[name="periodo_id"]'
    )?.value;

    const fechaInicio = document.querySelector(
        '[name="fecha_inicio"]'
    )?.value;

    const fechaFin = document.querySelector(
        '[name="fecha_fin"]'
    )?.value;


    if (apertura) {
        params.append('apertura_programatica_id', apertura);
    }

    if (periodo) {
        params.append('periodo_id', periodo);
    }

    if (fechaInicio) {
        params.append('fecha_inicio', fechaInicio);
    }

    if (fechaFin) {
        params.append('fecha_fin', fechaFin);
    }


    // TEXTOS PERSONALIZADOS
    const textoInforme =
        document.getElementById('textoInformeInput')?.value ?? '';

    const notaReporte =
        document.getElementById('notaReporteInput')?.value ?? '';

    const cierreReporte =
        document.getElementById('cierreReporteInput')?.value ?? '';


    params.append('texto_informe', textoInforme);
    params.append('nota_reporte', notaReporte);
    params.append('cierre_reporte', cierreReporte);


    // GENERAR PDF
    const url =
        "{{ route('reportes.aperturas.pdf') }}"
        + '?'
        + params.toString();

    window.location.href = url;
}
</script>

@endsection