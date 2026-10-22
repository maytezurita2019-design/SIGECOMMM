@extends('layouts.app')

@section('title', 'Emitir vale · SIGECOM')

@section('content')

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">

    <div>
        <div class="small text-success fw-bold text-uppercase mb-1">
            Combustible · GAMS
        </div>

        <h1 class="page-title h2 mb-1">
            Emitir vale
        </h1>

        <p class="page-subtitle mb-0">
            Seleccione el vehículo y registre el kilometraje.
            SIGECOM obtendrá automáticamente la AP, FF y OF.
        </p>
    </div>

    <a href="{{ route('vales-combustible.index', ['tipo' => 'GAMS']) }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Volver
    </a>

</div>


@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('vales-combustible.store') }}"
      method="POST"
      id="formVale">

    @csrf

    <div class="row g-3">

        {{-- PARTE IZQUIERDA --}}
        <div class="col-xl-8">

            <div class="card">

                <div class="card-body p-3 p-md-4">

                    {{-- VEHÍCULO --}}
                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="sig-kpi-icon">
                            <i class="bi bi-car-front"></i>
                        </div>

                        <div>
                            <h2 class="h5 fw-bold mb-0">
                                1. Vehículo
                            </h2>

                            <small class="text-muted">
                                Busque por placa, código o unidad.
                            </small>
                        </div>

                    </div>


                    <label for="busquedaVehiculo"
                           class="form-label">
                        Buscar vehículo
                    </label>

                    <div class="input-group mb-2">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input id="busquedaVehiculo"
                               type="text"
                               class="form-control"
                               autocomplete="off"
                               placeholder="Ej.: 1232ADS o C-20">

                    </div>


                    <select name="vehiculo_id"
                            id="vehiculo_id"
                            class="form-select @error('vehiculo_id') is-invalid @enderror"
                            required>

                        <option value="">
                            Seleccione un vehículo...
                        </option>

                        @foreach($vehiculos as $vehiculo)

                            @php
                                $apertura = $vehiculo->aperturaProgramatica;

                                $ff = $apertura?->fuenteFinanciamiento;

                                $of = $apertura?->organismoFinanciador;

                                $detalle = trim(
                                    'ASIGNADO A ' .
                                    mb_strtoupper($vehiculo->tipo ?? '') .
                                    (
                                        $vehiculo->unidad?->nombre
                                            ? ' DE ' . mb_strtoupper($vehiculo->unidad->nombre)
                                            : ''
                                    )
                                );
                            @endphp

                            <option
                                value="{{ $vehiculo->id }}"

                                data-search="{{ mb_strtoupper(
                                    ($vehiculo->placa ?? '') . ' ' .
                                    ($vehiculo->codigo ?? '') . ' ' .
                                    ($vehiculo->tipo ?? '') . ' ' .
                                    ($vehiculo->marca ?? '') . ' ' .
                                    ($vehiculo->unidad?->nombre ?? '')
                                ) }}"

                                data-codigo="{{ $vehiculo->codigo ?? '' }}"
                                data-placa="{{ $vehiculo->placa ?? '' }}"
                                data-tipo="{{ $vehiculo->tipo ?? '' }}"
                                data-marca="{{ $vehiculo->marca ?? '' }}"
                                data-unidad="{{ $vehiculo->unidad?->nombre ?? '' }}"

                                data-ap="{{ $apertura?->codigo ?? '' }}"
                                data-ff="{{ $ff?->codigo ?? '' }}"
                                data-of="{{ $of?->codigo ?? '' }}"

                                data-detalle="{{ $detalle }}"

                                data-tiene-ap="{{ $apertura ? '1' : '0' }}"

                                {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}
                            >

                                {{ $vehiculo->codigo }}

                                @if($vehiculo->placa)
                                    · {{ $vehiculo->placa }}
                                @endif

                                @if($vehiculo->unidad?->nombre)
                                    · {{ $vehiculo->unidad->nombre }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('vehiculo_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror


                    {{-- INFORMACIÓN AUTOMÁTICA --}}
                    <div id="vehiculoCard"
                         class="border rounded p-3 mt-3 bg-light d-none">

                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">

                            <div>
                                <div class="small text-muted">
                                    Vehículo seleccionado
                                </div>

                                <div class="fw-bold fs-5">
                                    <span id="vcodigo"></span>
                                    <span id="vplacaTexto"></span>
                                </div>
                            </div>

                            <span class="badge bg-success">
                                Asignación automática
                            </span>

                        </div>


                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Tipo
                                </small>

                                <strong id="vtipo">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Marca
                                </small>

                                <strong id="vmarca">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Unidad
                                </small>

                                <strong id="vunidad">—</strong>
                            </div>

                        </div>


                        <hr>


                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Apertura Programática
                                </small>

                                <strong id="vap">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    FF
                                </small>

                                <strong id="vff">—</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    OF
                                </small>

                                <strong id="vof">—</strong>
                            </div>

                        </div>


                        <div class="mt-3 p-3 border rounded bg-white">

                            <small class="text-muted d-block mb-1">
                                Detalle que utilizará el vale
                            </small>

                            <strong id="vdetalle">
                                —
                            </strong>

                        </div>


                        <div id="alertaSinAP"
                             class="alert alert-warning mt-3 mb-0 d-none">

                            <i class="bi bi-exclamation-triangle me-1"></i>

                            Este vehículo no tiene una Apertura Programática
                            asignada. Debe editar el vehículo antes de emitir
                            el vale.

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- DATOS DEL VALE --}}
                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="sig-kpi-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>

                        <div>
                            <h2 class="h5 fw-bold mb-0">
                                2. Datos del vale
                            </h2>

                            <small class="text-muted">
                                Registre fecha y kilometraje.
                            </small>
                        </div>

                    </div>


                    <div class="row g-3">

                        <div class="col-md-6">

                            <label for="fecha"
                                   class="form-label">
                                Fecha *
                            </label>

                            <input id="fecha"
                                   name="fecha"
                                   type="date"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>

                            @error('fecha')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        <div class="col-md-6">

    <label for="kilometraje" class="form-label">
    Kilometraje / Horómetro
</label>

<input id="kilometraje"
       name="kilometraje"
       type="text"
       maxlength="50"
       class="form-control @error('kilometraje') is-invalid @enderror"
       value="{{ old('kilometraje') }}"
       placeholder="Ej.: 45230 o NO FUNCIONA">

<div class="form-text">
    Puede registrar un número, texto o dejarlo vacío.
</div>

@error('kilometraje')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror
</div>


        {{-- PARTE DERECHA --}}
        <div class="col-xl-4">

            <div class="card">

                <div class="card-body p-3 p-md-4">

                    <div class="small text-muted mb-1">
                        Próximo vale
                    </div>

                    <div class="display-6 fw-bold text-success mb-3">
                        N.º {{ $numeroSugerido }}
                    </div>

                    <div class="alert alert-light border small">

                        <i class="bi bi-info-circle me-1"></i>

                        El número definitivo se asignará automáticamente
                        cuando se emita el vale.

                    </div>


                    <hr>


                    <div class="small text-muted mb-2">
                        Datos automáticos
                    </div>

                    <div class="small mb-1">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Apertura Programática
                    </div>

                    <div class="small mb-1">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Fuente de Financiamiento
                    </div>

                    <div class="small mb-1">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Organismo Financiador
                    </div>

                    <div class="small">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Detalle según vehículo y unidad
                    </div>


                    <div class="d-grid gap-2 mt-4">

                        <button type="submit"
                                id="btnEmitirImprimir"
                                class="btn btn-success btn-lg"
                                name="imprimir"
                                value="1">

                            <i class="bi bi-printer me-1"></i>
                            Emitir e imprimir

                        </button>


                        <button type="submit"
                                id="btnEmitir"
                                class="btn btn-outline-success"
                                name="imprimir"
                                value="0">

                            <i class="bi bi-check2-circle me-1"></i>
                            Emitir sin imprimir

                        </button>


                        <a href="{{ route(
                            'vales-combustible.index',
                            ['tipo' => 'GAMS']
                        ) }}"
                           class="btn btn-outline-secondary">

                            Cancelar

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const buscar =
        document.getElementById('busquedaVehiculo');

    const select =
        document.getElementById('vehiculo_id');

    const card =
        document.getElementById('vehiculoCard');

    const alertaSinAP =
        document.getElementById('alertaSinAP');

    const btnEmitir =
        document.getElementById('btnEmitir');

    const btnEmitirImprimir =
        document.getElementById('btnEmitirImprimir');

    const originales =
        select
            ? [...select.options].slice(1)
            : [];


    function texto(id, valor) {

        const elemento =
            document.getElementById(id);

        if (elemento) {
            elemento.textContent =
                valor || '—';
        }
    }


    function actualizarVehiculo() {

        const opcion =
            select?.selectedOptions[0];

        if (!opcion || !opcion.value) {

            card?.classList.add('d-none');

            if (btnEmitir) {
                btnEmitir.disabled = false;
            }

            if (btnEmitirImprimir) {
                btnEmitirImprimir.disabled = false;
            }

            return;
        }


        card?.classList.remove('d-none');


        texto(
            'vcodigo',
            opcion.dataset.codigo
        );

        texto(
            'vplacaTexto',
            opcion.dataset.placa
                ? ' · ' + opcion.dataset.placa
                : ''
        );

        texto(
            'vtipo',
            opcion.dataset.tipo
        );

        texto(
            'vmarca',
            opcion.dataset.marca
        );

        texto(
            'vunidad',
            opcion.dataset.unidad
        );

        texto(
            'vap',
            opcion.dataset.ap
        );

        texto(
            'vff',
            opcion.dataset.ff
        );

        texto(
            'vof',
            opcion.dataset.of
        );

        texto(
            'vdetalle',
            opcion.dataset.detalle
        );


        const tieneAP =
            opcion.dataset.tieneAp === '1';


        alertaSinAP?.classList.toggle(
            'd-none',
            tieneAP
        );


        if (btnEmitir) {
            btnEmitir.disabled = !tieneAP;
        }

        if (btnEmitirImprimir) {
            btnEmitirImprimir.disabled = !tieneAP;
        }

    }


    buscar?.addEventListener('input', () => {

        const consulta =
            buscar.value
                .toUpperCase()
                .replace(/\s+/g, ' ')
                .trim();


        const actual =
            select.value;


        select.innerHTML =
            '<option value="">Seleccione un vehículo...</option>';


        originales
            .filter(opcion =>
                !consulta ||
                (opcion.dataset.search || '')
                    .includes(consulta)
            )
            .slice(0, 80)
            .forEach(opcion => {

                select.appendChild(opcion);

            });


        if (
            [...select.options]
                .some(opcion =>
                    opcion.value === actual
                )
        ) {
            select.value = actual;
        }


        actualizarVehiculo();

    });


    select?.addEventListener(
        'change',
        actualizarVehiculo
    );


    actualizarVehiculo();

});

</script>

@endpush
