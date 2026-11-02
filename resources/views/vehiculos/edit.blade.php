@extends('layouts.app')

@section('title', 'Editar vehículo')

@section('content')

<h2>Editar vehículo</h2>

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

<form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST">

@csrf
@method('PUT')

<div class="row">

    {{-- CÓDIGO --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Código *</label>

        <input
            type="text"
            name="codigo"
            class="form-control"
            data-uppercase
            pattern="[A-Za-z0-9._/\-]+"
            maxlength="30"
            value="{{ old('codigo', $vehiculo->codigo) }}"
            required
        >
    </div>

    {{-- PLACA --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Placa *</label>

        <input
            type="text"
            name="placa"
            class="form-control"
            data-uppercase
            pattern="[A-Za-z0-9 ./\-]+"
            maxlength="20"
            value="{{ old('placa', $vehiculo->placa) }}"
            required
        >
    </div>

    {{-- TIPO --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Tipo</label>

        <input
            type="text"
            name="tipo"
            class="form-control"
            value="{{ old('tipo', $vehiculo->tipo) }}"
        >
    </div>

    {{-- INDUSTRIA --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Industria</label>

        <input
            type="text"
            name="industria"
            class="form-control"
            value="{{ old('industria', $vehiculo->industria) }}"
        >
    </div>

    {{-- MARCA --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Marca</label>

        <input
            type="text"
            name="marca"
            class="form-control"
            value="{{ old('marca', $vehiculo->marca) }}"
        >
    </div>

    {{-- COLOR --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Color</label>

        <input
            type="text"
            name="color"
            class="form-control"
            value="{{ old('color', $vehiculo->color) }}"
        >
    </div>

    {{-- UNIDAD --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">
            Unidad *
        </label>

        <select
            name="unidad_id"
            class="form-select"
            required
        >

            @foreach($unidades as $unidad)

                <option
                    value="{{ $unidad->id }}"
                    @selected(
                        old(
                            'unidad_id',
                            $vehiculo->unidad_id
                        ) == $unidad->id
                    )
                >
                    {{ $unidad->nombre }}
                </option>

            @endforeach

        </select>
    </div>

    {{-- DETALLE DE ASIGNACIÓN --}}
    <div class="col-12 mb-3">
        <label for="detalle_asignacion_predeterminado" class="form-label">
            Detalle de asignación
        </label>

        <textarea
            name="detalle_asignacion_predeterminado"
            id="detalle_asignacion_predeterminado"
            class="form-control"
            rows="3"
        >{{ old('detalle_asignacion_predeterminado', $vehiculo->detalle_asignacion_predeterminado) }}</textarea>
    </div>

    {{-- APERTURA PROGRAMÁTICA --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Asignación presupuestaria *
        </label>

        <select
            name="apertura_programatica_id"
            class="form-select"
            required
        >

            <option value="">
                Seleccione...
            </option>

            @foreach($aperturas as $apertura)

                <option
                    value="{{ $apertura->id }}"
                    @selected(
                        old(
                            'apertura_programatica_id',
                            $vehiculo->apertura_programatica_id
                        ) == $apertura->id
                    )
                >

                    {{ $apertura->codigo }}

                    @if(!empty($apertura->descripcion))
                        - {{ $apertura->descripcion }}
                    @endif

                </option>

            @endforeach

        </select>

        @if($vehiculo->apertura_programatica_id)
            <small class="text-muted">
                Se muestra la asignación actualmente registrada.
                Cámbiela solo si corresponde.
            </small>
        @endif

    </div>

    {{-- ESTADO --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Estado *
        </label>

        <select
            name="estado"
            class="form-select"
            required
        >

            <option
                value="1"
                @selected(
                    old(
                        'estado',
                        $vehiculo->estado
                    ) == 1
                )
            >
                Activo
            </option>

            <option
                value="0"
                @selected(
                    old(
                        'estado',
                        $vehiculo->estado
                    ) == 0
                )
            >
                Inactivo
            </option>

        </select>

    </div>

</div>

<button
    type="submit"
    class="btn btn-primary"
>
    <i class="bi bi-save me-1" aria-hidden="true"></i>
    Actualizar
</button>

<a
    href="{{ route('vehiculos.index') }}"
    class="btn btn-secondary"
>
    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
    Cancelar
</a>

</form>

</div>
</div>

@endsection