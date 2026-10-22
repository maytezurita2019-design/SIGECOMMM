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
                        value="{{ old('anio') }}"
                        min="2000"
                        max="2100"
                        placeholder="Ejemplo: 2027"
                        required
                    >

                    <div class="form-text">
                        Se generarán automáticamente los 12 períodos
                        de enero a diciembre.
                    </div>

                </div>

            </div>

            <button type="submit" class="btn btn-primary">
                Generar períodos
            </button>

            <a href="{{ route('periodos.index') }}"
               class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@endsection