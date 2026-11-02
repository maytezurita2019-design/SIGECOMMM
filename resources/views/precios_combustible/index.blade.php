@extends('layouts.app')

@section('title', 'Precios de combustible')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-success">Precios de combustible</h2>
            <p class="text-muted mb-0">Historial y vigencia de precios por tipo de combustible.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($tipos as $tipo)
            @php($vigente = $vigentes[$tipo->id] ?? null)
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100" style="border-left: 5px solid #198754;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold text-success">{{ $tipo->nombre }}</h5>
                            @if($vigente)
                                <span class="badge bg-success rounded-pill">Vigente</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Sin precio vigente</span>
                            @endif
                        </div>

                        <div class="display-6 fw-bold text-dark mb-1">
                            @if($vigente)
                                Bs {{ number_format((float) $vigente->precio_unitario, 4, ',', '.') }}
                            @else
                                —
                            @endif
                        </div>

                        <div class="text-muted small">
                            @if($vigente)
                                Desde {{ $vigente->fecha_inicio->format('d/m/Y') }}
                            @else
                                No existe una vigencia activa para este combustible.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success-subtle border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-success fw-bold">Actualizar precio</h5>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('precios-combustible.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="tipo_combustible_id" class="form-label fw-semibold">Tipo de combustible</label>
                        <select name="tipo_combustible_id" id="tipo_combustible_id" class="form-select @error('tipo_combustible_id') is-invalid @enderror" required>
                            <option value="">Seleccione...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_combustible_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_combustible_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="precio_unitario" class="form-label fw-semibold">Nuevo precio unitario (Bs)</label>
                        <input type="number" step="0.0001" min="0" name="precio_unitario" id="precio_unitario" class="form-control @error('precio_unitario') is-invalid @enderror" value="{{ old('precio_unitario') }}" required>
                        @error('precio_unitario')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label fw-semibold">Fecha de inicio de vigencia</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', now()->format('Y-m-d')) }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="motivo" class="form-label fw-semibold">Motivo del cambio</label>
                        <input type="text" name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" value="{{ old('motivo') }}" placeholder="Opcional">
                        @error('motivo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i>Guardar precio
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0">
            <h5 class="mb-0 fw-bold text-dark">Historial de precios</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Combustible</th>
                            <th>Precio</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($precios as $precio)
                            <tr>
                                <td>{{ $precio->tipoCombustible?->nombre ?? '-' }}</td>
                                <td>Bs {{ number_format((float) $precio->precio_unitario, 4, ',', '.') }}</td>
                                <td>{{ $precio->fecha_inicio ? $precio->fecha_inicio->format('d/m/Y') : '-' }}</td>
                                <td>{{ $precio->fecha_fin ? $precio->fecha_fin->format('d/m/Y') : 'Vigente' }}</td>
                                <td>
                                    @if($precio->fecha_fin === null)
                                        <span class="badge bg-success">Vigente</span>
                                    @else
                                        <span class="badge bg-secondary">Anterior</span>
                                    @endif
                                </td>
                                <td>{{ $precio->motivo ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay precios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
