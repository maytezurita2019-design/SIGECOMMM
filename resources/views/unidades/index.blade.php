@extends('layouts.app')

@section('title', 'Unidades')


@push('styles')

<style>

    /* ==========================================
       TABLA DE UNIDADES
    ========================================== */

    .tabla-unidades {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }


    /* DIVISIONES DE LAS CELDAS */

    .tabla-unidades th,
    .tabla-unidades td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }


    /* ENCABEZADO VERDE CLARO */

    .tabla-unidades thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }


    /* FILAS */

    .tabla-unidades tbody td {
        background-color: #ffffff;
    }


    /* AL PASAR EL MOUSE */

    .tabla-unidades tbody tr:hover td {
        background-color: #f3faf5;
    }


    /* ==========================================
       ANCHO DE LAS 6 COLUMNAS
    ========================================== */

    /* ID */
    .tabla-unidades th:nth-child(1),
    .tabla-unidades td:nth-child(1) {
        width: 7%;
    }

    /* Nombre */
    .tabla-unidades th:nth-child(2),
    .tabla-unidades td:nth-child(2) {
        width: 30%;
    }

    /* Sigla */
    .tabla-unidades th:nth-child(3),
    .tabla-unidades td:nth-child(3) {
        width: 13%;
        text-align: center;
    }

    /* Tipo */
    .tabla-unidades th:nth-child(4),
    .tabla-unidades td:nth-child(4) {
        width: 20%;
    }

    /* Estado */
    .tabla-unidades th:nth-child(5),
    .tabla-unidades td:nth-child(5) {
        width: 12%;
        text-align: center;
    }

    /* Acciones */
    .tabla-unidades th:nth-child(6),
    .tabla-unidades td:nth-child(6) {
        width: 18%;
        text-align: center;
    }

</style>

@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Unidades
        </h2>

        <p class="text-muted mb-0">
            Unidades organizacionales y beneficiarias
        </p>

    </div>


    <a href="{{ route('unidades.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nueva unidad

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-unidades">


                <!-- ENCABEZADO -->

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nombre</th>

                        <th>Sigla</th>

                        <th>Tipo</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <!-- DATOS -->

                <tbody>

                    @forelse($unidades as $unidad)

                        <tr>


                            <!-- ID -->

                            <td>
                                {{ $unidad->id }}
                            </td>


                            <!-- NOMBRE -->

                            <td>

                                <strong>
                                    {{ $unidad->nombre }}
                                </strong>

                            </td>


                            <!-- SIGLA -->

                            <td>
                                {{ $unidad->sigla ?? '-' }}
                            </td>


                            <!-- TIPO -->

                            <td>
                                {{ $unidad->tipo ?? '-' }}
                            </td>


                            <!-- ESTADO -->

                            <td>

                                @if($unidad->estado)

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
                                    href="{{ route('unidades.show', $unidad) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a
                                    href="{{ route('unidades.edit', $unidad) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>


                                <form
                                    action="{{ route('unidades.destroy', $unidad) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Eliminar"
                                        onclick="return confirm('¿Eliminar esta unidad?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen unidades registradas.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection