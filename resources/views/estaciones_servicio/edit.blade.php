@extends('layouts.app')

@section('title', 'Editar estación')

@section('content')

<h2>Editar estación de servicio</h2>

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

        <form action="{{ route('estaciones-servicio.update', $estacionServicio) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="row">

                <!-- NOMBRE -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="{{ old('nombre', $estacionServicio->nombre) }}"
                        required>

                </div>


                <!-- DIRECCIÓN -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        class="form-control"
                        value="{{ old('direccion', $estacionServicio->direccion) }}">

                </div>


                <!-- TELÉFONO -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Teléfono
                    </label>

                    <input
                        type="text"
                        name="telefono"
                        class="form-control"
                        value="{{ old('telefono', $estacionServicio->telefono) }}">

                </div>


                <!-- ESTADO -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Estado
                    </label>

                    <select name="estado" class="form-select">

                        <option value="1"
                            {{ old('estado', $estacionServicio->estado) == 1 ? 'selected' : '' }}>
                            Activo
                        </option>

                        <option value="0"
                            {{ old('estado', $estacionServicio->estado) == 0 ? 'selected' : '' }}>
                            Inactivo
                        </option>

                    </select>

                </div>

            </div>


            <button type="submit" class="btn btn-primary">
                Actualizar
            </button>

            <a href="{{ route('estaciones-servicio.index') }}"
               class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@endsection