@extends('layouts.app')

@section('title', 'Detalle del rol')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2>Detalle del rol</h2>
        <p class="text-muted mb-0">
            Información registrada
        </p>
    </div>

    <a href="{{ route('roles.index') }}"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left me-1"></i>
        Volver

    </a>

</div>

<div class="card shadow-sm">

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-3">
                <strong>ID</strong>
            </div>

            <div class="col-md-9">
                {{ $rol->id }}
            </div>

        </div>

        <hr>

        <div class="row mb-3">

            <div class="col-md-3">
                <strong>Nombre</strong>
            </div>

            <div class="col-md-9">
                {{ $rol->nombre }}
            </div>

        </div>

        <hr>

        <div class="row mb-3">

            <div class="col-md-3">
                <strong>Descripción</strong>
            </div>

            <div class="col-md-9">
                {{ $rol->descripcion ?? 'Sin descripción' }}
            </div>

        </div>

        <hr>

        <div class="row mb-3">

            <div class="col-md-3">
                <strong>Estado</strong>
            </div>

            <div class="col-md-9">

                @if($rol->estado)

                    <span class="badge bg-success">
                        Activo
                    </span>

                @else

                    <span class="badge bg-secondary">
                        Inactivo
                    </span>

                @endif

            </div>

        </div>

        <hr>

        <a href="{{ route('roles.edit', $rol) }}"
           class="btn btn-warning">

            <i class="bi bi-pencil me-1"></i>
            Editar

        </a>

    </div>

</div>

@endsection