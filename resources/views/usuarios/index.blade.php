@extends('layouts.app')

@section('title', 'Usuarios')


@push('styles')

<style>

    /* ==========================================
       TABLA DE USUARIOS
    ========================================== */

    .tabla-usuarios {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }


    /* DIVISIONES DE LAS CELDAS */

    .tabla-usuarios th,
    .tabla-usuarios td {
        border: 1px solid #d6e2da !important;
        padding: 12px 10px;
        vertical-align: middle;
    }


    /* ENCABEZADO VERDE CLARO */

    .tabla-usuarios thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }


    /* FILAS */

    .tabla-usuarios tbody td {
        background-color: #ffffff;
    }


    /* AL PASAR EL MOUSE */

    .tabla-usuarios tbody tr:hover td {
        background-color: #f3faf5;
    }


    /* ==========================================
       ANCHO DE LAS 7 COLUMNAS
    ========================================== */

    /* ID */
    .tabla-usuarios th:nth-child(1),
    .tabla-usuarios td:nth-child(1) {
        width: 5%;
    }

    /* Nombre */
    .tabla-usuarios th:nth-child(2),
    .tabla-usuarios td:nth-child(2) {
        width: 19%;
    }

    /* CI */
    .tabla-usuarios th:nth-child(3),
    .tabla-usuarios td:nth-child(3) {
        width: 11%;
    }

    /* Correo */
    .tabla-usuarios th:nth-child(4),
    .tabla-usuarios td:nth-child(4) {
        width: 22%;
    }

    /* Rol */
    .tabla-usuarios th:nth-child(5),
    .tabla-usuarios td:nth-child(5) {
        width: 13%;
    }

    /* Estado */
    .tabla-usuarios th:nth-child(6),
    .tabla-usuarios td:nth-child(6) {
        width: 10%;
        text-align: center;
    }

    /* Acciones */
    .tabla-usuarios th:nth-child(7),
    .tabla-usuarios td:nth-child(7) {
        width: 20%;
        text-align: center;
    }

</style>

@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Usuarios
        </h2>

        <p class="text-muted mb-0">
            Administración de usuarios del sistema
        </p>

    </div>


    <a href="{{ route('usuarios.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nuevo usuario

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-usuarios">


                <!-- ENCABEZADO -->

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nombre</th>

                        <th>CI</th>

                        <th>Correo</th>

                        <th>Rol</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <!-- DATOS -->

                <tbody>

                    @forelse($usuarios as $usuario)

                        <tr>


                            <!-- ID -->

                            <td>
                                {{ $usuario->id }}
                            </td>


                            <!-- NOMBRE -->

                            <td>

                                <strong>
                                    {{ $usuario->nombre }}
                                    {{ $usuario->apellido }}
                                </strong>

                            </td>


                            <!-- CI -->

                            <td>
                                {{ $usuario->ci }}
                            </td>


                            <!-- CORREO -->

                            <td>
                                {{ $usuario->correo }}
                            </td>


                            <!-- ROL -->

                            <td>
                                {{ $usuario->rol->nombre ?? 'Sin rol' }}
                            </td>


                            <!-- ESTADO -->

                            <td>

                                @if($usuario->estado)

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

                                {{-- VER --}}
                                <a
                                    href="{{ route('usuarios.show', $usuario) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver usuario">

                                    <i class="bi bi-eye"></i>

                                </a>


                                {{-- EDITAR --}}
                                <a
                                    href="{{ route('usuarios.edit', $usuario) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar usuario">

                                    <i class="bi bi-pencil"></i>

                                </a>


                              

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center py-4 text-muted">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen usuarios registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection