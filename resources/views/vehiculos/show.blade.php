@extends('layouts.app')

@section('title', 'Detalle del vehículo')

@section('content')

<h2>Detalle del vehículo</h2>

<div class="card shadow-sm">
<div class="card-body">

<p><strong>Código:</strong> {{ $vehiculo->codigo }}</p>
<hr>

<p><strong>Placa:</strong> {{ $vehiculo->placa }}</p>
<hr>

<p><strong>Tipo:</strong> {{ $vehiculo->tipo ?? '-' }}</p>
<hr>

<p><strong>Industria:</strong> {{ $vehiculo->industria ?? '-' }}</p>
<hr>

<p><strong>Marca:</strong> {{ $vehiculo->marca ?? '-' }}</p>
<hr>

<p><strong>Color:</strong> {{ $vehiculo->color ?? '-' }}</p>
<hr>

<p>
    <strong>Unidad:</strong>
    {{ $vehiculo->unidad->nombre ?? 'Sin unidad' }}
</p>

<hr>

<p>
    <strong>Estado:</strong>

    @if($vehiculo->estado)
        <span class="badge bg-success">Activo</span>
    @else
        <span class="badge bg-secondary">Inactivo</span>
    @endif
</p>

<a href="{{ route('vehiculos.edit', $vehiculo) }}"
   class="btn btn-warning">
    Editar
</a>

<a href="{{ route('vehiculos.index') }}"
   class="btn btn-secondary">
    Volver
</a>

</div>
</div>

@endsection