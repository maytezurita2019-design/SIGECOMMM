@extends('layouts.app')

@section('title', 'Editar vale · SIGECOM')

@section('content')

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">

    <div>
        <div class="small text-success fw-bold text-uppercase mb-1">
            Combustible · GAMS
        </div>

        <h1 class="page-title h2 mb-1">
            Editar vale N.º {{ sprintf('%06d', $valeCombustible->numero_vale) }}
        </h1>

        <p class="page-subtitle mb-0">
            Corrija el vehículo, la fecha o el kilometraje del vale pendiente.
        </p>
    </div>

    <a href="{{ route('vales-combustible.index', ['tipo' => 'GAMS']) }}"
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

    <div class="row g-3">

        <div class="col-xl-8">

            <div class="card">
                <div class="card-body p-3 p-md-4">

                    <h2 class="h5 fw-bold mb-4">
                        <i class="bi bi-pencil-square me-2"></i>
                        Datos del vale
                    </h2>


                    {{-- NÚMERO --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Número de vale
                        </label>

                        <input type="text"
                               class="form-control"
                               value="{{ sprintf('%06d', $valeCombustible->numero_vale) }}"
                               disabled>

                        <div class="form-text">
                            El número correlativo no puede modificarse.
                        </div>

                    </div>


                    {{-- VEHÍCULO --}}
                    <div class="mb-3">

                        <label for="vehiculo_id"
                               class="form-label">
                            Vehículo *
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
                                    $apertura = $vehiculo->aperturaProgramatica;
                                    $ff = $apertura?->fuenteFinanciamiento;
                                    $of = $apertura?->organismoFinanciador;
                                @endphp

                                <option
                                    value="{{ $vehiculo->id }}"

                                    data-codigo="{{ $vehiculo->codigo ?? '' }}"
                                    data-placa="{{ $vehiculo->placa ?? '' }}"
                                    data-tipo="{{ $vehiculo->tipo ?? '' }}"
                                    data-unidad="{{ $vehiculo->unidad?->nombre ?? '' }}"

                                    data-ap="{{ $apertura?->codigo ?? '' }}"
                                    data-ff="{{ $ff?->codigo ?? '' }}"
                                    data-of="{{ $of?->codigo ?? '' }}"

                                    data-tiene-ap="{{ $apertura ? '1' : '0' }}"

                                    @selected(
                                        old(
                                            'vehiculo_id',
                                            $valeCombustible->vehiculo_id
                                        ) == $vehiculo->id
                                    )
                                >

                                    {{ $vehiculo->codigo }}

                                    @if($vehiculo->placa)
                                        · {{ $vehiculo->placa }}
                                    @endif

                                    @if($vehiculo->unidad?->nombre)
                                        · {{ $vehiculo->unidad->nombre }}
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
                         class="border rounded bg-light p-3 mb-4 d-none">

                        <div class="small text-muted mb-3">
                            Datos que se asignarán automáticamente al vale
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted d-block">Tipo</small>
                                <strong id="datoTipo">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Unidad</small>
                                <strong id="datoUnidad">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Placa</small>
                                <strong id="datoPlaca">—</strong>
                            </div>

                        </div>

                        <hr>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Apertura Programática
                                </small>
                                <strong id="datoAP">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">FF</small>
                                <strong id="datoFF">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">OF</small>
                                <strong id="datoOF">—</strong>
                            </div>

                        </div>

                        <div id="sinAP"
                             class="alert alert-warning mt-3 mb-0 d-none">

                            <i class="bi bi-exclamation-triangle me-1"></i>

                            El vehículo seleccionado no tiene una
                            Apertura Programática asignada.

                        </div>

                    </div>


                    <div class="row g-3">

                        {{-- FECHA --}}
                        <div class="col-md-6">

                            <label for="fecha"
                                   class="form-label">
                                Fecha *
                            </label>

                            <input type="date"
                                   name="fecha"
                                   id="fecha"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old(
                                       'fecha',
                                       $valeCombustible->fecha?->format('Y-m-d')
                                   ) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>

                            @error('fecha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                       {{-- KILOMETRAJE --}}
<div class="col-md-6">

    <label for="kilometraje"
           class="form-label">
        Kilometraje / Horómetro
    </label>

    <input type="text"
           name="kilometraje"
           id="kilometraje"
           maxlength="50"
           class="form-control @error('kilometraje') is-invalid @enderror"
           value="{{ old(
               'kilometraje',
               $valeCombustible->kilometraje
           ) }}"
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

@if($valeCombustible->estado === 'COMPLETADO')

    <div class="col-12">
        <hr>

        <h5 class="fw-bold mb-3">
            <i class="bi bi-fuel-pump me-2"></i>
            Datos de carga y facturación
        </h5>
    </div>


    {{-- COMBUSTIBLE --}}
    <div class="col-md-6">

        <label for="tipo_combustible_id"
               class="form-label">
            Tipo de combustible *
        </label>

        <select
            name="tipo_combustible_id"
            id="tipo_combustible_id"
            class="form-select @error('tipo_combustible_id') is-invalid @enderror"
            required
        >

            <option value="">
                Seleccione combustible...
            </option>

            @foreach($tiposCombustible as $tipo)

                <option
                    value="{{ $tipo->id }}"
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


    {{-- ESTACIÓN --}}
    <div class="col-md-6">

        <label for="estacion_servicio_id"
               class="form-label">
            Estación de servicio *
        </label>

        <select
            name="estacion_servicio_id"
            id="estacion_servicio_id"
            class="form-select @error('estacion_servicio_id') is-invalid @enderror"
            required
        >

            <option value="">
                Seleccione estación...
            </option>

            @foreach($estacionesServicio as $estacion)

                <option
                    value="{{ $estacion->id }}"
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


    {{-- FACTURA --}}
    <div class="col-md-6">

        <label for="numero_factura"
               class="form-label">
            Número de factura *
        </label>

        <input
            type="text"
            name="numero_factura"
            id="numero_factura"
            maxlength="50"
            class="form-control @error('numero_factura') is-invalid @enderror"
            value="{{ old(
                'numero_factura',
                $valeCombustible->numero_factura
            ) }}"
            required
        >

        @error('numero_factura')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- LITROS --}}
    <div class="col-md-6">

        <label for="cantidad_litros"
               class="form-label">
            Cantidad de litros *
        </label>

        <input
            type="text"
            inputmode="decimal"
            name="cantidad_litros"
            id="cantidad_litros"
            class="form-control @error('cantidad_litros') is-invalid @enderror"
            value="{{ old(
                'cantidad_litros',
                $valeCombustible->cantidad_litros
            ) }}"
            required
        >

        @error('cantidad_litros')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- PRECIO --}}
    <div class="col-md-6">

        <label for="precio_unitario"
               class="form-label">
            Precio unitario (Bs) *
        </label>

        <input
            type="text"
            inputmode="decimal"
            name="precio_unitario"
            id="precio_unitario"
            class="form-control @error('precio_unitario') is-invalid @enderror"
            value="{{ old(
                'precio_unitario',
                $valeCombustible->precio_unitario
            ) }}"
            required
        >

        @error('precio_unitario')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- TOTAL --}}
    <div class="col-md-6">

        <label class="form-label">
            Total actual (Bs)
        </label>

        <input
            type="text"
            class="form-control bg-light"
            value="{{ number_format(
                (float) $valeCombustible->total,
                2,
                ',',
                '.'
            ) }}"
            disabled
        >

        <div class="form-text">
            El total se recalculará automáticamente al guardar.
        </div>

    </div>


    {{-- OBSERVACIÓN --}}
    <div class="col-12">

        <label for="observacion"
               class="form-label">
            Observación
        </label>

        <textarea
            name="observacion"
            id="observacion"
            rows="3"
            maxlength="1000"
            class="form-control @error('observacion') is-invalid @enderror"
        >{{ old(
            'observacion',
            $valeCombustible->observacion
        ) }}</textarea>

        @error('observacion')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>

@endif


        {{-- LATERAL --}}
        <div class="col-xl-4">

            <div class="card">
                <div class="card-body p-3 p-md-4">

                    <div class="small text-muted">
                        Vale
                    </div>

                    <div class="display-6 fw-bold text-success">
                        N.º {{ sprintf('%06d', $valeCombustible->numero_vale) }}
                    </div>

                    <hr>

                    <div class="mb-2">
                        <small class="text-muted d-block">
                            Estado
                        </small>

                        <strong>
                            {{ $valeCombustible->estado }}
                        </strong>
                    </div>

                    <div class="alert alert-light border small mt-3">

                        <i class="bi bi-info-circle me-1"></i>

                        Al cambiar el vehículo, SIGECOM también actualizará
                        la AP, FF y OF de este vale pendiente.

                    </div>

                    <div class="d-grid gap-2 mt-4">

                        <button type="submit"
                                id="btnGuardar"
                                class="btn btn-success">

                            <i class="bi bi-check2-circle me-1"></i>
                            Guardar cambios

                        </button>

                        <a href="{{ route(
                            'vales-combustible.show',
                            $valeCombustible
                        ) }}"
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

    const select =
        document.getElementById('vehiculo_id');

    const panel =
        document.getElementById('datosVehiculo');

    const alerta =
        document.getElementById('sinAP');

    const guardar =
        document.getElementById('btnGuardar');


    function poner(id, valor) {

        const elemento =
            document.getElementById(id);

        if (elemento) {
            elemento.textContent = valor || '—';
        }

    }


    function actualizar() {

        const opcion =
            select?.selectedOptions[0];


        if (!opcion || !opcion.value) {

            panel?.classList.add('d-none');

            if (guardar) {
                guardar.disabled = true;
            }

            return;
        }


        panel?.classList.remove('d-none');


        poner('datoTipo', opcion.dataset.tipo);

        poner('datoUnidad', opcion.dataset.unidad);

        poner('datoPlaca', opcion.dataset.placa);

        poner('datoAP', opcion.dataset.ap);

        poner('datoFF', opcion.dataset.ff);

        poner('datoOF', opcion.dataset.of);


        const tieneAP =
            opcion.dataset.tieneAp === '1';


        alerta?.classList.toggle(
            'd-none',
            tieneAP
        );


        if (guardar) {
            guardar.disabled = !tieneAP;
        }

    }


    select?.addEventListener(
        'change',
        actualizar
    );


    actualizar();

});

</script>

@endpush