@extends('layouts.app')

@section('title', 'Editar fuente')

@section('content')

<h2>Editar fuente de financiamiento</h2>

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

<form action="{{ route('fuentes-financiamiento.update', $fuenteFinanciamiento) }}"
      method="POST">

@csrf
@method('PUT')

<div class="mb-3">
    <label class="form-label">Código *</label>

    <input type="text"
           name="codigo"
           class="form-control"
           value="{{ old('codigo', $fuenteFinanciamiento->codigo) }}"
           required>
</div>

<div class="mb-3">
    <label class="form-label">Nombre *</label>

    <input type="text"
           name="nombre"
           class="form-control"
           value="{{ old('nombre', $fuenteFinanciamiento->nombre) }}"
           required>
</div>

<div class="mb-3">
    <label class="form-label">Descripción</label>

    <textarea name="descripcion"
              class="form-control"
              rows="3">{{ old('descripcion', $fuenteFinanciamiento->descripcion) }}</textarea>
</div>

<div class="mb-4">
    <label class="form-label">Estado</label>

    <select name="estado" class="form-select">

        <option value="1"
            {{ old('estado', $fuenteFinanciamiento->estado) == 1 ? 'selected' : '' }}>
            Activo
        </option>

        <option value="0"
            {{ old('estado', $fuenteFinanciamiento->estado) == 0 ? 'selected' : '' }}>
            Inactivo
        </option>

    </select>
</div>

<button class="btn btn-primary">Actualizar</button>

<a href="{{ route('fuentes-financiamiento.index') }}"
   class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@endsection