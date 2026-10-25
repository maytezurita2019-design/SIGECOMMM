@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')

<h2>Nuevo usuario</h2>
<p class="text-muted">Registrar usuario del sistema</p>

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

<form action="{{ route('usuarios.store') }}"
      method="POST"
      autocomplete="off">            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           value="{{ old('nombre') }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido *</label>
                    <input type="text"
                           name="apellido"
                           class="form-control"
                           value="{{ old('apellido') }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono *</label>
                    <input type="tel"
                           name="telefono"
                           class="form-control"
                           value="{{ old('telefono') }}"
                           maxlength="20"
                           placeholder="Ej.: 71234567"
                           required>
                </div>

                <div class="col-md-6 mb-3">
    <label class="form-label">Correo *</label>

    <input type="email"
           name="correo"
           class="form-control"
           value="{{ old('correo') }}"
           autocomplete="off"
           required>
</div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Rol *</label>

                    <select name="rol_id" class="form-select" required>
                        <option value="">Seleccione...</option>

                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}"
                                {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado *</label>

                    <select name="estado" class="form-select" required>
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

                <div class="col-md-6 mb-3">
    <label class="form-label">Contraseña *</label>

    <input type="password"
           name="contrasena"
           class="form-control"
           autocomplete="new-password"
           required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Confirmar contraseña *</label>

    <input type="password"
           name="contrasena_confirmation"
           class="form-control"
           autocomplete="new-password"
           required>
</div>

            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Guardar
            </button>

            <a href="{{ route('usuarios.index') }}"
               class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@endsection