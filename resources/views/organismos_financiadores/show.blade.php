@extends('layouts.app')

@section('title', 'Detalle del organismo')

@section('content')

<h2>Detalle del organismo financiador</h2>

<div class="card shadow-sm">
    <div class="card-body">

        <p>
            <strong>Código:</strong>
            {{ $organismoFinanciador->codigo }}
        </p>

        <hr>

        <p>
            <strong>Nombre:</strong>
            {{ $organismoFinanciador->nombre }}
        </p>

        <hr>

        <p>
            <strong>Estado:</strong>

            @if($organismoFinanciador->estado)
                <span class="badge bg-success">Activo</span>
            @else
                <span class="badge bg-secondary">Inactivo</span>
            @endif
        </p>

        <a href="{{ route('organismos-financiadores.edit', $organismoFinanciador) }}"
           class="btn btn-warning">
            Editar
        </a>

        <a href="{{ route('organismos-financiadores.index') }}"
           class="btn btn-secondary">
            Volver
        </a>

    </div>
</div>

@endsection