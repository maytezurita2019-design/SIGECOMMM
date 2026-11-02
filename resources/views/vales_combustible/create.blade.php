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

                                $detalle = $vehiculo->detalle_asignacion_predeterminado ?? '';
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

                        <div class="mt-3">
                            <label for="detalle_asignacion" class="form-label">
                                Detalle *
                            </label>

                            <textarea
                                name="detalle_asignacion"
                                id="detalle_asignacion"
                                rows="2"
                                maxlength="1000"
                                required
                                class="form-control @error('detalle_asignacion') is-invalid @enderror"
                                placeholder="SIN DETALLE CONFIGURADO"
                            >{{ old('detalle_asignacion') }}</textarea>

                            @error('detalle_asignacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Puede ajustar el detalle para este vale. El cambio no modifica el detalle predeterminado del vehículo.
                            </div>
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
                                Seleccione combustible, ubicación, fecha y kilometraje.
                            </small>
                        </div>

                    </div>


                    <div class="row g-3">

                        <div class="d-none">
                            <label for="tipo_combustible_id" class="form-label">Tipo de combustible *</label>
                            <select name="tipo_combustible_id" id="tipo_combustible_id"
                                    class="form-select @error('tipo_combustible_id') is-invalid @enderror" required>
                                <option value="">Seleccione combustible...</option>
                                @foreach($tiposCombustible as $tipo)
                                    <option
                                        value="{{ $tipo->id }}"
                                        data-siguiente="{{ $numerosSugeridos[$tipo->id] ?? '' }}"
                                        data-formateado="{{ $numerosFormateadosSugeridos[$tipo->id] ?? '' }}"
                                        data-serie="{{ $codigosSerieSugeridos[$tipo->id] ?? '' }}"
                                        data-estado="{{ $estadosSerieSugeridos[$tipo->id] ?? '' }}"
                                        data-error="{{ $erroresSugeridos[$codigosSerieSugeridos[$tipo->id] ?? ''] ?? '' }}"
                                        data-ubicacion="{{ str_contains(mb_strtoupper($tipo->nombre, 'UTF-8'), 'DIESEL') || str_contains(mb_strtoupper($tipo->nombre, 'UTF-8'), 'DIÉSEL') ? 'diesel' : 'gasolina' }}"
                                        @selected(
    (string) old('tipo_combustible_id',
        collect($tiposCombustible)->first(
            fn ($t) => \App\Models\NumeracionVale::codigoSerie(
                null, 'GAMS', $t->nombre
            ) === (
                ($combustibleSeleccionado ?? null) === 'gasolina'
                    ? 'GAMS-GAS'
                    : (
                        ($combustibleSeleccionado ?? null) === 'diesel'
                            ? 'GAMS-DIE'
                            : null
                    )
            )
        )?->id
    ) === (string) $tipo->id
)
                                    >
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_combustible_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6" id="estacionServicioContainer">
                            <label for="estacion_servicio_id" class="form-label">Estación de servicio *</label>
                            <select name="estacion_servicio_id" id="estacion_servicio_id"
                                    class="form-select @error('estacion_servicio_id') is-invalid @enderror">
                                <option value="">Seleccione estación...</option>
                                @foreach($estacionesServicio as $estacion)
                                    <option value="{{ $estacion->id }}" @selected(old('estacion_servicio_id') == $estacion->id)>
                                        {{ $estacion->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estacion_servicio_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 d-none" id="lugarEntregaContainer">
                            <label for="lugar_entrega_id" class="form-label">Lugar de entrega *</label>
                            <select name="lugar_entrega_id" id="lugar_entrega_id"
                                    class="form-select @error('lugar_entrega_id') is-invalid @enderror" disabled>
                                <option value="">Seleccione lugar de entrega...</option>
                                @foreach($lugaresEntrega as $lugar)
                                    <option value="{{ $lugar->id }}" @selected(old('lugar_entrega_id') == $lugar->id)>
                                        {{ $lugar->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lugar_entrega_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

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
                        Próximo vale · <span id="codigoSerieSugerido">GAMS-GAS</span>
                    </div>

                    <div id="numeroSiguiente" class="display-6 fw-bold text-success mb-3">
                        N.º {{ $numeroSugerido }}
                    </div>

                    <div id="seriePreviewError" class="alert alert-warning small d-none" role="alert"></div>

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
                        Detalle institucional del vehículo
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

                            <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
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

    const detalleAsignacion =
        document.getElementById('detalle_asignacion');
    const tipoCombustible =
        document.getElementById('tipo_combustible_id');
    const estacionServicio =
        document.getElementById('estacion_servicio_id');
    const lugarEntrega =
        document.getElementById('lugar_entrega_id');
    const numeroSiguiente =
        document.getElementById('numeroSiguiente');
    const codigoSerieSugerido =
        document.getElementById('codigoSerieSugerido');
    const fechaEmision =
        document.getElementById('fecha');
    const errorVistaPrevia =
        document.getElementById('seriePreviewError');
    const urlVistaPrevia =
        @json(route('vales-combustible.series.siguiente'));
    let anioVistaPreviaActual = @json($anio);
    let consultaVistaPrevia = 0;
    let consultandoVistaPrevia = false;

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

    function actualizarNumeroSugerido() {
        const opcion = tipoCombustible?.selectedOptions[0];
        const numero = opcion?.dataset.siguiente;
        const codigo = opcion?.dataset.serie;
        const formateado = opcion?.dataset.formateado;
        const estado = opcion?.dataset.estado;
        const mensaje = opcion?.dataset.error;

        if (numero && codigo && formateado) {
            numeroSiguiente.textContent = `N.º ${formateado}`;
            codigoSerieSugerido.textContent = codigo;
            if (estado !== 'ACTIVA' && mensaje) {
                errorVistaPrevia.textContent = mensaje;
                errorVistaPrevia.classList.remove('d-none');
            } else {
                errorVistaPrevia?.classList.add('d-none');
                if (errorVistaPrevia) errorVistaPrevia.textContent = '';
            }
        } else {
            numeroSiguiente.textContent = codigo ? 'No disponible' : 'Seleccione combustible';
            codigoSerieSugerido.textContent = codigo || 'GAMS';
            if (codigo && mensaje) {
                errorVistaPrevia.textContent = mensaje;
                errorVistaPrevia.classList.remove('d-none');
            } else {
                errorVistaPrevia?.classList.add('d-none');
                if (errorVistaPrevia) errorVistaPrevia.textContent = '';
            }
        }

        const serieNoDisponible = Boolean(codigo && (!numero || !formateado || estado !== 'ACTIVA'));
        if (btnEmitir) btnEmitir.disabled = serieNoDisponible || consultandoVistaPrevia;
        if (btnEmitirImprimir) btnEmitirImprimir.disabled = serieNoDisponible || consultandoVistaPrevia;
    }

    async function actualizarVistaPreviaPorAnio() {
        const anio = Number(fechaEmision?.value?.slice(0, 4));
        if (!anio || anio === anioVistaPreviaActual) return;

        const consultaActual = ++consultaVistaPrevia;
        anioVistaPreviaActual = anio;
        consultandoVistaPrevia = true;
        errorVistaPrevia.textContent = 'Consultando el correlativo del año seleccionado...';
        errorVistaPrevia.classList.remove('d-none');
        actualizarNumeroSugerido();

        try {
            const respuesta = await fetch(`${urlVistaPrevia}?anio=${encodeURIComponent(anio)}`, {
                headers: { Accept: 'application/json' },
            });
            const datos = await respuesta.json();
            if (!respuesta.ok) {
                throw new Error(datos.message || 'No se pudo consultar la serie del año seleccionado.');
            }
            if (consultaActual !== consultaVistaPrevia) return;

            tipoCombustible.querySelectorAll('option[data-serie]').forEach((opcion) => {
                const serie = datos.series?.[opcion.dataset.serie];
                opcion.dataset.siguiente = serie?.disponible ? String(serie.numero) : '';
                opcion.dataset.formateado = serie?.disponible ? serie.formateado : '';
                opcion.dataset.estado = serie?.estado || '';
                opcion.dataset.error = serie?.mensaje || '';
            });
        } catch (error) {
            if (consultaActual !== consultaVistaPrevia) return;
            tipoCombustible.querySelectorAll('option[data-serie]').forEach((opcion) => {
                opcion.dataset.siguiente = '';
                opcion.dataset.formateado = '';
                opcion.dataset.estado = '';
                opcion.dataset.error = error.message;
            });
        } finally {
            if (consultaActual === consultaVistaPrevia) {
                consultandoVistaPrevia = false;
                actualizarNumeroSugerido();
            }
        }
    }

    function actualizarUbicacion() {
        const esDiesel = tipoCombustible?.selectedOptions[0]?.dataset.ubicacion === 'diesel';
        const estacionContainer = document.getElementById('estacionServicioContainer');
        const lugarContainer = document.getElementById('lugarEntregaContainer');

        estacionContainer?.classList.remove('d-none');
        lugarContainer?.classList.toggle('d-none', !esDiesel);

        if (estacionServicio) {
            estacionServicio.disabled = false;
            estacionServicio.required = true;
        }
        if (lugarEntrega) {
            lugarEntrega.disabled = !esDiesel;
            lugarEntrega.required = esDiesel;
        }
    }


    function actualizarVehiculo(cambiarDetalle = false) {

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

        if (detalleAsignacion && (cambiarDetalle || !detalleAsignacion.value.trim())) {
            detalleAsignacion.value = opcion.dataset.detalle || '';
        }

        texto(
            'vdetalle',
            detalleAsignacion?.value || 'SIN DETALLE CONFIGURADO'
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
        () => actualizarVehiculo(true)
    );
    tipoCombustible?.addEventListener('change', () => {
        actualizarNumeroSugerido();
        actualizarUbicacion();
    });
    fechaEmision?.addEventListener('change', actualizarVistaPreviaPorAnio);

    detalleAsignacion?.addEventListener('input', () => {
        const opcion = select?.selectedOptions[0];
        texto(
            'vdetalle',
            detalleAsignacion.value || opcion?.dataset.detalle || 'SIN DETALLE CONFIGURADO'
        );
    });


    actualizarVehiculo();
    actualizarNumeroSugerido();
    actualizarUbicacion();
    actualizarVistaPreviaPorAnio();

});

</script>

@endpush
