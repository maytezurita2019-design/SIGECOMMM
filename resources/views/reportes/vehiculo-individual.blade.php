
@extends(request()->boolean('fragment') ? 'reportes.layouts.fragmento' : 'layouts.app')
@include('reportes.partials.auto-print')

@section('title', 'Reporte Individual por Vehículo')

@push('styles')
<style>
    :root {
        --verde-principal: #08712f;
        --verde-oscuro: #075326;
        --verde-claro: #e7f4eb;
    }

    .reporte-titulo {
        color: var(--verde-oscuro);
        font-weight: 700;
    }

    .reporte-card {
        border: 1px solid #d6e2da;
        border-radius: 12px;
        background: #fff;
    }

    .btn-verde {
        background: var(--verde-principal);
        border-color: var(--verde-principal);
        color: #fff;
    }

    .btn-verde:hover {
        background: var(--verde-oscuro);
        border-color: var(--verde-oscuro);
        color: #fff;
    }

    .documento-reporte {
        background: #fff;
        border: 1px solid #d6e2da;
        border-radius: 10px;
        padding: 25px;
    }

    /* ENCABEZADO */
    .encabezado-documento {
        display: grid;
        grid-template-columns: 200px 1fr 150px;
        align-items: center;
        gap: 15px;
        margin-bottom: 5px;
    }

    .logo-documento img {
        max-width: 180px;
        max-height: 80px;
        object-fit: contain;
    }

    .logo-documento.derecha {
        text-align: right;
    }

    .logo-documento.derecha img {
        max-width: 90px;
    }

    .institucion-documento {
        text-align: center;
        color: var(--verde-oscuro);
        font-weight: 800;
        font-size: 14px;
        line-height: 1.4;
    }

    .fecha-documento {
        text-align: right;
        font-size: 12px;
        margin: 5px 0 10px;
    }

    .referencia-documento {
        text-align: center;
        color: var(--verde-oscuro);
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 15px;
    }
.texto-editable {
    width: 100%;
    min-height: 75px;
    resize: vertical;

    border: 1px solid #cbd8cf;
    border-radius: 6px;

    padding: 10px;

    font-size: 13px;
    font-family: Arial, sans-serif;
    line-height: 1.5;

    /* IMPORTANTE: texto visible */
    color: #212529 !important;
    -webkit-text-fill-color: #212529 !important;
    caret-color: #212529 !important;

    background-color: #ffffff !important;
    opacity: 1 !important;
}
  .texto-editable:focus {
    color: #212529 !important;
    -webkit-text-fill-color: #212529 !important;
    background-color: #ffffff !important;

    border-color: var(--verde-principal);
    outline: none;
    box-shadow: 0 0 0 2px rgba(8, 113, 47, .10);
}

    /*
     * Este texto se actualiza mientras el usuario escribe.
     * También es el texto que se muestra al imprimir.
     */
    .texto-impreso {
        display: none;
        white-space: pre-line;
        text-align: justify;
        line-height: 1.5;
        font-size: 13px;
        margin: 8px 0 12px;
    }

    /* TÍTULOS */
    .titulo-seccion {
        margin-top: 16px;
        margin-bottom: 5px;
        padding: 7px 9px;
        background: var(--verde-claro);
        border-left: 4px solid var(--verde-principal);
        color: var(--verde-oscuro);
        font-weight: 800;
        font-size: 14px;
    }

    /* TABLA VEHÍCULO */
    .tabla-vehiculo {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-bottom: 15px;
    }

    .tabla-vehiculo th {
        background: var(--verde-principal);
        color: #fff;
        border: 1px solid var(--verde-oscuro);
        text-align: center;
        vertical-align: middle;
        padding: 8px 4px;
        font-size: 11px;
    }

    .tabla-vehiculo td {
        border: 1px solid #555;
        text-align: center;
        vertical-align: middle;
        padding: 9px 4px;
        font-size: 11px;
        font-weight: 600;
    }

    /* TABLA DETALLE */
    .tabla-reporte {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 11px;
    }

    .tabla-reporte th {
        background: var(--verde-principal);
        color: #fff;
        border: 1px solid var(--verde-oscuro);
        text-align: center;
        vertical-align: middle;
        padding: 7px 3px;
        line-height: 1.2;
    }

    .tabla-reporte td {
        border: 1px solid #555;
        vertical-align: middle;
        padding: 6px 4px;
    }

    .fila-total td {
        background: var(--verde-claro);
        font-weight: 800;
    }

    .fila-total .celda-total {
        background: var(--verde-principal);
        color: #fff;
        text-align: center;
    }

    .firma-final {
        margin-top: 15px;
        font-size: 12px;
    }

    /* IMPRESIÓN */
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
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .pagina {
            margin-left: 0 !important;
        }

        .contenido,
        .container-fluid {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .documento-reporte {
            border: 0 !important;
            border-radius: 0 !important;
            padding: 0 !important;
        }

        .encabezado-documento {
            grid-template-columns: 190px 1fr 120px;
        }

        .logo-documento img {
            max-width: 165px;
            max-height: 65px;
        }

        .logo-documento.derecha img {
            max-width: 70px;
        }

        .institucion-documento {
            color: #000;
            font-size: 11px;
        }

        .fecha-documento {
            font-size: 9px;
            margin: 2px 0 5px;
        }

        .referencia-documento {
            color: #000;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .texto-editable {
            display: none !important;
        }

        .texto-impreso {
            display: block !important;
            font-size: 9px;
            margin: 5px 0 8px;
        }

        .titulo-seccion {
            margin: 6px 0 3px;
            padding: 4px 5px;
            font-size: 9px;
            background: var(--verde-claro) !important;
            color: #000 !important;
            border-left: 3px solid var(--verde-principal) !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .tabla-vehiculo {
            margin-bottom: 7px;
        }

        .tabla-vehiculo th {
            background: var(--verde-principal) !important;
            color: #fff !important;
            border: 1px solid #000 !important;
            padding: 4px 2px;
            font-size: 7px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .tabla-vehiculo td {
            border: 1px solid #000 !important;
            padding: 4px 2px;
            font-size: 7px;
        }

        .tabla-reporte {
            font-size: 7px;
        }

        .tabla-reporte th {
            background: var(--verde-principal) !important;
            color: #fff !important;
            border: 1px solid #000 !important;
            padding: 4px 2px;
            font-size: 7px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .tabla-reporte td {
            border: 1px solid #000 !important;
            padding: 3px 2px;
            font-size: 7px;
        }

        .fila-total td {
            background: var(--verde-claro) !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .fila-total .celda-total {
            background: var(--verde-principal) !important;
            color: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .firma-final {
            margin-top: 8px;
            font-size: 9px;
        }

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
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- TÍTULO --}}
    <div class="mb-4 no-print">
        <h3 class="reporte-titulo mb-1">
            <i class="bi bi-car-front-fill me-2"></i>
            Reporte Individual por Vehículo
        </h3>

        <p class="text-muted mb-0">
            Consulte el historial de asignación y consumo de combustible
            de un vehículo específico.
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
                  action="{{ route('reportes.vehiculo-individual') }}">
                <input type="hidden" name="ambito" value="{{ request('ambito', 'todos') }}">

                <div class="row g-3">

                    {{-- VEHÍCULO --}}
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">
                            Vehículo
                        </label>

                        <select name="vehiculo_id"
                                class="form-select"
                                required>

                            <option value="">
                                Seleccione un vehículo
                            </option>

                            @foreach($vehiculos as $item)
                                <option value="{{ $item->id }}"
                                    {{ request('vehiculo_id') == $item->id ? 'selected' : '' }}>

                                    {{ $item->codigo }} - {{ $item->placa }}

                                    @if($item->unidad)
                                        - {{ $item->unidad->nombre ?? '' }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FECHA INICIAL --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            Fecha inicial
                        </label>

                        <input
                            type="date"
                            id="fecha_inicio"
                            name="fecha_inicio"
                            class="form-control"
                            value="{{ request('fecha_inicio') }}"
                            onclick="this.showPicker()"
                        >
                    </div>

                    {{-- FECHA FINAL --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            Fecha final
                        </label>

                        <input
                            type="date"
                            id="fecha_fin"
                            name="fecha_fin"
                            class="form-control"
                            value="{{ request('fecha_fin') }}"
                            onclick="this.showPicker()"
                        >
                    </div>

                    {{-- CONSULTAR --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit"
                                class="btn btn-verde w-100">
                            <i class="bi bi-search me-1"></i>
                            Consultar
                        </button>
                    </div>

                </div>

                <div class="mt-3">
                    <a href="{{ route('reportes.vehiculo-individual') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Limpiar filtros
                    </a>
                </div>

            </form>
        </div>
    </div>

    @if(!$vehiculo)

        <div class="alert alert-info no-print">
            <i class="bi bi-info-circle me-2"></i>
            Seleccione un vehículo para generar el reporte individual.
        </div>

    @else

        {{-- BOTONES --}}
        <div class="d-flex justify-content-end gap-2 mb-3 no-print">

            <button type="button"
                    onclick="prepararImpresion()"
                    class="btn btn-outline-success">
                <i class="bi bi-printer me-1"></i>
                Imprimir
            </button>

            <button type="button"
                    onclick="generarPdf()"
                    class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                PDF
            </button>

            <button type="button"
                    onclick="generarExcel()"
                    class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel me-1"></i>
                Excel
            </button>

        </div>

        {{-- DOCUMENTO --}}
        <div class="documento-reporte">

            {{-- ENCABEZADO --}}
            <div class="encabezado-documento">

                <div class="logo-documento">
                    <img src="{{ asset('images/logo-sacaba.jpg') }}"
                         alt="Logo Sacaba">
                </div>

                <div class="institucion-documento">
                    SECRETARIA MUNICIPAL DE FINANZAS Y ADMINISTRACION
                    <br>
                    DIRECCION ADMINISTRATIVA
                </div>

                <div class="logo-documento derecha">
                    <img src="{{ asset('images/logo-chapare.png') }}"
                         alt="Logo Chapare">
                </div>

            </div>

            {{-- FECHA --}}
            <div class="fecha-documento">
                Sacaba,
                {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}
            </div>

            {{-- REFERENCIA --}}
            <div class="referencia-documento">
                REF.: REPORTE ASIGNACIÓN DE COMBUSTIBLE
            </div>

            {{-- TEXTO SUPERIOR --}}
            <div class="no-print mb-3">
                <label class="form-label fw-semibold">
                    Texto superior del reporte
                </label>

                <textarea
                    id="textoSuperior"
                    class="texto-editable"
                    placeholder="Escriba aquí el texto que desea que aparezca antes de los datos del vehículo..."
                >{{ request('texto_superior', '') }}</textarea>

                <small class="text-muted">
                    Este texto aparecerá en la impresión y PDF.
                </small>
            </div>

            <div id="textoSuperiorImpreso"
                 class="texto-impreso"></div>

            {{-- DATOS DEL VEHÍCULO --}}
            <div class="titulo-seccion">
                DATOS RELATIVOS AL VEHÍCULO
            </div>

            <div class="table-responsive">
                <table class="tabla-vehiculo">

                    <thead>
                        <tr>
                            <th>TIPO</th>
                            <th>CÓD. ASIGNADO</th>
                            <th>INDUSTRIA</th>
                            <th>COLOR</th>
                            <th>PLACA</th>
                            <th>MARCA</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>{{ $vehiculo->tipo ?: '-' }}</td>
                            <td>{{ $vehiculo->codigo ?: '-' }}</td>
                            <td>{{ $vehiculo->industria ?: '-' }}</td>
                            <td>{{ $vehiculo->color ?: '-' }}</td>
                            <td>{{ $vehiculo->placa ?: '-' }}</td>
                            <td>{{ $vehiculo->marca ?: '-' }}</td>
                        </tr>
                    </tbody>

                </table>
            </div>

            {{-- DETALLE --}}
            <div class="titulo-seccion">
                DETALLE DE ASIGNACIÓN:
            </div>

            <div class="table-responsive">
                <table class="tabla-reporte">

                    <thead>
                        <tr>
                            <th style="width: 9%;">FECHA</th>
                            <th style="width: 8%;">VALE</th>
                            <th style="width: 10%;">CANTIDAD<br>(LT)</th>
                            <th style="width: 9%;">PRECIO<br>(LT)</th>
                            <th style="width: 10%;">TOTAL<br>(Bs)</th>
                            <th style="width: 11%;">N.º FACTURA</th>
                            <th style="width: 11%;">KILOMETRAJE</th>
                            <th style="width: 21%;">DETALLE DE ASIGNACIÓN</th>
                            <th style="width: 11%;">APERTURA<br>PROGRAM.</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($vales as $vale)

                            <tr>
                                <td class="text-center">
                                    {{ $vale->fecha
                                        ? $vale->fecha->format('d/m/Y')
                                        : '-' }}
                                </td>

                                <td class="text-center fw-semibold">
                                    {{ sprintf('%06d', (int) $vale->numero_vale) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        (float) $vale->cantidad_litros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        (float) $vale->precio_unitario,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        (float) $vale->total,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="text-center">
                                    {{ $vale->numero_factura ?: '-' }}
                                </td>

                                <td class="text-end">
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

                                <td class="text-center">
                                    {{ $vale->detalle_asignacion ?: '—' }}
                                </td>

                                <td class="text-center">
                                    {{ $vale->aperturaProgramatica->codigo ?? '-' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="9"
                                    class="text-center text-muted py-4">
                                    No existen registros de combustible
                                    para este vehículo en el período seleccionado.
                                </td>
                            </tr>

                        @endforelse

                        @if($vales->isNotEmpty())
                            <tr class="fila-total">

                                <td colspan="2"
                                    class="celda-total">
                                    TOTAL
                                </td>

                                <td class="text-end">
                                    {{ number_format(
                                        $totalLitros,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td></td>

                                <td class="text-end">
                                    {{ number_format(
                                        $totalMonto,
                                        2,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td colspan="4"></td>
                            </tr>
                        @endif

                    </tbody>

                </table>
            </div>

            {{-- TEXTO INFERIOR --}}
            <div class="no-print mt-4">
                <label class="form-label fw-semibold">
                    Texto final del reporte
                </label>

                <textarea
                    id="textoInferior"
                    class="texto-editable"
                    placeholder="Escriba aquí el texto que desea que aparezca después de la tabla..."
                >{{ request('texto_inferior', '') }}</textarea>

                <small class="text-muted">
                    Este texto aparecerá debajo de la tabla al imprimir y en PDF.
                </small>
            </div>

            <div id="textoInferiorImpreso"
                 class="texto-impreso mt-3"></div>

            {{-- FIRMA --}}
            <div class="firma-final">
                Atentamente.
            </div>

        </div>

    @endif

</div>
@endsection

@push('scripts')
<script>
    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR TEXTOS DEL REPORTE
    |--------------------------------------------------------------------------
    */

    function actualizarTexto(origenId, destinoId) {
        const origen = document.getElementById(origenId);
        const destino = document.getElementById(destinoId);

        if (!origen || !destino) {
            return;
        }

        const contenido = origen.value.trim();

        destino.textContent = contenido;
    }

    function actualizarTextosReporte() {
        actualizarTexto(
            'textoSuperior',
            'textoSuperiorImpreso'
        );

        actualizarTexto(
            'textoInferior',
            'textoInferiorImpreso'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', function () {

        const textoSuperior =
            document.getElementById('textoSuperior');

        const textoInferior =
            document.getElementById('textoInferior');

        textoSuperior?.addEventListener(
            'input',
            actualizarTextosReporte
        );

        textoInferior?.addEventListener(
            'input',
            actualizarTextosReporte
        );

        actualizarTextosReporte();
    });


    /*
    |--------------------------------------------------------------------------
    | IMPRIMIR
    |--------------------------------------------------------------------------
    */

    function prepararImpresion() {
        actualizarTextosReporte();
        window.print();
    }


    /*
    |--------------------------------------------------------------------------
    | PARÁMETROS COMUNES
    |--------------------------------------------------------------------------
    */

    function obtenerParametrosReporte(incluirTextos = false) {

        const vehiculoId =
            @json(request('vehiculo_id'));

        const fechaInicio =
            @json(request('fecha_inicio'));

        const fechaFin =
            @json(request('fecha_fin'));

        if (!vehiculoId) {
            alert('Seleccione un vehículo primero.');
            return null;
        }

        const parametros = new URLSearchParams();

        parametros.set(
            'ambito',
            @json(request('ambito', 'todos'))
        );

        parametros.set(
            'vehiculo_id',
            vehiculoId
        );

        if (fechaInicio) {
            parametros.set(
                'fecha_inicio',
                fechaInicio
            );
        }

        if (fechaFin) {
            parametros.set(
                'fecha_fin',
                fechaFin
            );
        }

        if (incluirTextos) {

            const textoSuperior =
                document.getElementById('textoSuperior')
                    ?.value
                    ?.trim() ?? '';

            const textoInferior =
                document.getElementById('textoInferior')
                    ?.value
                    ?.trim() ?? '';

            if (textoSuperior) {
                parametros.set(
                    'texto_superior',
                    textoSuperior
                );
            }

            if (textoInferior) {
                parametros.set(
                    'texto_inferior',
                    textoInferior
                );
            }
        }

        return parametros;
    }


    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    function generarPdf() {

        const parametros =
            obtenerParametrosReporte(true);

        if (!parametros) {
            return;
        }

        const url =
            @json(route('reportes.vehiculo-individual.pdf'))
            + '?'
            + parametros.toString();

        window.location.href = url;
    }


    /*
    |--------------------------------------------------------------------------
    | EXCEL
    |--------------------------------------------------------------------------
    */

    function generarExcel() {

        const parametros =
            obtenerParametrosReporte(false);

        if (!parametros) {
            return;
        }

        const url =
            @json(route('reportes.vehiculo-individual.excel'))
            + '?'
            + parametros.toString();

        window.location.href = url;
    }
</script>
@endpush