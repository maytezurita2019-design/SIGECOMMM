@extends('layouts.app')
@section('title','No se pudo completar')
@section('content')<div class="card"><div class="card-body text-center py-5"><div class="sig-kpi-icon mx-auto mb-3"><i class="bi bi-tools"></i></div><h1 class="h3 page-title">No se pudo completar la operación</h1><p class="text-muted">Intente nuevamente. Si el problema continúa, comuníquese con el administrador.</p><a href="{{ route('dashboard') }}" class="btn btn-success">Volver al inicio</a></div></div>@endsection
