@extends('layouts.app')

@section('title', 'Lugares de entrega')

@section('content')
@push('styles')
<style>
    .lugares-panel {
        border: 1px solid #dfe8e3;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(23, 63, 50, .05);
        overflow: hidden;
    }

    .lugares-panel .card-header {
        background: #eaf5ef;
        color: #234d3a;
    }

    .lugares-table thead th {
        color: #234d3a;
        white-space: nowrap;
    }

    .lugar-acciones {
        display: flex;
        justify-content: flex-end;
        gap: .4rem;
        flex-wrap: wrap;
    }

    .lugar-acciones form {
        margin: 0;
    }

    @media (max-width: 575.98px) {
        .lugar-acciones {
            justify-content: flex-start;
        }

        .lugar-acciones .btn {
            flex: 1 1 auto;
        }
    }
</style>
@endpush

@php
    $esTecnicoI = mb_strtoupper(trim((string) (auth()->user()->rol?->nombre ?? ''))) === 'TECNICO_I';
@endphp
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Lugares de entrega</h1>
        <p class="text-muted mb-0">Catálogo usado para los vales GAMS de diésel.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if($esTecnicoI)
    <div class="card lugares-panel mb-4">
        <div class="card-header"><strong><i class="bi bi-geo-alt-fill me-2"></i>Registrar lugar</strong></div>
        <div class="card-body">
            <form action="{{ route('lugares-entrega.store') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-12 col-md-8">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input id="nombre" name="nombre" value="{{ old('lugar_entrega_id') ? '' : old('nombre') }}" maxlength="150"
                           placeholder="Ej. Posta Municipal"
                           pattern=".*\S.*" title="Ingrese el nombre del lugar, sin dejarlo vacío."
                           class="form-control @error('nombre') is-invalid @enderror" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4 d-grid">
                    <button class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Agregar lugar</button>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="card lugares-panel">
    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
        <strong><i class="bi bi-list-ul me-2"></i>Catálogo</strong>
        <span class="text-muted small">{{ $lugares->total() }} {{ $lugares->total() === 1 ? 'lugar' : 'lugares' }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 lugares-table">
            <thead>
                <tr><th>Lugar</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($lugares as $lugar)
                    <tr>
                        <td class="fw-semibold">{{ $lugar->nombre }}</td>
                        <td>
                            <span class="badge {{ $lugar->estado ? 'bg-success' : 'bg-secondary' }}">
                                {{ $lugar->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($esTecnicoI)
                                <div class="lugar-acciones">
                                @if($lugar->estado)
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            data-bs-toggle="modal" data-bs-target="#editarLugarModal"
                                            data-lugar-id="{{ $lugar->id }}"
                                            data-lugar-nombre="{{ $lugar->nombre }}"
                                            data-update-url="{{ route('lugares-entrega.update', $lugar) }}">
                                        <i class="bi bi-pencil-square me-1"></i>Editar
                                    </button>
                                    <form action="{{ route('lugares-entrega.desactivar', $lugar) }}" method="POST"
                                          onsubmit="return confirm('¿Desactivar «{{ addslashes($lugar->nombre) }}»? No se eliminará ni se modificarán los vales históricos asociados.')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="bi bi-pause-circle me-1"></i> Desactivar
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('lugares-entrega.reactivar', $lugar) }}" method="POST"
                                          onsubmit="return confirm('¿Reactivar «{{ addslashes($lugar->nombre) }}»?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success" type="submit">
                                            <i class="bi bi-play-circle me-1"></i> Reactivar
                                        </button>
                                    </form>
                                @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">No hay lugares registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($lugares->hasPages())
        <div class="card-footer">{{ $lugares->links() }}</div>
    @endif
</div>

@if($esTecnicoI)
    <div class="modal fade" id="editarLugarModal" tabindex="-1" aria-labelledby="editarLugarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editarLugarForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="lugar_entrega_id" id="editarLugarId">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="editarLugarModalLabel">
                            <i class="bi bi-pencil-square me-2 text-success"></i>Editar lugar de entrega
                        </h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <label for="editarLugarNombre" class="form-label">Nombre del lugar</label>
                        <input type="text" name="nombre" id="editarLugarNombre" maxlength="150"
                               pattern=".*\S.*" title="Ingrese el nombre del lugar, sin dejarlo vacío."
                               class="form-control @error('nombre') is-invalid @enderror" required>
                        @error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="modal-footer flex-wrap">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('editarLugarModal');
            const form = document.getElementById('editarLugarForm');
            const idInput = document.getElementById('editarLugarId');
            const nameInput = document.getElementById('editarLugarNombre');

            modalElement?.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                form.action = trigger.dataset.updateUrl;
                idInput.value = trigger.dataset.lugarId;
                nameInput.value = trigger.dataset.lugarNombre;
            });

            @if($errors->has('nombre') && old('lugar_entrega_id'))
                const trigger = document.querySelector('[data-lugar-id="{{ old('lugar_entrega_id') }}"]');
                if (trigger && window.bootstrap?.Modal) {
                    form.action = trigger.dataset.updateUrl;
                    idInput.value = trigger.dataset.lugarId;
                    nameInput.value = @json(old('nombre'));
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            @endif
        });
    </script>
    @endpush
@endif
@endsection
