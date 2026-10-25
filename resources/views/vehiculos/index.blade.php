@extends('layouts.app')

@section('title', 'Vehículos · SIGECOM')

@section('content')

@php
    $esAdmin = str_contains(
        mb_strtoupper(auth()->user()->rol->nombre ?? ''),
        'ADMIN'
    );
@endphp


<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">

    <div>

        <div class="small text-success fw-bold text-uppercase">
            Parque automotor
        </div>

        <h1 class="page-title h2 mb-1">
            Vehículos
        </h1>

        <p class="page-subtitle mb-0">
            Consulta y administración del parque automotor institucional.
        </p>

    </div>


    @if($esAdmin)

        <a href="{{ route('vehiculos.create') }}"
           class="btn btn-success">

            <i class="bi bi-plus-circle me-1"></i>
            Nuevo vehículo

        </a>

    @endif

</div>


{{-- CONTROLES DE TABLA --}}
<div class="card mb-3">
    <div class="card-body py-3">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            {{-- MOSTRAR --}}
            <div class="d-flex align-items-center gap-2">

                <span class="small">
                    Mostrar
                </span>

                <select id="cantidadVehiculos"
                        class="form-select form-select-sm"
                        style="width: 70px;">

                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>

                <span class="small">
                    vehículos
                </span>

            </div>


            {{-- BUSCADOR AUTOMÁTICO --}}
            <div class="d-flex align-items-center gap-2">

                <label for="buscarVehiculo"
                       class="small mb-0">
                    Buscador:
                </label>

                <input type="text"
                       id="buscarVehiculo"
                       class="form-control form-control-sm"
                       style="width: 230px;"
                       autocomplete="off">

            </div>

        </div>

    </div>
</div>

{{-- TABLA --}}
<div class="card">

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0 tabla-responsive-cards"
       id="tablaVehiculos">

            <thead>

                <tr>
                    <th>Código</th>
                    <th>Placa</th>
                    <th>Tipo / Marca</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

            </thead>


            <tbody>

                @forelse($vehiculos as $vehiculo)

    <tr data-search="{{ mb_strtoupper(
        $vehiculo->codigo . ' ' .
        $vehiculo->placa . ' ' .
        ($vehiculo->tipo ?? '') . ' ' .
        ($vehiculo->marca ?? '') . ' ' .
        ($vehiculo->unidad->nombre ?? '')
    ) }}">

                        {{-- CÓDIGO --}}
                        <td data-label="Código">

                            <strong>
                                {{ $vehiculo->codigo }}
                            </strong>

                        </td>


                        {{-- PLACA --}}
                        <td data-label="Placa">

                            <span class="fw-bold">
                                {{ $vehiculo->placa }}
                            </span>

                        </td>


                        {{-- TIPO / MARCA --}}
                        <td data-label="Tipo / Marca">

                            {{ $vehiculo->tipo ?: '—' }}

                            @if($vehiculo->marca)

                                <span class="text-muted">
                                    · {{ $vehiculo->marca }}
                                </span>

                            @endif

                        </td>


                        {{-- UNIDAD --}}
                        <td data-label="Unidad">

                            {{ $vehiculo->unidad->nombre ?? 'Sin unidad' }}

                        </td>


                        {{-- ESTADO --}}
                        <td data-label="Estado">

                            <span class="badge border bg-light text-dark">

                                <i class="bi {{ $vehiculo->estado
                                    ? 'bi-check-circle'
                                    : 'bi-pause-circle' }} me-1">
                                </i>

                                {{ $vehiculo->estado
                                    ? 'Activo'
                                    : 'Inactivo' }}

                            </span>

                        </td>


                      {{-- ACCIONES --}}
<td data-label="Acciones">

    <div class="d-flex gap-1 flex-nowrap align-items-center acciones-vehiculo">

        {{-- VER --}}
        <a href="{{ route('vehiculos.show', $vehiculo) }}"
           class="btn btn-sm btn-info text-white"
           title="Ver vehículo">

            <i class="bi bi-eye"></i>

        </a>


        @if($esAdmin)

            {{-- EDITAR --}}
            <a href="{{ route('vehiculos.edit', $vehiculo) }}"
               class="btn btn-sm btn-warning text-dark"
               title="Editar vehículo">

                <i class="bi bi-pencil"></i>

            </a>


            {{-- ELIMINAR --}}
            <form action="{{ route('vehiculos.destroy', $vehiculo) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('¿Está seguro de eliminar este vehículo?');">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="btn btn-sm btn-danger"
                        title="Eliminar vehículo">

                    <i class="bi bi-trash"></i>

                </button>

            </form>

        @endif


        {{-- REPORTE --}}
        <a href="{{ route(
                'reportes.vehiculos',
                ['vehiculo_id' => $vehiculo->id]
            ) }}"
           class="btn btn-sm btn-success"
           title="Reporte del vehículo">

            <i class="bi bi-bar-chart"></i>

        </a>

    </div>

</td>
                @empty

                    <tr>

                        <td colspan="6"
                            class="text-center text-muted py-5">

                            <i class="bi bi-car-front fs-2 d-block mb-2"></i>

                            @if($buscar !== '')
                                No se encontraron vehículos con esa búsqueda.
                            @else
                                No hay vehículos registrados.
                            @endif

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


  {{-- PARTE INFERIOR --}}
<div class="card-footer bg-white">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div id="infoVehiculos"
             class="small text-muted">
        </div>

        <ul id="paginacionVehiculos"
            class="pagination pagination-sm mb-0">
        </ul>

    </div>

</div>

@endsection
@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const buscador =
        document.getElementById('buscarVehiculo');

    const selector =
        document.getElementById('cantidadVehiculos');

    const filas = [
        ...document.querySelectorAll(
            '#tablaVehiculos tbody tr[data-search]'
        )
    ];

    const info =
        document.getElementById('infoVehiculos');

    const paginacion =
        document.getElementById('paginacionVehiculos');

    let paginaActual = 1;


    function obtenerFiltrados() {

        const texto = buscador.value
            .toUpperCase()
            .trim();

        return filas.filter(function (fila) {

            return fila.dataset.search.includes(texto);

        });
    }


    function mostrarPagina() {

        const filtrados = obtenerFiltrados();

        const cantidad =
            parseInt(selector.value);

        const total =
            filtrados.length;

        const totalPaginas =
            Math.max(1, Math.ceil(total / cantidad));

        if (paginaActual > totalPaginas) {
            paginaActual = totalPaginas;
        }

        const inicio =
            (paginaActual - 1) * cantidad;

        const fin =
            inicio + cantidad;


        // OCULTAR TODAS
        filas.forEach(function (fila) {

            fila.classList.add('d-none');

        });


        // MOSTRAR PÁGINA ACTUAL
        filtrados
            .slice(inicio, fin)
            .forEach(function (fila) {

                fila.classList.remove('d-none');

            });


        // INFORMACIÓN
        if (total === 0) {

            info.textContent =
                'No se encontraron vehículos.';

        } else {

            info.textContent =
                'Mostrando ' +
                (inicio + 1) +
                ' a ' +
                Math.min(fin, total) +
                ' de ' +
                total +
                ' vehículos';
        }


        crearPaginacion(totalPaginas);
    }


    function crearPaginacion(totalPaginas) {

        paginacion.innerHTML = '';

        if (totalPaginas <= 1) {
            return;
        }


        // ANTERIOR
        agregarBoton(
            '‹ Anterior',
            paginaActual - 1,
            paginaActual === 1
        );


        // SOLO 3 NÚMEROS
        let inicio =
            Math.max(1, paginaActual - 1);

        let fin =
            Math.min(totalPaginas, inicio + 2);

        inicio =
            Math.max(1, fin - 2);


        for (let pagina = inicio; pagina <= fin; pagina++) {

            agregarBoton(
                pagina,
                pagina,
                false,
                pagina === paginaActual
            );
        }


        // SIGUIENTE
        agregarBoton(
            'Siguiente ›',
            paginaActual + 1,
            paginaActual === totalPaginas
        );
    }


    function agregarBoton(
        texto,
        pagina,
        deshabilitado = false,
        activo = false
    ) {

        const li =
            document.createElement('li');

        li.className =
            'page-item' +
            (deshabilitado ? ' disabled' : '') +
            (activo ? ' active' : '');


        const boton =
            document.createElement('button');

        boton.type = 'button';
        boton.className = 'page-link';
        boton.textContent = texto;


        if (!deshabilitado) {

            boton.addEventListener('click', function () {

                paginaActual = pagina;

                mostrarPagina();

            });
        }


        li.appendChild(boton);

        paginacion.appendChild(li);
    }


    // BUSCAR AUTOMÁTICAMENTE
    buscador.addEventListener('input', function () {

        paginaActual = 1;

        mostrarPagina();

    });


    // CAMBIAR 10 / 25 / 50 / 100
    selector.addEventListener('change', function () {

        paginaActual = 1;

        mostrarPagina();

    });


    // CARGA INICIAL
    mostrarPagina();

});
</script>

@endpush

