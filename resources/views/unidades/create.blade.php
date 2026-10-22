@extends('layouts.app')

@section('title', 'Nueva unidad')

@section('content')

<h2>Nueva unidad</h2>

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

<form action="{{ route('unidades.store') }}"
method="POST">

@csrf

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label">Nombre *</label>

<input type="text"
name="nombre"
class="form-control"
value="{{ old('nombre') }}"
required>
</div>

<div class="col-md-3 mb-3">
<label class="form-label">Sigla</label>

<input type="text"
name="sigla"
class="form-control"
value="{{ old('sigla') }}">
</div>

<div class="col-md-3 mb-3">
<label class="form-label">Tipo</label>

<input type="text"
name="tipo"
class="form-control"
value="{{ old('tipo') }}">
</div>

<div class="col-12 mb-3">
<label class="form-label">Descripción</label>

<textarea name="descripcion"
class="form-control"
rows="3">{{ old('descripcion') }}</textarea>
</div>

<div class="col-md-6 mb-3">

<label class="form-label">Estado</label>

<select name="estado"
class="form-select">

<option value="1">Activo</option>
<option value="0">Inactivo</option>

</select>

</div>

</div>

<button class="btn btn-primary">
Guardar
</button>

<a href="{{ route('unidades.index') }}"
class="btn btn-secondary">
Cancelar
</a>

</form>

</div>
</div>

@endsection