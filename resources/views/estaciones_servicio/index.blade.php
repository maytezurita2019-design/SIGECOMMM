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
                                    title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>

                                <a
                                    href="{{ route('estaciones-servicio.edit', $estacionServicio) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen estaciones registradas.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection