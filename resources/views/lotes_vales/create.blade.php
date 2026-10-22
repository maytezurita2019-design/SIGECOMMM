@extends('layouts.app')

@section('title', isset($loteVale) ? 'Editar lote de vales' : 'Nuevo lote de vales')

@section('content')

<h2>
    {{ isset($loteVale) ? 'Editar lote de vales' : 'Nuevo lote de vales' }}
</h2>

<p class="text-muted">
    Registre el rango de numeración de los vales recibidos.
    La numeración continúa entre períodos de una misma gestión
    y reinicia en 000001 al comenzar una nueva gestión.
</p>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">

        <form
            method="POST"
            action="{{ isset($loteVale)
                ? route('lotes-vales.update', $loteVale)
                : route('lotes-vales.store') }}"
        >

            @csrf

            @isset($loteVale)
                @method('PUT')
            @endisset

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label for="numero_inicial" class="form-label">
                        Número inicial *
                    </label>

                    <input
                        id="numero_inicial"
                        name="numero_inicial"
                        type="number"
                        min="1"
                        max="999999"
                        class="form-control"
                        value="{{ old('numero_inicial', $loteVale->numero_inicial ?? '') }}"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="numero_final" class="form-label">
                        Número final *
                    </label>

                    <input
                        id="numero_final"
                        name="numero_final"
                        type="number"
                        min="1"
                        max="999999"
                        class="form-control"
                        value="{{ old('numero_final', $loteVale->numero_final ?? '') }}"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="fecha_recepcion" class="form-label">
                        Fecha de recepción *
                    </label>

                    <input
                        id="fecha_recepcion"
                        name="fecha_recepcion"
                        type="date"
                        class="form-control"
                        value="{{ old(
                            'fecha_recepcion',
                            isset($loteVale)
                                ? $loteVale->fecha_recepcion->format('Y-m-d')
                                : date('Y-m-d')
                        ) }}"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="periodo_id" class="form-label">
                        Período *
                    </label>

                    <select
                        id="periodo_id"
                        name="periodo_id"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Seleccione un período...
                        </option>

                        @foreach($periodos as $periodo)
                            <option
                                value="{{ $periodo->id }}"
                                @selected(
                                    old(
                                        'periodo_id',
                                        $loteVale->periodo_id ?? ''
                                    ) == $periodo->id
                                )
                            >
                                {{ $periodo->nombre }}
                                ·
                                {{ $periodo->fecha_inicio->format('d/m/Y') }}
                                al
                                {{ $periodo->fecha_fin->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label">
                        Estado *
                    </label>

                    <select
                        id="estado"
                        name="estado"
                        class="form-select"
                        required
                    >
                        <option
                            value="1"
                            @selected(
                                old('estado', $loteVale->estado ?? 1) == 1
                            )
                        >
                            Activo
                        </option>

                        <option
                            value="0"
                            @selected(
                                old('estado', $loteVale->estado ?? 1) == 0
                            )
                        >
                            Inactivo
                        </option>
                    </select>
                </div>

            </div>

            <p
                id="resumen-rango"
                class="alert alert-info"
                aria-live="polite"
            >
                Ingrese el rango de números.
            </p>

            <p class="text-muted">
                No se permiten rangos superpuestos en un mismo año.
                Después de emitir un vale, el rango y el período
                del lote quedan fijos.
            </p>

            <button class="btn btn-primary">
                Guardar
            </button>

            <a
                href="{{ route('lotes-vales.index') }}"
                class="btn btn-secondary"
            >
                Cancelar
            </a>

        </form>

    </div>
</div>

<script>
const inicio = document.getElementById('numero_inicial');
const fin = document.getElementById('numero_final');

function resumenRango() {
    const a = Number(inicio.value);
    const b = Number(fin.value);

    document.getElementById('resumen-rango').textContent =
        a > 0 && b >= a
            ? `${b - a + 1} vales · del N.º ${String(a).padStart(6, '0')} al N.º ${String(b).padStart(6, '0')}`
            : 'Ingrese un rango válido: el número final debe ser mayor o igual al inicial.';

    fin.min = inicio.value || 1;
}

inicio.addEventListener('input', resumenRango);
fin.addEventListener('input', resumenRango);

resumenRango();
</script>

@endsection