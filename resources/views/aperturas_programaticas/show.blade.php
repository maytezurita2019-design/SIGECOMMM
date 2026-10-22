@extends('layouts.app')

@section('title', 'Detalle de apertura')

@section('content')

<h2>Detalle de apertura programática</h2>

<div class="card shadow-sm">
<div class="card-body">

<p>
<strong>Código:</strong>
{{ $aperturaProgramatica->codigo }}
</p>

<hr>

<p>
<strong>Descripción:</strong>
{{ $aperturaProgramatica->descripcion }}
</p>

<hr>

<p>
<strong>Fuente de financiamiento:</strong>
{{ $aperturaProgramatica->fuenteFinanciamiento->codigo ?? '' }}
-
{{ $aperturaProgramatica->fuenteFinanciamiento->nombre ?? '-' }}
</p>

<hr>

<p>
<strong>Organismo financiador:</strong>
{{ $aperturaProgramatica->organismoFinanciador->codigo ?? '' }}
-
{{ $aperturaProgramatica->organismoFinanciador->nombre ?? '-' }}
</p>

<hr>

<p>
<strong>Estado:</strong>

@if($aperturaProgramatica->estado)
<span class="badge bg-success">Activo</span>
@else
<span class="badge bg-secondary">Inactivo</span>
@endif
</p>

<a href="{{ route('aperturas-programaticas.edit', $aperturaProgramatica) }}"
   class="btn btn-warning">
Editar
</a>

<a href="{{ route('aperturas-programaticas.index') }}"
   class="btn btn-secondary">
Volver
</a>

</div>
</div>

@endsection