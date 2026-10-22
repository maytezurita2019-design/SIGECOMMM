@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')

<h2>Editar usuario</h2>
<p class="text-muted">Modificar información del usuario</p>

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

        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre *</label>

                    <input type="text"
                           name="nombre"
                           class="form-control"
                           value="{{ old('nombre', $usuario->nombre) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellido *</label>

                    <input type="text"
                           name="apellido"
                           class="form-control"
                           value="{{ old('apellido', $usuario->apellido) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">CI *</label>

                    <input type="text"
                           name="ci"
                           class="form-control"
                           value="{{ old('ci', $usuario->ci) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Correo *</label>

                    <input type="email"
                           name="correo"
                           class="form-control"
                           value="{{ old('correo', $usuario->correo) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Rol *</label>

                    <select name="rol_id"
                            class="form-select"
                            required>

                        @foreach($roles as $rol)

                            <option value="{{ $rol->id }}"
                                {{ old('rol_id', $usuario->rol_id) == $rol->id ? 'selected' : '' }}>

                                {{ $rol->nombre }}

                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado *</label>

                    <select name="estado"
                            class="form-select"
                            required>

                        <option value="1"
                            {{ old('estado', $usuario->estado) == 1 ? 'selected' : '' }}>
                            Activo
                        </option>

                        <option value="0"
                            {{ old('estado', $usuario->estado) == 0 ? 'selected' : '' }}>
                            Inactivo
                        </option>

                    </select>
                </div>

            </div>

            <button type="submit"
                    class="btn btn-primary">
                Actualizar
            </button>

            <a href="{{ route('usuarios.index') }}"
               class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@endsection