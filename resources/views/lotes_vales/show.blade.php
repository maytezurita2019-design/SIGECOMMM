@extends('layouts.app')

@section('title', 'Detalle del lote')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Detalle del lote</h2>

        <p class="text-muted mb-0">
            {{ sprintf('%06d', $loteVale->numero_inicial) }}
            –
            {{ sprintf('%06d', $loteVale->numero_final) }}
        </p>
    </div>

    <a
        href="{{ route('lotes-vales.index') }}"
        class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Volver
    </a>
</div>


{{-- INFORMACIÓN DEL LOTE --}}
<div class="card shadow-sm mb-3">

    <div class="card-header bg-light">
        <strong>
            <i class="bi bi-info-circle me-1"></i>
            Información del lote
        </strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-3">
                <small class="text-muted d-block">Período</small>
                <strong>{{ $loteVale->periodo->nombre }}</strong>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Fecha de recepción</small>
                <strong>
                    {{ $loteVale->fecha_recepcion->format('d/m/Y') }}
                </strong>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Número inicial</small>
                <strong>
                    {{ sprintf('%06d', $loteVale->numero_inicial) }}
                </strong>
            </div>

            <div class="col-md-3">
                <small class="text-muted d-block">Número final</small>
                <strong>
                    {{ sprintf('%06d', $loteVale->numero_final) }}
                </strong>
            </div>

        </div>

    </div>
</div>


{{-- RESUMEN --}}
<div class="card shadow-sm mb-3">

    <div class="card-header bg-light">
        <strong>
            <i class="bi bi-card-checklist me-1"></i>
            Resumen de vales
        </strong>
    </div>

    <div class="card-body">

        <div class="row text-center g-3">

            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted mb-1">
                        Total de vales
                    </div>

                    <h3 class="mb-0">
                        {{ $total }}
                    </h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted mb-1">
                        Disponibles
                    </div>

                    <h3 class="mb-0">
                        {{ $disponibles }}
                    </h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted mb-1">
                        Emitidos / asignados
                    </div>

                    <h3 class="mb-0">
                        {{ $cantidadVales }}
                    </h3>
                </div>
            </div>

        </div>

    </div>
</div>


{{-- DESTINO --}}
<div class="card shadow-sm mb-3">

    <div class="card-header bg-light">
        <strong>
            <i class="bi bi-arrow-left-right me-1"></i>
            Destino del lote
        </strong>
    </div>

    <div class="card-body">

        @if($entregaPolicia)

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-2">
                        <i class="bi bi-shield-check me-1"></i>
                        Policía
                    </h5>

                    <p class="mb-1">
                        Este lote tiene una asignación registrada a Policía.
                    </p>

                    <p class="text-muted mb-0">
                        Rango:
                        <strong>
                            {{ sprintf('%06d', $loteVale->numero_inicial) }}
                            –
                            {{ sprintf('%06d', $loteVale->numero_final) }}
                        </strong>
                    </p>
                </div>

                <a
                    href="{{ route('entregas-policia.show', $entregaPolicia->id) }}"
                    class="btn btn-outline-primary">

                    <i class="bi bi-eye me-1"></i>
                    Ver entrega

                </a>

            </div>

        @else

            <div class="text-center py-3">

                <i class="bi bi-inbox fs-2 text-muted"></i>

                <p class="text-muted mb-3">
                    Este lote todavía no tiene una entrega a Policía registrada.
                </p>

                <a
                    href="{{ route('entregas-policia.create', ['lote_vale_id' => $loteVale->id]) }}"
                    class="btn btn-success">

                    <i class="bi bi-shield-plus me-1"></i>
                    Nueva entrega a Policía

                </a>

            </div>

        @endif

    </div>
</div>


{{-- ACCIONES --}}
<div class="card shadow-sm">

    <div class="card-body d-flex gap-2 flex-wrap">

        <a
            href="{{ route('lotes-vales.edit', $loteVale) }}"
            class="btn btn-warning">

            <i class="bi bi-pencil me-1"></i>
            Editar lote

        </a>

        <form
            method="POST"
            action="{{ route('lotes-vales.destroy', $loteVale) }}">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="btn btn-outline-danger"
                onclick="return confirm('¿Está seguro de eliminar este lote? También se eliminarán sus registros relacionados.')">

                <i class="bi bi-trash me-1"></i>
                Eliminar lote

            </button>

        </form>

        <a
            href="{{ route('lotes-vales.index') }}"
            class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Volver

        </a>

    </div>

</div>

@endsection
