@extends('layouts.app')

@section('title', 'Nuevo organismo')

@section('content')

<h2>Nuevo organismo financiador</h2>

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

<form action="{{ route('organismos-financiadores.store') }}"
      method="POST">

@csrf

<div class="mb-3">
<label class="form-label">Código *</label>

<input type="text"
       name="codigo"
       class="form-control"
       value="{{ old('codigo') }}"
       required>
</div>

<div class="mb-3">
<label class="form-label">Nombre *</label>

<input type="text"
       name="nombre"
       class="form-control"
       value="{{ old('nombre') }}"
       required>
</div>



<div class="mb-4">
<label class="form-label">Estado</label>

<select name="estado" class="form-select">
<option value="1">Activo</option>
<option value="0">Inactivo</option>
</select>
</div>

<button class="btn btn-primary">Guardar</button>

<a href="{{ route('organismos-financiadores.index') }}"
   class="btn btn-secondary">
Cancelar
</a>

</form>

</div>
</div>

@endsection