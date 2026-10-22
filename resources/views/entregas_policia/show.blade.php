@extends('layouts.app')

@section('title', 'Detalle de entrega a Policía')

@push('styles')
<style>
    .ep-title {
        color: #173f32;
        font-weight: 800;
    }

    .ep-info {
        border: 1px solid #dfe8e3;
        border-radius: 14px;
        background: #fff;
    }

    .ep-info .dato {
        padding: .65rem;
    }

    .ep-info small {
        color: #718078;
        display: block;
    }

    .ep-table thead th {
        background: #eaf5ef;
        color: #234d3a;
        white-space: nowrap;
    }

    .ep-badge {
        border-radius: 999px;
        padding: .35rem .6rem;
        font-size: .76rem;
        font-weight: 800;
    }

    .ep-pend {
        background: #fff5d9;
        color: #765900;
    }

    .ep-ok {
        background: #e7f6ed;
        color: #1d6b42;
    }

    .ep-off {
        background: #edf3ef;
        color: #53675d;
    }

    .ep-no {
        background: #fdeaea;
        color: #9d3030;
    }

    .btn-ep {
        background: #287a55;
        color: #fff;
    }

    .btn-ep:hover {
        background: #206646;
        color: #fff;
    }

    @media(max-width: 767.98px) {
        .ep-actions .btn {
            width: 100%;
            margin-bottom: .35rem;
        }
    }
</style>
@endpush


@section('content')

{{-- =========================================================
     ENCABEZADO
========================================================= --}}
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">

    <div>

        <h2 class="ep-title">
            Entrega {{ $entregaPolicia->numero_inicial }}
            –
            {{ $entregaPolicia->numero_final }}
        </h2>

        <p class="text-muted mb-0">
            Seguimiento de los vales asignados a Policía.
        </p>

    </div>


    <div class="d-flex gap-2 flex-wrap ep-actions">

        <a
            class="btn btn-ep"
            target="_blank"
            href="{{ route('entregas-policia.imprimir', $entregaPolicia) }}"
        >
            <i class="bi bi-printer me-1"></i>
            Imprimir pendientes / PDF
        </a>


        <a
            class="btn btn-outline-secondary"
            href="{{ route('entregas-policia.index') }}"
        >
            Volver
        </a>

    </div>

</div>


{{-- =========================================================
     INFORMACIÓN DE LA ENTREGA
========================================================= --}}
<div class="ep-info shadow-sm p-3 mb-3">

    <div class="row">

        <div class="col-6 col-lg-3 dato">

            <small>Contrato</small>

            <strong>
                {{ $entregaPolicia->contrato }}
            </strong>

        </div>


        <div class="col-6 col-lg-3 dato">

            <small>Dependencia</small>

            <strong>
                {{ $entregaPolicia->dependencia }}
            </strong>

        </div>


        <div class="col-6 col-lg-3 dato">

            <small>Recibido por</small>

            <strong>
                {{ $entregaPolicia->recibido_por }}
            </strong>

        </div>


        <div class="col-6 col-lg-3 dato">

            <small>Fecha</small>

            <strong>
                {{ $entregaPolicia->fecha_entrega->format('d/m/Y') }}
            </strong>

        </div>

    </div>

</div>


{{-- =========================================================
     FILTROS
========================================================= --}}
<form method="GET"
      class="card border-0 shadow-sm mb-3">

    <div class="card-body">

        <div class="row g-2 align-items-end">


            {{-- BUSCAR NÚMERO --}}
            <div class="col-md-4">

                <label
                    for="numero"
                    class="form-label fw-semibold"
                >
                    N.º de vale
                </label>

                <input
                    id="numero"
                    name="numero"
                    type="text"
                    class="form-control"
                    value="{{ request('numero') }}"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    placeholder="Buscar por número..."
                >

            </div>


            {{-- ESTADO --}}
            <div class="col-md-3">

                <label
                    for="estado"
                    class="form-label fw-semibold"
                >
                    Estado
                </label>

                <select
                    id="estado"
                    name="estado"
                    class="form-select"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="ENTREGADO"
                        @selected(request('estado') === 'ENTREGADO')
                    >
                        Pendientes
                    </option>

                    <option
                        value="UTILIZADO"
                        @selected(request('estado') === 'UTILIZADO')
                    >
                        Utilizados
                    </option>

                    <option
                        value="DEVUELTO"
                        @selected(request('estado') === 'DEVUELTO')
                    >
                        Devueltos
                    </option>

                    <option
                        value="ANULADO"
                        @selected(request('estado') === 'ANULADO')
                    >
                        Anulados
                    </option>

                </select>

            </div>


            {{-- CANTIDAD POR PÁGINA --}}
            <div class="col-md-2">

                <label
                    for="por_pagina"
                    class="form-label fw-semibold"
                >
                    Mostrar
                </label>

                <select
                    id="por_pagina"
                    name="por_pagina"
                    class="form-select"
                >

                    @foreach([10, 30, 50, 100] as $n)

                        <option
                            value="{{ $n }}"
                            @selected($porPagina === $n)
                        >
                            {{ $n }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- BOTONES --}}
            <div class="col-md-3 d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-ep flex-grow-1"
                >
                    Filtrar
                </button>


                <a
                    href="{{ route('entregas-policia.show', $entregaPolicia) }}"
                    class="btn btn-outline-secondary"
                >
                    Limpiar
                </a>

            </div>

        </div>

    </div>

</form>


{{-- =========================================================
     TABLA DE VALES
========================================================= --}}
<div class="card border-0 shadow-sm">

    <div class="table-responsive">

        <table class="table ep-table align-middle mb-0">

            <thead>

                <tr>

                    <th>Vale</th>

                    <th>Estado</th>

                    <th>Fecha uso</th>

                    <th>Vehículo</th>

                    <th>Acciones</th>

                </tr>

            </thead>


            <tbody>

                @forelse($vales as $v)

                    <tr>

                        {{-- =========================================
                             NÚMERO DE VALE
                        ========================================== --}}
                        <td class="fw-bold">

                            {{ sprintf(
                                '%06d',
                                (int) $v->numero_vale
                            ) }}

                        </td>


                        {{-- =========================================
                             ESTADO
                        ========================================== --}}
                        <td>

                            @php

                                $clases = [
                                    'ENTREGADO' => 'ep-pend',
                                    'UTILIZADO' => 'ep-ok',
                                    'DEVUELTO' => 'ep-off',
                                    'ANULADO' => 'ep-no',
                                ];

                                $nombres = [
                                    'ENTREGADO' => 'PENDIENTE',
                                    'UTILIZADO' => 'UTILIZADO',
                                    'DEVUELTO' => 'DEVUELTO',
                                    'ANULADO' => 'ANULADO',
                                ];

                                $clase =
                                    $clases[$v->estado]
                                    ?? 'ep-off';

                                $nombre =
                                    $nombres[$v->estado]
                                    ?? $v->estado;

                            @endphp


                            <span class="ep-badge {{ $clase }}">

                                {{ $nombre }}

                            </span>


                            @if($v->motivo_anulacion)

                                <div class="small text-muted mt-1">

                                    {{ $v->motivo_anulacion }}

                                </div>

                            @endif

                        </td>


                        {{-- =========================================
                             FECHA DE USO
                        ========================================== --}}
                        <td>

                            {{ $v->fecha?->format('d/m/Y') ?? '—' }}

                        </td>


                        {{-- =========================================
                             VEHÍCULO
                        ========================================== --}}
                        <td>

                            {{ $v->vehiculo?->codigo ?? '—' }}

                            <br>

                            <small class="text-muted">

                                {{ $v->vehiculo?->placa ?? '' }}

                            </small>

                        </td>


                        {{-- =========================================
                             ACCIONES
                        ========================================== --}}
                        <td>

                            <div class="d-flex gap-2 flex-wrap">


                                {{-- VER --}}
                                <a
                                    href="{{ route(
                                        'vales-combustible.show',
                                        $v
                                    ) }}"
                                    class="btn btn-sm btn-outline-success"
                                >

                                    <i class="bi bi-eye me-1"></i>

                                    Ver

                                </a>


                                {{-- =================================
                                     VALE PENDIENTE
                                ================================== --}}
                                @if($v->estado === 'ENTREGADO')

                                    <a
                                        href="{{ route(
                                            'vales-combustible.edit',
                                            $v
                                        ) }}"
                                        class="btn btn-sm btn-success"
                                    >

                                        <i class="bi bi-pencil-square me-1"></i>

                                        Registrar uso

                                    </a>


                                {{-- =================================
                                     VALE YA UTILIZADO
                                ================================== --}}
                                @elseif($v->estado === 'UTILIZADO')

                                    <span
                                        class="btn btn-sm btn-success disabled"
                                    >

                                        <i class="bi bi-check-circle-fill me-1"></i>

                                        Completado

                                    </span>

                                @endif


                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center text-muted py-5"
                        >

                            No se encontraron vales.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
     PAGINACIÓN
========================================================= --}}
<div class="mt-3">

    {{ $vales->links() }}

</div>


{{-- =========================================================
     MODALES PARA DEVOLVER O ANULAR
========================================================= --}}
@foreach($vales as $v)

    @if($v->estado === 'ENTREGADO')

        <div
            class="modal fade"
            id="cerrar{{ $v->id }}"
            tabindex="-1"
        >

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <form
                        method="POST"
                        action="{{ route(
                            'entregas-policia.cerrar',
                            $v
                        ) }}"
                    >

                        @csrf


                        <div class="modal-header">

                            <h5 class="modal-title">

                                Cerrar vale

                                {{ sprintf(
                                    '%06d',
                                    (int) $v->numero_vale
                                ) }}

                            </h5>


                            <button
                                class="btn-close"
                                data-bs-dismiss="modal"
                                type="button"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <p class="text-muted">

                                El número se conservará y no podrá reutilizarse.

                            </p>


                            <label class="form-label fw-semibold">

                                Resultado

                            </label>


                            <select
                                name="estado"
                                class="form-select mb-3"
                                required
                            >

                                <option value="DEVUELTO">

                                    Devuelto sin usar

                                </option>

                                <option value="ANULADO">

                                    Anulado

                                </option>

                            </select>


                            <label class="form-label fw-semibold">

                                Motivo *

                            </label>


                            <textarea
                                name="motivo"
                                class="form-control"
                                rows="3"
                                maxlength="1000"
                                required
                            ></textarea>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal"
                            >

                                Cancelar

                            </button>


                            <button
                                type="submit"
                                class="btn btn-danger"
                            >

                                Confirmar cierre

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

@endforeach

@endsection