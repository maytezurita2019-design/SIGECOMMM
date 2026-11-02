
@extends('layouts.app')

@section('title', 'Nueva gestión')

@section('content')

<h2>Nueva gestión</h2>

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

        <form action="{{ route('periodos.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label for="anio" class="form-label">
                        Año de la gestión *
                    </label>

                    <input
                        type="number"
                        name="anio"
                        id="anio"
                        class="form-control"
                        value="{{ old('anio', now()->year) }}"
                        min="2000"
                        max="{{ now()->year }}"
                        placeholder="Ejemplo: {{ now()->year }}"
                        required
                    >

                    <div class="form-text">
                        Se generarán automáticamente los 12 períodos
                        de enero a diciembre de la gestión seleccionada.
                        No se permite registrar años futuros.
                    </div>

                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-calendar-plus me-1" aria-hidden="true"></i>
                Generar períodos
            </button>

            <a href="{{ route('periodos.index') }}"
               class="btn btn-secondary">
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                Cancelar
            </a>

        </form>

    </div>
</div>

@endsection
