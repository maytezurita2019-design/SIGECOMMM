@extends('layouts.app')

@section('title', 'Detalle del usuario')

@section('content')

<div class="d-flex justify-content-between mb-4">
    <div>
        <h2>Detalle del usuario</h2>
        <p class="text-muted">Información registrada</p>
    </div>

    <a href="{{ route('usuarios.index') }}"
       class="btn btn-secondary">
        Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <p><strong>ID:</strong> {{ $usuario->id }}</p>

        <hr>

        <p>
            <strong>Nombre:</strong>
            {{ $usuario->nombre }} {{ $usuario->apellido }}
        </p>

        <hr>

        <p><strong>CI:</strong> {{ $usuario->ci }}</p>

        <hr>

        <p><strong>Correo:</strong> {{ $usuario->correo }}</p>

        <hr>

        <p>
            <strong>Rol:</strong>
            {{ $usuario->rol->nombre ?? 'Sin rol' }}
        </p>

        <hr>

        <p>
            <strong>Estado:</strong>

            @if($usuario->estado)
                <span class="badge bg-success">Activo</span>
            @else
                <span class="badge bg-secondary">Inactivo</span>
            @endif
        </p>

        <a href="{{ route('usuarios.edit', $usuario) }}"
           class="btn btn-warning">
            Editar
        </a>

    </div>
</div>

@endsection