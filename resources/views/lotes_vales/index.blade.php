@extends('layouts.app')
@section('title', 'Lotes de vales')
@section('content')
<div class="d-flex justify-content-between mb-3"><h2>Lotes de vales por período</h2><a class="btn btn-primary" href="{{ route('lotes-vales.create') }}">Nuevo lote</a></div>
<a class="btn btn-outline-primary mb-3" href="{{ route('entregas-policia.index') }}">Entregas a Policía</a> <a class="btn btn-primary mb-3" href="{{ route('entregas-policia.create') }}">Nueva entrega a Policía</a>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table table-bordered table-hover align-middle"><thead class="table-light"><tr><th>Período</th><th>Rango</th><th>Recepción</th><th>Próximo número</th><th>Disponibles</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
@forelse($lotes as $loteVale)
<tr><td>{{ $loteVale->periodo->nombre }}</td><td>{{ sprintf('%06d', $loteVale->numero_inicial) }} – {{ sprintf('%06d', $loteVale->numero_final) }}</td><td>{{ $loteVale->fecha_recepcion->format('d/m/Y') }}</td><td>{{ $loteVale->siguiente_numero !== null ? sprintf('%06d', $loteVale->siguiente_numero) : 'No disponible para emisión' }}</td><td>{{ $loteVale->disponibles }}</td><td>{{ $loteVale->estado && $loteVale->periodo->estado ? 'Activo' : 'Inactivo' }}</td>
<td><a class="btn btn-sm btn-info" href="{{ route('lotes-vales.show', $loteVale) }}">Ver</a> <a class="btn btn-sm btn-warning" href="{{ route('lotes-vales.edit', $loteVale) }}">Editar</a>
<form class="d-inline" method="POST" action="{{ route('lotes-vales.destroy', $loteVale) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este lote vacío?')">Eliminar</button></form></td></tr>
@empty<tr><td colspan="7" class="text-center">No existen lotes registrados.</td></tr>@endforelse
</tbody></table></div></div>
@endsection
