@extends('layouts.app')

@section('title', 'Detalle de unidad')

@section('content')

<h2>Detalle de unidad</h2>

<div class="card shadow-sm">
<div class="card-body">

<p>
<strong>ID:</strong>
{{ $unidad->id }}
</p>

<hr>

<p>
<strong>Nombre:</strong>
{{ $unidad->nombre }}
</p>

<hr>

<p>
<strong>Sigla:</strong>
{{ $unidad->sigla ?? '-' }}
</p>

<hr>

<p>
<strong>Tipo:</strong>
{{ $unidad->tipo ?? '-' }}
</p>

<hr>

<p>
<strong>Descripción:</strong>
{{ $unidad->descripcion ?? 'Sin descripción' }}
</p>

<hr>

<p>
<strong>Estado:</strong>

@if($unidad->estado)
<span class="badge bg-success">Activo</span>
@else
<span class="badge bg-secondary">Inactivo</span>
@endif

</p>

<a href="{{ route('unidades.edit', $unidad) }}"
class="btn btn-warning">
Editar
</a>

<a href="{{ route('unidades.index') }}"
class="btn btn-secondary">
Volver
</a>

</div>
</div>

@endsection