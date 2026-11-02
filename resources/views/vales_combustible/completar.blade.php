@extends('layouts.app')

@section('title', 'Completar vale')

@section('content')

@php
    $nombreTipoActual = mb_strtoupper((string) ($valeCombustible->tipoCombustible?->nombre ?? ''), 'UTF-8');
    $tipoActualDiesel = str_contains($nombreTipoActual, 'DIESEL') || str_contains($nombreTipoActual, 'DIÉSEL');
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2>
            Completar vale N.º
            {{ $valeCombustible->numero_vale_formateado }}
        </h2>

        <p class="text-muted mb-0">
            Registre los datos de la carga y la factura para cerrar este vale.
        </p>

    </div>

    <a
        href="{{ route('vales-combustible.show', $valeCombustible) }}"
        class="btn btn-secondary"
    >
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Volver
    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        {{-- ===================================================== --}}
        {{-- DATOS DEL VALE --}}
        {{-- ===================================================== --}}

        <div class="mb-4">

            <h5 class="mb-3">
                Datos del vale
            </h5>

            <div class="row">

                {{-- NÚMERO DE VALE --}}
                <div class="col-md-3 mb-3">

                    <strong>N.º de vale</strong>

                    <div>
                        {{ $valeCombustible->numero_vale_formateado }}
                    </div>

                </div>


                {{-- FECHA --}}
                <div class="col-md-3 mb-3">

                    <strong>Fecha</strong>

                    <div>
                        {{ $valeCombustible->fecha?->format('d/m/Y') ?? '-' }}
                    </div>

                </div>


                {{-- KILOMETRAJE --}}
                <div class="col-md-3 mb-3">

                    <strong>Kilometraje</strong>

                    <div>
                        {{ $valeCombustible->kilometraje ?? '-' }}
                    </div>

                </div>


                {{-- ESTADO --}}
                <div class="col-md-3 mb-3">

                    <strong>Estado</strong>

                    <div>
                        <span class="badge bg-warning text-dark">
                            EMITIDO / PENDIENTE
                        </span>
                    </div>

                </div>

            </div>


            <div class="row">

                {{-- VEHÍCULO --}}
                <div class="col-md-4 mb-3">

                    <strong>Vehículo</strong>

                    <div>
                        {{ $valeCombustible->vehiculo?->codigo ?? '-' }}

                        @if($valeCombustible->vehiculo?->placa)
                            · {{ $valeCombustible->vehiculo->placa }}
                        @endif
                    </div>

                </div>


                {{-- UNIDAD --}}
                <div class="col-md-4 mb-3">

                    <strong>Unidad</strong>

                    <div>
                        {{ $valeCombustible->vehiculo?->unidad?->nombre ?? 'Sin unidad' }}
                    </div>

                </div>


                {{-- APERTURA PROGRAMÁTICA --}}
                <div class="col-md-4 mb-3">

                    <strong>Apertura programática</strong>

                    <div>
                        {{ $valeCombustible->aperturaProgramatica?->codigo ?? '-' }}
                    </div>

                </div>

            </div>


            @if($valeCombustible->observacion)

                <div class="row">

                    <div class="col-12">

                        <strong>Observación</strong>

                        <div>
                            {{ $valeCombustible->observacion }}
                        </div>

                    </div>

                </div>

            @endif

        </div>


        <hr>


        {{-- ===================================================== --}}
        {{-- ERRORES --}}
        {{-- ===================================================== --}}

        @if($errors->any())

            <div class="alert alert-danger" role="alert">

                <strong>
                    No se pudo completar el vale.
                </strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- FORMULARIO --}}
        {{-- ===================================================== --}}

        <form
            action="{{ route(
                'vales-combustible.completar',
                $valeCombustible
            ) }}"
            method="POST"
        >

            @csrf

         <div class="row">

    {{-- TIPO DE COMBUSTIBLE --}}
    <div class="col-md-6 mb-3">

        <label
            for="tipo_combustible_id"
            class="form-label"
        >
            Tipo de combustible
            @if($valeCombustible->tipo_combustible_id === null)
                *
            @endif
        </label>

        <select
            id="tipo_combustible_id"
            name="tipo_combustible_id"
            class="form-select @error('tipo_combustible_id') is-invalid @enderror"
            @disabled($valeCombustible->tipo_combustible_id !== null)
            @if($valeCombustible->tipo_combustible_id === null)
                required
            @endif
        >
            <option value="">
                Seleccione combustible...
            </option>

            @foreach($tiposCombustible as $tipo)
                <option
                    value="{{ $tipo->id }}"
                    data-ubicacion="{{ str_contains(mb_strtoupper($tipo->nombre, 'UTF-8'), 'DIESEL') || str_contains(mb_strtoupper($tipo->nombre, 'UTF-8'), 'DIÉSEL') ? 'diesel' : 'gasolina' }}"
                    @selected(
                        ($valeCombustible->tipo_combustible_id ?? old('tipo_combustible_id'))
                            == $tipo->id
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


    {{-- UBICACIÓN DEL COMBUSTIBLE --}}
    <div class="col-md-6 mb-3 {{ $tipoActualDiesel ? 'd-none' : '' }}" id="estacionServicioContainer">

        <label
            for="estacion_servicio_id"
            class="form-label"
        >
            Estación de servicio
        </label>

        <select
            id="estacion_servicio_id"
            name="estacion_servicio_id"
            class="form-select @error('estacion_servicio_id') is-invalid @enderror"
            @disabled($tipoActualDiesel || $valeCombustible->estacion_servicio_id !== null)
        >
            <option value="">
                Seleccione estación...
            </option>

            @foreach($estacionesServicio as $estacion)
                <option
                    value="{{ $estacion->id }}"
                    @selected(
                        ($valeCombustible->estacion_servicio_id ?? old('estacion_servicio_id'))
                            == $estacion->id
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

    <div class="col-md-6 mb-3 {{ $tipoActualDiesel ? '' : 'd-none' }}" id="lugarEntregaContainer">
        <label for="lugar_entrega_id" class="form-label">Lugar de entrega</label>
        <select
            id="lugar_entrega_id"
            name="lugar_entrega_id"
            class="form-select @error('lugar_entrega_id') is-invalid @enderror"
            @disabled(!$tipoActualDiesel || $valeCombustible->lugar_entrega_id !== null)
        >
            <option value="">Seleccione lugar de entrega...</option>
            @foreach($lugaresEntrega as $lugar)
                <option value="{{ $lugar->id }}"
                    @disabled(!$lugar->estado)
                    @selected(old('lugar_entrega_id', $valeCombustible->lugar_entrega_id) == $lugar->id)>
                    {{ $lugar->nombre }}{{ $lugar->estado ? '' : ' (Inactivo)' }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Los vales históricos sin lugar asignado se mantienen sin cambios si no selecciona uno.</div>
        @error('lugar_entrega_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>
            <div class="row">

                {{-- CANTIDAD DE LITROS --}}
                <div class="col-md-4 mb-3">

                    <label
                        for="cantidad_litros"
                        class="form-label"
                    >
                        Cantidad de litros *
                    </label>

                    <input
                        id="cantidad_litros"
                        name="cantidad_litros"
                        type="text"
                        inputmode="decimal"
                        class="form-control @error('cantidad_litros') is-invalid @enderror"
                        value="{{ old('cantidad_litros') }}"
                        required
                    >

                    @error('cantidad_litros')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- PRECIO UNITARIO --}}
                <div class="col-md-4 mb-3">

                    <label
                        for="precio_unitario"
                        class="form-label"
                    >
                        Precio unitario (Bs) *
                    </label>

                    <input
                        id="precio_unitario"
                        name="precio_unitario"
                        type="text"
                        inputmode="decimal"
                        class="form-control @error('precio_unitario') is-invalid @enderror"
                        value="{{ old('precio_unitario') }}"
                        required
                    >

                    @error('precio_unitario')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- TOTAL --}}
                <div class="col-md-4 mb-3">

                    <label
                        for="total_visual"
                        class="form-label"
                    >
                        Total calculado (Bs)
                    </label>

                    <input
                        id="total_visual"
                        type="text"
                        class="form-control bg-light"
                        readonly
                    >

                </div>


                {{-- NÚMERO DE FACTURA --}}
                <div class="col-md-6 mb-3">

                    <label
                        for="numero_factura"
                        class="form-label"
                    >
                        Número de factura *
                    </label>

                    <input
                        id="numero_factura"
                        name="numero_factura"
                        type="text"
                        maxlength="50"
                        class="form-control @error('numero_factura') is-invalid @enderror"
                        value="{{ old('numero_factura') }}"
                        required
                    >

                    @error('numero_factura')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                    <small class="text-muted">
                        Dato de control interno; no se imprime en el vale.
                    </small>

                </div>

            </div>


            <p class="text-muted small">

                Use coma o punto decimal sin separador de miles.

                Litros: hasta 13 decimales;

                precio: hasta 4.

            </p>


            <button
                type="submit"
                class="btn btn-success"
            >

                <i class="bi bi-check-circle me-1"></i>

                Completar vale

            </button>


            <a
                href="{{ route(
                    'vales-combustible.show',
                    $valeCombustible
                ) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                Cancelar
            </a>

        </form>

    </div>

</div>


<script src="{{ asset('js/total-vale.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipo = document.getElementById('tipo_combustible_id');
    const estacion = document.getElementById('estacion_servicio_id');
    const lugar = document.getElementById('lugar_entrega_id');
    const estaciones = document.getElementById('estacionServicioContainer');
    const lugares = document.getElementById('lugarEntregaContainer');

    function actualizarUbicacion() {
        const diesel = tipo?.selectedOptions[0]?.dataset.ubicacion === 'diesel';
        estaciones?.classList.toggle('d-none', diesel);
        lugares?.classList.toggle('d-none', !diesel);
        if (estacion && !{{ $valeCombustible->estacion_servicio_id !== null ? 'true' : 'false' }}) {
            estacion.disabled = diesel;
        }
        if (lugar && !{{ $valeCombustible->lugar_entrega_id !== null ? 'true' : 'false' }}) {
            lugar.disabled = !diesel;
        }
    }

    tipo?.addEventListener('change', actualizarUbicacion);
    actualizarUbicacion();
});
</script>

@endsection