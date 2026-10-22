@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-key-fill me-2"></i>
                Restablecer contraseña
            </h3>

            <p class="text-muted mb-0">
                Asigne una nueva contraseña al usuario.
            </p>
        </div>

        <a href="{{ route('usuarios.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Volver
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="alert alert-light border mb-4">
                <strong>Usuario:</strong>
                {{ $usuario->nombre }} {{ $usuario->apellido }}

                <br>

                <strong>Correo:</strong>
                {{ $usuario->correo }}
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Revise los siguientes datos:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('usuarios.actualizar-contrasena', $usuario) }}"
                method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="contrasena" class="form-label">
                        Nueva contraseña
                    </label>

                    <input
                        type="password"
                        name="contrasena"
                        id="contrasena"
                        class="form-control"
                        minlength="8"
                        required>

                    <small class="text-muted">
                        Debe contener al menos 8 caracteres.
                    </small>
                </div>

                <div class="mb-4">
                    <label for="contrasena_confirmation" class="form-label">
                        Confirmar nueva contraseña
                    </label>

                    <input
                        type="password"
                        name="contrasena_confirmation"
                        id="contrasena_confirmation"
                        class="form-control"
                        minlength="8"
                        required>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('usuarios.index') }}"
                       class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bi bi-key-fill me-1"></i>
                        Restablecer contraseña
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection