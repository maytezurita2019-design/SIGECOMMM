
@extends('layouts.app')

@section('title', 'Vales de combustible')

@push('styles')
<style>
.vales-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    flex-wrap: wrap;
}

.vales-head h2 {
    color: #173f32;
    font-weight: 800;
    margin: 0;
}

.panel-filtros {
    border: 1px solid #dfe8e3;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(23,63,50,.06);
}

.filtros-estado {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.filtros-estado .btn {
    border-radius: 999px;
    padding: .45rem .85rem;
}

.btn-sig {
    background: #287a55;
    color: #fff;
    border-color: #287a55;
}

.btn-sig:hover {
    background: #206646;
    color: #fff;
    border-color: #206646;
}

.btn-sig-outline {
    color: #287a55;
    border: 1px solid #9fc5b1;
    background: #fff;
}

.btn-sig-outline:hover,
.btn-sig-outline.active {
    background: #eaf5ef;
    color: #1c5c3e;
    border-color: #287a55;
}

.tabla-vales {
    font-size: .92rem;
}

.tabla-vales thead th {
    background: #eaf5ef !important;
    color: #234d3a !important;
    border-color: #d9e6df !important;
    white-space: nowrap;
}

.tabla-vales td {
    border-color: #e6ece9;
    vertical-align: middle;
}

.estado {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 999px;
    padding: .35rem .65rem;
    font-size: .76rem;
    font-weight: 800;
}

.estado-em {
    background: #fff5d9;
    color: #7a5a00;
}

.estado-ok {
    background: #e7f6ed;
    color: #1d6b42;
}

.estado-no {
    background: #fdeaea;
    color: #9d3030;
}

.estado-po {
    background: #edf3ef;
    color: #3d5d4c;
}

.acciones-vale {
    display: flex;
    gap: .35rem;
    flex-wrap: wrap;
}

.acciones-vale .btn {
    border-radius: 9px;
}

.vale-mobile {
    display: none;
}

.contador {
    color: #66756e;
    font-size: .9rem;
}

.monto-total {
    font-size: 1.05rem;
    font-weight: 800;
    color: #245d42;
}

@media (max-width: 767.98px) {
    .tabla-escritorio {
        display: none;
    }

    .vale-mobile {
        display: block;
    }

    .vales-head .btn-principal {
        width: 100%;
    }

    .panel-filtros .card-body {
        padding: 1rem;
    }

    .vale-card {
        border: 1px solid #dfe8e3;
        border-radius: 14px;
        padding: 1rem;
        margin-bottom: .8rem;
        background: #fff;
        box-shadow: 0 5px 16px rgba(23,63,50,.05);
    }

    .vale-card-top {
        display: flex;
        justify-content: space-between;
        gap: .6rem;
        align-items: flex-start;
    }

    .vale-num {
        font-size: 1.05rem;
        font-weight: 800;
        color: #173f32;
    }

    .vale-datos {
        color: #5f6e67;
        font-size: .9rem;
        margin: .65rem 0;
    }

    .vale-totales {
        display: flex;
        justify-content: space-between;
        gap: .5rem;
        border-top: 1px solid #edf1ef;
        padding-top: .65rem;
        margin-top: .65rem;
    }

    .acciones-vale .btn {
        flex: 1;
        min-width: 95px;
    }

    .filtros-estado {
        overflow-x: auto;
        flex-wrap: nowrap;
        padding-bottom: .25rem;
    }

    .filtros-estado .btn {
        white-space: nowrap;
    }
}

@media print {
    .no-print,
    .menu-lateral,
    .barra-superior {
        display: none !important;
    }

    .pagina {
        margin-left: 0 !important;
    }

    .contenido {
        padding: 0 !important;
    }

    .tabla-escritorio {
        display: block !important;
    }

    .vale-mobile {
        display: none !important;
    }
}
</style>
@endpush

@section('content')

@php
    $nombreRol = mb_strtoupper(auth()->user()->rol->nombre ?? '');

    $esAdministrador = str_contains($nombreRol, 'ADMIN');
    $esAuxiliar = str_contains($nombreRol, 'AUX');
    $puedeOperar = $esAdministrador || $esAuxiliar;

    $tipoActual = request('tipo', 'GAMS');

    $filtrosBase = request()->except(['estado', 'page']);

    $estadosDisponibles = $tipoActual === 'POLICIA'
        ? [
            'ENTREGADO' => 'Entregados',
            'UTILIZADO' => 'Utilizados',
            'DEVUELTO' => 'Devueltos',
            'ANULADO' => 'Anulados',
        ]
        : [
            'EMITIDO' => 'Pendientes',
            'COMPLETADO' => 'Completados',
            'ANULADO' => 'Anulados',
        ];
@endphp

<div class="vales-head mb-3">

    <div>
        <div class="small text-success fw-bold text-uppercase mb-1">
            Combustible
        </div>

        <h2>
            <i class="bi bi-ticket-perforated me-2"></i>
            Vales de combustible
        </h2>

        <p class="text-muted mb-0">
            GAMS y Policía trabajan con flujos separados para evitar confusiones.
        </p>
    </div>

  @if($puedeOperar)
        <div class="d-flex gap-2 flex-wrap no-print">

            <a href="{{ route('entregas-policia.index') }}"
               class="btn btn-sig-outline">
                <i class="bi bi-shield-check me-1"></i>
                Asignaciones a Policía
            </a>

            <a href="{{ route('vales-combustible.create') }}"
               class="btn btn-sig btn-principal">
                <i class="bi bi-plus-circle me-1"></i>
                Emitir vale
            </a>

        </div>
    @endif

</div>

<div class="d-flex gap-2 mb-3 no-print">

    <a
        href="{{ route('vales-combustible.index', array_merge(request()->except(['tipo', 'estado', 'page']), ['tipo' => 'GAMS'])) }}"
        class="btn {{ $tipoActual === 'GAMS' ? 'btn-sig' : 'btn-sig-outline' }}"
    >
        <i class="bi bi-building me-1"></i>
        VALES GAMS
    </a>

    <a
        href="{{ route('vales-combustible.index', array_merge(request()->except(['tipo', 'estado', 'page']), ['tipo' => 'POLICIA'])) }}"
        class="btn {{ $tipoActual === 'POLICIA' ? 'btn-sig' : 'btn-sig-outline' }}"
    >
        <i class="bi bi-shield-check me-1"></i>
        POLICÍA
    </a>

</div>

<form method="GET" action="{{ route('vales-combustible.index') }}"
      class="card panel-filtros mb-3 no-print">

    <div class="card-body">

        <div class="row g-2 align-items-end">

            <div class="col-lg-7">

                <label class="form-label fw-semibold">
                    Buscar
                </label>

                <div class="input-group">

                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        class="form-control"
                        placeholder="N.º vale, placa, código o factura"
                    >

                </div>

            </div>

            <input type="hidden"
                   name="tipo"
                   value="{{ $tipoActual }}">

            <div class="col-6 col-lg-2">

                <label class="form-label fw-semibold">
                    Mostrar
                </label>

                <select name="por_pagina"
                        class="form-select"
                        onchange="this.form.submit()">

                    @foreach([10, 30] as $n)

                        <option value="{{ $n }}"
                            @selected((int) $porPagina === $n)>
                            {{ $n }} registros
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="col-lg-3 d-flex gap-2">

                <button type="submit"
                        class="btn btn-sig flex-grow-1">

                    <i class="bi bi-search me-1"></i>
                    Buscar

                </button>

                <a href="{{ route('vales-combustible.index') }}"
                   class="btn btn-outline-secondary">
                    Limpiar
                </a>

            </div>

        </div>

        <div class="filtros-estado mt-3">

            <a
                class="btn btn-sm btn-sig-outline {{ !request('estado') ? 'active' : '' }}"
                href="{{ route('vales-combustible.index', request()->except(['estado', 'page'])) }}"
            >
                Todos
            </a>

            @foreach($estadosDisponibles as $valor => $texto)

                <a
                    class="btn btn-sm btn-sig-outline {{ request('estado') === $valor ? 'active' : '' }}"
                    href="{{ route('vales-combustible.index', array_merge(request()->except(['estado', 'page']), ['estado' => $valor])) }}"
                >
                    {{ $texto }}
                </a>

            @endforeach

        </div>

    </div>

</form>

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">

    <div class="contador">

        Mostrando

        <strong>
            {{ $vales->firstItem() ?? 0 }}–{{ $vales->lastItem() ?? 0 }}
        </strong>

        de

        <strong>
            {{ $vales->total() }}
        </strong>

        registros

    </div>

    <div class="monto-total">

        Total filtrado:
        Bs {{ number_format($totalFiltrado, 2, ',', '.') }}

    </div>

</div>

<div class="card shadow-sm border-0 tabla-escritorio">

    <div class="table-responsive">

        <table class="table tabla-vales mb-0">

            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Vale</th>
                    <th>Vehículo</th>
                    <th>Litros</th>
                    <th>Total Bs</th>
                    <th>Factura</th>
                    <th>Km/Hor.</th>
                    <th>Estado</th>
                    <th class="no-print">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($vales as $v)

                    <tr>

                        <td>
                            {{ $v->fecha?->format('d/m/Y') ?? '—' }}
                        </td>

                        <td class="fw-bold">
                            {{ $v->numero_vale }}
                        </td>

                        <td>

                            <strong>
                                {{ $v->vehiculo->codigo ?? '—' }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                {{ $v->vehiculo->placa ?? 'Sin placa' }}
                            </small>

                        </td>

            

                        <td>
                            {{ $v->cantidad_litros !== null
                                ? number_format($v->cantidad_litros, 2, ',', '.')
                                : '—' }}
                        </td>

                        <td>
                            {{ $v->total !== null
                                ? number_format($v->total, 2, ',', '.')
                                : '—' }}
                        </td>

                        <td>
                            {{ $v->numero_factura ?: '—' }}
                        </td>

                        <td>
                            {{ $v->kilometraje ?? '—' }}
                        </td>

                        <td>
                            @include('vales_combustible.partials.estado', ['vale' => $v])
                        </td>

                        <td class="no-print">
                            @include('vales_combustible.partials.acciones', ['vale' => $v])
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9"
                            class="text-center text-muted py-5">

                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                            No se encontraron vales con estos filtros.

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="vale-mobile">

    @forelse($vales as $v)

        <div class="vale-card">

            <div class="vale-card-top">

                <div>

                    <div class="vale-num">
                        Vale {{ $v->numero_vale }}
                    </div>

                    <small class="text-muted">
                        {{ $v->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                    </small>

                </div>

                @include('vales_combustible.partials.estado', ['vale' => $v])

            </div>

            <div class="vale-datos">

                <strong>
                    {{ $v->vehiculo->codigo ?? 'Sin vehículo' }}
                </strong>

                · {{ $v->vehiculo->placa ?? 'Sin placa' }}

                <br>

                {{ $v->vehiculo->unidad->nombre ?? 'Sin unidad' }}

                @if($v->numero_factura)

                    <br>
                    Factura: {{ $v->numero_factura }}

                @endif

            </div>

            <div class="vale-totales">

                <span>

                    {{ $v->cantidad_litros !== null
                        ? number_format($v->cantidad_litros, 2, ',', '.') . ' L'
                        : 'Pendiente de consumo' }}

                </span>

                <strong>

                    {{ $v->total !== null
                        ? 'Bs ' . number_format($v->total, 2, ',', '.')
                        : '—' }}

                </strong>

            </div>

            <div class="acciones-vale mt-3">

                @include('vales_combustible.partials.acciones', ['vale' => $v])

            </div>

        </div>

    @empty

        <div class="text-center text-muted py-5">
            No se encontraron vales.
        </div>

    @endforelse

</div>

<div class="mt-3 no-print">
    {{ $vales->links() }}
</div>

@endsection

