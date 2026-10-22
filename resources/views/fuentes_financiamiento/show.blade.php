@extends('layouts.app')

@section('title', 'Detalle de fuente')

@section('content')

<h2>Detalle de fuente de financiamiento</h2>

<div class="card shadow-sm">
<div class="card-body">

<p><strong>ID:</strong> {{ $fuenteFinanciamiento->id }}</p>
<hr>

<p><strong>Código:</strong> {{ $fuenteFinanciamiento->codigo }}</p>
<hr>

<p><strong>Nombre:</strong> {{ $fuenteFinanciamiento->nombre }}</p>
<hr>

<p>
    <strong>Descripción:</strong>
    {{ $fuenteFinanciamiento->descripcion ?? 'Sin descripción' }}
</p>

<hr>

<p>
    <strong>Estado:</strong>

    @if($fuenteFinanciamiento->estado)
        <span class="badge bg-success">Activo</span>
    @else
        <span class="badge bg-secondary">Inactivo</span>
    @endif
</p>

<a href="{{ route('fuentes-financiamiento.edit', $fuenteFinanciamiento) }}"
   class="btn btn-warning">
    Editar
</a>

<a href="{{ route('fuentes-financiamiento.index') }}"
   class="btn btn-secondary">
    Volver
</a>

</div>
</div>

@endsection