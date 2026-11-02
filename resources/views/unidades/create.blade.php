@extends('layouts.app')

@section('title', 'Nueva unidad')

@section('content')

<h2>Nueva unidad</h2>

<div class="card shadow-sm">
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Existen errores en el formulario:</strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('unidades.store') }}"
              method="POST">

            @csrf

            <div class="row">

                {{-- NOMBRE --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="{{ old('nombre') }}"
                        maxlength="150"
                        required>

                </div>

                {{-- SIGLA --}}
                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Sigla *
                    </label>

                    <input
                        type="text"
                        name="sigla"
                        class="form-control"
                        value="{{ old('sigla') }}"
                        maxlength="20"
                        placeholder="Ej.: DMA"
                        required>

                </div>

                {{-- TIPO --}}
                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Tipo *
                    </label>

                    <input
                        type="text"
                        name="tipo"
                        class="form-control"
                        value="{{ old('tipo') }}"
                        maxlength="100"
                        placeholder="Ej.: DIRECCIÓN"
                        required>

                </div>

                {{-- DESCRIPCIÓN --}}
                <div class="col-12 mb-3">

                    <label class="form-label">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        class="form-control"
                        rows="3"
                        maxlength="500">{{ old('descripcion') }}</textarea>

                </div>

                {{-- ESTADO --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Estado *
                    </label>

                    <select
                        name="estado"
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

            </div>

            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-save me-1"></i>
                    Guardar

                </button>

                <a href="{{ route('unidades.index') }}"
                   class="btn btn-secondary">

                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                    Cancelar

                </a>

            </div>

        </form>

    </div>
</div>

@endsection