@extends('layouts.app')

@section('title', 'Periodos')


@push('styles')

<style>

    /* ==========================================
       TABLA DE PERIODOS
    ========================================== */

    .tabla-periodos {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }


    /* DIVISIONES DE LAS CELDAS */

    .tabla-periodos th,
    .tabla-periodos td {
        border: 1px solid #d6e2da !important;
        padding: 12px 10px;
        vertical-align: middle;
    }


    /* ENCABEZADO VERDE CLARO */

    .tabla-periodos thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }


    /* FILAS */

    .tabla-periodos tbody td {
        background-color: #ffffff;
    }


    /* AL PASAR EL MOUSE */

    .tabla-periodos tbody tr:hover td {
        background-color: #f3faf5;
    }



   /* ==========================================
   ANCHO DE LAS 6 COLUMNAS
========================================== */


/* Mes */
.tabla-periodos th:nth-child(1),
.tabla-periodos td:nth-child(1) {
    width: 22%;
}

/* Año */
.tabla-periodos th:nth-child(2),
.tabla-periodos td:nth-child(2) {
    width: 10%;
    text-align: center;
}

/* Inicio */
.tabla-periodos th:nth-child(3),
.tabla-periodos td:nth-child(3) {
    width: 16%;
    text-align: center;
}

/* Fin */
.tabla-periodos th:nth-child(4),
.tabla-periodos td:nth-child(4) {
    width: 16%;
    text-align: center;
}

/* Estado */
.tabla-periodos th:nth-child(5),
.tabla-periodos td:nth-child(5) {
    width: 16%;
    text-align: center;
}

/* Acciones */
.tabla-periodos th:nth-child(6),
.tabla-periodos td:nth-child(6) {
    width: 20%;
    text-align: center;
}
</style>

@endpush


@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="mb-1">
            Periodos
        </h2>

        <p class="text-muted mb-0">
            Gestión de periodos
        </p>

    </div>


    <a href="{{ route('periodos.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-circle me-1"></i>
        Nuevo periodo

    </a>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-periodos">


                <!-- ENCABEZADO -->

                <thead>

                    <tr>

                   
                        <th>Mes</th>

                        <th>Año</th>

                        <th>Inicio</th>

                        <th>Fin</th>

                        <th>Estado</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <!-- DATOS -->

                <tbody>

                    @forelse($periodos as $periodo)

                        <tr>


                            <!-- MES -->

<td>
    @php
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
    @endphp

    {{ $meses[$periodo->mes] ?? 'Sin definir' }}
</td>


                            <!-- AÑO -->

                            <td>
                                {{ $periodo->anio }}
                            </td>


                            <!-- FECHA INICIO -->

                            <td>
                                {{ $periodo->fecha_inicio?->format('d/m/Y') }}
                            </td>


                            <!-- FECHA FIN -->

                            <td>
                                {{ $periodo->fecha_fin?->format('d/m/Y') }}
                            </td>


                            <!-- ESTADO -->

                            <td>

                                @if($periodo->estado)

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
                                    href="{{ route('periodos.show', $periodo) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a
                                    href="{{ route('periodos.edit', $periodo) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>


                                <form
                                    action="{{ route('periodos.destroy', $periodo) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Eliminar"
                                        onclick="return confirm('¿Eliminar este periodo?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen periodos registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection