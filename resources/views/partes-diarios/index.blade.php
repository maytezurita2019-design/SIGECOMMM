@extends('layouts.app')

@section('title', 'Partes Diarios')

@section('content')

<div class="container-fluid py-4">

    {{-- TÍTULO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-journal-check me-2"></i>
                Partes Diarios
            </h3>

            <p class="text-muted mb-0">
                Registro de presentación de partes diarios por vehículo.
            </p>
        </div>
    </div>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revise los siguientes datos:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- SELECCIONAR VEHÍCULO --}}
<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-success text-white">

        <strong>
            <i class="bi bi-car-front-fill me-2"></i>
            Seleccionar vehículo
        </strong>

    </div>

    <div class="card-body">

        <label
            for="buscadorVehiculo"
            class="form-label fw-semibold"
        >
            Código, placa o unidad del vehículo
        </label>


        <div class="position-relative">

            {{-- CAMPO VISIBLE --}}
            <input
                type="text"
                id="buscadorVehiculo"
                class="form-control form-control-lg"
                placeholder="Escriba código, placa o unidad..."
                autocomplete="off"
                value="{{ $vehiculo
                    ? ($vehiculo->codigo . ' · ' .
                       ($vehiculo->placa ?? 'SIN PLACA') . ' · ' .
                       ($vehiculo->unidad?->nombre ?? 'SIN UNIDAD'))
                    : ''
                }}"
            >


            {{-- RESULTADOS --}}
            <div
                id="listaVehiculos"
                class="list-group position-absolute w-100 shadow d-none"
                style="
                    z-index: 1050;
                    max-height: 320px;
                    overflow-y: auto;
                "
            >

                @foreach($vehiculos as $v)

                    <button
                        type="button"
                        class="list-group-item list-group-item-action opcion-vehiculo"
                        data-id="{{ $v->id }}"
                        data-codigo="{{ $v->codigo }}"
                        data-placa="{{ $v->placa }}"
                        data-unidad="{{ $v->unidad?->nombre }}"
                        data-texto="{{
                            mb_strtoupper(
                                ($v->codigo ?? '') . ' ' .
                                ($v->placa ?? '') . ' ' .
                                ($v->unidad?->nombre ?? '')
                            )
                        }}"
                    >

                        <div class="d-flex justify-content-between gap-3">

                            <div>

                                <strong class="text-success">
                                    {{ $v->codigo }}
                                </strong>

                                @if($v->placa)

                                    <span class="ms-2 fw-semibold">
                                        {{ $v->placa }}
                                    </span>

                                @endif

                                <div class="small text-muted">
                                    {{ $v->unidad?->nombre ?? 'Sin unidad' }}
                                </div>

                            </div>

                            <div>
                                <i class="bi bi-car-front"></i>
                            </div>

                        </div>

                    </button>

                @endforeach

            </div>

        </div>


        <div class="form-text mt-2">

            <i class="bi bi-info-circle me-1"></i>

            Haga clic para desplegar los vehículos o escriba
            parte del código, placa o unidad.

        </div>

    </div>

</div>


    {{-- RESULTADO DE BÚSQUEDA --}}

        @if($vehiculo)

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-light">
                    <strong class="text-success">
                        <i class="bi bi-car-front-fill me-2"></i>
                        Vehículo encontrado
                    </strong>
                </div>

                <div class="card-body">

                    <div class="row g-3 mb-4">

                        <div class="col-md-3">
                            <small class="text-muted d-block">
                                Código
                            </small>

                            <strong>
                                {{ $vehiculo->codigo ?? '-' }}
                            </strong>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">
                                Placa
                            </small>

                            <strong>
                                {{ $vehiculo->placa ?? '-' }}
                            </strong>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">
                                Tipo de vehículo
                            </small>

                            <strong>
                                {{ $vehiculo->tipo ?? $vehiculo->tipo_vehiculo ?? '-' }}
                            </strong>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">
                                Unidad
                            </small>

                            <strong>
                                {{ $vehiculo->unidad?->nombre ?? '-' }}
                            </strong>
                        </div>

                    </div>


                    {{-- REGISTRAR PARTE --}}
                    <hr>

                    <h5 class="fw-bold mb-3">
                        Registrar Parte Diario
                    </h5>

                    <form method="POST"
                          action="{{ route('partes-diarios.store') }}">

                        @csrf

                        <input type="hidden"
                               name="vehiculo_id"
                               value="{{ $vehiculo->id }}">

                        <div class="row g-3">

                            {{-- FECHA --}}
                            <div class="col-md-4">

                                <label class="form-label fw-semibold">
                                    Fecha del parte
                                </label>

                                <input type="date"
                                       name="fecha"
                                       class="form-control"
                                       value="{{ old('fecha', date('Y-m-d')) }}"
                                       required>

                            </div>


                           {{-- PRESENTÓ --}}
<div class="col-md-4">

    <label class="form-label fw-semibold">
        ¿Presentó Parte Diario?
    </label>

    <select
        name="presentado"
        class="form-select"
        required
    >

        <option value="">
            Seleccione
        </option>

        <option
            value="1"
            {{ old('presentado') === '1' ? 'selected' : '' }}
        >
            ✓ SÍ
        </option>

        <option
            value="0"
            {{ old('presentado') === '0' ? 'selected' : '' }}
        >
            ✕ NO
        </option>

    </select>

</div>

                            {{-- OBSERVACIÓN --}}
                            <div class="col-md-4">

                                <label class="form-label fw-semibold">
                                    Observación
                                </label>

                                <input type="text"
                                       name="observacion"
                                       class="form-control"
                                       value="{{ old('observacion') }}"
                                       placeholder="Opcional">

                            </div>

                        </div>


                        <div class="mt-4">

                            <button type="submit"
                                    class="btn btn-success px-4">

                                <i class="bi bi-check-circle me-1"></i>
                                Registrar Parte Diario

                            </button>

                        </div>

                    </form>

                </div>

            </div>
@endif


    {{-- HISTORIAL --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-success text-white">

            <strong>
                <i class="bi bi-clock-history me-2"></i>
                Historial de Partes Diarios
            </strong>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Fecha</th>
                            <th>Código</th>
                            <th>Placa</th>
                            <th>Unidad</th>
                            <th class="text-center">
                                Parte Diario
                            </th>
                            <th>Observación</th>
                            <th class="text-center">
                                Acción
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($partes as $parte)

                           <tr>

    {{-- FECHA --}}
    <td>
        {{ $parte->fecha?->format('d/m/Y') }}
    </td>


    {{-- CÓDIGO --}}
    <td>
        {{ $parte->vehiculo?->codigo ?? '-' }}
    </td>


    {{-- PLACA --}}
    <td>
        <strong>
            {{ $parte->vehiculo?->placa ?? '-' }}
        </strong>
    </td>


    {{-- UNIDAD --}}
    <td>
        {{ $parte->vehiculo?->unidad?->nombre ?? '-' }}
    </td>


    {{-- PARTE DIARIO --}}
    <td class="text-center">

        @if($parte->presentado)

            <span class="badge bg-success">
                <i class="bi bi-check-circle-fill me-1"></i>
                SÍ
            </span>

        @else

            <span class="badge bg-danger">
                <i class="bi bi-x-circle-fill me-1"></i>
                NO
            </span>

        @endif

    </td>


    {{-- OBSERVACIÓN --}}
    <td>
        {{ $parte->observacion ?: '-' }}
    </td>


    {{-- ACCIÓN --}}
    <td class="text-center">

        <button
            type="button"
            class="btn btn-sm btn-outline-success"
            data-bs-toggle="modal"
            data-bs-target="#editarParte{{ $parte->id }}"
        >

            <i class="bi bi-pencil-square me-1"></i>
            Editar

        </button>

    </td>

</tr>

                            {{-- MODAL EDITAR --}}
                            <div class="modal fade"
                                 id="editarParte{{ $parte->id }}"
                                 tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <form method="POST"
                                              action="{{ route('partes-diarios.update', $parte) }}">

                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">

                                                <h5 class="modal-title">
                                                    Editar Parte Diario
                                                </h5>

                                                <button type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal">
                                                </button>

                                            </div>

                                            <div class="modal-body">

                                                <div class="mb-3">

                                                    <label class="form-label">
                                                        Vehículo
                                                    </label>

                                                    <input type="text"
                                                           class="form-control"
                                                           value="{{ $parte->vehiculo?->placa }}"
                                                           disabled>

                                                </div>

                                                <div class="mb-3">

                                                    <label class="form-label">
                                                        Fecha
                                                    </label>

                                                    <input type="date"
                                                           name="fecha"
                                                           class="form-control"
                                                           value="{{ $parte->fecha?->format('Y-m-d') }}"
                                                           required>

                                                </div>

                                                <div class="mb-3">

                                                    <label class="form-label">
                                                        ¿Presentó Parte Diario?
                                                    </label>

                                                    <select
    name="presentado"
    class="form-select"
    required
>

    <option
        value="1"
        {{ $parte->presentado ? 'selected' : '' }}
    >
        ✓ SÍ
    </option>

    <option
        value="0"
        {{ !$parte->presentado ? 'selected' : '' }}
    >
        ✕ NO
    </option>

</select>

                                                </div>

                                                <div class="mb-3">

                                                    <label class="form-label">
                                                        Observación
                                                    </label>

                                                    <textarea name="observacion"
                                                              class="form-control"
                                                              rows="3">{{ $parte->observacion }}</textarea>

                                                </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button"
                                                        class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>

                                                <button type="submit"
                                                        class="btn btn-success">

                                                    <i class="bi bi-save me-1"></i>
                                                    Guardar cambios

                                                </button>

                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center text-muted py-4">

                                    No existen partes diarios registrados.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINACIÓN --}}
            @if($partes->hasPages())

                <div class="mt-3">

                    {{ $partes->withQueryString()->links() }}

                </div>

            @endif

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const input =
        document.getElementById('buscadorVehiculo');

    const lista =
        document.getElementById('listaVehiculos');

    const opciones =
        document.querySelectorAll('.opcion-vehiculo');


    if (!input || !lista) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR TEXTO
    |--------------------------------------------------------------------------
    */

    function normalizar(texto) {

        return (texto || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toUpperCase()
            .trim();
    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR TODOS
    |--------------------------------------------------------------------------
    */

    function mostrarTodos() {

        opciones.forEach(function (opcion) {

            opcion.classList.remove('d-none');

        });

        lista.classList.remove('d-none');
    }


    /*
    |--------------------------------------------------------------------------
    | AL HACER CLIC EN EL CAMPO
    |--------------------------------------------------------------------------
    */

    input.addEventListener('focus', function () {

        mostrarTodos();

    });


    input.addEventListener('click', function () {

        mostrarTodos();

    });


    /*
    |--------------------------------------------------------------------------
    | FILTRAR MIENTRAS ESCRIBE
    |--------------------------------------------------------------------------
    */

    input.addEventListener('input', function () {

        const buscar =
            normalizar(input.value);

        let encontrados = 0;


        opciones.forEach(function (opcion) {

            const texto =
                normalizar(opcion.dataset.texto);

            const coincide =
                texto.includes(buscar);


            opcion.classList.toggle(
                'd-none',
                !coincide
            );


            if (coincide) {
                encontrados++;
            }

        });


        lista.classList.remove('d-none');

    });


    /*
    |--------------------------------------------------------------------------
    | SELECCIONAR VEHÍCULO
    |--------------------------------------------------------------------------
    */

    opciones.forEach(function (opcion) {

        opcion.addEventListener('click', function () {

            const id =
                opcion.dataset.id;

            /*
            |--------------------------------------------------------------
            | CARGAR VEHÍCULO SELECCIONADO
            |--------------------------------------------------------------
            */

            const url =
                new URL(window.location.href);

            url.searchParams.set(
                'vehiculo_id',
                id
            );

            window.location.href =
                url.toString();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | CERRAR AL HACER CLIC FUERA
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        if (
            !input.contains(event.target) &&
            !lista.contains(event.target)
        ) {

            lista.classList.add('d-none');

        }

    });

});

</script>
@endsection