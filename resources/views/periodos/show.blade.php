@extends('layouts.app')

@section('title', 'Detalle del periodo')

@section('content')

<h2>Detalle del periodo</h2>

<div class="card shadow-sm">
<div class="card-body">

<p><strong>ID:</strong> {{ $periodo->id }}</p>
<hr>



<p><strong>Mes:</strong> {{ $periodo->mes }}</p>
<hr>

<p><strong>Año:</strong> {{ $periodo->anio }}</p>
<hr>

<p>
<strong>Fecha inicio:</strong>
{{ $periodo->fecha_inicio?->format('d/m/Y') }}
</p>

<hr>

<p>
<strong>Fecha fin:</strong>
{{ $periodo->fecha_fin?->format('d/m/Y') }}
</p>

<hr>

<p>
<strong>Estado:</strong>

@if($periodo->estado)
<span class="badge bg-success">Activo</span>
@else
<span class="badge bg-secondary">Inactivo</span>
@endif

</p>

<a href="{{ route('periodos.edit', $periodo) }}"
   class="btn btn-warning">
Editar
</a>

<a href="{{ route('periodos.index') }}"
   class="btn btn-secondary">
Volver
</a>

</div>
</div>

@endsection