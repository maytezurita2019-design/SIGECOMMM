@extends('layouts.app')

@section('title', isset($loteVale) ? 'Editar lote de vales' : 'Nuevo lote de vales')

@section('content')

<h2>
    {{ isset($loteVale) ? 'Editar lote de vales' : 'Nuevo lote de vales' }}
</h2>

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
            <input
    type="hidden"
    name="iniciar_nueva_serie"
    id="iniciar_nueva_serie"
    value="0"
>

            @isset($loteVale)
                @method('PUT')
            @endisset

            <div class="row">

                {{-- NÚMERO SUGERIDO --}}
@if(!isset($loteVale))
    <div class="col-12 mb-3">
        <div class="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-2 mb-0">

            <div>
                <small class="d-block">
                    Siguiente número sugerido
                </small>

                <strong style="font-size: 1.2rem;">
                    N.º {{ str_pad($numeroSugerido, 6, '0', STR_PAD_LEFT) }}
                </strong>

                @if($ultimoNumero > 0)
                    <small class="d-block text-muted mt-1">
                        Último número utilizado:
                        N.º {{ str_pad($ultimoNumero, 6, '0', STR_PAD_LEFT) }}
                    </small>
                @endif
            </div>

            <button
                type="button"
                id="usar-numero-sugerido"
                class="btn btn-success"
                data-numero="{{ $numeroSugerido }}"
            >
                <i class="bi bi-check-lg"></i>
                Usar
            </button>

        </div>
    </div>
@endif

                {{-- NÚMERO INICIAL --}}
                <div class="col-md-6 mb-3">
                    <label for="numero_inicial" class="form-label">
                        Número inicial *
                    </label>

                    <input
                        id="numero_inicial"
                        type="number"
                        name="numero_inicial"
                        min="1"
                        step="1"
                        class="form-control"
                        value="{{ old('numero_inicial', $loteVale->numero_inicial ?? '') }}"
                        required
                    >
                </div>

                {{-- CANTIDAD --}}
                <div class="col-md-6 mb-3">
                    <label for="cantidad_vales" class="form-label">
                        Cantidad de vales *
                    </label>

                    <input
                        id="cantidad_vales"
                        type="number"
                        name="cantidad_vales"
                        min="1"
                        step="1"
                        class="form-control"
                        value="{{ old('cantidad_vales') }}"
                        placeholder="Ej.: 200, 500, 8000"
                        required
                    >
                </div>

                {{-- NÚMERO FINAL --}}
                <div class="col-md-6 mb-3">
                    <label for="numero_final" class="form-label">
                        Número final *
                    </label>

                    <input
                        id="numero_final"
                        type="number"
                        name="numero_final"
                        min="1"
                        step="1"
                        class="form-control"
                        value="{{ old('numero_final', $loteVale->numero_final ?? '') }}"
                        readonly
                        required
                    >

                    <small class="text-muted">
                        Se calcula automáticamente según el número inicial y la cantidad.
                    </small>
                </div>

                {{-- FECHA --}}
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

                {{-- PERÍODO --}}
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

            </div>

            <button type="submit" class="btn btn-primary">
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
document.addEventListener('DOMContentLoaded', function () {

    const inicio = document.getElementById('numero_inicial');
    const cantidad = document.getElementById('cantidad_vales');
    const fin = document.getElementById('numero_final');
    const botonSugerido = document.getElementById('usar-numero-sugerido');
    const nuevaSerie = document.getElementById('iniciar_nueva_serie');

const numeroSugerido = @json($numeroSugerido ?? null);

if (botonSugerido) {
    botonSugerido.addEventListener('click', function () {

        inicio.value = this.dataset.numero;

        inicio.dispatchEvent(new Event('input'));

        inicio.focus();
    });
}
    function calcularNumeroFinal() {

        const numeroInicial = parseInt(inicio.value, 10);
        const cantidadVales = parseInt(cantidad.value, 10);

        if (
            Number.isInteger(numeroInicial) &&
            numeroInicial >= 1 &&
            Number.isInteger(cantidadVales) &&
            cantidadVales >= 1
        ) {
            fin.value = numeroInicial + cantidadVales - 1;
        } else {
            fin.value = '';
        }
    }

    inicio.addEventListener('input', calcularNumeroFinal);
    cantidad.addEventListener('input', calcularNumeroFinal);

    calcularNumeroFinal();

    const formulario = inicio.closest('form');

formulario.addEventListener('submit', function (event) {

    const numeroInicial = parseInt(inicio.value, 10);
    const sugerido = parseInt(numeroSugerido, 10);

    nuevaSerie.value = '0';

    if (
        Number.isInteger(numeroInicial) &&
        Number.isInteger(sugerido) &&
        numeroInicial < sugerido
    ) {

        const confirmar = confirm(
            'El número ingresado es menor al correlativo actual.\n\n' +
            'Último número de la serie actual: N.º ' +
            String(sugerido - 1).padStart(6, '0') +
            '\n\n' +
            'Nueva serie desde: N.º ' +
            String(numeroInicial).padStart(6, '0') +
            '\n\n' +
            '¿Desea iniciar una nueva serie?'
        );

        if (!confirmar) {
            event.preventDefault();
            nuevaSerie.value = '0';
            return;
        }

        nuevaSerie.value = '1';
    }
});
});
</script>

@endsection