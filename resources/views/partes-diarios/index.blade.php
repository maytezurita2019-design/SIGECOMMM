
@extends('layouts.app')

@section('title', 'Partes Diarios')

@section('content')
<div class="container-fluid py-4">

    <h3 class="fw-bold mb-4">
        <i class="bi bi-journal-check me-2"></i>
        Registro de partes diarios por vehículo
    </h3>

    @foreach(['success' => 'success', 'error' => 'danger'] as $tipo => $color)
        @if(session($tipo))
            <div class="alert alert-{{ $color }} alert-dismissible fade show">
                {{ session($tipo) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revise los siguientes datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- BUSCADOR --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body position-relative">

            <input type="text" id="buscadorVehiculo"
                   class="form-control form-control-lg"
                   placeholder="Escriba código, placa o unidad..."
                   autocomplete="off"
                   value="{{ $vehiculo && !session('success') ? $vehiculo->codigo.' · '.($vehiculo->placa ?? 'SIN PLACA').' · '.($vehiculo->unidad?->nombre ?? 'SIN UNIDAD') : '' }}">

            <div id="listaVehiculos"
                 class="list-group position-absolute start-0 w-100 shadow d-none"
                 style="z-index:1050;max-height:320px;overflow-y:auto;">

                @foreach($vehiculos as $v)
                    <button type="button"
                            class="list-group-item list-group-item-action opcion-vehiculo"
                            data-id="{{ $v->id }}"
                            data-texto="{{ mb_strtoupper(($v->codigo ?? '').' '.($v->placa ?? '').' '.($v->unidad?->nombre ?? '')) }}">

                        <div class="d-flex justify-content-between">
                            <div>
                                <strong class="text-success">{{ $v->codigo }}</strong>
                                <span class="ms-2">{{ $v->placa }}</span>
                                <div class="small text-muted">
                                    {{ $v->unidad?->nombre ?? 'Sin unidad' }}
                                </div>
                            </div>
                            <i class="bi bi-car-front"></i>
                        </div>
                    </button>
                @endforeach

            </div>
        </div>
    </div>

    {{-- REGISTRAR PARTE --}}
@if($vehiculo && request()->filled('vehiculo_id') && !session('success'))
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-light text-success fw-bold">
                <i class="bi bi-car-front-fill me-2"></i>
                Vehículo encontrado
            </div>

            <div class="card-body">

                <div class="row g-3 mb-4">
                    @foreach([
                        'Código' => $vehiculo->codigo,
                        'Placa' => $vehiculo->placa,
                        'Tipo de vehículo' => $vehiculo->tipo ?? $vehiculo->tipo_vehiculo ?? null,
                        'Unidad' => $vehiculo->unidad?->nombre
                    ] as $etiqueta => $valor)
                        <div class="col-md-3">
                            <small class="text-muted d-block">{{ $etiqueta }}</small>
                            <strong>{{ $valor ?? '-' }}</strong>
                        </div>
                    @endforeach
                </div>

                <hr>
                <h5 class="fw-bold mb-3">Registrar Parte Diario</h5>

                <form method="POST" class="form-parte-diario"
                      action="{{ route('partes-diarios.store') }}">
                    @csrf

                    <input type="hidden" name="vehiculo_id" value="{{ $vehiculo->id }}">

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha del parte</label>
                            <input type="date" name="fecha" class="form-control"
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">¿Presentó Parte Diario?</label>
                            <select name="presentado" class="form-select campo-presentado" required>
                                <option value="">Seleccione</option>
                                <option value="1" @selected(old('presentado') === '1')>✓ SÍ</option>
                                <option value="0" @selected(old('presentado') === '0')>✕ NO</option>
                            </select>
                        </div>

                        <div class="col-md-4 bloque-odometro d-none">
                            <label class="form-label fw-semibold">¿Funciona el odómetro?</label>
                            <select name="odometro_funciona" class="form-select campo-odometro" disabled>
                                <option value="">Seleccione</option>
                                <option value="1" @selected(old('odometro_funciona') === '1')>Sí, funciona</option>
                                <option value="0" @selected(old('odometro_funciona') === '0')>No funciona / Dañado</option>
                            </select>
                        </div>

                        <div class="col-12 bloque-kilometraje d-none">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">KM inicial</label>
                                    <input type="number" name="km_inicial" min="0" step="1"
                                           class="form-control campo-km-inicial"
                                           value="{{ old('km_inicial') }}"
                                           placeholder="Ej. 45200">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">KM final</label>
                                    <input type="number" name="km_final" min="0" step="1"
                                           class="form-control campo-km-final"
                                           value="{{ old('km_final') }}"
                                           placeholder="Ej. 45285">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">KM recorridos (automático)</label>
                                    <input type="text" class="form-control campo-km-recorridos bg-light"
                                           value="—" readonly>
                                </div>

                                <div class="col-12 text-danger small aviso-kilometraje d-none">
                                    El kilometraje final no puede ser menor al inicial.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Observación (opcional)</label>
                            <input type="text" name="observacion" class="form-control"
                                   value="{{ old('observacion') }}"
                                   maxlength="1000" placeholder="Opcional">
                        </div>

                    </div>

                    <button type="submit" class="btn btn-success px-4 mt-4">
                        <i class="bi bi-check-circle me-1"></i>
                        Registrar Parte Diario
                    </button>
                </form>

            </div>
        </div>
    @endif

    {{-- HISTORIAL --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Código</th>
                            <th>Placa</th>
                            <th>Unidad</th>
                            <th class="text-center">Parte Diario</th>
                            <th>Odómetro</th>
                            <th class="text-end">KM inicial</th>
                            <th class="text-end">KM final</th>
                            <th class="text-end">Recorridos</th>
                            <th>Observación</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($partes as $parte)
                            <tr>
                                <td>{{ $parte->fecha?->format('d/m/Y') }}</td>
                                <td>{{ $parte->vehiculo?->codigo ?? '-' }}</td>
                                <td><strong>{{ $parte->vehiculo?->placa ?? '-' }}</strong></td>
                                <td>{{ $parte->vehiculo?->unidad?->nombre ?? '-' }}</td>

                                <td class="text-center">
                                    <span class="badge bg-{{ $parte->presentado ? 'success' : 'danger' }}">
                                        {{ $parte->presentado ? 'SÍ' : 'NO' }}
                                    </span>
                                </td>

                                <td>
                                    @if(!$parte->presentado)
                                        <span class="text-muted">No aplica</span>
                                    @elseif($parte->odometro_funciona === true)
                                        <span class="badge bg-success">Funciona</span>
                                    @elseif($parte->odometro_funciona === false)
                                        <span class="badge bg-warning text-dark">No funciona</span>
                                    @else
                                        <span class="text-muted">Sin información</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    {{ $parte->km_inicial !== null ? number_format($parte->km_inicial, 0, ',', '.') : '—' }}
                                </td>

                                <td class="text-end">
                                    {{ $parte->km_final !== null ? number_format($parte->km_final, 0, ',', '.') : '—' }}
                                </td>

                                <td class="text-end fw-semibold">
                                    {{ $parte->km_recorridos !== null ? number_format($parte->km_recorridos, 0, ',', '.').' km' : '—' }}
                                </td>

                                <td>{{ $parte->observacion ?: '-' }}</td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarParte{{ $parte->id }}">
                                        <i class="bi bi-pencil-square me-1"></i>Editar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    No existen partes diarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($partes->hasPages())
                <div class="mt-3">{{ $partes->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>

    {{-- MODALES DE EDICIÓN --}}
    @foreach($partes as $parte)
        <div class="modal fade" id="editarParte{{ $parte->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="POST" class="form-parte-diario"
                          action="{{ route('partes-diarios.update', $parte) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title">Editar Parte Diario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label">Vehículo</label>
                                <input type="text" class="form-control"
                                       value="{{ $parte->vehiculo?->placa }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control"
                                       value="{{ $parte->fecha?->format('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">¿Presentó Parte Diario?</label>
                                <select name="presentado" class="form-select campo-presentado" required>
                                    <option value="1" @selected($parte->presentado)>✓ SÍ</option>
                                    <option value="0" @selected(!$parte->presentado)>✕ NO</option>
                                </select>
                            </div>

                            <div class="mb-3 bloque-odometro d-none">
                                <label class="form-label">¿Funciona el odómetro?</label>
                                <select name="odometro_funciona" class="form-select campo-odometro" disabled>
                                    <option value="">Seleccione</option>
                                    <option value="1" @selected($parte->odometro_funciona === true)>Sí, funciona</option>
                                    <option value="0" @selected($parte->odometro_funciona === false)>No funciona / Dañado</option>
                                </select>
                            </div>

                            <div class="bloque-kilometraje d-none">
                                <div class="row g-3 mb-3">

                                    <div class="col-sm-6">
                                        <label class="form-label">KM inicial</label>
                                        <input type="number" name="km_inicial" min="0" step="1"
                                               class="form-control campo-km-inicial"
                                               value="{{ $parte->km_inicial }}">
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label">KM final</label>
                                        <input type="number" name="km_final" min="0" step="1"
                                               class="form-control campo-km-final"
                                               value="{{ $parte->km_final }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">KM recorridos (automático)</label>
                                        <input type="text" class="form-control campo-km-recorridos bg-light"
                                               value="—" readonly>

                                        <div class="text-danger small aviso-kilometraje d-none">
                                            El kilometraje final no puede ser menor al inicial.
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Observación (opcional)</label>
                                <textarea name="observacion" class="form-control"
                                          rows="3" maxlength="1000"
                                          placeholder="Opcional">{{ $parte->observacion }}</textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal"><i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancelar</button>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i>Guardar cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endforeach

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {


    // Una misma lógica para registrar y editar.
    document.querySelectorAll('.form-parte-diario').forEach(form => {
        const $ = selector => form.querySelector(selector);

        const presentado = $('.campo-presentado');
        const odometro = $('.campo-odometro');
        const inicial = $('.campo-km-inicial');
        const final = $('.campo-km-final');
        const recorridos = $('.campo-km-recorridos');

        function actualizar() {
            const entrego = presentado.value === '1';
            const funciona = entrego && odometro.value === '1';

            $('.bloque-odometro').classList.toggle('d-none', !entrego);
            $('.bloque-kilometraje').classList.toggle('d-none', !funciona);

            odometro.disabled = !entrego;
            odometro.required = entrego;

            [inicial, final].forEach(campo => {
                campo.disabled = !funciona;
                campo.required = funciona;
            });

            const completo = funciona && inicial.value !== '' && final.value !== '';
            const invalido = completo && Number(final.value) < Number(inicial.value);

            recorridos.value = completo && !invalido
                ? (Number(final.value) - Number(inicial.value)).toLocaleString('es-BO') + ' km'
                : '—';

            $('.aviso-kilometraje').classList.toggle('d-none', !invalido);

            final.setCustomValidity(invalido
                ? 'El kilometraje final no puede ser menor al inicial.'
                : '');
        }

        [presentado, odometro, inicial, final].forEach(campo => {
            campo.addEventListener('input', actualizar);
            campo.addEventListener('change', actualizar);
        });

        actualizar();
    });

    // Si el registro fue exitoso, limpiar cualquier selección persistente en la URL.
    @if(session('success'))
    const urlLimpia = new URL(window.location.href);
    if (urlLimpia.searchParams.has('vehiculo_id')) {
        urlLimpia.searchParams.delete('vehiculo_id');
        window.history.replaceState({}, '', urlLimpia.toString());
    }
    @endif

    // Buscador por código, placa o unidad.
    const buscador = document.getElementById('buscadorVehiculo');
    const lista = document.getElementById('listaVehiculos');

    if (!buscador || !lista) return;

    const opciones = lista.querySelectorAll('.opcion-vehiculo');

    const normalizar = texto => (texto || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toUpperCase()
        .trim();

    buscador.addEventListener('input', () => {
        const texto = normalizar(buscador.value);
        let encontrados = 0;

        opciones.forEach(opcion => {
            const coincide = texto !== '' &&
                normalizar(opcion.dataset.texto).includes(texto);

            opcion.classList.toggle('d-none', !coincide);
            if (coincide) encontrados++;
        });

        lista.classList.toggle('d-none', encontrados === 0);
    });

    opciones.forEach(opcion => {
        opcion.addEventListener('click', () => {
            const url = new URL(window.location.href);
            url.search = '';
            url.searchParams.set('vehiculo_id', opcion.dataset.id);
            window.location.href = url.toString();
        });
    });

    document.addEventListener('click', evento => {
        if (!buscador.contains(evento.target) && !lista.contains(evento.target)) {
            lista.classList.add('d-none');
        }
    });

});
</script>
@endsection
