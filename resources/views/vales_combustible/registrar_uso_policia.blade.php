@extends('layouts.app')

@section('title', 'Registrar uso Policía · SIGECOM')

@section('content')

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <div class="small text-success fw-bold text-uppercase mb-1">
            Combustible · Policía
        </div>

        <h1 class="h2 mb-1">
            Registrar uso · Vale N.º {{ sprintf('%06d', $valeCombustible->numero_vale) }}
        </h1>

        <p class="text-muted mb-0">
            Complete todos los datos correspondientes al uso del vale.
        </p>
    </div>

    <a href="{{ route('entregas-policia.show', $valeCombustible->entrega_policia_id) }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Volver
    </a>
</div>


@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('vales-combustible.update', $valeCombustible) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="row g-4">

        <div class="col-xl-8">

            {{-- DATOS DEL VALE --}}
            <div class="card mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-ticket-perforated me-2"></i>
                        Datos del vale
                    </h5>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Número de vale</label>

                            <input type="text"
                                   class="form-control"
                                   value="{{ sprintf('%06d', $valeCombustible->numero_vale) }}"
                                   disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Estado</label>

                            <input type="text"
                                   class="form-control"
                                   value="{{ $valeCombustible->estado }}"
                                   disabled>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Entrega Policía</label>

                            <input type="text"
                                   class="form-control"
                                   value="N.º {{ $valeCombustible->entrega_policia_id }}"
                                   disabled>
                        </div>

                    </div>

                </div>
            </div>


            {{-- VEHÍCULO --}}
            <div class="card mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-car-front me-2"></i>
                        Vehículo
                    </h5>

                    <div class="mb-3">

                        <label for="vehiculo_id" class="form-label">
                            Seleccionar vehículo *
                        </label>

                        <select name="vehiculo_id"
                                id="vehiculo_id"
                                class="form-select @error('vehiculo_id') is-invalid @enderror"
                                required>

                            <option value="">
                                Seleccione un vehículo...
                            </option>

                            @foreach($vehiculos as $vehiculo)

                                @php
                                    $ap = $vehiculo->aperturaProgramatica;
                                    $ff = $ap?->fuenteFinanciamiento;
                                    $of = $ap?->organismoFinanciador;
                                @endphp

                                <option
                                    value="{{ $vehiculo->id }}"

                                    data-codigo="{{ $vehiculo->codigo ?? '' }}"
                                    data-placa="{{ $vehiculo->placa ?? '' }}"
                                    data-tipo="{{ $vehiculo->tipo ?? '' }}"
                                    data-unidad="{{ $vehiculo->unidad?->nombre ?? '' }}"

                                    data-ap="{{ $ap?->codigo ?? '' }}"
                                    data-ff="{{ $ff?->codigo ?? '' }}"
                                    data-of="{{ $of?->codigo ?? '' }}"

                                    data-tiene-ap="{{ $ap ? '1' : '0' }}"

                                  @selected(
    old(
        'vehiculo_id',
        $valeCombustible->vehiculo_id
    ) == $vehiculo->id
)
                                >
                                    {{ $vehiculo->codigo }}

                                    @if($vehiculo->placa)
                                        - {{ $vehiculo->placa }}
                                    @endif

                                    @if($vehiculo->unidad?->nombre)
                                        - {{ $vehiculo->unidad->nombre }}
                                    @endif
                                </option>

                            @endforeach

                        </select>

                        @error('vehiculo_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- DATOS AUTOMÁTICOS --}}
                    <div id="datosVehiculo"
                         class="border rounded bg-light p-3 d-none">

                        <div class="small text-muted mb-3">
                            Datos obtenidos automáticamente
                        </div>

                        <div class="row g-3">

                            <div class="col-md-3">
                                <small class="text-muted d-block">Código</small>
                                <strong id="datoCodigo"></strong>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">Placa</small>
                                <strong id="datoPlaca"></strong>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">Tipo</small>
                                <strong id="datoTipo"></strong>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">Unidad</small>
                                <strong id="datoUnidad"></strong>
                            </div>

                        </div>

                        <hr>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Apertura Programática
                                </small>
                                <strong id="datoAP"></strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    FF
                                </small>
                                <strong id="datoFF"></strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    OF
                                </small>
                                <strong id="datoOF"></strong>
                            </div>

                        </div>

                        <hr>

                        <div>
                            <small class="text-muted d-block">
                                Detalle
                            </small>

                            <strong id="datoDetalle"></strong>
                        </div>

                        <div id="sinAP"
                             class="alert alert-warning mt-3 mb-0 d-none">

                            <i class="bi bi-exclamation-triangle me-1"></i>

                            Este vehículo no tiene una Apertura Programática asignada.
                        </div>

                    </div>

                </div>
            </div>


            {{-- DATOS DE USO --}}
            <div class="card mb-4">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Datos de uso
                    </h5>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label for="fecha" class="form-label">
                                Fecha *
                            </label>

                            <input type="date"
       name="fecha"
       id="fecha"
       value="{{ old(
           'fecha',
           $valeCombustible->fecha?->format('Y-m-d') ?? date('Y-m-d')
       ) }}"
       class="form-control @error('fecha') is-invalid @enderror"
       required>

                            @error('fecha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

    <label for="kilometraje" class="form-label">
        Kilometraje / Horómetro
    </label>

    <input type="text"
           name="kilometraje"
           id="kilometraje"
           maxlength="50"
           value="{{ old(
               'kilometraje',
               $valeCombustible->kilometraje
           ) }}"
           class="form-control @error('kilometraje') is-invalid @enderror"
           placeholder="Ej.: 306680 o NO FUNCIONA">

    <div class="form-text">
        Puede registrar un número, texto o dejarlo vacío.
    </div>

    @error('kilometraje')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

</div>


            {{-- DATOS DE COMBUSTIBLE --}}
            <div class="card">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-fuel-pump me-2"></i>
                        Datos de combustible y factura
                    </h5>

                    <div class="row g-3 mb-3">

    {{-- TIPO DE COMBUSTIBLE --}}
    <div class="col-md-6">

        <label for="tipo_combustible_id"
               class="form-label">
            Tipo de combustible *
        </label>

        <select name="tipo_combustible_id"
                id="tipo_combustible_id"
                class="form-select @error('tipo_combustible_id') is-invalid @enderror"
                required>

            <option value="">
                Seleccione combustible...
            </option>

            @foreach($tiposCombustible as $tipo)

                <option value="{{ $tipo->id }}"
                    @selected(
                        old(
                            'tipo_combustible_id',
                            $valeCombustible->tipo_combustible_id
                        ) == $tipo->id
                    )
                >
                    {{ $tipo->nombre }}
                </option>

            @endforeach

        </select>

        @error('tipo_combustible_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- ESTACIÓN DE SERVICIO --}}
    <div class="col-md-6">

        <label for="estacion_servicio_id"
               class="form-label">
            Estación de servicio *
        </label>

        <select name="estacion_servicio_id"
                id="estacion_servicio_id"
                class="form-select @error('estacion_servicio_id') is-invalid @enderror"
                required>

            <option value="">
                Seleccione estación...
            </option>

            @foreach($estacionesServicio as $estacion)

                <option value="{{ $estacion->id }}"
                    @selected(
                        old(
                            'estacion_servicio_id',
                            $valeCombustible->estacion_servicio_id
                        ) == $estacion->id
                    )
                >
                    {{ $estacion->nombre }}
                </option>

            @endforeach

        </select>

        @error('estacion_servicio_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

</div>

                    <div class="mb-3">

                        <label for="numero_factura" class="form-label">
                            Número de factura *
                        </label>

                        <input type="text"
                               name="numero_factura"
                               id="numero_factura"
                               maxlength="50"
                               value="{{ old('numero_factura') }}"
                               class="form-control @error('numero_factura') is-invalid @enderror"
                               required>

                        @error('numero_factura')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="row g-3">

                        <div class="col-md-6">

                            <label for="cantidad_litros" class="form-label">
                                Cantidad de litros *
                            </label>

                            <input type="text"
                                   name="cantidad_litros"
                                   id="cantidad_litros"
                                   inputmode="decimal"
                                   value="{{ old('cantidad_litros') }}"
                                   class="form-control @error('cantidad_litros') is-invalid @enderror"
                                   placeholder="Ej.: 45.50"
                                   required>

                            @error('cantidad_litros')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

                            <label for="precio_unitario" class="form-label">
                                Precio por litro (Bs) *
                            </label>

                            <input type="text"
                                   name="precio_unitario"
                                   id="precio_unitario"
                                   inputmode="decimal"
                                   value="{{ old('precio_unitario') }}"
                                   class="form-control @error('precio_unitario') is-invalid @enderror"
                                   placeholder="Ej.: 3.74"
                                   required>

                            @error('precio_unitario')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>


                    <div class="mt-3">

                        <label class="form-label">
                            Total Bs
                        </label>

                        <div class="input-group input-group-lg">

                            <span class="input-group-text">
                                Bs
                            </span>

                            <input type="text"
                                   id="totalVista"
                                   class="form-control fw-bold"
                                   value="0.00"
                                   readonly>

                        </div>

                        <div class="form-text">
                            Se calcula automáticamente: litros × precio por litro.
                        </div>
<div class="mt-3">

    <label for="observacion" class="form-label">
        Observación
    </label>

    <textarea name="observacion"
              id="observacion"
              rows="3"
              maxlength="1000"
              class="form-control @error('observacion') is-invalid @enderror"
              placeholder="Ingrese una observación si corresponde...">{{ old(
                  'observacion',
                  $valeCombustible->observacion
              ) }}</textarea>

    @error('observacion')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror

    <div class="form-text">
        Campo opcional.
    </div>

</div>
                    </div>

                </div>
            </div>

        </div>


        {{-- PANEL DERECHO --}}
        <div class="col-xl-4">

            <div class="card sticky-top" style="top: 20px;">

                <div class="card-body p-4">

                    <div class="small text-muted">
                        Vale Policía
                    </div>

                    <div class="display-6 fw-bold text-success">
                        N.º {{ sprintf('%06d', $valeCombustible->numero_vale) }}
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Estado actual
                        </small>

                        <strong>
                            {{ $valeCombustible->estado }}
                        </strong>
                    </div>

                    <div class="alert alert-light border small">

                        Al seleccionar el vehículo se obtendrán
                        automáticamente:

                        <br><br>

                        Código, placa, tipo, unidad, AP, FF y OF.

                    </div>

                    <div class="alert alert-success small">

                        Al registrar se guardarán el vehículo,
                        fecha, kilometraje, factura, litros,
                        precio unitario y total.

                    </div>

                    <div class="d-grid gap-2">

                        <button type="submit"
                                id="btnGuardar"
                                class="btn btn-success"
                                disabled>

                            <i class="bi bi-check2-circle me-1"></i>
                            Registrar uso

                        </button>

                        <a href="{{ route('entregas-policia.show', $valeCombustible->entrega_policia_id) }}"
                           class="btn btn-outline-secondary">

                            Cancelar
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('vehiculo_id');
    const panel = document.getElementById('datosVehiculo');
    const sinAP = document.getElementById('sinAP');
    const btnGuardar = document.getElementById('btnGuardar');

    const litros = document.getElementById('cantidad_litros');
    const precio = document.getElementById('precio_unitario');
    const total = document.getElementById('totalVista');


    function colocar(id, valor) {

        const elemento = document.getElementById(id);

        if (elemento) {
            elemento.textContent = valor || '';
        }
    }


    function actualizarVehiculo() {

        const opcion = select?.selectedOptions[0];

        if (!opcion || !opcion.value) {

            panel?.classList.add('d-none');

            if (btnGuardar) {
                btnGuardar.disabled = true;
            }

            return;
        }


        panel?.classList.remove('d-none');


        colocar('datoCodigo', opcion.dataset.codigo);
        colocar('datoPlaca', opcion.dataset.placa);
        colocar('datoTipo', opcion.dataset.tipo);
        colocar('datoUnidad', opcion.dataset.unidad);

        colocar('datoAP', opcion.dataset.ap);
        colocar('datoFF', opcion.dataset.ff);
        colocar('datoOF', opcion.dataset.of);


        let detalle = 'ASIGNADO A';

        if (opcion.dataset.tipo) {
            detalle += ' ' + opcion.dataset.tipo.toUpperCase();
        }

        if (opcion.dataset.unidad) {
            detalle += ' DE ' + opcion.dataset.unidad.toUpperCase();
        }

        colocar('datoDetalle', detalle);


        const tieneAP = opcion.dataset.tieneAp === '1';

        sinAP?.classList.toggle('d-none', tieneAP);

        if (btnGuardar) {
            btnGuardar.disabled = !tieneAP;
        }
    }


    function convertirNumero(valor) {

        valor = String(valor || '')
            .trim()
            .replace(',', '.');

        const numero = parseFloat(valor);

        return isNaN(numero) ? 0 : numero;
    }


    function calcularTotal() {

        const cantidad = convertirNumero(litros?.value);
        const precioUnitario = convertirNumero(precio?.value);

        const resultado = cantidad * precioUnitario;

        if (total) {
            total.value = resultado.toFixed(2);
        }
    }


    select?.addEventListener('change', actualizarVehiculo);

    litros?.addEventListener('input', calcularTotal);
    precio?.addEventListener('input', calcularTotal);


    actualizarVehiculo();
    calcularTotal();

});

</script>

@endpush