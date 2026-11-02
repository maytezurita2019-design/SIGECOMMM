@php
    $maxVehiculo = max(1, (float) $dashboard['vehiculos']->max('litros'));
    $maxMes = max(1, (float) collect($dashboard['meses'])->max('litros'));
@endphp

<section class="mb-4" aria-label="Resumen y gráficos del reporte">
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="report-stat-card">
                <small><i class="bi bi-receipt me-1 text-success"></i>Vales consumidos</small>
                <strong>{{ number_format($dashboard['totalVales'], 0, ',', '.') }}</strong>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="report-stat-card">
                <small><i class="bi bi-droplet-half me-1 text-success"></i>Litros consumidos</small>
                <strong>{{ number_format($dashboard['totalLitros'], 2, ',', '.') }} <span class="fs-6 fw-normal">L</span></strong>
            </article>
        </div>
        @if($dashboard['mostrarEntregados'])
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="report-stat-card">
                    <small><i class="bi bi-hourglass-split me-1 text-warning"></i>Vales Policía pendientes de uso</small>
                    <strong>{{ number_format($dashboard['entregados'], 0, ',', '.') }}</strong>
                    <span class="report-stat-note">Acumulado del ámbito seleccionado; independiente de los demás filtros.</span>
                </article>
            </div>
        @endif
        @if($dashboard['mostrarPendientes'])
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="report-stat-card">
                    <small><i class="bi bi-pencil-square me-1 text-warning"></i>Vales GAMS pendientes de completar</small>
                    <strong>{{ number_format($dashboard['pendientes'], 0, ',', '.') }}</strong>
                    <span class="report-stat-note">Acumulado del ámbito seleccionado; independiente de los demás filtros.</span>
                </article>
            </div>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <article class="card report-dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-1 text-success fw-bold">Consumo de combustible por vehículo</h5>
                    <small class="text-muted">Litros de vales consumidos dentro de los filtros seleccionados.</small>
                </div>
                <div class="card-body">
                    @if($dashboard['vehiculos']->isEmpty())
                        <div class="text-center text-muted py-4">No hay consumos para graficar con estos filtros.</div>
                    @else
                        <div class="report-chart-list" role="list" aria-label="Litros consumidos por vehículo">
                            @foreach($dashboard['vehiculos'] as $vehiculo)
                                <div class="report-chart-row" role="listitem" aria-label="{{ $vehiculo['label'] }}: {{ number_format($vehiculo['litros'], 2, ',', '.') }} litros">
                                    <span class="report-chart-label" title="{{ $vehiculo['label'] }}">{{ $vehiculo['label'] }}</span>
                                    <div class="report-chart-track" aria-hidden="true">
                                        <div class="report-chart-bar" style="width: {{ min(100, ($vehiculo['litros'] / $maxVehiculo) * 100) }}%"></div>
                                    </div>
                                    <span class="report-chart-value">{{ number_format($vehiculo['litros'], 2, ',', '.') }} L</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>
        </div>
        <div class="col-12 col-xl-4">
            <article class="card report-dashboard-card h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-1 text-success fw-bold">{{ $dashboard['mostrarMeses'] ? 'Consumo anual por mes' : 'Distribución por combustible' }}</h5>
                    <small class="text-muted">{{ $dashboard['mostrarMeses'] ? 'Litros por mes del año seleccionado.' : 'Litros por tipo de combustible.' }}</small>
                </div>
                <div class="card-body">
                    @if($dashboard['mostrarMeses'])
                        <div class="report-month-chart" role="img" aria-label="Litros consumidos por mes">
                            @foreach($dashboard['meses'] as $mes)
                                <div class="report-month-column" title="{{ $mes['label'] }}: {{ number_format($mes['litros'], 2, ',', '.') }} litros">
                                    <div class="report-month-bar" style="height: {{ min(100, ($mes['litros'] / $maxMes) * 100) }}%" aria-hidden="true"></div>
                                    <small>{{ $mes['label'] }}</small>
                                </div>
                            @endforeach
                        </div>
                    @elseif($dashboard['combustibles']->isNotEmpty())
                        <div class="report-fuel-list" role="list" aria-label="Consumo por combustible">
                            @foreach($dashboard['combustibles'] as $combustible)
                                <div class="report-fuel-item" role="listitem">
                                    <span>{{ $combustible['label'] }}</span>
                                    <strong>{{ number_format($combustible['litros'], 2, ',', '.') }} L</strong>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">No hay consumos por combustible para mostrar.</div>
                    @endif
                </div>
            </article>
        </div>
    </div>
</section>
