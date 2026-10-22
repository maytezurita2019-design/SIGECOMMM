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


{{-- BUSCADOR --}}
<div class="card mb-3">
    <div class="card-body">

        <div class="input-group">

            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>

            <input
                id="buscarVehiculo"
                class="form-control"
                placeholder="Buscar por código, placa, tipo, marca o unidad"
                autocomplete="off">

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
                    $vehiculo->codigo.' '.
                    $vehiculo->placa.' '.
                    $vehiculo->tipo.' '.
                    $vehiculo->marca.' '.
                    ($vehiculo->unidad->nombre ?? '')
                ) }}">

                    <td data-label="Código">
                        <strong>
                            {{ $vehiculo->codigo }}
                        </strong>
                    </td>

                    <td data-label="Placa">
                        <span class="fw-bold">
                            {{ $vehiculo->placa }}
                        </span>
                    </td>

                    <td data-label="Tipo / Marca">

                        {{ $vehiculo->tipo ?: '—' }}

                        @if($vehiculo->marca)
                            <span class="text-muted">
                                · {{ $vehiculo->marca }}
                            </span>
                        @endif

                    </td>

                    <td data-label="Unidad">
                        {{ $vehiculo->unidad->nombre ?? 'Sin unidad' }}
                    </td>

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

                        <div class="d-flex gap-1 flex-nowrap align-items-center">

                            {{-- VER --}}
                            <a href="{{ route('vehiculos.show', $vehiculo) }}"
                               class="btn btn-sm btn-outline-success">

                                <i class="bi bi-eye me-1"></i>
                                Ver

                            </a>


                            @if($esAdmin)

                                {{-- EDITAR --}}
                                <a href="{{ route('vehiculos.edit', $vehiculo) }}"
                                   class="btn btn-sm btn-outline-success">

                                    <i class="bi bi-pencil me-1"></i>
                                    Editar

                                </a>


                                {{-- ELIMINAR --}}
                                <form
                                    action="{{ route('vehiculos.destroy', $vehiculo) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Está seguro de eliminar este vehículo? Esta acción no se puede deshacer.');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger">

                                        <i class="bi bi-trash me-1"></i>
                                        Eliminar

                                    </button>

                                </form>

                            @endif


                            {{-- REPORTE --}}
                            <a href="{{ route(
                                    'reportes.vehiculos',
                                    ['vehiculo_id' => $vehiculo->id]
                                ) }}"
                               class="btn btn-sm btn-outline-secondary">

                                <i class="bi bi-bar-chart me-1"></i>
                                Reporte

                            </a>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6"
                        class="text-center text-muted py-5">

                        <i class="bi bi-car-front fs-2 d-block mb-2"></i>

                        No hay vehículos registrados.

                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>


<div id="sinResultados"
     class="text-center text-muted py-4 d-none">

    No se encontraron vehículos con esa búsqueda.

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const input =
        document.getElementById('buscarVehiculo');

    const filas =
        [...document.querySelectorAll(
            '#tablaVehiculos tbody tr[data-search]'
        )];

    const sinResultados =
        document.getElementById('sinResultados');


    input?.addEventListener('input', () => {

        const busqueda =
            input.value.toUpperCase().trim();

        let encontrados = 0;

        filas.forEach(fila => {

            const coincide =
                !busqueda ||
                fila.dataset.search.includes(busqueda);

            fila.classList.toggle(
                'd-none',
                !coincide
            );

            if (coincide) {
                encontrados++;
            }

        });

        sinResultados?.classList.toggle(
            'd-none',
            encontrados > 0
        );

    });

});

</script>

@endpush