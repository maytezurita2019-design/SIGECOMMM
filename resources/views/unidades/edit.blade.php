@extends('layouts.app')

@section('title', 'Editar unidad')

@section('content')

<h2>Editar unidad</h2>

<div class="card shadow-sm">
<div class="card-body">

<form action="{{ route('unidades.update', $unidad) }}"
method="POST">

@csrf
@method('PUT')

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Nombre *</label>

<input type="text"
name="nombre"
class="form-control"
value="{{ old('nombre', $unidad->nombre) }}"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">Sigla</label>

<input type="text"
name="sigla"
class="form-control"
value="{{ old('sigla', $unidad->sigla) }}">

</div>

<div class="col-md-3 mb-3">

<label class="form-label">Tipo</label>

<input type="text"
name="tipo"
class="form-control"
value="{{ old('tipo', $unidad->tipo) }}">

</div>

<div class="col-12 mb-3">

<label class="form-label">Descripción</label>

<textarea name="descripcion"
class="form-control"
rows="3">{{ old('descripcion', $unidad->descripcion) }}</textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Estado</label>

<select name="estado"
class="form-select">

<option value="1"
{{ old('estado', $unidad->estado) == 1 ? 'selected' : '' }}>
Activo
</option>

<option value="0"
{{ old('estado', $unidad->estado) == 0 ? 'selected' : '' }}>
Inactivo
</option>

</select>

</div>

</div>

<button class="btn btn-primary">
Actualizar
</button>

<a href="{{ route('unidades.index') }}"
class="btn btn-secondary">
Cancelar
</a>

</form>

</div>
</div>

@endsection