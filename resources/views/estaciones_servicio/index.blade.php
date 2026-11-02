@extends('layouts.app')

@section('title', 'Estaciones de servicio')

@push('styles')

<style>

    /* ==========================================
       TABLA DE ESTACIONES DE SERVICIO
    ========================================== */

    .tabla-estaciones {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    /* DIVISIONES DE LAS CELDAS */

    .tabla-estaciones th,
    .tabla-estaciones td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }

    /* ENCABEZADO VERDE CLARO */

    .tabla-estaciones thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }

    /* FILAS */

    .tabla-estaciones tbody td {
        background-color: #ffffff;
    }

    /* AL PASAR EL MOUSE */

    .tabla-estaciones tbody tr:hover td {
        background-color: #f3faf5;
    }

    /* ==========================================
       ANCHO DE LAS 5 COLUMNAS
    ========================================== */

    /* Nombre */
    .tabla-estaciones th:nth-child(1),
    .tabla-estaciones td:nth-child(1) {
        width: 25%;
    }

    /* Dirección */
    .tabla-estaciones th:nth-child(2),
    .tabla-estaciones td:nth-child(2) {
        width: 30%;
    }

    /* Teléfono */
    .tabla-estaciones th:nth-child(3),
    .tabla-estaciones td:nth-child(3) {
        width: 15%;
        text-align: center;
    }

    /* Estado */
    .tabla-estaciones th:nth-child(4),
    .tabla-estaciones td:nth-child(4) {
        width: 15%;
        text-align: center;
    }

    /* Acciones */
    .tabla-estaciones th:nth-child(5),
    .tabla-estaciones td:nth-child(5) {
        width: 15%;
        text-align: center;
    }

</style>

@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Estaciones de servicio
        </h2>

        <p class="text-muted mb-0">
            Administración de estaciones proveedoras
        </p>

    </div>

    <a href="{{ route('estaciones-servicio.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nueva estación

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <form method="GET" action="{{ route('estaciones-servicio.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-8 col-lg-6">
                    <label for="buscar" class="form-label">Buscar estación</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-success">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="search"
                            name="buscar"
                            id="buscar"
                            class="form-control @error('buscar') is-invalid @enderror"
                            value="{{ request('buscar') }}"
                            placeholder="Nombre, dirección o teléfono"
                        >
                        @error('buscar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                        <option value="">Todas las estaciones</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activas</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivas</option>
                    </select>
                    @error('estado')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-12 col-lg-3 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>Buscar
                    </button>
                    <a href="{{ route('estaciones-servicio.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>
                        Limpiar filtros
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-estaciones">

                <thead>

                    <tr>

                        <th>Nombre</th>

                        <th>Dirección</th>

                        <th>Teléfono</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($estaciones as $estacionServicio)

                        <tr>

                            <!-- NOMBRE -->

                            <td>
                                <strong>
                                    {{ $estacionServicio->nombre }}
                                </strong>
                            </td>

                            <!-- DIRECCIÓN -->

                            <td>
                                {{ $estacionServicio->direccion ?? '-' }}
                            </td>

                            <!-- TELÉFONO -->

                            <td>
                                {{ $estacionServicio->telefono ?? '-' }}
                            </td>

                            <!-- ESTADO -->

                            <td>

                                @if($estacionServicio->estado)

                                    <span class="badge bg-success">
                                        Activo
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactivo
                                    </span>

                                @endif

                            </td>

                            <!-- ACCIONES -->

                            <td>

                                <a
                                    href="{{ route('estaciones-servicio.show', $estacionServicio) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver"
                                    aria-label="Ver estación de servicio">

                                    <i class="bi bi-eye"></i>

                                </a>

                                <a
                                    href="{{ route('estaciones-servicio.edit', $estacionServicio) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar"
                                    aria-label="Editar estación de servicio">

                                    <i class="bi bi-pencil"></i>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No se encontraron registros.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection