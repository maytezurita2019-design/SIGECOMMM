@extends('layouts.app')

@section('title', 'Nuevo rol')

@section('content')

<div class="mb-4">
    <h2>Nuevo rol</h2>
    <p class="text-muted">Registrar un nuevo rol en el sistema</p>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Existen errores en el formulario:</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('roles.store') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label">
                    Nombre *
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    class="form-control"
                    value="{{ old('nombre') }}"
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
                    rows="3">{{ old('descripcion') }}</textarea>
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

                    <option value="1"
                        {{ old('estado', '1') == '1' ? 'selected' : '' }}>
                        Activo
                    </option>

                    <option value="0"
                        {{ old('estado') == '0' ? 'selected' : '' }}>
                        Inactivo
                    </option>

                </select>
            </div>

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>
                    Guardar
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