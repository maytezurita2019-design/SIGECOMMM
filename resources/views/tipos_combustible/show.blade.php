@extends('layouts.app')

@section('title', 'Detalle del combustible')

@section('content')

<h2>Detalle del tipo de combustible</h2>

<div class="card shadow-sm">
    <div class="card-body">

        <p><strong>ID:</strong> {{ $tipoCombustible->id }}</p>
        <hr>

        <p><strong>Nombre:</strong> {{ $tipoCombustible->nombre }}</p>
        <hr>

        <p>
            <strong>Unidad de medida:</strong>
            {{ $tipoCombustible->unidad_medida }}
        </p>
        <hr>

        <p>
            <strong>Estado:</strong>

            @if($tipoCombustible->estado)
                <span class="badge bg-success">Activo</span>
            @else
                <span class="badge bg-secondary">Inactivo</span>
            @endif
        </p>

        <a href="{{ route('tipos-combustible.edit', $tipoCombustible) }}"
           class="btn btn-warning">
            Editar
        </a>

        <a href="{{ route('tipos-combustible.index') }}"
           class="btn btn-secondary">
            Volver
        </a>

    </div>
</div>

@endsection