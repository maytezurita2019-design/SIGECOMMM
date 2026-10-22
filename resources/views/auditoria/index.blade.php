@extends('layouts.app')

@section('title', 'Auditoría · SIGECOM')

@section('content')

{{-- =========================================================
     ENCABEZADO
========================================================= --}}

<div class="audit-header mb-4">

    <div>

        <div class="audit-section">
            <i class="bi bi-shield-lock-fill me-1"></i>
            SEGURIDAD
        </div>

        <h1 class="audit-title">
            Auditoría del sistema
        </h1>

        <p class="audit-subtitle">
            Consulte quién realizó cada operación, qué registro fue afectado
            y los cambios realizados en SIGECOM.
        </p>

    </div>

    <div class="audit-header-icon">
        <i class="bi bi-clock-history"></i>
    </div>

</div>


{{-- =========================================================
     FILTROS
========================================================= --}}

<div class="card audit-card mb-4">

    <div class="card-header audit-card-header">

        <div>

            <h2>
                <i class="bi bi-funnel me-2"></i>
                Filtros de auditoría
            </h2>

            <p>
                Utilice uno o varios criterios para localizar operaciones.
            </p>

        </div>

    </div>


    <div class="card-body">

        <form method="GET">

            <div class="row g-3">


                {{-- BUSCADOR GENERAL --}}

                <div class="col-lg-4">

                    <label class="form-label audit-label">
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
                            placeholder="Usuario, acción, módulo, IP..."
                        >

                    </div>

                </div>


                {{-- USUARIO --}}

                <div class="col-md-6 col-lg-2">

                    <label class="form-label audit-label">
                        Usuario
                    </label>

                    <select
                        name="usuario_id"
                        class="form-select"
                    >

                        <option value="">
                            Todos
                        </option>

                        @foreach($usuarios as $usuario)

                            <option
                                value="{{ $usuario->id }}"
                                @selected(
                                    request('usuario_id') == $usuario->id
                                )
                            >
                                {{ trim(
                                    $usuario->nombre . ' ' .
                                    $usuario->apellido
                                ) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ACCIÓN --}}

                <div class="col-md-6 col-lg-2">

                    <label class="form-label audit-label">
                        Acción
                    </label>

                    <select
                        name="accion"
                        class="form-select"
                    >

                        <option value="">
                            Todas
                        </option>

                        @foreach($acciones as $accion)

                            <option
                                value="{{ $accion }}"
                                @selected(
                                    request('accion') == $accion
                                )
                            >
                                {{ strtoupper($accion) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- MÓDULO --}}

                <div class="col-md-6 col-lg-2">

                    <label class="form-label audit-label">
                        Módulo
                    </label>

                    <select
                        name="tabla"
                        class="form-select"
                    >

                        <option value="">
                            Todos
                        </option>

                        @foreach($tablas as $tabla)

                            <option
                                value="{{ $tabla }}"
                                @selected(
                                    request('tabla') == $tabla
                                )
                            >
                                {{ ucfirst(
                                    str_replace('_', ' ', $tabla)
                                ) }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- BOTÓN --}}

                <div class="col-md-6 col-lg-2 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-success w-100"
                    >
                        <i class="bi bi-search me-1"></i>
                        Buscar
                    </button>

                </div>


                {{-- FECHA DESDE --}}

                <div class="col-md-6 col-lg-3">

                    <label class="form-label audit-label">
                        Fecha desde
                    </label>

                    <input
                        type="date"
                        name="fecha_desde"
                        value="{{ request('fecha_desde') }}"
                        class="form-control"
                    >

                </div>


                {{-- FECHA HASTA --}}

                <div class="col-md-6 col-lg-3">

                    <label class="form-label audit-label">
                        Fecha hasta
                    </label>

                    <input
                        type="date"
                        name="fecha_hasta"
                        value="{{ request('fecha_hasta') }}"
                        class="form-control"
                    >

                </div>


                {{-- LIMPIAR --}}

                <div class="col-md-6 col-lg-2 d-flex align-items-end">

                    <a
                        href="{{ route('auditoria.index') }}"
                        class="btn btn-outline-secondary w-100"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Limpiar
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     TABLA
========================================================= --}}

<div class="card audit-card">

    <div class="card-header audit-card-header">

        <div>

            <h2>
                <i class="bi bi-list-check me-2"></i>
                Historial de operaciones
            </h2>

            <p>
                {{ $auditorias->total() }}
                movimiento(s) encontrado(s)
            </p>

        </div>

    </div>


    <div class="table-responsive">

        <table class="table audit-table align-middle mb-0">

            <thead>

                <tr>

                    <th>Fecha / hora</th>

                    <th>Usuario</th>

                    <th>Acción</th>

                    <th>Módulo</th>

                    <th>Registro</th>

                    <th>Descripción</th>

                    <th>IP</th>

                    <th class="text-center">
                        Detalle
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($auditorias as $a)

                    @php

                        $accion =
                            strtoupper($a->accion ?? '');

                        $badgeClass =
                            match (true) {

                                str_contains($accion, 'CREA'),
                                str_contains($accion, 'REGISTR')
                                    => 'audit-action-create',

                                str_contains($accion, 'MODIF'),
                                str_contains($accion, 'ACTUAL')
                                    => 'audit-action-update',

                                str_contains($accion, 'ANUL')
                                    => 'audit-action-cancel',

                                str_contains($accion, 'ELIM')
                                    => 'audit-action-delete',

                                default
                                    => 'audit-action-other',
                            };


                        $icono =
                            match (true) {

                                str_contains($accion, 'CREA'),
                                str_contains($accion, 'REGISTR')
                                    => 'bi-plus-circle-fill',

                                str_contains($accion, 'MODIF'),
                                str_contains($accion, 'ACTUAL')
                                    => 'bi-pencil-square',

                                str_contains($accion, 'ANUL')
                                    => 'bi-exclamation-circle-fill',

                                str_contains($accion, 'ELIM')
                                    => 'bi-trash-fill',

                                default
                                    => 'bi-activity',
                            };


                        $nombreUsuario =
                            $a->usuario
                                ? trim(
                                    $a->usuario->nombre . ' ' .
                                    $a->usuario->apellido
                                )
                                : 'Sistema';


                        $rolUsuario =
                            $a->usuario?->rol?->nombre
                            ?? 'Sistema';


                        $modulo =
                            ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $a->tabla ?? '—'
                                )
                            );


                        $modalId =
                            'auditoriaDetalle' . $a->id;

                    @endphp


                    <tr>

                        {{-- FECHA --}}

                        <td class="text-nowrap">

                            <div class="audit-date">
                                {{ $a->created_at?->format('d/m/Y') }}
                            </div>

                            <small>
                                {{ $a->created_at?->format('H:i:s') }}
                            </small>

                        </td>


                        {{-- USUARIO --}}

                        <td>

                            <div class="audit-user">

                                <div class="audit-avatar">

                                    {{ strtoupper(
                                        mb_substr(
                                            $a->usuario?->nombre
                                                ?? 'S',
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>

                                <div>

                                    <strong>
                                        {{ $nombreUsuario }}
                                    </strong>

                                    <small>
                                        {{ $rolUsuario }}
                                    </small>

                                </div>

                            </div>

                        </td>


                        {{-- ACCIÓN --}}

                        <td>

                            <span class="audit-action {{ $badgeClass }}">

                                <i class="bi {{ $icono }}"></i>

                                {{ $accion ?: '—' }}

                            </span>

                        </td>


                        {{-- MÓDULO --}}

                        <td>

                            <span class="audit-module">

                                <i class="bi bi-grid me-1"></i>

                                {{ $modulo }}

                            </span>

                        </td>


                        {{-- REGISTRO --}}

                        <td>

                            @if($a->registro_id)

                                <strong>
                                    #{{ $a->registro_id }}
                                </strong>

                            @else

                                —

                            @endif

                        </td>


                        {{-- DESCRIPCIÓN --}}

                        <td class="audit-description">

                            {{ $a->descripcion ?: 'Sin descripción adicional.' }}

                        </td>


                        {{-- IP --}}

                        <td class="text-nowrap">

                            <span class="audit-ip">
                                {{ $a->ip ?: '—' }}
                            </span>

                        </td>


                        {{-- DETALLE --}}

                        <td class="text-center">

                            <button
                                type="button"
                                class="btn audit-detail-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#{{ $modalId }}"
                                title="Ver detalle"
                            >
                                <i class="bi bi-eye"></i>
                            </button>

                        </td>

                    </tr>


                    {{-- =================================================
                         MODAL
                    ================================================= --}}

                    <div
                        class="modal fade"
                        id="{{ $modalId }}"
                        tabindex="-1"
                        aria-hidden="true"
                    >

                        <div class="modal-dialog modal-lg modal-dialog-scrollable">

                            <div class="modal-content audit-modal">


                                {{-- HEADER --}}

                                <div class="modal-header">

                                    <div>

                                        <div class="audit-modal-small">
                                            DETALLE DE AUDITORÍA
                                        </div>

                                        <h5 class="modal-title">
                                            Movimiento #{{ $a->id }}
                                        </h5>

                                    </div>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Cerrar"
                                    ></button>

                                </div>


                                <div class="modal-body">


                                    {{-- RESUMEN --}}

                                    <div class="row g-3 mb-4">


                                        <div class="col-md-6">

                                            <div class="audit-info-box">

                                                <span>
                                                    Usuario
                                                </span>

                                                <strong>
                                                    {{ $nombreUsuario }}
                                                </strong>

                                                <small>
                                                    {{ $rolUsuario }}
                                                </small>

                                            </div>

                                        </div>


                                        <div class="col-md-6">

                                            <div class="audit-info-box">

                                                <span>
                                                    Fecha y hora
                                                </span>

                                                <strong>
                                                    {{ $a->created_at?->format(
                                                        'd/m/Y H:i:s'
                                                    ) }}
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div class="audit-info-box">

                                                <span>
                                                    Acción
                                                </span>

                                                <strong>
                                                    {{ $accion ?: '—' }}
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div class="audit-info-box">

                                                <span>
                                                    Módulo
                                                </span>

                                                <strong>
                                                    {{ $modulo }}
                                                </strong>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div class="audit-info-box">

                                                <span>
                                                    Registro
                                                </span>

                                                <strong>

                                                    {{ $a->registro_id
                                                        ? '#' . $a->registro_id
                                                        : '—'
                                                    }}

                                                </strong>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- DESCRIPCIÓN --}}

                                    <div class="audit-modal-section">

                                        <h6>
                                            <i class="bi bi-card-text me-2"></i>
                                            Descripción
                                        </h6>

                                        <div class="audit-description-box">

                                            {{ $a->descripcion
                                                ?: 'No se registró una descripción adicional.'
                                            }}

                                        </div>

                                    </div>


                                    {{-- CAMBIOS --}}

                                    @if(
                                        !empty($a->datos_anteriores)
                                        || !empty($a->datos_nuevos)
                                    )

                                        <div class="audit-modal-section">

                                            <h6>
                                                <i class="bi bi-arrow-left-right me-2"></i>
                                                Cambios registrados
                                            </h6>


                                            <div class="row g-3">


                                                {{-- ANTES --}}

                                                <div class="col-md-6">

                                                    <div class="audit-change audit-before">

                                                        <div class="audit-change-title">

                                                            <i class="bi bi-clock-history"></i>

                                                            Antes

                                                        </div>


                                                        @if(!empty($a->datos_anteriores))

                                                            <div class="table-responsive">

                                                                <table class="table table-sm mb-0">

                                                                    <tbody>

                                                                        @foreach(
                                                                            $a->datos_anteriores
                                                                            as $campo => $valor
                                                                        )

                                                                            <tr>

                                                                                <td class="audit-field">
                                                                                    {{ ucfirst(
                                                                                        str_replace(
                                                                                            '_',
                                                                                            ' ',
                                                                                            $campo
                                                                                        )
                                                                                    ) }}
                                                                                </td>

                                                                                <td>

                                                                                    @if(is_array($valor))

                                                                                        {{ json_encode(
                                                                                            $valor,
                                                                                            JSON_UNESCAPED_UNICODE
                                                                                        ) }}

                                                                                    @elseif(is_bool($valor))

                                                                                        {{ $valor
                                                                                            ? 'Sí'
                                                                                            : 'No'
                                                                                        }}

                                                                                    @else

                                                                                        {{ $valor === null
                                                                                            || $valor === ''
                                                                                            ? '—'
                                                                                            : $valor
                                                                                        }}

                                                                                    @endif

                                                                                </td>

                                                                            </tr>

                                                                        @endforeach

                                                                    </tbody>

                                                                </table>

                                                            </div>

                                                        @else

                                                            <div class="audit-no-data">
                                                                Sin datos anteriores.
                                                            </div>

                                                        @endif

                                                    </div>

                                                </div>


                                                {{-- DESPUÉS --}}

                                                <div class="col-md-6">

                                                    <div class="audit-change audit-after">

                                                        <div class="audit-change-title">

                                                            <i class="bi bi-check-circle"></i>

                                                            Después

                                                        </div>


                                                        @if(!empty($a->datos_nuevos))

                                                            <div class="table-responsive">

                                                                <table class="table table-sm mb-0">

                                                                    <tbody>

                                                                        @foreach(
                                                                            $a->datos_nuevos
                                                                            as $campo => $valor
                                                                        )

                                                                            <tr>

                                                                                <td class="audit-field">
                                                                                    {{ ucfirst(
                                                                                        str_replace(
                                                                                            '_',
                                                                                            ' ',
                                                                                            $campo
                                                                                        )
                                                                                    ) }}
                                                                                </td>

                                                                                <td>

                                                                                    @if(is_array($valor))

                                                                                        {{ json_encode(
                                                                                            $valor,
                                                                                            JSON_UNESCAPED_UNICODE
                                                                                        ) }}

                                                                                    @elseif(is_bool($valor))

                                                                                        {{ $valor
                                                                                            ? 'Sí'
                                                                                            : 'No'
                                                                                        }}

                                                                                    @else

                                                                                        {{ $valor === null
                                                                                            || $valor === ''
                                                                                            ? '—'
                                                                                            : $valor
                                                                                        }}

                                                                                    @endif

                                                                                </td>

                                                                            </tr>

                                                                        @endforeach

                                                                    </tbody>

                                                                </table>

                                                            </div>

                                                        @else

                                                            <div class="audit-no-data">
                                                                Sin datos nuevos.
                                                            </div>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endif


                                    {{-- INFORMACIÓN TÉCNICA --}}

                                    <div class="audit-modal-section mb-0">

                                        <h6>
                                            <i class="bi bi-pc-display me-2"></i>
                                            Información técnica
                                        </h6>

                                        <div class="audit-technical">

                                            <div>

                                                <span>
                                                    Dirección IP
                                                </span>

                                                <strong>
                                                    {{ $a->ip ?: 'No registrada' }}
                                                </strong>

                                            </div>


                                            <div>

                                                <span>
                                                    ID de auditoría
                                                </span>

                                                <strong>
                                                    #{{ $a->id }}
                                                </strong>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <div class="modal-footer">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                    >
                                        Cerrar
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >

                            <div class="audit-empty">

                                <i class="bi bi-shield-check"></i>

                                <strong>
                                    Aún no existen movimientos auditados
                                </strong>

                                <span>
                                    Las operaciones registradas aparecerán aquí.
                                </span>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- PAGINACIÓN --}}

@if($auditorias->hasPages())

    <div class="mt-3">
        {{ $auditorias->links() }}
    </div>

@endif

@endsection


@push('styles')

<style>

    .audit-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .audit-section {
        color: #08712f;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .7px;
    }

    .audit-title {
        margin: 3px 0;
        color: #1f3c2b;
        font-size: 28px;
        font-weight: 800;
    }

    .audit-subtitle {
        margin: 0;
        color: #748078;
        font-size: 13px;
    }

    .audit-header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        color: #08712f;
        background: #eaf7ef;
        border-radius: 12px;
        font-size: 22px;
    }

    .audit-card {
        border: 1px solid #e4eae6 !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,.04) !important;
        overflow: hidden;
    }

    .audit-card-header {
        padding: 14px 18px;
        background: #fff !important;
        border-bottom: 1px solid #edf0ee !important;
    }

    .audit-card-header h2 {
        margin: 0;
        color: #304238;
        font-size: 14px;
        font-weight: 800;
    }

    .audit-card-header p {
        margin: 3px 0 0;
        color: #909990;
        font-size: 10px;
    }

    .audit-label {
        color: #667269;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .audit-table thead th {
        padding: 12px 14px;
        color: #526159;
        background: #eef7f1;
        border-bottom: 1px solid #dce9e0;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .audit-table tbody td {
        padding: 12px 14px;
        color: #3d4941;
        border-bottom: 1px solid #eff2f0;
        font-size: 11px;
        vertical-align: middle;
    }

    .audit-date {
        font-weight: 700;
    }

    .audit-table td > small {
        color: #8b948d;
    }

    .audit-user {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 150px;
    }

    .audit-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        height: 31px;
        flex-shrink: 0;
        color: #08712f;
        background: #e9f7ee;
        border-radius: 50%;
        font-size: 11px;
        font-weight: 800;
    }

    .audit-user strong,
    .audit-user small {
        display: block;
    }

    .audit-user strong {
        font-size: 11px;
    }

    .audit-user small {
        color: #929a94;
        font-size: 9px;
    }

    .audit-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 8px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .audit-action-create {
        color: #166534;
        background: #e9f7ee;
    }

    .audit-action-update {
        color: #1d4ed8;
        background: #eaf2ff;
    }

    .audit-action-cancel {
        color: #92400e;
        background: #fff4dd;
    }

    .audit-action-delete {
        color: #b42318;
        background: #feeceb;
    }

    .audit-action-other {
        color: #5b6470;
        background: #f0f2f4;
    }

    .audit-module {
        white-space: nowrap;
    }

    .audit-description {
        min-width: 220px;
        max-width: 350px;
        color: #69736c !important;
    }

    .audit-ip {
        padding: 4px 7px;
        color: #526159;
        background: #f4f6f5;
        border-radius: 5px;
        font-family: monospace;
        font-size: 10px;
    }

    .audit-detail-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        height: 31px;
        padding: 0;
        color: #08712f;
        background: #fff;
        border: 1px solid #cfe3d6;
        border-radius: 7px;
    }

    .audit-detail-btn:hover {
        color: #fff;
        background: #08712f;
        border-color: #08712f;
    }

    .audit-modal {
        border: 0;
        border-radius: 12px;
    }

    .audit-modal-small {
        color: #08712f;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .6px;
    }

    .audit-info-box {
        height: 100%;
        padding: 11px 13px;
        background: #f8faf9;
        border: 1px solid #e8ece9;
        border-radius: 8px;
    }

    .audit-info-box span,
    .audit-info-box small {
        display: block;
    }

    .audit-info-box span {
        margin-bottom: 3px;
        color: #89928b;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .audit-info-box strong {
        color: #34443a;
        font-size: 12px;
    }

    .audit-info-box small {
        margin-top: 2px;
        color: #8c958e;
        font-size: 9px;
    }

    .audit-modal-section {
        margin-bottom: 22px;
    }

    .audit-modal-section h6 {
        margin-bottom: 10px;
        color: #314339;
        font-size: 12px;
        font-weight: 800;
    }

    .audit-description-box {
        padding: 12px 14px;
        color: #58655c;
        background: #f8faf9;
        border-left: 3px solid #08712f;
        border-radius: 6px;
        font-size: 11px;
    }

    .audit-change {
        height: 100%;
        overflow: hidden;
        border: 1px solid #e4e8e5;
        border-radius: 8px;
    }

    .audit-change-title {
        padding: 9px 11px;
        font-size: 10px;
        font-weight: 800;
    }

    .audit-before .audit-change-title {
        color: #92400e;
        background: #fff7e7;
    }

    .audit-after .audit-change-title {
        color: #166534;
        background: #ecf8f0;
    }

    .audit-change td {
        padding: 7px 10px;
        font-size: 10px;
    }

    .audit-field {
        width: 42%;
        color: #78837b;
        font-weight: 700;
    }

    .audit-no-data {
        padding: 20px;
        color: #929a94;
        text-align: center;
        font-size: 10px;
    }

    .audit-technical {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .audit-technical > div {
        min-width: 180px;
        padding: 9px 12px;
        background: #f8faf9;
        border-radius: 7px;
    }

    .audit-technical span,
    .audit-technical strong {
        display: block;
    }

    .audit-technical span {
        color: #8a938c;
        font-size: 9px;
    }

    .audit-technical strong {
        margin-top: 2px;
        color: #455249;
        font-size: 10px;
    }

    .audit-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        color: #8c958e;
    }

    .audit-empty i {
        margin-bottom: 4px;
        color: #8eb39a;
        font-size: 30px;
    }

    .audit-empty strong {
        color: #536058;
        font-size: 12px;
    }

    .audit-empty span {
        font-size: 10px;
    }

    @media (max-width: 767.98px) {

        .audit-header-icon {
            display: none;
        }

        .audit-title {
            font-size: 23px;
        }

        .audit-technical {
            display: block;
        }

        .audit-technical > div {
            margin-bottom: 7px;
        }
    }

</style>

@endpush
