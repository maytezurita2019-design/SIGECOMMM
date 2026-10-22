@extends('layouts.app')

@section('title', 'Organismos financiadores')

@push('styles')
<style>

    /* ==========================================
       TABLA DE ORGANISMOS FINANCIADORES
    ========================================== */

    .tabla-organismos {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    .tabla-organismos th,
    .tabla-organismos td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }

    .tabla-organismos thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }

    .tabla-organismos tbody td {
        background-color: #ffffff;
    }

    .tabla-organismos tbody tr:hover td {
        background-color: #f3faf5;
    }

    /* ==========================================
       ANCHO DE LAS 4 COLUMNAS
    ========================================== */

    /* Código */
    .tabla-organismos th:nth-child(1),
    .tabla-organismos td:nth-child(1) {
        width: 25%;
    }

    /* Nombre */
    .tabla-organismos th:nth-child(2),
    .tabla-organismos td:nth-child(2) {
        width: 40%;
    }

    /* Estado */
    .tabla-organismos th:nth-child(3),
    .tabla-organismos td:nth-child(3) {
        width: 15%;
        text-align: center;
    }

    /* Acciones */
    .tabla-organismos th:nth-child(4),
    .tabla-organismos td:nth-child(4) {
        width: 20%;
        text-align: center;
    }

</style>
@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">
            Organismos financiadores
        </h2>

        <p class="text-muted mb-0">
            Administración de organismos financiadores
        </p>
    </div>

    <a href="{{ route('organismos-financiadores.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nuevo organismo

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-organismos">

                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($organismos as $organismoFinanciador)

                        <tr>

                            <td>
                                <strong>
                                    {{ $organismoFinanciador->codigo }}
                                </strong>
                            </td>

                            <td>
                                {{ $organismoFinanciador->nombre }}
                            </td>

                            <td>

                                @if($organismoFinanciador->estado)

                                    <span class="badge bg-success">
                                        Activo
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Inactivo
                                    </span>

                                @endif

                            </td>

                            <td>

                                <a href="{{ route('organismos-financiadores.show', $organismoFinanciador) }}"
                                   class="btn btn-sm btn-info text-white"
                                   title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>

                                <a href="{{ route('organismos-financiadores.edit', $organismoFinanciador) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen organismos financiadores registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection