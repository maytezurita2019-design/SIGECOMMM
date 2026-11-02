
@extends('layouts.app')

@section('title', 'Lotes de vales')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
        </button>
    </div>
@endif

<div class="d-flex justify-content-between mb-3">

    <h2>Lotes de vales por período</h2>

    <a class="btn btn-primary"
       href="{{ route('lotes-vales.create') }}">
        <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>
        Nuevo lote
    </a>

</div>

<a class="btn btn-primary mb-3"
   href="{{ route('entregas-policia.create') }}">
    <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>
    Nueva entrega a Policía
</a>

<div class="card shadow-sm">

    <div class="card-body table-responsive">

        <form method="GET" action="{{ route('lotes-vales.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="periodo_id" class="form-label">Período</label>
                    <select name="periodo_id" id="periodo_id" class="form-select @error('periodo_id') is-invalid @enderror">
                        <option value="">Todos los períodos</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}" @selected((string) request('periodo_id') === (string) $periodo->id)>
                                {{ $periodo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('periodo_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label for="estado_lote" class="form-label">Estado</label>
                    <select name="estado_lote" id="estado_lote" class="form-select @error('estado_lote') is-invalid @enderror">
                        <option value="">Todos</option>
                        <option value="disponible" @selected(request('estado_lote') === 'disponible')>Disponible</option>
                        <option value="entregado" @selected(request('estado_lote') === 'entregado')>Entregado</option>
                    </select>
                    @error('estado_lote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-8 col-lg-4">
                    <label for="numero_vale" class="form-label">Número de vale o rango</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-success">
                            <i class="bi bi-search"></i>
                        </span>
                        <input
                            type="text"
                            name="numero_vale"
                            id="numero_vale"
                            class="form-control @error('numero_vale') is-invalid @enderror"
                            value="{{ request('numero_vale') }}"
                            placeholder="Ej. 000301 o 000301-000302"
                            inputmode="numeric"
                        >
                        @error('numero_vale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1"></i>Buscar
                    </button>
                    <a href="{{ route('lotes-vales.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>
                        Limpiar filtros
                    </a>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th>Período</th>
                    <th>Rango</th>
                    <th>Recepción</th>
                    <th>Creado por</th>
                    <th>Entregado por</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($lotes as $loteVale)

                    @php
                        $entrega = $entregasPorLote->get($loteVale->id);
                        $entregado = $entrega !== null;
                    @endphp

                    <tr>

                        {{-- PERÍODO --}}
                        <td>
                            {{ $loteVale->periodo?->nombre ?? 'Sin período' }}
                        </td>

                        {{-- RANGO --}}
                        <td>
                            {{ sprintf('%06d', $loteVale->numero_inicial) }}
                            –
                            {{ sprintf('%06d', $loteVale->numero_final) }}
                        </td>

                        {{-- RECEPCIÓN --}}
                        <td>
                            {{ $loteVale->fecha_recepcion->format('d/m/Y') }}
                        </td>

                        {{-- CREADO POR --}}
                        <td>
                            @if($loteVale->usuario)
                                <span class="text-success fw-semibold">
                                    <i class="bi bi-person-check me-1"></i>
                                    {{ $loteVale->usuario->nombre }}
                                </span>
                            @else
                                <span class="text-muted">
                                    No registrado
                                </span>
                            @endif
                        </td>

                        {{-- ENTREGADO POR --}}
                        <td>
                            @if($entregado)
                                @if($entrega->usuario_nombre)
                                    <span class="text-success fw-semibold">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $entrega->usuario_nombre }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        Usuario no disponible
                                    </span>
                                @endif
                            @else
                                <span class="text-muted">
                                    Pendiente de entrega
                                </span>
                            @endif
                        </td>


{{-- CANTIDAD TOTAL DE VALES DEL LOTE --}}
<td>
    <span class="fw-semibold">
        {{ $loteVale->numero_final - $loteVale->numero_inicial + 1 }}
    </span>
</td>


                        {{-- ESTADO --}}
                        <td>
                            @if($entregado)

                                <span class="badge bg-primary">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Entregado
                                </span>

                            @else

                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Disponible
                                </span>

                            @endif
                        </td>

                        {{-- ACCIONES --}}
                        <td>

                            <div class="d-flex gap-1">

                                {{-- VER --}}
                                <a
                                    href="{{ route('lotes-vales.show', $loteVale) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver lote"
                                    aria-label="Ver lote"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            
                                {{-- EDITAR --}}
                                @if($entregado)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        title="No se puede editar porque este lote ya fue entregado a la Policía"
                                        aria-label="Edición bloqueada: lote entregado a Policía"
                                        disabled
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                @else
                                    <a
                                        href="{{ route('lotes-vales.edit', $loteVale) }}"
                                        class="btn btn-sm btn-warning text-dark"
                                        title="Editar lote"
                                        aria-label="Editar lote"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif

                                {{-- ELIMINAR --}}
                                @if($entregado)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        title="No se puede eliminar porque este lote ya fue entregado a la Policía"
                                        aria-label="Eliminación bloqueada: lote entregado a Policía"
                                        disabled
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('lotes-vales.destroy', $loteVale) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('¿Está seguro de eliminar este lote?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                            title="Eliminar lote"
                                            aria-label="Eliminar lote"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No se encontraron registros.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection
