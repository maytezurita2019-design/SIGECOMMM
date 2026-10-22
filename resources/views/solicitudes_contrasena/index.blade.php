@extends('layouts.app')

@section('title', 'Solicitudes de contraseña')

@push('styles')

<style>

    .tabla-solicitudes {
        width: 100%;
        border-collapse: collapse;
    }

    .tabla-solicitudes th,
    .tabla-solicitudes td {
        border: 1px solid #d6e2da !important;
        padding: 12px 10px;
        vertical-align: middle;
    }

    .tabla-solicitudes thead th {
        background-color: #eaf7ee !important;
        color: #174c2b;
        font-weight: 700;
    }

    .tabla-solicitudes tbody td {
        background-color: #ffffff;
    }

    .tabla-solicitudes tbody tr:hover td {
        background-color: #f3faf5;
    }

</style>

@endpush


@section('content')

<div class="mb-4">

    <h2>Solicitudes de contraseña</h2>

    <p class="text-muted mb-0">
        Solicitudes de restablecimiento realizadas por los usuarios
    </p>

</div>


<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle tabla-solicitudes">

                <thead>

                    <tr>
                        <th>Usuario</th>
                        <th>CI</th>
                        <th>Correo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($solicitudes as $solicitud)

                        <tr>

                            <td>
                                <strong>
                                    {{ $solicitud->usuario->nombre }}
                                    {{ $solicitud->usuario->apellido }}
                                </strong>
                            </td>

                            <td>
                                {{ $solicitud->usuario->ci }}
                            </td>

                            <td>
                                {{ $solicitud->usuario->correo }}
                            </td>

                            <td>
                                {{ $solicitud->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td>

                                @if($solicitud->estado === 'pendiente')

                                    <span class="badge bg-warning text-dark">
                                        Pendiente
                                    </span>

                                @elseif($solicitud->estado === 'atendida')

                                    <span class="badge bg-success">
                                        Atendida
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Rechazada
                                    </span>

                                @endif

                            </td>

                            <td class="text-center">

                                @if($solicitud->estado === 'pendiente')

                                    <a
                                        href="{{ route('usuarios.restablecer-contrasena', $solicitud->usuario) }}"
                                        class="btn btn-sm btn-success">

                                        <i class="bi bi-key-fill me-1"></i>

                                        Restablecer

                                    </a>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted py-4">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No existen solicitudes de contraseña.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection