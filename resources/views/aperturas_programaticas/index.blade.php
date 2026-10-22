@extends('layouts.app')

@section('title', 'Aperturas programáticas')


@push('styles')

<style>

    /* ==========================================
       TABLA DE APERTURAS PROGRAMÁTICAS
    ========================================== */

    .tabla-aperturas {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }


    /* DIVISIONES */

    .tabla-aperturas th,
    .tabla-aperturas td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }


    /* ENCABEZADO */

    .tabla-aperturas thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }


    /* FILAS */

    .tabla-aperturas tbody td {
        background-color: #ffffff;
    }


    /* EFECTO AL PASAR EL MOUSE */

    .tabla-aperturas tbody tr:hover td {
        background-color: #f3faf5;
    }


    /* ==========================================
       ANCHO DE LAS 6 COLUMNAS
    ========================================== */

    /* Código */
    .tabla-aperturas th:nth-child(1),
    .tabla-aperturas td:nth-child(1) {
        width: 12%;
    }

    /* Descripción */
    .tabla-aperturas th:nth-child(2),
    .tabla-aperturas td:nth-child(2) {
        width: 27%;
    }

    /* Fuente */
    .tabla-aperturas th:nth-child(3),
    .tabla-aperturas td:nth-child(3) {
        width: 19%;
    }

    /* Organismo */
    .tabla-aperturas th:nth-child(4),
    .tabla-aperturas td:nth-child(4) {
        width: 19%;
    }

    /* Estado */
    .tabla-aperturas th:nth-child(5),
    .tabla-aperturas td:nth-child(5) {
        width: 10%;
        text-align: center;
    }

    /* Acciones */
    .tabla-aperturas th:nth-child(6),
    .tabla-aperturas td:nth-child(6) {
        width: 13%;
        text-align: center;
    }

</style>

@endpush


@section('content')


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Aperturas programáticas
        </h2>

        <p class="text-muted mb-0">
            Administración de aperturas programáticas
        </p>

    </div>


    <a href="{{ route('aperturas-programaticas.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>

        Nueva apertura

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-aperturas">


                <!-- ENCABEZADO -->

                <thead>

                    <tr>

                        <th>
                            Código
                        </th>

                        <th>
                            Descripción
                        </th>

                        <th>
                            Fuente
                        </th>

                        <th>
                            Organismo
                        </th>

                        <th>
                            Estado
                        </th>

                        <th>
                            Acciones
                        </th>

                    </tr>

                </thead>


                <!-- DATOS -->

                <tbody>

                    @forelse($aperturas as $aperturaProgramatica)

                        <tr>


                            <!-- CÓDIGO -->

                            <td>
                                <strong>
                                    {{ $aperturaProgramatica->codigo }}
                                </strong>
                            </td>


                            <!-- DESCRIPCIÓN -->

                            <td>
                                {{ $aperturaProgramatica->descripcion }}
                            </td>


                            <!-- FUENTE DE FINANCIAMIENTO -->

                            <td>
                                {{ $aperturaProgramatica->fuenteFinanciamiento->nombre ?? '-' }}
                            </td>


                            <!-- ORGANISMO FINANCIADOR -->

                            <td>
                                {{ $aperturaProgramatica->organismoFinanciador->nombre ?? '-' }}
                            </td>


                            <!-- ESTADO -->

                            <td>

                                @if($aperturaProgramatica->estado)

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
                                    href="{{ route('aperturas-programaticas.show', $aperturaProgramatica) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a
                                    href="{{ route('aperturas-programaticas.edit', $aperturaProgramatica) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>


                               

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen aperturas programáticas registradas.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


@endsection