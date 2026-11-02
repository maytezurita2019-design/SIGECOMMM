@extends(request()->boolean('fragment') ? 'reportes.layouts.fragmento' : 'layouts.app')
@include('reportes.partials.auto-print')
@section('title', 'Reporte mensual por vehículo · ' . $ambitoLabel)
@section('content')
{{-- Encabezado institucional para impresión --}}
<div class="encabezado-impresion">

    <div class="logo-izquierdo">
        <img src="{{ asset('images/logo-sacaba.jpg') }}"
             alt="Logo Gobierno Autónomo Municipal de Sacaba">
    </div>

    <div class="titulo-institucional">
        <strong>
            SECRETARÍA MUNICIPAL DE FINANZAS Y ADMINISTRACIÓN
        </strong>

        <div>DIRECCIÓN ADMINISTRATIVA</div>

        <h3>
            REPORTE DE CONSUMO DE COMBUSTIBLE · {{ mb_strtoupper($ambitoLabel) }}
            POR VEHÍCULO
        </h3>

        <p>
            Período:
            {{ $periodLabel }}
        </p>
    </div>

    <div class="logo-derecho">
        <img src="{{ asset('images/logo-chapare.png') }}"
             alt="Escudo institucional">
    </div>

</div>
<div class="container-fluid">
  <div class="card shadow-sm tarjeta-filtros" style="border-top:4px solid #287a55">
    <div class="card-header bg-white"><h4 class="mb-0" style="color:#173f32">Reporte {{ $periodicidad === 'anual' ? 'anual' : ($periodicidad === 'personalizado' ? 'por rango' : 'mensual') }} · Por vehículo · {{ $ambitoLabel }}</h4><small>Incluye únicamente vales consumidos según el ámbito seleccionado.</small></div>
    <div class="card-body">
      <form method="GET" action="{{ route('reportes.policia-vehiculos') }}" id="filtrosPolicia">
        <div class="form-group">
          <label>Ámbito</label>
          <select class="form-control" name="ambito">
            <option value="todos" @selected($ambito === 'todos')>Todos (GAMS y Policía)</option>
            <option value="gams" @selected($ambito === 'gams')>GAMS</option>
            <option value="policia" @selected($ambito === 'policia')>Policía</option>
          </select>
        </div>
        <div class="row">
          <div class="col-md-2 form-group"><label>Mes</label><select class="form-control" name="mes">@foreach(range(1,12) as $m)<option value="{{ $m }}" @selected($mes==$m)>{{ \Carbon\Carbon::create(2026,$m,1)->locale('es')->translatedFormat('F') }}</option>@endforeach</select></div>
          <div class="col-md-2 form-group"><label>Año</label><input class="form-control" name="anio" type="number" min="2000" max="2100" value="{{ $anio }}"></div>
          <div class="col-md-3 form-group"><label>Combustible</label><select class="form-control" name="combustible"><option value="todos">Gasolina y diésel</option><option value="gasolina" @selected($combustible==='gasolina')>Gasolina</option><option value="diesel" @selected($combustible==='diesel')>Diésel</option></select></div>
          <div class="col-md-5 form-group"><label>Vehículo</label><select class="form-control" name="vehiculo_id"><option value="">Todos los vehículos</option>@foreach($vehiculos as $v)<option value="{{ $v->id }}" @selected($vehiculo_id==$v->id)>{{ $v->codigo }} · {{ $v->placa }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label>Observaciones para PDF / Excel (opcional)</label><textarea name="observaciones" maxlength="2000" class="form-control" rows="2" placeholder="Redacte aquí una aclaración institucional...">{{ $observaciones }}</textarea></div>
        <button class="btn btn-success" type="submit"><i class="bi bi-search me-1" aria-hidden="true"></i>Consultar</button>
        <button class="btn btn-outline-danger" type="submit" formaction="{{ route('reportes.policia-vehiculos.pdf') }}"><i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>Descargar PDF</button>
        <button class="btn btn-outline-success" type="submit" formaction="{{ route('reportes.policia-vehiculos.excel') }}"><i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>Descargar Excel</button>
        <button class="btn btn-outline-secondary" type="button" id="imprimirPolicia"><i class="bi bi-printer me-1" aria-hidden="true"></i>Imprimir</button>
      </form>
    </div>
  </div>
  
{{-- Ficha institucional del vehículo --}}
@if(!empty($esReporteIndividual) && !empty($vehiculoSeleccionado))
<div class="card mb-3 ficha-vehiculo">
  <div class="card-body">
    <h5 class="seccion-institucional">DATOS RELATIVOS AL VEHÍCULO</h5>
    <div class="table-responsive">
      <table class="table table-bordered tabla-institucional">
        <thead><tr><th>TIPO</th><th>CÓD. ASIGNADO</th><th>INDUSTRIA</th><th>COLOR</th><th>PLACA</th><th>MARCA</th></tr></thead>
        <tbody><tr>
          <td>{{ $vehiculoSeleccionado->tipo ?: '—' }}</td>
          <td>{{ $vehiculoSeleccionado->codigo ?: '—' }}</td>
          <td>{{ $vehiculoSeleccionado->industria ?: '—' }}</td>
          <td>{{ $vehiculoSeleccionado->color ?: '—' }}</td>
          <td>{{ $vehiculoSeleccionado->placa ?: '—' }}</td>
          <td>{{ $vehiculoSeleccionado->marca ?: '—' }}</td>
        </tr></tbody>
      </table>
    </div>
  </div>
</div>
@endif

  <div class="row mb-3">
    <div class="col-md-3"><div class="alert alert-light border"><strong>Vales utilizados:</strong> {{ $totalVales }}</div></div>
    <div class="col-md-3"><div class="alert alert-light border"><strong>Vehículos:</strong> {{ $totalVehiculos }}</div></div>
    <div class="col-md-3"><div class="alert alert-light border"><strong>Litros:</strong> {{ number_format($totalLitros,2,',','.') }}</div></div>
    <div class="col-md-3"><div class="alert alert-light border"><strong>Total Bs:</strong> {{ number_format($totalBs,2,',','.') }}</div></div>
  </div>
  @if($sinPrecio>0)<div class="alert alert-warning">{{ $sinPrecio }} consumo(s) no tienen precio unitario registrado. Verifique los importes históricos antes de utilizar este reporte como documento contable.</div>@endif
   <div class="card reporte-tabla"><div class="card-body table-responsive">
    <h5 class="seccion-institucional">{{ !empty($esReporteIndividual) ? 'DETALLE DE ASIGNACIÓN' : 'CONSUMO DE COMBUSTIBLE POR VEHÍCULO' }}</h5>
    <table class="table table-sm table-bordered tabla-institucional">
      <thead><tr>
        <th>FECHA</th><th>VALE</th>
        @if(empty($esReporteIndividual))<th>CÓDIGO</th><th>PLACA</th>@endif
        <th>COMBUSTIBLE</th><th>CANTIDAD (LT)</th><th>PRECIO (LT)</th><th>TOTAL (Bs)</th>
        @if(!empty($esReporteIndividual))<th>N.º DE FACTURA</th><th>KILOMETRAJE</th><th>DETALLE DE ASIGNACIÓN</th>@endif
        <th>APERTURA PROGRAM.</th>
      </tr></thead>
      <tbody>
        @forelse($filas as $f)
        <tr>
          <td>{{ $f['fecha'] }}</td><td>{{ $f['vale'] }}</td>
          @if(empty($esReporteIndividual))<td>{{ $f['codigo'] }}</td><td>{{ $f['placa'] }}</td>@endif
          <td>{{ $f['combustible'] }}</td>
          <td class="text-right">{{ number_format($f['litros'],2,',','.') }}</td>
          <td class="text-right">{{ $f['precio'] === null ? 'SIN DATO' : number_format($f['precio'],2,',','.') }}</td>
          <td class="text-right">{{ $f['total'] === null ? 'SIN DATO' : number_format($f['total'],2,',','.') }}</td>
          @if(!empty($esReporteIndividual))
            <td>{{ ($f['numero_factura'] ?? '') !== '' ? $f['numero_factura'] : '—' }}</td>
            <td>{{ $f['kilometraje'] ?? '—' }}</td>
            <td>{{ ($f['detalle_asignacion'] ?? '') !== '' ? $f['detalle_asignacion'] : '—' }}</td>
          @endif
          <td>{{ $f['apertura'] }}</td>
        </tr>
        @empty
        <tr><td colspan="{{ !empty($esReporteIndividual) ? 10 : 9 }}" class="text-center">No se encontraron consumos para los filtros seleccionados.</td></tr>
        @endforelse
      </tbody>
      <tfoot><tr class="font-weight-bold fila-total">
        <td colspan="{{ !empty($esReporteIndividual) ? 4 : 5 }}">TOTAL ({{ $totalVales }} vales)</td>
        <td class="text-right">{{ number_format($totalLitros,2,',','.') }}</td>
        <td class="text-right">{{ $precioResumen === null ? 'VARIABLE / SIN DATO' : number_format($precioResumen,2,',','.') }}</td>
        <td class="text-right">{{ number_format($totalBs,2,',','.') }}</td>
        <td colspan="{{ !empty($esReporteIndividual) ? 3 : 1 }}"></td>
      </tr></tfoot>
    </table>
    @if(trim($observaciones ?? '') !== '')
      <div class="observaciones-reporte"><strong>OBSERVACIONES:</strong> {{ $observaciones }}</div>
    @endif
   </div></div>
 </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filtrosPolicia');
    const printBtn = document.getElementById('imprimirPolicia');

    if (!form) return;

    const btnConsultar = form.querySelector('button[type="submit"]:not([formaction])');
    const btnPDF = form.querySelector('button[formaction*="/pdf"]');
    const btnExcel = form.querySelector('button[formaction*="/excel"]');

    const botones = [btnConsultar, btnPDF, btnExcel].filter(Boolean);

    const textosOriginales = new Map();

    botones.forEach(function (boton) {
        textosOriginales.set(boton, boton.innerHTML);
    });

    function restaurarBotones() {
        botones.forEach(function (boton) {
            boton.disabled = false;
            boton.removeAttribute('disabled');
            boton.innerHTML = textosOriginales.get(boton);
        });
    }

    function descargarSinBloquear(boton) {
        if (!boton) return;

        boton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            const url = boton.getAttribute('formaction');
            const parametros = new URLSearchParams(new FormData(form));
            const separador = url.includes('?') ? '&' : '?';

            // Descarga independiente del formulario principal.
            const enlace = document.createElement('a');
            enlace.href = url + separador + parametros.toString();
            enlace.target = '_blank';
            enlace.rel = 'noopener';

            document.body.appendChild(enlace);
            enlace.click();
            enlace.remove();

            restaurarBotones();
        }, true);
    }

    descargarSinBloquear(btnPDF);
    descargarSinBloquear(btnExcel);

    // Restaurar el botón Consultar al volver a la página.
    window.addEventListener('pageshow', function () {
        restaurarBotones();
    });

    if (printBtn) {
        printBtn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            window.print();
        }, true);
    }
});
</script>

<style>
.seccion-institucional{font-size:15px;font-weight:700;margin:0 0 12px;color:#172f4e}
.tabla-institucional{width:100%;border-collapse:collapse;font-size:12px}
.tabla-institucional th,.tabla-institucional td{border:1px solid #333 !important;padding:7px 5px;vertical-align:middle}
.tabla-institucional thead th{background:#315b91 !important;color:white !important;text-align:center;font-weight:700}
.tabla-institucional .fila-total td{background:#e8f0f8 !important;color:#182a3b !important;font-weight:700}
.observaciones-reporte{margin-top:15px;white-space:pre-wrap}
.encabezado-impresion {
    display: none;
}

@media print {

    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    .main-sidebar,
    .main-header,
    .main-footer,
    #filtrosPolicia,
    .tarjeta-filtros,
    .card-header,
    .alert {
        display: none !important;
    }

    .encabezado-impresion {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #287a55;
    }

    .logo-izquierdo,
    .logo-derecho {
        width: 16%;
        text-align: center;
    }

    .logo-izquierdo img,
    .logo-derecho img {
        max-width: 90px;
        max-height: 85px;
        object-fit: contain;
    }

    .titulo-institucional {
        width: 68%;
        text-align: center;
        font-size: 12px;
    }

    .titulo-institucional h3 {
        font-size: 15px;
        font-weight: bold;
        margin-top: 15px;
    }

    .content-wrapper,
    .wrapper,
    .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        box-shadow: none !important;
        border: 0 !important;
    }

    table {
        width: 100% !important;
        font-size: 8px;
        border-collapse: collapse;
    }

    .tabla-institucional thead th {
        background: #315b91 !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .tabla-institucional .fila-total td {
        background: #e8f0f8 !important;
        color: #182a3b !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .card-body { padding: 5px !important; }
    .seccion-institucional { font-size: 11px; }
    thead {
        display: table-header-group;
    }

    tr {
        break-inside: avoid;
    }
}

</style>
@stop
