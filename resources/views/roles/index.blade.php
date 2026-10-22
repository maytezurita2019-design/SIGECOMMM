@extends('layouts.app')

@section('title', 'Roles')


@push('styles')

<style>

    /* TABLA DE ROLES */

    .tabla-roles {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }


    /* DIVISIONES DE LAS CELDAS */

    .tabla-roles th,
    .tabla-roles td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }


    /* ENCABEZADO VERDE CLARO */

    .tabla-roles thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }


    /* FILAS */

    .tabla-roles tbody td {
        background-color: #ffffff;
    }


    /* AL PASAR EL MOUSE */

    .tabla-roles tbody tr:hover td {
        background-color: #f3faf5;
    }


    /* ANCHO DE COLUMNAS */

    .tabla-roles th:nth-child(1),
    .tabla-roles td:nth-child(1) {
        width: 7%;
    }

    .tabla-roles th:nth-child(2),
    .tabla-roles td:nth-child(2) {
        width: 23%;
    }

    .tabla-roles th:nth-child(3),
    .tabla-roles td:nth-child(3) {
        width: 40%;
    }

    .tabla-roles th:nth-child(4),
    .tabla-roles td:nth-child(4) {
        width: 15%;
        text-align: center;
    }

    .tabla-roles th:nth-child(5),
    .tabla-roles td:nth-child(5) {
        width: 15%;
        text-align: center;
    }

</style>

@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Roles
        </h2>

        <p class="text-muted mb-0">
            Administración de roles del sistema
        </p>

    </div>


    <a href="{{ route('roles.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>

        Nuevo rol

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">


            <table class="table table-bordered table-hover align-middle tabla-roles">


                <!-- ENCABEZADO -->

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nombre</th>

                        <th>Descripción</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <!-- DATOS -->

                <tbody>

                    @forelse($roles as $rol)

                        <tr>


                            <!-- ID -->

                            <td>
                                {{ $rol->id }}
                            </td>


                            <!-- NOMBRE -->

                            <td>

                                <strong>
                                    {{ $rol->nombre }}
                                </strong>

                            </td>


                            <!-- DESCRIPCIÓN -->

                            <td>

                                {{ $rol->descripcion ?? 'Sin descripción' }}

                            </td>


                            <!-- ESTADO -->

                            <td>

                                @if($rol->estado)

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
                                    href="{{ route('roles.show', $rol) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a
                                    href="{{ route('roles.edit', $rol) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>

                            </td>


                        </tr>


                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center py-4 text-muted">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen roles registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection