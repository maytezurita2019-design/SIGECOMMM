@extends('layouts.app')

@section('title', 'Unidades')

@push('styles')
<style>

    .tabla-unidades {
        table-layout: fixed;
        width: 100%;
        border-collapse: collapse;
    }

    .tabla-unidades th,
    .tabla-unidades td {
        border: 1px solid #d6e2da !important;
        padding: 12px 15px;
        vertical-align: middle;
    }

    .tabla-unidades thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }

    .tabla-unidades tbody td {
        background-color: #ffffff;
    }

    .tabla-unidades tbody tr:hover td {
        background-color: #f3faf5;
    }

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

    /* CONTROLES */
    .control-tabla {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .control-tabla .form-select {
        width: 70px;
    }

    .buscador-unidades {
        width: 230px;
    }

    @media (max-width: 768px) {

        .buscador-unidades {
            width: 100%;
        }

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


        {{-- MOSTRAR Y BUSCADOR --}}
        <div class="control-tabla">

            {{-- MOSTRAR --}}
            <div class="d-flex align-items-center gap-2">

                <span class="small">
                    Mostrar
                </span>

                <select id="cantidadUnidades"
                        class="form-select form-select-sm">

                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>

                </select>

                <span class="small">
                    unidades
                </span>

            </div>


            {{-- BUSCADOR --}}
            <div class="d-flex align-items-center gap-2">

                <label for="buscarUnidad"
                       class="small mb-0">

                    Buscador:

                </label>

                <input type="text"
                       id="buscarUnidad"
                       class="form-control form-control-sm buscador-unidades"
                       autocomplete="off">

            </div>

        </div>


        {{-- TABLA --}}
        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-unidades"
                   id="tablaUnidades">

                <thead>

                    <tr>

                        <th>Nro</th>
                        <th>Nombre</th>
                        <th>Sigla</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($unidades as $unidad)

                        <tr data-search="{{ mb_strtoupper(
                            $unidad->id . ' ' .
                            $unidad->nombre . ' ' .
                            ($unidad->sigla ?? '') . ' ' .
                            ($unidad->tipo ?? '') . ' ' .
                            ($unidad->estado ? 'ACTIVO' : 'INACTIVO')
                        ) }}">

                            {{-- ID --}}
                            <td class="numero-fila"></td>


                            {{-- NOMBRE --}}
                            <td>

                                <strong>
                                    {{ $unidad->nombre }}
                                </strong>

                            </td>


                            {{-- SIGLA --}}
                            <td>
                                {{ $unidad->sigla ?? '-' }}
                            </td>


                            {{-- TIPO --}}
                            <td>
                                {{ $unidad->tipo ?? '-' }}
                            </td>


                            {{-- ESTADO --}}
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


                            {{-- ACCIONES --}}
                            <td>

                                <a href="{{ route('unidades.show', $unidad) }}"
                                   class="btn btn-sm btn-info text-white"
                                   title="Ver">

                                    <i class="bi bi-eye"></i>

                                </a>


                                <a href="{{ route('unidades.edit', $unidad) }}"
                                   class="btn btn-sm btn-warning"
                                   title="Editar">

                                    <i class="bi bi-pencil"></i>

                                </a>


                                <form action="{{ route('unidades.destroy', $unidad) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
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


        {{-- PARTE INFERIOR --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">

            <div id="infoUnidades"
                 class="small text-muted">
            </div>

            <ul id="paginacionUnidades"
                class="pagination pagination-sm mb-0">
            </ul>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const buscador =
        document.getElementById('buscarUnidad');

    const selector =
        document.getElementById('cantidadUnidades');

    const filas = [
        ...document.querySelectorAll(
            '#tablaUnidades tbody tr[data-search]'
        )
    ];

    const info =
        document.getElementById('infoUnidades');

    const paginacion =
        document.getElementById('paginacionUnidades');

    let paginaActual = 1;


    function obtenerFiltrados() {

        const texto = buscador.value
            .toUpperCase()
            .trim();

        return filas.filter(function (fila) {

            return fila.dataset.search.includes(texto);

        });
    }


    function mostrarPagina() {

        const filtrados = obtenerFiltrados();

        const cantidad =
            parseInt(selector.value);

        const total =
            filtrados.length;

        const totalPaginas =
            Math.max(1, Math.ceil(total / cantidad));

        if (paginaActual > totalPaginas) {
            paginaActual = totalPaginas;
        }

        const inicio =
            (paginaActual - 1) * cantidad;

        const fin =
            inicio + cantidad;


        {{-- OCULTAR TODAS --}}
        filas.forEach(function (fila) {

            fila.classList.add('d-none');

        });


        {{-- MOSTRAR LAS DE LA PÁGINA --}}
        filtrados
    .slice(inicio, fin)
    .forEach(function (fila, indice) {

        fila.classList.remove('d-none');

        const numero =
            fila.querySelector('.numero-fila');

        if (numero) {
            numero.textContent =
                inicio + indice + 1;
        }

    });


        {{-- INFORMACIÓN --}}
        if (total === 0) {

            info.textContent =
                'No se encontraron unidades.';

        } else {

            info.textContent =
                'Mostrando ' +
                (inicio + 1) +
                ' a ' +
                Math.min(fin, total) +
                ' de ' +
                total +
                ' unidades';
        }


        crearPaginacion(totalPaginas);
    }


    function crearPaginacion(totalPaginas) {

        paginacion.innerHTML = '';

        if (totalPaginas <= 1) {
            return;
        }


        {{-- ANTERIOR --}}
        agregarBoton(
            '‹ Anterior',
            paginaActual - 1,
            paginaActual === 1
        );


        {{-- SOLO 3 NÚMEROS --}}
        let inicio =
            Math.max(1, paginaActual - 1);

        let fin =
            Math.min(totalPaginas, inicio + 2);

        inicio =
            Math.max(1, fin - 2);


        for (
            let pagina = inicio;
            pagina <= fin;
            pagina++
        ) {

            agregarBoton(
                pagina,
                pagina,
                false,
                pagina === paginaActual
            );
        }


        {{-- SIGUIENTE --}}
        agregarBoton(
            'Siguiente ›',
            paginaActual + 1,
            paginaActual === totalPaginas
        );
    }


    function agregarBoton(
        texto,
        pagina,
        deshabilitado = false,
        activo = false
    ) {

        const li =
            document.createElement('li');

        li.className =
            'page-item' +
            (deshabilitado ? ' disabled' : '') +
            (activo ? ' active' : '');


        const boton =
            document.createElement('button');

        boton.type = 'button';
        boton.className = 'page-link';
        boton.textContent = texto;


        if (!deshabilitado) {

            boton.addEventListener(
                'click',
                function () {

                    paginaActual = pagina;

                    mostrarPagina();

                }
            );
        }


        li.appendChild(boton);

        paginacion.appendChild(li);
    }


    {{-- BUSCADOR AUTOMÁTICO --}}
    buscador.addEventListener(
        'input',
        function () {

            paginaActual = 1;

            mostrarPagina();

        }
    );


    {{-- CAMBIAR CANTIDAD --}}
    selector.addEventListener(
        'change',
        function () {

            paginaActual = 1;

            mostrarPagina();

        }
    );


    {{-- CARGA INICIAL --}}
    mostrarPagina();

});
</script>

@endpush