@extends('layouts.app')

@section('title', 'Editar periodo')

@section('content')

<h2>Editar periodo</h2>

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

<form action="{{ route('periodos.update', $periodo) }}" method="POST">

@csrf
@method('PUT')

<div class="row">


<div class="col-md-6 mb-3">
<label class="form-label">Mes</label>

<select name="mes" class="form-select">

@for($mes = 1; $mes <= 12; $mes++)

<option value="{{ $mes }}"
{{ old('mes', $periodo->mes) == $mes ? 'selected' : '' }}>
{{ $mes }}
</option>

@endfor

</select>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Año</label>

<input type="number"
       name="anio"
       class="form-control"
       value="{{ old('anio', $periodo->anio) }}"
       required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Fecha inicio</label>

<input type="date"
       name="fecha_inicio"
       class="form-control"
       value="{{ old('fecha_inicio', $periodo->fecha_inicio?->format('Y-m-d')) }}"
       required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Fecha fin</label>

<input type="date"
       name="fecha_fin"
       class="form-control"
       value="{{ old('fecha_fin', $periodo->fecha_fin?->format('Y-m-d')) }}"
       required>
</div>

<div class="col-md-6 mb-3">

<label class="form-label">Estado</label>

<select name="estado" class="form-select">

<option value="1"
{{ old('estado', $periodo->estado) == 1 ? 'selected' : '' }}>
Activo
</option>

<option value="0"
{{ old('estado', $periodo->estado) == 0 ? 'selected' : '' }}>
Inactivo
</option>

</select>

</div>

</div>

<button class="btn btn-primary">Actualizar</button>

<a href="{{ route('periodos.index') }}"
   class="btn btn-secondary">
Cancelar
</a>

</form>

</div>
</div>

@endsection