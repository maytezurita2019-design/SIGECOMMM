@extends('layouts.app')

@section('title', 'Entrega a Policía')

@section('content')

<h2>Entrega de vales a Policía</h2>

<p>
    Seleccione el lote que será entregado a la Policía.
    La cantidad de vales y el rango se calcularán automáticamente.
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

<form method="POST"
      action="{{ route('entregas-policia.store') }}"
      class="card card-body">

    @csrf

    <input
        type="hidden"
        name="solicitud_token"
        value="{{ old('solicitud_token', $token) }}"
    >

    <div class="row">

        {{-- LOTE --}}
        <div class="col-md-8 mb-3">

            <label for="lote" class="form-label">
                Lote *
            </label>

            <select
                id="lote"
                name="lote_vale_id"
                class="form-select"
                required
            >

                <option value="">Seleccione...</option>

                @foreach($lotes as $lote)

                    <option
                        value="{{ $lote->id }}"
                        data-inicial="{{ $lote->numero_inicial }}"
                        data-final="{{ $lote->numero_final }}"
                        @selected(
                            old('lote_vale_id', request('lote_vale_id')) == $lote->id
                        )
                    >
                        {{ $lote->periodo->nombre }}
                        · {{ sprintf('%06d', $lote->numero_inicial) }}
                        -
                        {{ sprintf('%06d', $lote->numero_final) }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- CANTIDAD AUTOMÁTICA --}}
        <div class="col-md-4 mb-3">

            <label for="cantidad_mostrar" class="form-label">
                Cantidad de vales
            </label>

            <input
                id="cantidad_mostrar"
                type="number"
                class="form-control"
                readonly
                placeholder="Seleccione un lote"
            >

            <small class="text-muted">
                Se calcula automáticamente según el lote.
            </small>

        </div>


        {{-- DATOS DE ENTREGA --}}
        @foreach([
            'contrato' => 'Número o referencia del contrato',
            'dependencia' => 'Dependencia policial',
            'recibido_por' => 'Nombre de quien recibe'
        ] as $campo => $etiqueta)

            <div class="col-md-4 mb-3">

                <label for="{{ $campo }}" class="form-label">
                    {{ $etiqueta }} *
                </label>

                <input
                    id="{{ $campo }}"
                    name="{{ $campo }}"
                    value="{{ old($campo) }}"
                    maxlength="150"
                    class="form-control"
                    required
                >

            </div>

        @endforeach


        {{-- FECHA --}}
        <div class="col-md-4 mb-3">

            <label for="fecha_entrega" class="form-label">
                Fecha de entrega *
            </label>

            <input
                id="fecha_entrega"
                name="fecha_entrega"
                type="date"
                value="{{ old('fecha_entrega', date('Y-m-d')) }}"
                class="form-control"
                required
            >

        </div>


        {{-- APERTURA PROGRAMÁTICA --}}
        <div class="col-md-8 mb-3">

            <label for="apertura" class="form-label">
                Apertura programática (solo en el sistema) *
            </label>

            <select
                id="apertura"
                name="apertura_programatica_id"
                class="form-select"
                required
            >

                <option value="">Seleccione...</option>

                @foreach($aperturas as $apertura)

                    <option
                        value="{{ $apertura->id }}"
                        @selected(
                            old('apertura_programatica_id') == $apertura->id
                        )
                    >
                        {{ $apertura->codigo }}
                        —
                        {{ $apertura->descripcion }}
                    </option>

                @endforeach

            </select>

        </div>

    </div>


    {{-- RESUMEN DEL LOTE --}}
    <div
        id="resumenLote"
        class="alert alert-light border d-none"
    >

        <strong>Resumen de la entrega</strong>

        <div class="row mt-2">

            <div class="col-md-4">
                <strong>Desde:</strong>
                <span id="numeroInicial">—</span>
            </div>

            <div class="col-md-4">
                <strong>Hasta:</strong>
                <span id="numeroFinal">—</span>
            </div>

            <div class="col-md-4">
                <strong>Total:</strong>
                <span id="cantidadResumen">—</span> vales
            </div>

        </div>

    </div>


    <p class="text-muted">
        Al registrar la entrega, el lote completo quedará asignado a la Policía.
        Reimprimir la entrega no genera nuevos números.
    </p>


    <div>

        <button type="submit" class="btn btn-primary">
            Registrar entrega
        </button>

        <a
            href="{{ route('entregas-policia.index') }}"
            class="btn btn-secondary"
        >
            Volver
        </a>

    </div>

</form>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const lote = document.getElementById('lote');
    const cantidad = document.getElementById('cantidad_mostrar');

    const resumen = document.getElementById('resumenLote');
    const numeroInicial = document.getElementById('numeroInicial');
    const numeroFinal = document.getElementById('numeroFinal');
    const cantidadResumen = document.getElementById('cantidadResumen');

    function formatearNumero(numero) {
        return String(numero).padStart(6, '0');
    }

    function actualizarLote() {

        const opcion = lote.options[lote.selectedIndex];

        if (!opcion || !opcion.value) {

            cantidad.value = '';

            numeroInicial.textContent = '—';
            numeroFinal.textContent = '—';
            cantidadResumen.textContent = '—';

            resumen.classList.add('d-none');

            return;
        }

        const inicial = parseInt(opcion.dataset.inicial);
        const final = parseInt(opcion.dataset.final);

        const total = final - inicial + 1;

        cantidad.value = total;

        numeroInicial.textContent = formatearNumero(inicial);
        numeroFinal.textContent = formatearNumero(final);
        cantidadResumen.textContent = total;

        resumen.classList.remove('d-none');
    }

    lote.addEventListener('change', actualizarLote);

    actualizarLote();

});
</script>

@endsection