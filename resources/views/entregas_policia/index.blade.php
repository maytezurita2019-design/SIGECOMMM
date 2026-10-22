@extends('layouts.app')

@section('title', 'Asignaciones a Policía')

@push('styles')
<style>
    .pol-head h2 {
        color: #173f32;
        font-weight: 800;
    }

    .pol-card {
        border: 1px solid #dfe8e3;
        border-radius: 15px;
    }

    .pol-card thead th {
        background: #eaf5ef;
        color: #234d3a;
        white-space: nowrap;
    }

    .pol-num {
        font-weight: 800;
        color: #245d42;
    }

    .pol-resumen {
        display: flex;
        gap: .35rem;
        flex-wrap: wrap;
    }

    .pol-resumen span {
        background: #f1f5f3;
        border-radius: 999px;
        padding: .25rem .5rem;
        font-size: .78rem;
    }

    .btn-pol {
        background: #287a55;
        color: white;
    }

    .btn-pol:hover {
        background: #206646;
        color: white;
    }

    .pol-filtros label {
        color: #294c3e;
        font-size: .88rem;
    }

    @media(max-width: 767.98px) {
        .tabla-pol {
            min-width: 850px;
        }
    }
</style>
@endpush


@section('content')

{{-- ENCABEZADO --}}
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3 pol-head">

    <div>
        <h2>
            <i class="bi bi-shield-check me-2"></i>
            Asignaciones a Policía
        </h2>

        <p class="text-muted mb-0">
            Entrega, seguimiento, utilización y devolución de vales asignados.
        </p>
    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('vales-combustible.index', ['tipo' => 'GAMS']) }}"
            class="btn btn-outline-success"
        >
            Vales GAMS
        </a>

        <a
            href="{{ route('entregas-policia.create') }}"
            class="btn btn-pol"
        >
            <i class="bi bi-plus-circle me-1"></i>
            Nueva entrega
        </a>

    </div>

</div>


{{-- FILTROS --}}
<div class="card pol-card shadow-sm mb-3">

    <div class="card-body pol-filtros">

        <form
            method="GET"
            action="{{ route('entregas-policia.index') }}"
        >

            <div class="row g-3 align-items-end">


                {{-- MES --}}
                <div class="col-lg-2 col-md-3">

                    <label class="form-label fw-semibold">
                        Mes
                    </label>

                    <select
                        name="mes"
                        class="form-select"
                    >

                        <option value="">Todos</option>

                        <option value="1" @selected(request('mes') == '1')>
                            Enero
                        </option>

                        <option value="2" @selected(request('mes') == '2')>
                            Febrero
                        </option>

                        <option value="3" @selected(request('mes') == '3')>
                            Marzo
                        </option>

                        <option value="4" @selected(request('mes') == '4')>
                            Abril
                        </option>

                        <option value="5" @selected(request('mes') == '5')>
                            Mayo
                        </option>

                        <option value="6" @selected(request('mes') == '6')>
                            Junio
                        </option>

                        <option value="7" @selected(request('mes') == '7')>
                            Julio
                        </option>

                        <option value="8" @selected(request('mes') == '8')>
                            Agosto
                        </option>

                        <option value="9" @selected(request('mes') == '9')>
                            Septiembre
                        </option>

                        <option value="10" @selected(request('mes') == '10')>
                            Octubre
                        </option>

                        <option value="11" @selected(request('mes') == '11')>
                            Noviembre
                        </option>

                        <option value="12" @selected(request('mes') == '12')>
                            Diciembre
                        </option>

                    </select>

                </div>


                {{-- AÑO --}}
                <div class="col-lg-2 col-md-3">

                    <label class="form-label fw-semibold">
                        Año
                    </label>

                    <select
                        name="anio"
                        class="form-select"
                    >

                        <option value="">Todos</option>

                        @foreach($anios as $anio)

                            <option
                                value="{{ $anio }}"
                                @selected(request('anio') == $anio)
                            >
                                {{ $anio }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ESTADO --}}
                <div class="col-lg-3 col-md-3">

                    <label class="form-label fw-semibold">
                        Estado de vales
                    </label>

                    <select
                        name="estado"
                        class="form-select"
                    >

                        <option value="">Todos</option>

                        <option
                            value="ENTREGADO"
                            @selected(request('estado') === 'ENTREGADO')
                        >
                            Con pendientes
                        </option>

                        <option
                            value="UTILIZADO"
                            @selected(request('estado') === 'UTILIZADO')
                        >
                            Con utilizados
                        </option>

                        <option
                            value="DEVUELTO"
                            @selected(request('estado') === 'DEVUELTO')
                        >
                            Con devueltos
                        </option>

                        <option
                            value="ANULADO"
                            @selected(request('estado') === 'ANULADO')
                        >
                            Con anulados
                        </option>

                    </select>

                </div>


                {{-- MOSTRAR --}}
                <div class="col-lg-2 col-md-3">

                    <label class="form-label fw-semibold">
                        Mostrar
                    </label>

                    <select
                        name="por_pagina"
                        class="form-select"
                    >

                        @foreach([10, 30] as $n)

                            <option
                                value="{{ $n }}"
                                @selected($porPagina === $n)
                            >
                                {{ $n }} registros
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BOTONES --}}
                <div class="col-lg-3 col-md-12">

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-pol"
                        >
                            <i class="bi bi-funnel me-1"></i>
                            Filtrar
                        </button>

                        <a
                            href="{{ route('entregas-policia.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Limpiar
                        </a>

                    </div>

                </div>

            </div>


            {{-- CANTIDAD DE RESULTADOS --}}
            <div class="text-muted mt-3 text-end">

                Mostrando
                {{ $entregas->firstItem() ?? 0 }}
                –
                {{ $entregas->lastItem() ?? 0 }}
                de
                {{ $entregas->total() }}
                entregas

            </div>

        </form>

    </div>

</div>


{{-- TABLA --}}
<div class="card pol-card shadow-sm">

    <div class="table-responsive">

        <table class="table align-middle mb-0 tabla-pol">

            <thead>

                <tr>
                    <th>Entrega</th>
                    <th>Contrato / Dependencia</th>
                    <th>Año / Rango</th>
                    <th>Resumen de vales</th>
                    <th>Acción</th>
                </tr>

            </thead>


            <tbody>

                @forelse($entregas as $e)

                    <tr>

                        {{-- ENTREGA --}}
                        <td>

                            <strong>
                                {{ $e->fecha_entrega->format('d/m/Y') }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                Recibió: {{ $e->recibido_por }}
                            </small>

                        </td>


                        {{-- CONTRATO / DEPENDENCIA --}}
                        <td>

                            {{ $e->contrato }}

                            <br>

                            <small class="text-muted">
                                {{ $e->dependencia }}
                            </small>

                        </td>


                        {{-- AÑO / RANGO --}}
                        <td>

                            {{ $e->loteVale->periodo->anio }}

                            <br>

                            <span class="pol-num">
                                {{ $e->numero_inicial }}
                                –
                                {{ $e->numero_final }}
                            </span>

                        </td>


                        {{-- RESUMEN DE VALES --}}
                        <td>

                            <div class="pol-resumen">

                                <span>
                                    Pendientes:
                                    <b>{{ $e->pendientes }}</b>
                                </span>

                                <span>
                                    Utilizados:
                                    <b>{{ $e->utilizados }}</b>
                                </span>

                                <span>
                                    Devueltos:
                                    <b>{{ $e->devueltos }}</b>
                                </span>

                                <span>
                                    Anulados:
                                    <b>{{ $e->anulados }}</b>
                                </span>

                            </div>

                        </td>


                        {{-- ACCIÓN --}}
                        <td>

                            <a
                                href="{{ route('entregas-policia.show', $e) }}"
                                class="btn btn-sm btn-outline-success"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Ver entrega
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-5"
                        >
                            <i class="bi bi-search fs-4 d-block mb-2"></i>

                            No se encontraron entregas con los filtros seleccionados.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- PAGINACIÓN --}}
<div class="mt-3">

    {{ $entregas->links() }}

</div>

@endsection