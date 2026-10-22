@extends('layouts.app')

@section('title', 'Nueva apertura programática')

@section('content')

<h2>Nueva apertura programática</h2>

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

<form action="{{ route('aperturas-programaticas.store') }}" method="POST">
@csrf

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Código *</label>

    <input type="text"
           name="codigo"
           class="form-control"
           value="{{ old('codigo') }}"
           required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Estado *</label>

    <select name="estado" class="form-select">
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
    </select>
</div>

<div class="col-12 mb-3">
    <label class="form-label">
        Descripción
        <span class="text-muted">(opcional)</span>
    </label>

    <textarea name="descripcion"
              class="form-control"
              rows="3">{{ old('descripcion') }}</textarea>
</div>
<div class="col-md-6 mb-3">
    <label class="form-label">Fuente de financiamiento *</label>

    <select name="fuente_financiamiento_id"
            class="form-select"
            required>

        <option value="">Seleccione...</option>

        @foreach($fuentes as $fuente)
            <option value="{{ $fuente->id }}"
                {{ old('fuente_financiamiento_id') == $fuente->id ? 'selected' : '' }}>
                {{ $fuente->codigo }} - {{ $fuente->nombre }}
            </option>
        @endforeach

    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Organismo financiador *</label>

    <select name="organismo_financiador_id"
            class="form-select"
            required>

        <option value="">Seleccione...</option>

        @foreach($organismos as $organismo)
            <option value="{{ $organismo->id }}"
                {{ old('organismo_financiador_id') == $organismo->id ? 'selected' : '' }}>
                {{ $organismo->codigo }} - {{ $organismo->nombre }}
            </option>
        @endforeach

    </select>
</div>

</div>

<button class="btn btn-primary">Guardar</button>

<a href="{{ route('aperturas-programaticas.index') }}"
   class="btn btn-secondary">
Cancelar
</a>

</form>

</div>
</div>

@endsection