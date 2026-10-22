@extends('layouts.app')
@section('title','Acceso restringido')
@section('content')<div class="card"><div class="card-body text-center py-5"><div class="sig-kpi-icon mx-auto mb-3"><i class="bi bi-shield-lock"></i></div><h1 class="h3 page-title">Acceso restringido</h1><p class="text-muted">No tiene permiso para realizar esta operación.</p><a href="{{ route('dashboard') }}" class="btn btn-success"><i class="bi bi-house me-1"></i>Volver al inicio</a></div></div>@endsection
