@extends('layouts.app')

@section('title', 'Lotes de vales')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar">
        </button>
    </div>
@endif
<div class="d-flex justify-content-between mb-3">

    <h2>Lotes de vales por período</h2>

    <a class="btn btn-primary"
       href="{{ route('lotes-vales.create') }}">
        Nuevo lote
    </a>

</div>

<a class="btn btn-primary mb-3"
   href="{{ route('entregas-policia.create') }}">
    Nueva entrega a Policía
</a>


<div class="card shadow-sm">

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-light">

                <tr>
                    <th>Período</th>
                    <th>Rango</th>
                    <th>Recepción</th>
                    
                    <th>Disponibles</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                @forelse($lotes as $loteVale)

                    @php
                        $entregado = DB::table('entregas_policia')
                            ->where('lote_vale_id', $loteVale->id)
                            ->exists();
                    @endphp

                    <tr>

                        {{-- PERÍODO --}}
                        <td>
                            {{ $loteVale->periodo->nombre }}
                        </td>


                        {{-- RANGO --}}
                        <td>
                            {{ sprintf('%06d', $loteVale->numero_inicial) }}
                            –
                            {{ sprintf('%06d', $loteVale->numero_final) }}
                        </td>


                        {{-- RECEPCIÓN --}}
                        <td>
                            {{ $loteVale->fecha_recepcion->format('d/m/Y') }}
                        </td>

                        {{-- DISPONIBLES --}}
                        <td>

                            @if($entregado)

                                0

                            @else

                                {{ $loteVale->disponibles }}

                            @endif

                        </td>


                        {{-- ESTADO --}}
                        <td>

                            @if($entregado)

                                <span class="badge bg-primary">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Entregado
                                </span>

                            @else

                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Disponible
                                </span>

                            @endif

                        </td>


                        {{-- ACCIONES --}}
                        <td>

                            <div class="d-flex gap-1">

                                {{-- VER --}}
                                <a
                                    href="{{ route('lotes-vales.show', $loteVale) }}"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ver lote"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>


                                {{-- EDITAR --}}
                                <a
                                    href="{{ route('lotes-vales.edit', $loteVale) }}"
                                    class="btn btn-sm btn-warning text-dark"
                                    title="Editar lote"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>


                                {{-- ELIMINAR --}}
                                <form
                                    method="POST"
                                    action="{{ route('lotes-vales.destroy', $loteVale) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Está seguro de eliminar este lote?');"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger"
                                        title="Eliminar lote"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan=6"
                            class="text-center">
                            No existen lotes registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection

