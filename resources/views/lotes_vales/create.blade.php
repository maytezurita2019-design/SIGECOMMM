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

@if(!empty($errorSerie))

    <div class="alert alert-warning" role="alert">{{ $errorSerie }}</div>

@endif

@php

    $editando = isset($loteVale);

    $numeroInicialActual = old(

        'numero_inicial',

        $editando

            ? $loteVale->numero_inicial

            : ($numeroSugerido ?? '')

    );

    $numeroFinalActual = old(

        'numero_final',

        $editando ? $loteVale->numero_final : ''

    );

    $cantidadActual = old(

        'cantidad_vales',

        $editando

            ? ((int) $loteVale->numero_final - (int) $loteVale->numero_inicial + 1)

            : ''

    );

    $numeroInicialParaMostrar = (string) $numeroInicialActual;

    if (

        preg_match('/\A[0-9]+\z/', $numeroInicialParaMostrar) === 1

        && (int) $numeroInicialParaMostrar >= 1

        && (int) $numeroInicialParaMostrar <= 999999

    ) {

        $numeroInicialParaMostrar = sprintf(

            '%06d',

            (int) $numeroInicialParaMostrar

        );

    }

    $numeroFinalParaMostrar = (string) $numeroFinalActual;

    if (

        preg_match('/\A[0-9]+\z/', $numeroFinalParaMostrar) === 1

        && (int) $numeroFinalParaMostrar >= 1

        && (int) $numeroFinalParaMostrar <= 999999

    ) {

        $numeroFinalParaMostrar = sprintf(

            '%06d',

            (int) $numeroFinalParaMostrar

        );

    }

@endphp

<div class="card shadow-sm">

    <div class="card-body">

        <form

            method="POST"

            action="{{ $editando

                ? route('lotes-vales.update', $loteVale)

                : route('lotes-vales.store') }}"

        >

            @csrf

            @if($editando)

                @method('PUT')

            @endif

            @unless($editando)

                <div class="alert alert-light border">

                    Serie <strong>{{ $codigoSerie ?? 'POL-GAS' }}</strong>

                    · Gestión <strong>{{ $anioSerie ?? now()->year }}</strong>

                    · Último correlativo

                    <strong>{{ sprintf('%06d', $ultimoNumero ?? 0) }}</strong>

                </div>

            @endunless

            <div class="row">

                {{-- NÚMERO INICIAL --}}

                <div class="col-md-6 mb-3">

                    <label for="numero_inicial" class="form-label">

                        Número inicial *

                    </label>

                    <input

                        id="numero_inicial"

                        type="text"

                        name="numero_inicial"

                        inputmode="numeric"

                        pattern="[0-9]+"

                        maxlength="6"

                        class="form-control"

                        value="{{ $numeroInicialParaMostrar }}"

                        required

                    >

                    <small class="text-muted">

                        El número se muestra con seis dígitos.

                    </small>

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

                        max="999999"

                        step="1"

                        class="form-control"

                        value="{{ $cantidadActual }}"

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

                        type="text"

                        name="numero_final"

                        inputmode="numeric"

                        pattern="[0-9]+"

                        maxlength="6"

                        class="form-control"

                        value="{{ $numeroFinalParaMostrar }}"

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

                            $editando

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

                        <option value="">Seleccione un período...</option>

                        @foreach($periodos as $periodo)

                            @if(

                                $editando ||

                                (

                                    (int) $periodo->anio === (int) now()->year &&

                                    (int) $periodo->mes === (int) now()->month

                                )

                            )

                                <option

                                    value="{{ $periodo->id }}"

                                    @selected(

                                        old(

                                            'periodo_id',

                                            $editando ? $loteVale->periodo_id : null

                                        ) == $periodo->id

                                    )

                                >

                                    {{ $periodo->nombre }}

                                    ·

                                    {{ $periodo->fecha_inicio->format('d/m/Y') }}

                                    al

                                    {{ $periodo->fecha_fin->format('d/m/Y') }}

                                </option>

                            @endif

                        @endforeach

                    </select>

                </div>

            </div>

            <button type="submit" class="btn btn-primary" @disabled(!empty($errorSerie) && !$editando)>

                <i class="bi bi-save me-1" aria-hidden="true"></i>

                Guardar

            </button>

            <a

                href="{{ route('lotes-vales.index') }}"

                class="btn btn-secondary"

            >

                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>

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

    function formatearNumero(numero) {

        return String(numero).padStart(6, '0');

    }

    function calcularNumeroFinal() {

        const textoInicio = inicio.value.trim();

        const textoCantidad = cantidad.value.trim();

        if (

            !/^[0-9]+$/.test(textoInicio) ||

            !/^[0-9]+$/.test(textoCantidad)

        ) {

            fin.value = '';

            return;

        }

        const numeroInicial = Number(textoInicio);

        const cantidadVales = Number(textoCantidad);

        if (

            Number.isSafeInteger(numeroInicial) &&

            numeroInicial >= 1 &&

            numeroInicial <= 999999 &&

            Number.isSafeInteger(cantidadVales) &&

            cantidadVales >= 1 &&

            cantidadVales <= 999999

        ) {

            const numeroFinal = numeroInicial + cantidadVales - 1;

            fin.value = Number.isSafeInteger(numeroFinal) && numeroFinal <= 999999

                ? formatearNumero(numeroFinal)

                : '';

        } else {

            fin.value = '';

        }

    }

    inicio.addEventListener('input', calcularNumeroFinal);

    cantidad.addEventListener('input', calcularNumeroFinal);

    inicio.addEventListener('input', function () {

        inicio.setCustomValidity('');

    });

    cantidad.addEventListener('input', function () {

        cantidad.setCustomValidity('');

    });

    inicio.addEventListener('blur', function () {

        const valor = inicio.value.trim();

        if (/^[0-9]+$/.test(valor) && Number(valor) >= 1) {

            inicio.value = formatearNumero(Number(valor));

        }

        calcularNumeroFinal();

    });

    calcularNumeroFinal();

    const formulario = inicio.closest('form');

    formulario.addEventListener('submit', function (event) {

        const numeroInicial = Number(inicio.value);

        const cantidadVales = Number(cantidad.value);

        const numeroInicialValido =

            /^[0-9]+$/.test(inicio.value.trim()) &&

            Number.isSafeInteger(numeroInicial) &&

            numeroInicial >= 1 &&

            numeroInicial <= 999999;

        const cantidadValida =

            /^[0-9]+$/.test(cantidad.value.trim()) &&

            Number.isSafeInteger(cantidadVales) &&

            cantidadVales >= 1 &&

            cantidadVales <= 999999;

        calcularNumeroFinal();

        if (!numeroInicialValido) {

            event.preventDefault();

            inicio.setCustomValidity(

                'El número inicial debe estar entre 000001 y 999999.'

            );

            inicio.reportValidity();

            return;

        }

        if (!cantidadValida || fin.value === '') {

            event.preventDefault();

            cantidad.setCustomValidity(

                'La cantidad debe ser un entero positivo y el rango no puede superar 999999.'

            );

            cantidad.reportValidity();

            return;

        }

    });

});

</script>

@endsection
