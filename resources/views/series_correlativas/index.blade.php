
@extends('layouts.app')

@section('title', 'Series correlativas')

@section('content')
@php
    $rolActual = strtoupper(trim(auth()->user()->rol->nombre ?? ''));
    $puedeModificarSeries = $rolActual === 'TECNICO_II';
    $nombresSeries = [
        'GAMS-GAS' => 'GAMS – Gasolina',
        'GAMS-DIE' => 'GAMS – Diésel',
        'POL-GAS' => 'Policía – Gasolina',
    ];
    $subtitulosSeries = [
        'GAMS-GAS' => 'Vales de uso interno (GAM Sacaba)',
        'GAMS-DIE' => 'Vales de uso interno (GAM Sacaba)',
        'POL-GAS' => 'Vales por lotes (Entrega a Policía)',
    ];
    $ordenSeries = ['GAMS-GAS' => 0, 'GAMS-DIE' => 1, 'POL-GAS' => 2];
    $seriesOrdenadas = collect($series)->sortBy(fn ($s) => $ordenSeries[$s->codigo_serie] ?? 99)->values();
    $puedeIniciarGestion = $puedeModificarSeries && now()->year >= 2027;
@endphp

<style>
    .sc-wrap { --sc-verde: #075c36; --sc-verde-oscuro: #173f32; --sc-borde: #e1eae5; }
    .sc-titulo { color: #173f32; font-weight: 750; letter-spacing: -.4px; }
    .sc-panel { background: #fff; border: 1px solid var(--sc-borde); border-radius: 13px; box-shadow: 0 5px 16px rgba(16, 64, 42, .06); }
    .sc-barra { padding: 13px 17px; background: #f8faf9; border: 1px solid var(--sc-borde); border-radius: 12px; }
    .sc-serie { border-left: 4px solid #13884b; height: 100%; padding: 20px; }
    .sc-icono { display: inline-flex; align-items: center; justify-content: center; width: 47px; height: 47px; border-radius: 12px; background: #e5f7eb; color: #08743d; font-size: 23px; flex-shrink: 0; }
    .sc-numero { color: #075c36; font-size: clamp(1.2rem, 2vw, 1.65rem); font-weight: 750; font-variant-numeric: tabular-nums; letter-spacing: .3px; }
    .sc-estado { background: #d6f4df; color: #145c32; font-size: .78rem; padding: 5px 9px; border-radius: 9px; font-weight: 700; }
    .sc-estado-pendiente { background: #fff0c7; color: #79520c; }
    .sc-tabla thead th { background: #075c36; color: white; font-weight: 600; padding: 13px; white-space: nowrap; }
    .sc-tabla tbody td { padding: 12px 13px; vertical-align: middle; }
    .sc-tabla tbody tr:last-child td { border-bottom: 0; }
    .sc-codigo { background: #d7f5df; color: #145c32; font-weight: 700; padding: 5px 8px; border-radius: 7px; white-space: nowrap; }
    .sc-info { background: #edf9f0; border: 1px solid #d5eedc; border-radius: 12px; padding: 18px; }
    .sc-modal-head { background: #173f32; color: white; }
    .sc-modal-head .btn-close { filter: invert(1) grayscale(100%); }
    .sc-small { font-size: .85rem; }
    .sc-btn { background: #087a40; border-color: #087a40; color: #fff; }
    .sc-btn:hover { background: #056534; border-color: #056534; color: #fff; }
    @media (max-width: 575.98px) { .sc-serie { padding: 15px; } .sc-numero { font-size: 1.2rem; } }
</style>

<div class="sc-wrap">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-layers fs-2 text-success" aria-hidden="true"></i>
            <div>
                <h1 class="h3 sc-titulo mb-1">Series correlativas</h1>
                <p class="text-muted mb-0">Administración de la numeración de vales por serie y gestión.</p>
            </div>
        </div>
        <span class="text-muted small"><i class="bi bi-house-door me-1"></i> Combustible / Series correlativas</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Revise los datos ingresados:</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="sc-barra d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <form method="GET" action="{{ route('series-correlativas.index') }}" class="d-flex align-items-center flex-wrap gap-2">
            <label for="anio" class="fw-semibold mb-0"><i class="bi bi-calendar3 me-1 text-success"></i> Gestión:</label>
            <input id="anio" name="anio" type="number" min="2026"  value="{{ $anio }}" class="form-control" style="width: 115px" required>
            <button class="btn btn-outline-success" type="submit">Ver</button>
        </form>
        <div class="d-flex flex-wrap gap-2">
           
            @if($puedeModificarSeries)
                <button class="btn sc-btn" type="button" data-bs-toggle="modal" data-bs-target="#iniciarAnioModal">
                    <i class="bi bi-plus-lg me-1"></i> Iniciar nueva gestión
                </button>
            @endif
        </div>
    </div>

    @if($puedeModificarSeries && now()->year >= 2027 && $seriesAnioActual < 3)
        <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-exclamation-triangle me-1"></i> La gestión {{ now()->year }} aún no tiene sus tres series configuradas.</span>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#iniciarAnioModal">Configurar gestión</button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @forelse($seriesOrdenadas as $serie)
            <div class="col-xl-4 col-md-6">
                <div class="sc-panel sc-serie">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="sc-icono"><i class="bi {{ $serie->codigo_serie === 'POL-GAS' ? 'bi-layers' : 'bi-fuel-pump-fill' }}" aria-hidden="true"></i></span>
                            <div>
                                <h2 class="h6 fw-bold mb-1">{{ $nombresSeries[$serie->codigo_serie] ?? $serie->codigo_serie }}</h2>
                                <div class="text-muted sc-small">{{ $subtitulosSeries[$serie->codigo_serie] ?? $serie->institucion }}</div>
                            </div>
                        </div>
                        <span class="sc-estado {{ $serie->estado === 'ACTIVA' ? '' : 'sc-estado-pendiente' }}">{{ $serie->estado === 'ACTIVA' ? 'Activa' : 'En configuración' }}</span>
                    </div>
                    <hr class="my-3">
                    <div class="row text-center g-2">
                        <div class="col-6 border-end">
                            <div class="text-muted sc-small mb-1">Último número</div>
                            <div class="sc-numero">{{ $serie->ultimo_formateado }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted sc-small mb-1">Siguiente número</div>
                            <div class="sc-numero">{{ $serie->proximo_formateado ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info mb-0">No hay series registradas para la gestión {{ $anio }}.</div></div>
        @endforelse
    </div>

    <div class="row g-3">
        <div class="col-xl-9">
            <div class="sc-panel overflow-hidden">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <h2 class="h5 fw-bold mb-0"><i class="bi bi-list-ul me-2 text-success"></i> Detalle de series</h2>
                    <span class="text-muted small">Gestión {{ $anio }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover sc-tabla mb-0">
                        <thead><tr><th>Serie</th><th>Descripción</th><th>Último número</th><th>Siguiente</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody>
                            @forelse($seriesOrdenadas as $serie)
                                <tr>
                                    <td><span class="sc-codigo">{{ $serie->codigo_serie }}</span></td>
                                    <td>{{ $nombresSeries[$serie->codigo_serie] ?? $serie->codigo_serie }}</td>
                                    <td class="fw-semibold text-nowrap">{{ $serie->ultimo_formateado }}</td>
                                    <td class="fw-semibold text-nowrap">{{ $serie->proximo_formateado ?? 'Límite alcanzado' }}</td>
                                    <td><span class="sc-estado {{ $serie->estado === 'ACTIVA' ? '' : 'sc-estado-pendiente' }}">{{ $serie->estado === 'ACTIVA' ? 'Activa' : 'En configuración' }}</span></td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#detalleSerie{{ $serie->id }}"><i class="bi bi-eye me-1"></i> Ver</button>
                                        @if($puedeModificarSeries && $serie->estado === 'EN_CONFIGURACION')
                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#configurarSerie{{ $serie->id }}"><i class="bi bi-gear me-1"></i> Configurar</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No hay series para esta gestión.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <aside class="sc-info h-100">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-info-circle-fill me-1 text-success"></i> Información</h2>
                <p class="small mb-2"><strong>GAMS:</strong> Gasolina y Diésel usan numeraciones independientes, sin depender de lotes.</p>
                <p class="small mb-2"><strong>Policía:</strong> Numeración por lotes y rangos autorizados.</p>
                <p class="small mb-0">Las nuevas series anuales conservan el historial de gestiones anteriores.</p>
            </aside>
        </div>
    </div>
</div>

{{-- Historial: navegación por gestión usando la ruta existente, sin agregar rutas nuevas. --}}
<div class="modal fade" id="historialSeriesModal" tabindex="-1" aria-labelledby="historialSeriesTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header sc-modal-head"><h2 class="modal-title fs-5" id="historialSeriesTitulo"><i class="bi bi-clock-history me-2"></i> Historial de gestiones</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <form method="GET" action="{{ route('series-correlativas.index') }}">
                <div class="modal-body">
                    <p class="text-muted">Seleccione una gestión para consultar las series registradas.</p>
                    <label class="form-label" for="anioHistorial">Año de gestión</label>
                    <input type="number" id="anioHistorial" name="anio" min="2026"  value="{{ $anio }}" class="form-control" required>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn sc-btn">Consultar</button></div>
            </form>
        </div>
    </div>
</div>

@foreach($seriesOrdenadas as $serie)
    <div class="modal fade" id="detalleSerie{{ $serie->id }}" tabindex="-1" aria-labelledby="detalleTitulo{{ $serie->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-header sc-modal-head"><h2 class="modal-title fs-5" id="detalleTitulo{{ $serie->id }}">{{ $nombresSeries[$serie->codigo_serie] ?? $serie->codigo_serie }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-6">Código</dt><dd class="col-6">{{ $serie->codigo_serie }}</dd>
                    <dt class="col-6">Gestión</dt><dd class="col-6">{{ $serie->anio }}</dd>
                    <dt class="col-6">Estado</dt><dd class="col-6">{{ $serie->estado }}</dd>
                    <dt class="col-6">Último número</dt><dd class="col-6 fw-bold">{{ $serie->ultimo_formateado }}</dd>
                    <dt class="col-6">Siguiente número</dt><dd class="col-6 fw-bold">{{ $serie->proximo_formateado ?? 'Límite alcanzado' }}</dd>
                </dl>
                @if($serie->estado === 'ACTIVA')
                    <div class="alert alert-success small mb-0 mt-2">Esta serie está activa. Su numeración avanza mediante las operaciones autorizadas.</div>
                @endif
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div></div>
    </div>

    @if($puedeModificarSeries && $serie->estado === 'EN_CONFIGURACION')
        <div class="modal fade" id="configurarSerie{{ $serie->id }}" tabindex="-1" aria-labelledby="configurarTitulo{{ $serie->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
                <div class="modal-header sc-modal-head"><h2 class="modal-title fs-5" id="configurarTitulo{{ $serie->id }}">Configurar {{ $serie->codigo_serie }} · {{ $serie->anio }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h3 class="h6 fw-bold">1. Configurar numeración</h3>
                            <form method="POST" action="{{ route('series-correlativas.update', $serie->codigo_serie) }}">
                                @csrf
                                <input type="hidden" name="anio" value="{{ $serie->anio }}">
                                <label class="form-label" for="primer-{{ $serie->id }}">Primer número que se emitirá</label>
                                <input id="primer-{{ $serie->id }}" name="primer_numero" type="text" inputmode="numeric" pattern="[0-9]{1,{{ $serie->codigo_serie === 'GAMS-DIE' ? 7 : 6 }}}" maxlength="{{ $serie->codigo_serie === 'GAMS-DIE' ? 7 : 6 }}" value="{{ old('primer_numero', $serie->proximo_formateado) }}" class="form-control mb-3" required>
                                <label class="form-label" for="motivo-config-{{ $serie->id }}">Motivo</label>
                                <textarea id="motivo-config-{{ $serie->id }}" name="motivo" rows="2" maxlength="1000" class="form-control mb-3" required>{{ old('motivo') }}</textarea>
                                <label class="form-label" for="confirm-config-{{ $serie->id }}">Escriba <strong>CONFIGURAR {{ $serie->codigo_serie }} {{ $serie->anio }}</strong></label>
                                <input id="confirm-config-{{ $serie->id }}" name="confirmacion" class="form-control mb-3" autocomplete="off" required>
                                <button class="btn btn-outline-success" type="submit">Guardar configuración</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h6 fw-bold">2. Activar serie</h3>
                            <form method="POST" action="{{ route('series-correlativas.activar', $serie->codigo_serie) }}">
                                @csrf
                                <input type="hidden" name="anio" value="{{ $serie->anio }}">
                                <div class="alert alert-warning small">Al activar la serie, no podrá modificarse manualmente su numeración durante esta gestión.</div>
                                <label class="form-label" for="motivo-activar-{{ $serie->id }}">Motivo de activación</label>
                                <textarea id="motivo-activar-{{ $serie->id }}" name="motivo" rows="2" maxlength="1000" class="form-control mb-3" required></textarea>
                                <label class="form-label" for="confirm-activar-{{ $serie->id }}">Escriba <strong>ACTIVAR {{ $serie->codigo_serie }} {{ $serie->anio }}</strong></label>
                                <input id="confirm-activar-{{ $serie->id }}" name="confirmacion" class="form-control mb-3" autocomplete="off" required>
                                <button class="btn sc-btn" type="submit">Activar serie</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button></div>
            </div></div>
        </div>
    @endif
@endforeach

@if($puedeModificarSeries)
<div class="modal fade" id="iniciarAnioModal" tabindex="-1" aria-labelledby="iniciarAnioTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form method="POST" action="{{ route('series-correlativas.iniciar-anio') }}" id="iniciarAnioForm">
            @csrf
            <div class="modal-header sc-modal-head"><h2 class="modal-title fs-5" id="iniciarAnioTitulo"><i class="bi bi-calendar-plus me-2"></i> Iniciar nueva gestión</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <div class="modal-body">
                @unless($puedeIniciarGestion)
                    <div class="alert alert-warning small">La gestión 2027 todavía no puede iniciarse: el sistema solo permite iniciar gestiones hasta el año actual.</div>
                @endunless
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nuevoAnio" class="form-label fw-semibold">Año de gestión <span class="text-danger">*</span></label>
                        <input id="nuevoAnio" name="anio" type="number" min="2027" max="{{ now()->year }}" class="form-control @error('anio') is-invalid @enderror" value="{{ old('anio', $anioInicioSugerido) }}" required>
                        @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 d-flex align-items-end"><div class="sc-info w-100 py-2 small"><i class="bi bi-info-circle-fill me-1 text-success"></i> Se crearán las tres series de la nueva gestión sin alterar las anteriores.</div></div>
                    <div class="col-md-4">
                        <label for="primerGamsGas" class="form-label fw-semibold">GAMS – Gasolina (6 dígitos)</label>
                        <input id="primerGamsGas" name="primer_gams_gas" type="text" inputmode="numeric" pattern="[0-9]{1,6}" maxlength="6" class="form-control @error('primer_gams_gas') is-invalid @enderror" value="{{ old('primer_gams_gas', '000001') }}" required>
                        @error('primer_gams_gas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="primerGamsDie" class="form-label fw-semibold">GAMS – Diésel (7 dígitos)</label>
                        <input id="primerGamsDie" name="primer_gams_die" type="text" inputmode="numeric" pattern="[0-9]{1,7}" maxlength="7" class="form-control @error('primer_gams_die') is-invalid @enderror" value="{{ old('primer_gams_die', '0000001') }}" required>
                        @error('primer_gams_die')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="primerPolGas" class="form-label fw-semibold">Policía – Gasolina (6 dígitos)</label>
                        <input id="primerPolGas" name="primer_pol_gas" type="text" inputmode="numeric" pattern="[0-9]{1,6}" maxlength="6" class="form-control @error('primer_pol_gas') is-invalid @enderror" value="{{ old('primer_pol_gas', '000001') }}" required>
                        @error('primer_pol_gas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="motivoInicioAnio" class="form-label fw-semibold">Motivo de la gestión <span class="text-danger">*</span></label>
                        <textarea id="motivoInicioAnio" name="motivo" rows="2" maxlength="1000" class="form-control @error('motivo') is-invalid @enderror" placeholder="Ej.: Inicio de gestión anual" required>{{ old('motivo') }}</textarea>
                        @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="confirmacionInicioAnio" class="form-label fw-semibold">Confirmación: escriba <strong id="textoConfirmacionInicioAnio">INICIAR {{ old('anio', $anioInicioSugerido) }}</strong></label>
                        <input id="confirmacionInicioAnio" name="confirmacion" type="text" class="form-control @error('confirmacion') is-invalid @enderror" value="{{ old('confirmacion') }}" autocomplete="off" required>
                        @error('confirmacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn sc-btn" @unless($puedeIniciarGestion) disabled title="Disponible desde 2027" @endunless><i class="bi bi-check2 me-1"></i> Iniciar gestión</button>
            </div>
        </form>
    </div></div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {
    const nuevoAnio = document.getElementById('nuevoAnio');
    const confirmacion = document.getElementById('textoConfirmacionInicioAnio');
    nuevoAnio?.addEventListener('input', () => {
        if (confirmacion) confirmacion.textContent = `INICIAR ${nuevoAnio.value || '____'}`;
    });
    @if($errors->has('anio') || $errors->has('primer_gams_gas') || $errors->has('primer_pol_gas') || $errors->has('primer_gams_die') || $errors->has('motivo') || $errors->has('confirmacion'))
        const modal = document.getElementById('iniciarAnioModal');
        if (modal && window.bootstrap?.Modal) window.bootstrap.Modal.getOrCreateInstance(modal).show();
    @endif
});
</script>
@endsection
