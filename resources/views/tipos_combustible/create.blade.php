@extends('layouts.app')

@section('title', 'Nuevo tipo de combustible')

@section('content')

<h2>Nuevo tipo de combustible</h2>
<p class="text-muted">Registrar un combustible</p>

<div class="card shadow-sm">
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tipos-combustible.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nombre *</label>

                <input type="text"
                       name="nombre"
                       class="form-control"
                       value="{{ old('nombre') }}"
                       placeholder="Ej.: Gasolina"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Unidad de medida *</label>

                <input type="text"
                       name="unidad_medida"
                       class="form-control"
                       value="{{ old('unidad_medida', 'Litros') }}"
                       required>
            </div>

            <div class="mb-4">
                <label class="form-label">Estado *</label>

                <select name="estado" class="form-select" required>
                    <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>
                        Activo
                    </option>

                    <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>
                        Inactivo
                    </option>
                </select>
            </div>

            <button class="btn btn-primary">
                <i class="bi bi-save me-1"></i>
                Guardar
            </button>

            <a href="{{ route('tipos-combustible.index') }}"
               class="btn btn-secondary">
                Cancelar
            </a>
        </form>

    </div>
</div>

@endsection