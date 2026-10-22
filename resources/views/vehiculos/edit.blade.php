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

    <div class="col-md-6 mb-3">
        <label class="form-label">Código *</label>
        <input type="text"
               name="codigo"
               class="form-control" data-uppercase pattern="[A-Za-z0-9._/\-]+" maxlength="30"
               value="{{ old('codigo', $vehiculo->codigo) }}"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Placa *</label>
        <input type="text"
               name="placa"
               class="form-control" data-uppercase pattern="[A-Za-z0-9 ./\-]+" maxlength="20"
               value="{{ old('placa', $vehiculo->placa) }}"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Tipo</label>
        <input type="text"
               name="tipo"
               class="form-control"
               value="{{ old('tipo', $vehiculo->tipo) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Industria</label>
        <input type="text"
               name="industria"
               class="form-control"
               value="{{ old('industria', $vehiculo->industria) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Marca</label>
        <input type="text"
               name="marca"
               class="form-control"
               value="{{ old('marca', $vehiculo->marca) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Color</label>
        <input type="text"
               name="color"
               class="form-control"
               value="{{ old('color', $vehiculo->color) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Unidad *</label>

        <select name="unidad_id" class="form-select" required>
            @foreach($unidades as $unidad)
                <option value="{{ $unidad->id }}"
                    {{ old('unidad_id', $vehiculo->unidad_id) == $unidad->id ? 'selected' : '' }}>
                    {{ $unidad->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Estado *</label>

        <select name="estado" class="form-select">

            <option value="1"
                {{ old('estado', $vehiculo->estado) == 1 ? 'selected' : '' }}>
                Activo
            </option>

            <option value="0"
                {{ old('estado', $vehiculo->estado) == 0 ? 'selected' : '' }}>
                Inactivo
            </option>

        </select>
    </div>

</div>

<button class="btn btn-primary">Actualizar</button>

<a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@endsection