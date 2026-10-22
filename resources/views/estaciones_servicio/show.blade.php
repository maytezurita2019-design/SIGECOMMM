@extends('layouts.app')

@section('title', 'Detalle de estación')

@section('content')

<h2>Detalle de estación de servicio</h2>

<div class="card shadow-sm">
    <div class="card-body">

        <p>
            <strong>Nombre:</strong>
            {{ $estacionServicio->nombre }}
        </p>

        <hr>

        <p>
            <strong>Dirección:</strong>
            {{ $estacionServicio->direccion ?? '-' }}
        </p>

        <hr>

        <p>
            <strong>Teléfono:</strong>
            {{ $estacionServicio->telefono ?? '-' }}
        </p>

        <hr>

        <p>
            <strong>Estado:</strong>

            @if($estacionServicio->estado)
                <span class="badge bg-success">
                    Activo
                </span>
            @else
                <span class="badge bg-secondary">
                    Inactivo
                </span>
            @endif
        </p>

        <a href="{{ route('estaciones-servicio.edit', $estacionServicio) }}"
           class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i>
            Editar
        </a>

        <a href="{{ route('estaciones-servicio.index') }}"
           class="btn btn-secondary">
            Volver
        </a>

    </div>
</div>

@endsection