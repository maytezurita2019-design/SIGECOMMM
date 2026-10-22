@extends('layouts.app')

@section('title', 'Principal · SIGECOM')

@section('content')

@php
    $rol = strtoupper(auth()->user()->rol->nombre ?? 'USUARIO');

    $esAdmin = str_contains($rol, 'ADMIN');
    $esAuxiliar = str_contains($rol, 'AUX');

    $puedeOperar = $esAdmin || $esAuxiliar;

    /*
    |--------------------------------------------------------------------------
    | DATOS PARA EL CALENDARIO
    |--------------------------------------------------------------------------
    */

    $primerDiaMes = \Carbon\Carbon::create(
        $anioActual,
        $mesActual,
        1
    );

    $diasMes = $primerDiaMes->daysInMonth;

    // 1 = lunes / 7 = domingo
    $diaSemanaInicio = $primerDiaMes->dayOfWeekIso;

    /*
    |--------------------------------------------------------------------------
    | ESCALA PARA INTENSIDAD DEL CALENDARIO
    |--------------------------------------------------------------------------
    */

    $maxLitrosDia = collect($consumoDiario)
        ->max('litros') ?? 0;
@endphp


{{-- =========================================================
     ENCABEZADO
========================================================= --}}

<div class="dash-header mb-4">

    <div class="row align-items-center g-3">

        <div class="col-lg">

            <div class="dash-system">
                <i class="bi bi-fuel-pump-fill me-2"></i>
                SIGECOM · GAM SACABA
            </div>

            <h1 class="dash-welcome">

                {{ now()->hour < 12
                    ? 'Buenos días'
                    : (now()->hour < 19
                        ? 'Buenas tardes'
                        : 'Buenas noches')
                }},

                {{ auth()->user()->nombre ?: auth()->user()->correo }}

            </h1>

            <div class="dash-subtitle">
                Resumen del consumo de combustible
            </div>

        </div>


        <div class="col-lg-auto">

            <div class="dash-period">

                <div class="dash-period-icon">
                    <i class="bi bi-calendar3"></i>
                </div>

                <div>

                    <small>PERÍODO ACTUAL</small>

                    <strong>
                        {{ $nombreMesActual }} {{ $anioActual }}
                    </strong>

                </div>

            </div>

        </div>

    </div>


    @if($puedeOperar)

        <div class="dash-actions">

            <a
                href="{{ route('vales-combustible.create') }}"
                class="btn btn-success"
            >
                <i class="bi bi-plus-circle-fill me-2"></i>
                Emitir vale
            </a>


            <a
                href="{{ route('vales-combustible.index', [
                    'estado' => 'EMITIDO',
                    'tipo' => 'GAMS'
                ]) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-clock-history me-2"></i>
                Pendientes
            </a>


            <a
                href="{{ route('entregas-policia.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-shield-check me-2"></i>
                Policía
            </a>

        </div>

    @endif

</div>


{{-- =========================================================
     INDICADORES DEL MES
========================================================= --}}

<div class="row g-3 mb-4">


    {{-- VALES --}}
    <div class="col-sm-6 col-xl-3">

        <div class="dash-stat dash-stat-blue">

            <div>

                <div class="dash-stat-label">
                    Vales del mes
                </div>

                <div class="dash-stat-value">
                    {{ number_format($valesMesActual) }}
                </div>

                <div class="dash-stat-description">
                    Consumos registrados
                </div>

            </div>

            <div class="dash-stat-icon">
                <i class="bi bi-ticket-perforated-fill"></i>
            </div>

        </div>

    </div>


    {{-- LITROS --}}
    <div class="col-sm-6 col-xl-3">

        <div class="dash-stat dash-stat-green">

            <div>

                <div class="dash-stat-label">
                    Litros del mes
                </div>

                <div class="dash-stat-value dash-stat-value-medium">
                    {{ number_format($litrosMesActual, 2, ',', '.') }}
                </div>

                <div class="dash-stat-description">
                    Litros consumidos
                </div>

            </div>

            <div class="dash-stat-icon">
                <i class="bi bi-fuel-pump-fill"></i>
            </div>

        </div>

    </div>


    {{-- DINERO --}}
    <div class="col-sm-6 col-xl-3">

        <div class="dash-stat dash-stat-purple">

            <div>

                <div class="dash-stat-label">
                    Consumo del mes
                </div>

                <div class="dash-stat-value dash-stat-money">
                    Bs {{ number_format($totalMesActual, 2, ',', '.') }}
                </div>

                <div class="dash-stat-description">
                    Importe registrado
                </div>

            </div>

            <div class="dash-stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>

        </div>

    </div>


    {{-- PENDIENTES --}}
    <div class="col-sm-6 col-xl-3">

        <a
            href="{{ route('vales-combustible.index', [
                'estado' => 'EMITIDO',
                'tipo' => 'GAMS'
            ]) }}"
            class="text-decoration-none"
        >

            <div class="dash-stat dash-stat-orange">

                <div>

                    <div class="dash-stat-label">
                        Pendientes GAMS
                    </div>

                    <div class="dash-stat-value">
                        {{ number_format($pendientes) }}
                    </div>

                    <div class="dash-stat-description">
                        Requieren completar
                    </div>

                </div>

                <div class="dash-stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>

            </div>

        </a>

    </div>

</div>


{{-- =========================================================
     GRÁFICO + CALENDARIO
========================================================= --}}

<div class="row g-3 mb-4">


    {{-- =====================================================
         GRÁFICO
    ===================================================== --}}

    <div class="col-xl-7">

        <div class="card dash-card h-100">

            <div class="card-header dash-card-header">

                <div>

                    <h2>
                        Consumo de {{ $nombreMesActual }}
                    </h2>

                    <p>
                        Litros registrados diariamente
                    </p>

                </div>


                <div class="dash-comparison">

                    @if($variacionConsumo > 0)

                        <span class="comparison-up">

                            <i class="bi bi-arrow-up-right"></i>

                            {{ number_format(abs($variacionConsumo), 1, ',', '.') }}%

                        </span>

                    @elseif($variacionConsumo < 0)

                        <span class="comparison-down">

                            <i class="bi bi-arrow-down-right"></i>

                            {{ number_format(abs($variacionConsumo), 1, ',', '.') }}%

                        </span>

                    @else

                        <span class="comparison-equal">

                            <i class="bi bi-dash"></i>

                            0%

                        </span>

                    @endif

                    <small>
                        vs. {{ $nombreMesAnterior }}
                    </small>

                </div>

            </div>


            <div class="card-body">

                <div class="dash-chart-container">

                    <canvas id="consumoDiarioChart"></canvas>

                </div>


                <div class="dash-chart-summary">

                    <div>

                        <span>Mes anterior</span>

                        <strong>
                            {{ number_format($litrosMesAnterior, 2, ',', '.') }} L
                        </strong>

                    </div>


                    <div>

                        <span>Mes actual</span>

                        <strong>
                            {{ number_format($litrosMesActual, 2, ',', '.') }} L
                        </strong>

                    </div>


                    <div>

                        <span>Mayor consumo</span>

                        <strong>

                            @if($diaMayorConsumo)

                                Día {{ $diaMayorConsumo }}
                                ·
                                {{ number_format($mayorConsumo, 2, ',', '.') }} L

                            @else

                                Sin registros

                            @endif

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         CALENDARIO
    ===================================================== --}}

    <div class="col-xl-5">

        <div class="card dash-card h-100">

            <div class="card-header dash-card-header">

                <div>

                    <h2>
                        Calendario de consumo
                    </h2>

                    <p>
                        {{ $nombreMesActual }} {{ $anioActual }}
                    </p>

                </div>

                <div class="calendar-icon">
                    <i class="bi bi-calendar3"></i>
                </div>

            </div>


            <div class="card-body">

                <div class="dash-calendar">

                    {{-- DÍAS DE LA SEMANA --}}

                    <div class="calendar-weekday">Lun</div>
                    <div class="calendar-weekday">Mar</div>
                    <div class="calendar-weekday">Mié</div>
                    <div class="calendar-weekday">Jue</div>
                    <div class="calendar-weekday">Vie</div>
                    <div class="calendar-weekday">Sáb</div>
                    <div class="calendar-weekday">Dom</div>


                    {{-- ESPACIOS ANTES DEL DÍA 1 --}}

                    @for($i = 1; $i < $diaSemanaInicio; $i++)

                        <div class="calendar-empty"></div>

                    @endfor


                    {{-- DÍAS DEL MES --}}

                    @for($dia = 1; $dia <= $diasMes; $dia++)

                        @php

                            $datosDia = $consumoDiario[$dia] ?? [
                                'litros' => 0,
                                'total' => 0,
                                'vales' => 0,
                            ];

                            $litrosDia = (float) $datosDia['litros'];

                            $intensidad = '';

                            if ($litrosDia > 0 && $maxLitrosDia > 0) {

                                $porcentajeDia =
                                    ($litrosDia / $maxLitrosDia) * 100;

                                if ($porcentajeDia >= 75) {

                                    $intensidad = 'calendar-high';

                                } elseif($porcentajeDia >= 40) {

                                    $intensidad = 'calendar-medium';

                                } else {

                                    $intensidad = 'calendar-low';

                                }

                            }

                            $esHoy =
                                $dia == now()->day
                                && $mesActual == now()->month
                                && $anioActual == now()->year;

                        @endphp


                        <div
                            class="
                                calendar-day
                                {{ $intensidad }}
                                {{ $esHoy ? 'calendar-today' : '' }}
                            "
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="
                                {{ number_format($litrosDia, 2, ',', '.') }} L ·
                                {{ $datosDia['vales'] }} vale(s) ·
                                Bs {{ number_format($datosDia['total'], 2, ',', '.') }}
                            "
                        >

                            <span class="calendar-number">
                                {{ $dia }}
                            </span>

                            @if($litrosDia > 0)

                                <span class="calendar-dot"></span>

                            @endif

                        </div>

                    @endfor

                </div>


                {{-- LEYENDA --}}

                <div class="calendar-legend">

                    <span>
                        <i class="legend-dot legend-low"></i>
                        Bajo
                    </span>

                    <span>
                        <i class="legend-dot legend-medium"></i>
                        Medio
                    </span>

                    <span>
                        <i class="legend-dot legend-high"></i>
                        Alto
                    </span>

                </div>


                @if($diaMayorConsumo)

                    <div class="calendar-highlight">

                        <i class="bi bi-info-circle-fill"></i>

                        <div>

                            <small>
                                Día con mayor consumo
                            </small>

                            <strong>
                                {{ $diaMayorConsumo }}
                                de {{ strtolower($nombreMesActual) }}:
                                {{ number_format($mayorConsumo, 2, ',', '.') }} litros
                            </strong>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     ACTIVIDAD RECIENTE
========================================================= --}}

<div class="card dash-card">

    <div class="card-header dash-card-header">

        <div>

            <h2>
                Actividad reciente
            </h2>

            <p>
                Últimos movimientos registrados en SIGECOM
            </p>

        </div>


        <a
            href="{{ route('vales-combustible.index') }}"
            class="btn btn-sm btn-outline-success"
        >
            Ver todos

            <i class="bi bi-arrow-right ms-1"></i>
        </a>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table dash-table mb-0">

                <thead>

                    <tr>

                        <th>Vale</th>

                        <th>Vehículo</th>

                        <th>Fecha</th>

                        <th>Estado</th>

                        <th class="text-end">
                            Acción
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($ultimosVales as $vale)

                        @php
                            $estado = strtoupper(
                                $vale->estado ?? ''
                            );
                        @endphp

                        <tr>

                            <td>

                                <strong class="vale-number">
                                    {{ $vale->numero_vale }}
                                </strong>

                            </td>


                            <td>

                                <div class="fw-semibold">
                                    {{ $vale->vehiculo->codigo ?? '—' }}
                                </div>

                                <small class="text-muted">
                                    {{ $vale->vehiculo->placa ?? 'Sin placa' }}
                                </small>

                            </td>


                            <td>

                                {{ $vale->fecha?->format('d/m/Y') ?? '—' }}

                            </td>


                            <td>

                                @if(
                                    $estado === 'COMPLETADO'
                                    || $estado === 'UTILIZADO'
                                )

                                    <span class="status status-success">
                                        {{ $vale->estado }}
                                    </span>

                                @elseif($estado === 'ANULADO')

                                    <span class="status status-danger">
                                        {{ $vale->estado }}
                                    </span>

                                @elseif($estado === 'EMITIDO')

                                    <span class="status status-warning">
                                        {{ $vale->estado }}
                                    </span>

                                @else

                                    <span class="status status-info">
                                        {{ $vale->estado }}
                                    </span>

                                @endif

                            </td>


                            <td class="text-end">

                                <a
                                    href="{{ route(
                                        'vales-combustible.show',
                                        $vale
                                    ) }}"
                                    class="btn btn-sm btn-light border"
                                    title="Ver vale"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                Todavía no existen movimientos registrados.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection


{{-- =========================================================
     ESTILOS
========================================================= --}}

@push('styles')

<style>

    /* =========================================================
       ENCABEZADO
    ========================================================= */

   .dash-header {
    padding: 18px 22px;

    background: #ffffff;

    border: 1px solid #e7ebe8;
    border-left: 5px solid #08712f;

    border-radius: 13px;

    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}


    .dash-system {
        margin-bottom: 6px;

        color: #08712f;

        font-size: 11px;
        font-weight: 800;

        letter-spacing: .8px;
    }


    .dash-welcome {
        margin: 0;

        color: #24372a;

        font-size: 25px;
        font-weight: 800;
    }


    .dash-subtitle {
        margin-top: 5px;

        color: #7e8981;

        font-size: 13px;
    }


    .dash-period {
        display: flex;
        align-items: center;

        gap: 10px;

        min-width: 180px;

        padding: 10px 14px;

        background: #f6f8f7;

        border: 1px solid #e4e9e5;
        border-radius: 9px;
    }


    .dash-period-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 36px;
        height: 36px;

        color: #08712f;

        background: #e8f5ed;

        border-radius: 8px;
    }


    .dash-period small {
        display: block;

        color: #89928b;

        font-size: 9px;
        font-weight: 800;

        letter-spacing: .5px;
    }


    .dash-period strong {
        display: block;

        color: #344239;

        font-size: 13px;
    }


    .dash-actions {
        display: flex;
        flex-wrap: wrap;

        gap: 8px;

        margin-top: 18px;
    }


    /* =========================================================
       INDICADORES
    ========================================================= */

    .dash-stat {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    min-height: 105px;
    padding: 16px 18px;

    background: #ffffff;

    border: 1px solid #e8ece9;
    border-radius: 12px;

    box-shadow: 0 2px 7px rgba(0,0,0,.035);

    transition:
        transform .15s ease,
        box-shadow .15s ease;
}


    a:hover .dash-stat {
        transform: translateY(-2px);

        box-shadow:
            0 6px 16px rgba(0,0,0,.07);
    }


    .dash-stat-label {
        margin-bottom: 5px;

        color: #7e8981;

        font-size: 11px;
        font-weight: 800;

        letter-spacing: .4px;
        text-transform: uppercase;
    }


    .dash-stat-value {
        color: #26382c;

        font-size: 28px;
        font-weight: 800;

        line-height: 1.2;
    }


    .dash-stat-value-medium {
        font-size: 24px;
    }


    .dash-stat-money {
        font-size: 21px;
    }


    .dash-stat-description {
        margin-top: 7px;

        color: #98a099;

        font-size: 11px;
    }


    .dash-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 45px;
        height: 45px;

        flex-shrink: 0;

        border-radius: 11px;

        font-size: 20px;
    }


    /* AZUL */

    .dash-stat-blue {
        border-top: 3px solid #3b82f6;
    }

    .dash-stat-blue .dash-stat-icon {
        color: #2563eb;
        background: #eaf2ff;
    }


    /* VERDE */

    .dash-stat-green {
        border-top: 3px solid #16a34a;
    }

    .dash-stat-green .dash-stat-icon {
        color: #15803d;
        background: #e9f7ee;
    }


    /* MORADO */

    .dash-stat-purple {
        border-top: 3px solid #8b5cf6;
    }

    .dash-stat-purple .dash-stat-icon {
        color: #7c3aed;
        background: #f2edff;
    }


    /* NARANJA */

    .dash-stat-orange {
        border-top: 3px solid #f59e0b;
    }

    .dash-stat-orange .dash-stat-icon {
        color: #d97706;
        background: #fff5df;
    }


    /* =========================================================
       CARDS
    ========================================================= */

    .dash-card {
        overflow: hidden;

        border: 1px solid #e7ebe8 !important;
        border-radius: 12px !important;

        box-shadow:
            0 2px 8px rgba(0,0,0,.04) !important;
    }


.dash-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    min-height: 58px;
    padding: 11px 15px;

    background: #ffffff !important;
    border-bottom: 1px solid #edf0ee !important;
}


    .dash-card-header h2 {
        margin: 0;

        color: #2c3d31;

        font-size: 15px;
        font-weight: 800;
    }


    .dash-card-header p {
        margin: 3px 0 0;

        color: #8c958e;

        font-size: 11px;
    }


    /* =========================================================
       COMPARACIÓN
    ========================================================= */

    .dash-comparison {
        text-align: right;
    }


    .dash-comparison > span {
        display: block;

        font-size: 16px;
        font-weight: 800;
    }


    .dash-comparison small {
        color: #8a938c;
        font-size: 10px;
    }


    .comparison-up {
        color: #d97706;
    }


    .comparison-down {
        color: #15803d;
    }


    .comparison-equal {
        color: #64748b;
    }


    /* =========================================================
       GRÁFICO
    ========================================================= */

 .dash-chart-container {
    position: relative;
    width: 100%;
    height: 190px;
}


    .dash-chart-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 10px;
}

.dash-chart-summary > div {
    padding: 8px 10px;
    background: #f8faf9;
    border: 1px solid #edf0ee;
    border-radius: 8px;
}

.dash-chart-summary span {
    display: block;
    margin-bottom: 2px;
    color: #8b948d;
    font-size: 8px;
    font-weight: 800;
    text-transform: uppercase;
}

.dash-chart-summary strong {
    color: #34463a;
    font-size: 11px;
}

    /* =========================================================
       CALENDARIO
    ========================================================= */

    .calendar-icon {
        display: flex;
        align-items: center;
        justify-content: center;

        width: 35px;
        height: 35px;

        color: #2563eb;
        background: #eaf2ff;

        border-radius: 8px;
    }


.dash-calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    max-width: 470px;
    margin: 0 auto;
}


    .calendar-weekday {
        padding: 5px 0 8px;

        color: #879188;

        font-size: 9px;
        font-weight: 800;

        text-align: center;
        text-transform: uppercase;
    }


 .calendar-day,
.calendar-empty {
    height: 38px;
}

    .calendar-day {
    position: relative;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: default;

    color: #48564c;
    background: #f8faf9;

    border: 1px solid #edf0ee;
    border-radius: 6px;

    font-size: 10px;
    font-weight: 700;

    transition: .15s ease;
}
    .calendar-day:hover {
        transform: translateY(-1px);

        border-color: #aebbb2;
    }


    .calendar-low {
        color: #166534;
        background: #edf8f1;
        border-color: #d8eddf;
    }


    .calendar-medium {
        color: #1d4ed8;
        background: #edf4ff;
        border-color: #d9e7ff;
    }


    .calendar-high {
        color: #92400e;
        background: #fff4dd;
        border-color: #f8dfaa;
    }


    .calendar-today {
        box-shadow:
            inset 0 0 0 2px #08712f;
    }


    .calendar-dot {
        position: absolute;

        width: 4px;
        height: 4px;

        bottom: 4px;

        background: currentColor;

        border-radius: 50%;
    }


    .calendar-legend {
        display: flex;
        justify-content: center;

        gap: 15px;

        margin-top: 14px;

        color: #808a82;

        font-size: 9px;
    }


    .legend-dot {
        display: inline-block;

        width: 7px;
        height: 7px;

        margin-right: 3px;

        border-radius: 50%;
    }


    .legend-low {
        background: #4caf72;
    }


    .legend-medium {
        background: #4c83db;
    }


    .legend-high {
        background: #e9a53a;
    }


    .calendar-highlight {
        display: flex;
        align-items: center;

        gap: 9px;

        margin-top: 15px;

        padding: 10px 12px;

        color: #70511c;
        background: #fff8e8;

        border: 1px solid #f4e4bd;
        border-radius: 8px;
    }


    .calendar-highlight i {
        color: #d99724;
    }


    .calendar-highlight small {
        display: block;

        color: #9b8155;

        font-size: 9px;
    }


    .calendar-highlight strong {
        display: block;

        font-size: 11px;
    }


    /* =========================================================
       TABLA
    ========================================================= */

    .dash-table thead th {
        padding: 11px 17px;

        color: #7f8981;
        background: #fafbfa;

        border-bottom:
            1px solid #e8ece9;

        font-size: 9px;
        font-weight: 800;

        letter-spacing: .5px;
        text-transform: uppercase;
    }


    .dash-table tbody td {
        padding: 12px 17px;

        color: #39483e;

        border-bottom:
            1px solid #f0f2f1;

        font-size: 12px;

        vertical-align: middle;
    }


    .dash-table tbody tr:last-child td {
        border-bottom: 0;
    }


    .dash-table tbody tr:hover {
        background: #fbfdfb;
    }


    .vale-number {
        color: #08712f;
    }


    /* =========================================================
       ESTADOS
    ========================================================= */

    .status {
        display: inline-block;

        padding: 4px 8px;

        border-radius: 20px;

        font-size: 9px;
        font-weight: 800;
    }


    .status-success {
        color: #166534;
        background: #e9f7ee;
    }


    .status-warning {
        color: #92400e;
        background: #fff4dd;
    }


    .status-danger {
        color: #b42318;
        background: #feeceb;
    }


    .status-info {
        color: #1d4ed8;
        background: #eaf2ff;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

@media (max-width: 991.98px) {

    .dash-chart-container {
        height: 190px;
    }

    .dash-calendar {
        max-width: 500px;
    }

}


    @media (max-width: 767.98px) {

        .dash-header {
            padding: 20px;
        }
        .calendar-day,
.calendar-empty {
    height: 34px;
}

.dash-calendar {
    max-width: 100%;
}


        .dash-welcome {
            font-size: 21px;
        }


        .dash-period {
            width: 100%;
        }


        .dash-actions .btn {
            flex: 1 1 auto;
        }


        .dash-chart-summary {
            grid-template-columns: 1fr;
        }


        .calendar-day {
            font-size: 9px;
        }


        .dash-stat {
            min-height: 115px;
            padding: 16px;
        }


        .dash-stat-value {
            font-size: 23px;
        }


        .dash-stat-money {
            font-size: 18px;
        }

    }

    /* Dashboard compacto */

.dash-card > .card-body {
    padding: 14px;
}

.dash-calendar + .calendar-legend {
    margin-top: 9px;
}

.calendar-highlight {
    margin-top: 10px;
    padding: 8px 10px;
}

.calendar-weekday {
    padding: 3px 0 5px;
    font-size: 8px;
}

</style>



@endpush


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | TOOLTIPS BOOTSTRAP
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(function (elemento) {

            new bootstrap.Tooltip(elemento);

        });


    /*
    |--------------------------------------------------------------------------
    | DATOS DEL CONSUMO DIARIO
    |--------------------------------------------------------------------------
    */

    const consumoDiario =
        @json($consumoDiario);


    const dias = [];

    const litros = [];


    Object.keys(consumoDiario)
        .forEach(function (dia) {

            dias.push(dia);

            litros.push(
                Number(
                    consumoDiario[dia].litros ?? 0
                )
            );

        });


    /*
    |--------------------------------------------------------------------------
    | GRÁFICO
    |--------------------------------------------------------------------------
    */

    const canvas =
        document.getElementById(
            'consumoDiarioChart'
        );


    if (canvas) {

        new Chart(canvas, {

            type: 'line',

            data: {

                labels: dias,

                datasets: [

                    {

                        label: 'Litros',

                        data: litros,

                        borderColor:
                            '#2563eb',

                        backgroundColor:
                            'rgba(37, 99, 235, 0.08)',

                        borderWidth: 2,

                        pointRadius: 3,

                        pointHoverRadius: 5,

                        pointBackgroundColor:
                            '#08712f',

                        pointBorderColor:
                            '#ffffff',

                        pointBorderWidth: 2,

                        fill: true,

                        tension: 0.35

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,


                interaction: {

                    intersect: false,

                    mode: 'index'

                },


                plugins: {

                    legend: {
                        display: false
                    },


                    tooltip: {

                        callbacks: {

                            label: function (context) {

                                return (
                                    context.parsed.y
                                        .toLocaleString(
                                            'es-BO',
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }
                                        )
                                    + ' litros'
                                );

                            }

                        }

                    }

                },


                scales: {

                    x: {

                        grid: {
                            display: false
                        },

                        ticks: {

                            color: '#8b948d',

                            font: {
                                size: 9
                            }

                        }

                    },


                    y: {

                        beginAtZero: true,

                        border: {
                            display: false
                        },

                        grid: {

                            color:
                                'rgba(0,0,0,.045)'

                        },

                        ticks: {

                            color: '#8b948d',

                            font: {
                                size: 9
                            },

                            callback: function (value) {
                                return value + ' L';
                            }

                        }

                    }

                }

            }

        });

    }

});

</script>

@endpush