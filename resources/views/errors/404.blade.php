@extends('layouts.app')
@section('title','Página no encontrada')
@section('content')<div class="card"><div class="card-body text-center py-5"><div class="sig-kpi-icon mx-auto mb-3"><i class="bi bi-compass"></i></div><h1 class="h3 page-title">Página no encontrada</h1><p class="text-muted">La dirección solicitada no existe o cambió.</p><a href="{{ route('dashboard') }}" class="btn btn-success">Volver al inicio</a></div></div>@endsection
