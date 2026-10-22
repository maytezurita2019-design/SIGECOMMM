@extends('layouts.app')

@section('title', 'Tipos de combustible')


@push('styles')

<style>

    /* ==========================================
       TABLA DE TIPOS DE COMBUSTIBLE
    ========================================== */

    .tabla-tipos-combustible {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }


    /* DIVISIONES DE LAS CELDAS */

    .tabla-tipos-combustible th,
    .tabla-tipos-combustible td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }


    /* ENCABEZADO VERDE CLARO */

    .tabla-tipos-combustible thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }


    /* FILAS */

    .tabla-tipos-combustible tbody td {
        background-color: #ffffff;
    }


    /* AL PASAR EL MOUSE */

    .tabla-tipos-combustible tbody tr:hover td {
        background-color: #f3faf5;
    }


    /* ==========================================
       ANCHO DE LAS 4 COLUMNAS
    ========================================== */


    /* Nombre */
    .tabla-tipos-combustible th:nth-child(2),
    .tabla-tipos-combustible td:nth-child(2) {
        width: 20%;
    }

    /* Unidad de medida */
    .tabla-tipos-combustible th:nth-child(3),
    .tabla-tipos-combustible td:nth-child(3) {
        width: 30%;
    }

    /* Estado */
    .tabla-tipos-combustible th:nth-child(4),
    .tabla-tipos-combustible td:nth-child(4) {
        width: 20%;
        text-align: center;
    }

    /* Acciones */
    .tabla-tipos-combustible th:nth-child(5),
    .tabla-tipos-combustible td:nth-child(5) {
        width: 40%;
        text-align: center;
    }

</style>

@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Tipos de combustible
        </h2>

        <p class="text-muted mb-0">
            Administración de combustibles
        </p>

    </div>


    <a href="{{ route('tipos-combustible.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nuevo tipo

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-tipos-combustible">


                <!-- ENCABEZADO -->

                <thead>

                    <tr>

                        

                        <th>Nombre</th>

                        <th>Unidad de medida</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <!-- DATOS -->

                <tbody>

                    @forelse($tiposCombustible as $tipoCombustible)

                        <tr>



                            <!-- NOMBRE -->

                            <td>

                                <strong>
                                    {{ $tipoCombustible->nombre }}
                                </strong>

                            </td>


                            <!-- UNIDAD DE MEDIDA -->

                            <td>
                                {{ $tipoCombustible->unidad_medida }}
                            </td>


                            <!-- ESTADO -->

                            <td>

                                @if($tipoCombustible->estado)

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
                                    href="{{ route('tipos-combustible.show', $tipoCombustible) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a
                                    href="{{ route('tipos-combustible.edit', $tipoCombustible) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>


                                <form
                                    action="{{ route('tipos-combustible.destroy', $tipoCombustible) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Eliminar"
                                        onclick="return confirm('¿Eliminar este tipo de combustible?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen tipos de combustible registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection