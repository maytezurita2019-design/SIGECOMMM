@extends('layouts.app')

@section('title', 'Editar rol')

@section('content')

<div class="mb-4">
    <h2>Editar rol</h2>
    <p class="text-muted">Modificar información del rol</p>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('roles.update', $rol) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label for="nombre" class="form-label">
                    Nombre *
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    class="form-control"
                    value="{{ old('nombre', $rol->nombre) }}"
                    required>

            </div>

            <div class="mb-3">

                <label for="descripcion" class="form-label">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    id="descripcion"
                    class="form-control"
                    rows="3">{{ old('descripcion', $rol->descripcion) }}</textarea>

            </div>

            <div class="mb-4">

                <label for="estado" class="form-label">
                    Estado
                </label>

                <select
                    name="estado"
                    id="estado"
                    class="form-select"
                    required>

                    <option
                        value="1"
                        {{ old('estado', $rol->estado) == 1 ? 'selected' : '' }}>
                        Activo
                    </option>

                    <option
                        value="0"
                        {{ old('estado', $rol->estado) == 0 ? 'selected' : '' }}>
                        Inactivo
                    </option>

                </select>

            </div>

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Actualizar
                </button>

                <a href="{{ route('roles.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

            </div>

        </form>

    </div>
</div>

@endsection