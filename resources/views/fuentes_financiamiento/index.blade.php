@extends('layouts.app')

@section('title', 'Fuentes de financiamiento')

@push('styles')
<style>
    .tabla-fuentes {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    .tabla-fuentes th,
    .tabla-fuentes td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }

    .tabla-fuentes thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }

    .tabla-fuentes tbody td {
        background-color: #ffffff;
    }

    .tabla-fuentes tbody tr:hover td {
        background-color: #f3faf5;
    }

    /* Código */
    .tabla-fuentes th:nth-child(1),
    .tabla-fuentes td:nth-child(1) {
        width: 25%;
    }

    /* Nombre */
    .tabla-fuentes th:nth-child(2),
    .tabla-fuentes td:nth-child(2) {
        width: 40%;
    }

    /* Estado */
    .tabla-fuentes th:nth-child(3),
    .tabla-fuentes td:nth-child(3) {
        width: 15%;
        text-align: center;
    }

    /* Acciones */
    .tabla-fuentes th:nth-child(4),
    .tabla-fuentes td:nth-child(4) {
        width: 20%;
        text-align: center;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">
            Fuentes de financiamiento
        </h2>

        <p class="text-muted mb-0">
            Administración de fuentes de financiamiento
        </p>
    </div>

    <a href="{{ route('fuentes-financiamiento.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nueva fuente

    </a>

</div>

<div class="card shadow-sm">

    <div class="card-body">
        <div class="table-responsive">
        <div class="d-flex justify-content-end mb-3">

    <form method="GET"
          action="{{ route('fuentes-financiamiento.index') }}"
          class="d-flex align-items-center gap-2">

        <label for="estado" class="fw-semibold mb-0">
            Mostrar:
        </label>

        <select name="estado"
                id="estado"
                class="form-select"
                onchange="this.form.submit()">

            <option value="activos"
                {{ $estado === 'activos' ? 'selected' : '' }}>
                Activos
            </option>

            <option value="inactivos"
                {{ $estado === 'inactivos' ? 'selected' : '' }}>
                Inactivos
            </option>

            <option value="todos"
                {{ $estado === 'todos' ? 'selected' : '' }}>
                Todos
            </option>

        </select>

    </form>

</div>

        

            <table class="table table-bordered table-hover align-middle tabla-fuentes">

                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($fuentes as $fuenteFinanciamiento)

                        <tr>

                            <td>
                                <strong>
                                    {{ $fuenteFinanciamiento->codigo }}
                                </strong>
                            </td>

                            <td>
                                {{ $fuenteFinanciamiento->nombre }}
                            </td>

                            <td>
                                @if($fuenteFinanciamiento->estado)
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

                                <a href="{{ route('fuentes-financiamiento.show', $fuenteFinanciamiento) }}"
                                   class="btn btn-sm btn-info text-white"
                                   title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>

                                <a href="{{ route('fuentes-financiamiento.edit', $fuenteFinanciamiento) }}"
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
                                No existen fuentes registradas.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection