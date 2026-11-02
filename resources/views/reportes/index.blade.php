@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid">
    <div class="report-hero mb-4">
        <div>
            <span class="report-eyebrow">SIGECOM · ANÁLISIS INSTITUCIONAL</span>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-bar-chart-fill me-2"></i>Centro de Reportes
            </h2>
            <p class="text-muted mb-0">Consulta y análisis del consumo de combustible de GAMS y Policía.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>No se pudo generar el reporte. Revise estos datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card report-filter-card shadow-sm border-0">
        <div class="card-header bg-white border-success border-top border-4">
            <h5 class="mb-0 text-success"><i class="bi bi-funnel me-2"></i>Filtros del informe</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.index') }}" id="formCentroReportes" data-skip-submit-processing="true">
                <input type="hidden" name="formato" id="formatoReporte" value="html">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6 col-xl-3">
                        <label for="tipo_reporte" class="form-label fw-semibold">Tipo de reporte</label>
                        <select name="tipo_reporte" id="tipo_reporte" class="form-select" required>
                            <option value="">Seleccione un informe...</option>
                            <option value="general" @selected(old('tipo_reporte', request('tipo_reporte')) === 'general')>General de consumo</option>
                            <option value="vehiculo" @selected(old('tipo_reporte', request('tipo_reporte')) === 'vehiculo')>Por vehículo</option>
                            <option value="individual" @selected(old('tipo_reporte', request('tipo_reporte')) === 'individual')>Individual por vehículo</option>
                            <option value="apertura" @selected(old('tipo_reporte', request('tipo_reporte')) === 'apertura')>Por apertura programática</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <label for="ambito" class="form-label fw-semibold">Institución / ámbito</label>
                        <select name="ambito" id="ambito" class="form-select" required>
                            <option value="todos" @selected(old('ambito', request('ambito', 'todos')) === 'todos')>Todos (GAMS y Policía)</option>
                            <option value="gams" @selected(old('ambito', request('ambito')) === 'gams')>GAMS</option>
                            <option value="policia" @selected(old('ambito', request('ambito')) === 'policia')>Policía</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 report-filter" data-report-types="general,vehiculo,individual,apertura">
                        <label for="tipo_combustible_id" class="form-label fw-semibold">Tipo de combustible</label>
                        <select name="tipo_combustible_id" id="tipo_combustible_id" class="form-select">
                            <option value="">Todos los combustibles</option>
                            @foreach($tiposCombustible as $tipoCombustible)
                                <option value="{{ $tipoCombustible->id }}" @selected(old('tipo_combustible_id', request('tipo_combustible_id')) == $tipoCombustible->id)>{{ $tipoCombustible->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3" id="periodicidad-wrapper">
                        <label for="periodicidad" class="form-label fw-semibold">Período</label>
                        <select name="periodicidad" id="periodicidad" class="form-select">
                            <option value="mensual" @selected(old('periodicidad', request('periodicidad', 'personalizado')) === 'mensual')>Mensual</option>
                            <option value="anual" @selected(old('periodicidad', request('periodicidad')) === 'anual')>Anual</option>
                            <option value="personalizado" @selected(old('periodicidad', request('periodicidad', 'personalizado')) === 'personalizado')>Rango personalizado</option>
                        </select>
                    </div>
                </div>

                <fieldset id="filtro-periodo" class="report-filter mt-3" data-report-types="general,vehiculo,individual,apertura">
                    <legend class="fs-6 fw-semibold text-success">Período de consulta</legend>
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3" id="mes-wrapper">
                            <label for="mes" class="form-label">Mes</label>
                            <select name="mes" id="mes" class="form-select">
                                @foreach(range(1, 12) as $mes)
                                    <option value="{{ $mes }}" @selected(old('mes', request('mes', date('n'))) == $mes)>
                                        {{ \Carbon\Carbon::create(now()->year, $mes, 1)->locale('es')->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3" id="anio-wrapper">
                            <label for="anio" class="form-label">Año</label>
                            <input type="number" name="anio" id="anio" min="2000" max="2100" class="form-control" value="{{ old('anio', request('anio', date('Y'))) }}">
                        </div>
                        <div class="col-12 col-md-3" id="fecha-inicio-wrapper">
                            <label for="fecha_inicio" class="form-label">Fecha inicial</label>
                            <div class="input-group">
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', request('fecha_inicio')) }}" aria-describedby="fecha_rango_error">
                                <button type="button" class="btn btn-outline-success" id="abrir-calendario-inicio" aria-label="Abrir calendario de fecha inicial" title="Abrir calendario">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-3" id="fecha-fin-wrapper">
                            <label for="fecha_fin" class="form-label">Fecha final</label>
                            <div class="input-group">
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin', request('fecha_fin')) }}" aria-describedby="fecha_rango_error">
                                <button type="button" class="btn btn-outline-success" id="abrir-calendario-fin" aria-label="Abrir calendario de fecha final" title="Abrir calendario">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-danger py-2 mt-2" id="fecha_rango_error" role="alert" hidden>
                        La fecha final no puede ser anterior a la fecha inicial.
                    </div>
                </fieldset>

                <fieldset class="report-filter mt-3" data-report-types="general,vehiculo,individual">
                    <legend class="fs-6 fw-semibold text-success">Filtros adicionales</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="vehiculo_id" class="form-label">Vehículo</label>
                            <select name="vehiculo_id" id="vehiculo_id" class="form-select">
                                <option value="">Todos los vehículos</option>
                                @foreach($vehiculos as $vehiculo)
                                    <option value="{{ $vehiculo->id }}" @selected(old('vehiculo_id', request('vehiculo_id')) == $vehiculo->id)>{{ $vehiculo->codigo }} · {{ $vehiculo->placa }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Deje «Todos los vehículos» para incluir todos.</div>
                        </div>
                        <fieldset class="col-md-6 p-0 border-0" data-report-types="general">
                            <label for="unidad_id" class="form-label">Unidad</label>
                            <select name="unidad_id" id="unidad_id" class="form-select">
                                <option value="">Todas las unidades</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}" @selected(old('unidad_id', request('unidad_id')) == $unidad->id)>{{ $unidad->nombre }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                </fieldset>

                <fieldset class="report-filter mt-3" data-report-types="apertura">
                    <legend class="fs-6 fw-semibold text-success">Apertura programática</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="apertura_programatica_id" class="form-label">Apertura programática</label>
                            <select name="apertura_programatica_id" id="apertura_programatica_id" class="form-select">
                                <option value="">Todas las aperturas</option>
                                @foreach($aperturas as $apertura)
                                    <option value="{{ $apertura->id }}" @selected(old('apertura_programatica_id', request('apertura_programatica_id')) == $apertura->id)>{{ $apertura->codigo }}{{ $apertura->descripcion ? ' · ' . $apertura->descripcion : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="periodo_id" class="form-label">Período</label>
                            <select name="periodo_id" id="periodo_id" class="form-select">
                                <option value="">Todos los períodos</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{ $periodo->id }}" @selected(old('periodo_id', request('periodo_id')) == $periodo->id)>{{ $periodo->mes }}/{{ $periodo->anio }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="report-filter mt-3" data-report-types="vehiculo">
                    <legend class="fs-6 fw-semibold text-success">Observaciones institucionales</legend>
                    <label for="observaciones" class="form-label">Observaciones para informe, PDF y Excel (opcional)</label>
                    <textarea name="observaciones" id="observaciones" maxlength="2000" rows="2" class="form-control">{{ old('observaciones', request('observaciones')) }}</textarea>
                </fieldset>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                    <button class="btn btn-success btn-generar-reporte" type="submit" data-formato="html">
                        <i class="bi bi-play-fill me-1"></i>Generar reporte
                    </button>
                    <button class="btn btn-outline-danger" type="submit" data-formato="pdf">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF
                    </button>
                    <button class="btn btn-outline-success" type="submit" data-formato="excel">
                        <i class="bi bi-file-earmark-excel me-1"></i>Descargar Excel
                    </button>
                    <button class="btn btn-outline-secondary" type="submit" data-formato="imprimir">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                </div>
            </form>
        </div>
    </div>

    <section class="mt-4" aria-labelledby="resultado-reporte-titulo" aria-live="polite">
        <h3 id="resultado-reporte-titulo" class="visually-hidden">Resultado del reporte</h3>
        <div id="estadoReporte" class="alert alert-info" role="status" hidden></div>
        <div id="resultadoReporte">
            @if($dashboard)
                @include('reportes.partials.dashboard', ['dashboard' => $dashboard])
            @endif
            @if($reportHtml)
                @include('reportes.partials.resultado', ['reportHtml' => $reportHtml])
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reportType = document.getElementById('tipo_reporte');
    const form = document.getElementById('formCentroReportes');
    const formatInput = document.getElementById('formatoReporte');
    const groups = document.querySelectorAll('.report-filter');
    const resultContainer = document.getElementById('resultadoReporte');
    const statusMessage = document.getElementById('estadoReporte');
    const dateGroup = document.getElementById('filtro-periodo');
    const periodicity = document.getElementById('periodicidad');
    const monthWrapper = document.getElementById('mes-wrapper');
    const monthInput = document.getElementById('mes');
    const yearWrapper = document.getElementById('anio-wrapper');
    const yearInput = document.getElementById('anio');
    const startDateWrapper = document.getElementById('fecha-inicio-wrapper');
    const endDateWrapper = document.getElementById('fecha-fin-wrapper');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const abrirCalendarioInicio = document.getElementById('abrir-calendario-inicio');
    const abrirCalendarioFin = document.getElementById('abrir-calendario-fin');
    const fechaRangoError = document.getElementById('fecha_rango_error');
    const activeOperations = new Set();
    const buttons = form.querySelectorAll('button[data-formato]');
    let periodicityTouched = periodicity.value !== 'personalizado';

    buttons.forEach((button) => {
        button.dataset.originalHtml = button.innerHTML;
    });

    function setButtonProcessing(button, processing) {
        if (!button) {
            return;
        }

        button.disabled = processing;
        button.innerHTML = processing
            ? '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Procesando...'
            : button.dataset.originalHtml;
    }

    function showOperationError(message) {
        statusMessage.hidden = false;
        statusMessage.className = 'alert alert-danger';
        statusMessage.textContent = message;
    }

    function updateFilters() {
        const selected = reportType.value;
        document.getElementById('periodicidad-wrapper').hidden = !selected;

        groups.forEach((group) => {
            const types = (group.dataset.reportTypes || '').split(/[,\s]+/);
            const visible = types.includes(selected);

            group.hidden = !visible;
            group.disabled = !visible;
        });

        const individualVehicle = document.getElementById('vehiculo_id');
        if (individualVehicle) {
            individualVehicle.required = selected === 'individual';
        }

        document.querySelectorAll('fieldset[data-report-types="general"]:not(.report-filter)').forEach((element) => {
            const visible = selected === 'general';
            element.hidden = !visible;
            element.disabled = !visible;
        });

        updatePeriodicity();
    }

    function updatePeriodicity() {
        const selected = periodicity.value;
        const supportsPeriod = dateGroup && !dateGroup.disabled;
        const monthly = supportsPeriod && selected === 'mensual';
        const annual = supportsPeriod && selected === 'anual';
        const custom = supportsPeriod && selected === 'personalizado';

        monthWrapper.hidden = !monthly;
        monthInput.disabled = !monthly;
        yearWrapper.hidden = !(monthly || annual);
        yearInput.disabled = !(monthly || annual);
        yearInput.required = monthly || annual;
        monthInput.required = monthly;
        startDateWrapper.hidden = !custom;
        endDateWrapper.hidden = !custom;
        fechaInicio.disabled = !custom;
        fechaFin.disabled = !custom;
        abrirCalendarioInicio.disabled = !custom;
        abrirCalendarioFin.disabled = !custom;

        updateDateRange();
    }

    function openDatePicker(input) {
        if (input.disabled) {
            return;
        }

        if (typeof input.showPicker === 'function') {
            input.showPicker();
        } else {
            input.focus();
        }
    }

    function updateDateRange() {
        const supportsDateRange = dateGroup && !dateGroup.disabled && periodicity.value === 'personalizado';
        const fechaInicial = supportsDateRange ? fechaInicio.value : '';
        const fechaFinal = supportsDateRange ? fechaFin.value : '';
        const rangoInvalido = Boolean(fechaInicial && fechaFinal && fechaFinal < fechaInicial);

        fechaInicio.max = fechaFinal;
        fechaFin.min = fechaInicial;
        fechaFin.setCustomValidity(rangoInvalido ? fechaRangoError.textContent.trim() : '');
        fechaFin.classList.toggle('is-invalid', rangoInvalido);
        fechaRangoError.hidden = !rangoInvalido;
    }

    reportType.addEventListener('change', () => {
        if (reportType.value === 'policia' && !periodicityTouched) {
            periodicity.value = 'mensual';
        }
        updateFilters();
    });
    periodicity.addEventListener('change', () => {
        periodicityTouched = true;
        updatePeriodicity();
    });
    fechaInicio.addEventListener('input', updateDateRange);
    fechaInicio.addEventListener('change', updateDateRange);
    fechaFin.addEventListener('input', updateDateRange);
    fechaFin.addEventListener('change', updateDateRange);
    abrirCalendarioInicio.addEventListener('click', () => openDatePicker(fechaInicio));
    abrirCalendarioFin.addEventListener('click', () => openDatePicker(fechaFin));
    form.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-formato]');
        if (button) {
            formatInput.value = button.dataset.formato;
        }
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = event.submitter || form.querySelector('button[data-formato="html"]');
        const format = submitButton?.dataset.formato || formatInput.value;
        const operationKey = format;

        if (activeOperations.has(operationKey)) {
            return;
        }

        activeOperations.add(operationKey);
        formatInput.value = format;
        setButtonProcessing(submitButton, true);

        statusMessage.hidden = false;
        statusMessage.className = 'alert alert-info';
        statusMessage.textContent = format === 'html'
            ? 'Generando reporte…'
            : `Abriendo ${submitButton?.textContent.trim() || 'el reporte'}…`;

        try {
            const parameters = new URLSearchParams(new FormData(form));
            if (periodicity.value === 'mensual') {
                const year = Number(yearInput.value);
                const month = Number(monthInput.value);
                if (!year || !month) {
                    throw new Error('Seleccione el mes y año del reporte.');
                }
                const lastDay = new Date(year, month, 0).getDate();
                parameters.set('fecha_inicio', `${year}-${String(month).padStart(2, '0')}-01`);
                parameters.set('fecha_fin', `${year}-${String(month).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`);
            } else if (periodicity.value === 'anual') {
                if (!yearInput.value) {
                    throw new Error('Seleccione el año del reporte.');
                }
                parameters.set('fecha_inicio', `${yearInput.value}-01-01`);
                parameters.set('fecha_fin', `${yearInput.value}-12-31`);
            }
            const url = new URL(form.action, window.location.href);
            url.search = parameters.toString();

            if (format !== 'html') {
                const reportWindow = window.open(url.href, '_blank');
                if (!reportWindow) {
                    throw new Error('El navegador bloqueó la nueva ventana. Permita ventanas emergentes para abrir el reporte.');
                }
                reportWindow.opener = null;
                statusMessage.hidden = true;
                return;
            }

            const response = await fetch(url.href, {
                headers: {
                    'Accept': 'application/json, text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                let message = `No se pudo consultar el reporte (HTTP ${response.status}).`;
                const errorText = await response.text();
                if (errorText) {
                    try {
                        const error = JSON.parse(errorText);
                        message = error.errors
                            ? Object.values(error.errors).flat().join(' ')
                            : error.message || message;
                    } catch {
                        message = errorText;
                    }
                }
                throw new Error(message);
            }

            const responseData = await response.json();
            const parsed = new DOMParser().parseFromString(responseData.reportHtml, 'text/html');
            const reportFragment = parsed.querySelector('.report-result-fragment');

            if (!reportFragment) {
                throw new Error('La respuesta no contiene el resultado del reporte.');
            }

            reportFragment.querySelectorAll('form').forEach((reportForm) => {
                const filterCard = reportForm.closest('.card');
                if (filterCard) {
                    filterCard.remove();
                } else {
                    reportForm.remove();
                }
            });
            reportFragment.querySelectorAll('.no-print:has(a[href*="/pdf"], a[href*="/excel"], button[onclick*="print"], button[onclick*="Pdf"], button[id*="imprimir"])')
                .forEach((actions) => actions.remove());

            resultContainer.innerHTML = responseData.dashboardHtml;
            resultContainer.append(...reportFragment.childNodes);
            statusMessage.hidden = true;
            statusMessage.textContent = '';
            window.history.replaceState({}, '', url.href);
        } catch (error) {
            showOperationError(error.message || 'No se pudo completar la operación.');
        } finally {
            activeOperations.delete(operationKey);
            setButtonProcessing(submitButton, false);
        }
    });

    window.addEventListener('pageshow', () => {
        activeOperations.clear();
        buttons.forEach((button) => setButtonProcessing(button, false));
    });
    updateFilters();
});
</script>
@endpush

@push('styles')
<style>
.report-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border: 1px solid #dce8df;
    border-left: 5px solid #198754;
    border-radius: 1rem;
    background: linear-gradient(110deg, #f3faf5, #fff 68%);
}
.report-eyebrow {
    display: block;
    margin-bottom: .35rem;
    color: #64766b;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .12em;
}
.report-filter-card,
.report-dashboard-card {
    border: 1px solid #e0e9e3 !important;
    border-radius: 1rem !important;
}
.btn-generar-reporte {
    min-width: 190px;
    min-height: 46px;
    margin-left: auto;
    border-radius: .75rem;
    font-weight: 700;
}
.report-stat-card {
    height: 100%;
    padding: 1rem 1.1rem;
    border: 1px solid #e1eae4;
    border-radius: .9rem;
    background: #fff;
    box-shadow: 0 .2rem .8rem rgba(23, 60, 42, .04);
}
.report-stat-card small {
    color: #6b7c72;
    font-weight: 600;
}
.report-stat-note {
    display: block;
    margin-top: .35rem;
    color: #718077;
    font-size: .75rem;
}
.report-stat-card strong {
    display: block;
    margin-top: .35rem;
    color: #173d2b;
    font-size: 1.45rem;
}
.report-chart-list {
    display: grid;
    gap: .75rem;
    max-height: 410px;
    overflow: auto;
}
.report-chart-row {
    display: grid;
    grid-template-columns: minmax(110px, 1fr) minmax(100px, 3fr) minmax(75px, auto);
    align-items: center;
    gap: .75rem;
}
.report-chart-label,
.report-chart-value {
    overflow: hidden;
    color: #52645a;
    font-size: .82rem;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.report-chart-value {
    color: #173d2b;
    font-weight: 700;
    text-align: right;
}
.report-chart-track {
    height: .7rem;
    overflow: hidden;
    border-radius: 1rem;
    background: #edf3ef;
}
.report-chart-bar {
    height: 100%;
    min-width: 2px;
    border-radius: inherit;
    background: linear-gradient(90deg, #198754, #55aa79);
}
.report-month-chart {
    display: grid;
    grid-template-columns: repeat(12, minmax(22px, 1fr));
    align-items: end;
    gap: .45rem;
    min-height: 190px;
    padding: 1rem .25rem .25rem;
}
.report-month-column {
    display: flex;
    height: 160px;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: .4rem;
}
.report-month-bar {
    width: min(100%, 28px);
    min-height: 2px;
    border-radius: .35rem .35rem 0 0;
    background: #198754;
}
.report-month-column small {
    color: #66776d;
    font-size: .68rem;
}
.report-fuel-list {
    display: grid;
    gap: .75rem;
}
.report-fuel-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: .65rem .8rem;
    border-radius: .65rem;
    background: #f5f9f6;
}
.report-filter {
    border: 1px solid #dce7e0;
    border-radius: .5rem;
    padding: 1rem;
}
.report-filter[hidden] {
    display: none !important;
}
@media (max-width: 575.98px) {
    .report-hero { padding: 1rem; }
    .btn-generar-reporte { width: 100%; margin-left: 0; }
    .report-chart-row { grid-template-columns: minmax(85px, 1fr) minmax(60px, 2fr) minmax(62px, auto); gap: .45rem; }
    .report-chart-label, .report-chart-value { font-size: .72rem; }
    .report-month-chart { gap: .2rem; }
    .report-month-column small { font-size: .58rem; }
}
</style>
@endpush
