@extends('layouts.app')

@section('title', 'Detalle del vale')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2>
            Vale N.º {{ sprintf('%06d', (int) $valeCombustible->numero_vale) }}

            @if($valeCombustible->periodo)
                · {{ $valeCombustible->periodo->nombre }}
            @endif
        </h2>

        <p class="text-muted mb-0">
            Detalle del consumo de combustible
        </p>
    </div>

    @if($valeCombustible->entrega_policia_id)

        <a href="{{ route(
            'entregas-policia.show',
            $valeCombustible->entrega_policia_id
        ) }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>
            Volver

        </a>

    @else

        <a href="{{ route('vales-combustible.index') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left"></i>
            Volver

        </a>

    @endif

</div>


<div class="card shadow-sm">

    {{-- =====================================================
         INFORMACIÓN POLICÍA
    ====================================================== --}}
    @if($valeCombustible->entrega_policia_id)

        <div class="alert alert-info m-3">

            Entrega a Policía · Estado:
            <strong>{{ $valeCombustible->estado }}</strong>.

            <a href="{{ route(
                'entregas-policia.show',
                $valeCombustible->entrega_policia_id
            ) }}">
                Ver entrega y contrato
            </a>

            @if($valeCombustible->motivo_anulacion)

                <p class="mb-0 mt-2">
                    <strong>Motivo:</strong>
                    {{ $valeCombustible->motivo_anulacion }}
                </p>

            @endif

        </div>

    @endif


    <div class="card-body">

        {{-- =================================================
             ESTADO
        ================================================== --}}
        <div class="mb-3">

            <strong>Estado:</strong>

            @if($valeCombustible->estado === 'EMITIDO')

                <span class="badge bg-warning text-dark">
                    EMITIDO / PENDIENTE
                </span>

            @elseif($valeCombustible->estado === 'COMPLETADO')

                <span class="badge bg-success">
                    COMPLETADO
                </span>

            @elseif($valeCombustible->estado === 'ENTREGADO')

                <span class="badge bg-warning text-dark">
                    ENTREGADO
                </span>

            @elseif($valeCombustible->estado === 'UTILIZADO')

                <span class="badge bg-success">
                    UTILIZADO
                </span>

            @elseif($valeCombustible->estado === 'ANULADO')

                <span class="badge bg-danger">
                    ANULADO
                </span>

            @elseif($valeCombustible->estado === 'DEVUELTO')

                <span class="badge bg-secondary">
                    DEVUELTO
                </span>

            @else

                <span class="badge bg-secondary">
                    {{ $valeCombustible->estado }}
                </span>

            @endif

        </div>


        {{-- =================================================
             INFORMACIÓN DE ANULACIÓN
        ================================================== --}}
        @if($valeCombustible->estado === 'ANULADO')

            <div class="alert alert-danger">

                <strong>Motivo de anulación:</strong>

                {{ $valeCombustible->motivo_anulacion ?? 'Sin motivo registrado' }}

                @if($valeCombustible->fecha_anulacion)

                    <br>

                    <strong>Fecha:</strong>

                    {{ $valeCombustible->fecha_anulacion->format('d/m/Y H:i') }}

                @endif

            </div>

        @endif


        {{-- =================================================
             DATOS DEL VALE
        ================================================== --}}
        <div class="row">

            {{-- NÚMERO --}}
            <div class="col-md-6 mb-3">

                <strong>Número de vale</strong>

                <div>
                    {{ sprintf(
                        '%06d',
                        (int) $valeCombustible->numero_vale
                    ) }}
                </div>

            </div>


            {{-- FECHA --}}
            <div class="col-md-6 mb-3">

                <strong>Fecha</strong>

                <div>
                    {{ $valeCombustible->fecha?->format('d/m/Y') ?? '-' }}
                </div>

            </div>


            {{-- LOTE SOLO POLICÍA --}}
            @if($valeCombustible->entrega_policia_id)

                <div class="col-md-6 mb-3">

                    <strong>Lote</strong>

                    <div>

                        @if($valeCombustible->loteVale)

                            {{ sprintf(
                                '%06d',
                                (int) $valeCombustible->loteVale->numero_inicial
                            ) }}

                            -

                            {{ sprintf(
                                '%06d',
                                (int) $valeCombustible->loteVale->numero_final
                            ) }}

                        @else

                            -

                        @endif

                    </div>

                </div>

            @endif


            {{-- PERÍODO --}}
            @if($valeCombustible->periodo)

                <div class="col-md-6 mb-3">

                    <strong>Período</strong>

                    <div>
                        {{ $valeCombustible->periodo->nombre }}
                    </div>

                </div>

            @endif


            {{-- VEHÍCULO --}}
            <div class="col-md-6 mb-3">

                <strong>Vehículo</strong>

                <div>

                    @if($valeCombustible->vehiculo)

                        {{ $valeCombustible->vehiculo->codigo }}

                        @if($valeCombustible->vehiculo->placa)
                            -
                            {{ $valeCombustible->vehiculo->placa }}
                        @endif

                    @else

                        -

                    @endif

                </div>

            </div>


            {{-- APERTURA PROGRAMÁTICA --}}
            <div class="col-md-6 mb-3">

                <strong>Apertura programática</strong>

                <div>
                    {{ $valeCombustible->aperturaProgramatica?->codigo ?? '-' }}
                </div>

            </div>


            {{-- COMBUSTIBLE Y ESTACIÓN --}}
            <div class="col-md-6 mb-3">

                <strong>Combustible / Estación</strong>

                <div>

                    {{ $valeCombustible->tipoCombustible?->nombre ?? 'Pendiente' }}

                    /

                    {{ $valeCombustible->estacionServicio?->nombre ?? 'Pendiente' }}

                </div>

            </div>


            {{-- CANTIDAD DE LITROS --}}
            <div class="col-md-6 mb-3">

                <strong>Cantidad de litros</strong>

                <div>

                    @if($valeCombustible->cantidad_litros !== null)

                        {{
                            str_replace(
                                '.',
                                ',',
                                rtrim(
                                    rtrim(
                                        (string) $valeCombustible->cantidad_litros,
                                        '0'
                                    ),
                                    '.'
                                )
                            )
                        }}

                    @else

                        Pendiente

                    @endif

                </div>

            </div>


            {{-- FACTURA --}}
            <div class="col-md-6 mb-3">

                <strong>Factura</strong>

                <div>
                    {{ $valeCombustible->numero_factura ?? 'Sin factura' }}
                </div>

            </div>


            {{-- PRECIO UNITARIO --}}
            <div class="col-md-3 mb-3">

                <strong>Precio unitario</strong>

                <div>

                    @if($valeCombustible->precio_unitario !== null)

                        Bs {{ number_format(
                            $valeCombustible->precio_unitario,
                            4,
                            ',',
                            '.'
                        ) }}

                    @else

                        Pendiente

                    @endif

                </div>

            </div>


            {{-- TOTAL --}}
            <div class="col-md-3 mb-3">

                <strong>Total</strong>

                <div class="fs-5">

                    <strong>

                        @if($valeCombustible->total !== null)

                            Bs {{ number_format(
                                $valeCombustible->total,
                                2,
                                ',',
                                '.'
                            ) }}

                        @else

                            Pendiente

                        @endif

                    </strong>

                </div>

            </div>


            {{-- KILOMETRAJE --}}
            <div class="col-md-6 mb-3">

                <strong>Kilometraje / Horómetro</strong>

                <div>
                    {{ $valeCombustible->kilometraje ?? '-' }}
                </div>

            </div>


            {{-- REGISTRADO POR --}}
            <div class="col-md-6 mb-3">

                <strong>Registrado por</strong>

                <div>

                    {{ $valeCombustible->usuario?->nombre ?? '-' }}

                    {{ $valeCombustible->usuario?->apellido ?? '' }}

                </div>

            </div>


            {{-- OBSERVACIÓN --}}
            <div class="col-12 mb-3">

                <strong>Observación</strong>

                <div>
                    {{ $valeCombustible->observacion ?: 'Sin observación' }}
                </div>

            </div>

        </div>


        <hr>


        {{-- =================================================
             ACCIONES GAMS - EMITIDO
        ================================================== --}}
        @if(
            !$valeCombustible->entrega_policia_id &&
            $valeCombustible->estado === 'EMITIDO'
        )

            <div class="d-flex gap-2 flex-wrap">

                {{-- COMPLETAR --}}
                <a href="{{ route(
                    'vales-combustible.completar.formulario',
                    $valeCombustible
                ) }}"
                   class="btn btn-success">

                    <i class="bi bi-check-circle me-1"></i>
                    Completar

                </a>


                {{-- IMPRIMIR --}}
                <a target="_blank"
                   rel="noopener"
                   href="{{ route(
                        'vales-combustible.imprimir',
                        $valeCombustible
                   ) }}"
                   class="btn btn-outline-primary">

                    <i class="bi bi-printer me-1"></i>
                    Imprimir

                </a>


                {{-- ANULAR --}}
                <a href="#anular-vale"
                   class="btn btn-outline-danger">

                    <i class="bi bi-x-circle me-1"></i>
                    Anular

                </a>

            </div>


            {{-- FORMULARIO ANULAR GAMS --}}
            <form id="anular-vale"
                  action="{{ route(
                      'vales-combustible.anular',
                      $valeCombustible
                  ) }}"
                  method="POST"
                  class="border rounded p-3 mt-4">

                @csrf

                <h3 class="h5">
                    Anular vale
                </h3>

                <p class="text-muted">
                    La anulación conserva el número y no se puede revertir.
                </p>

                <label class="form-label"
                       for="motivo_anulacion">

                    Motivo de anulación *

                </label>

                <textarea id="motivo_anulacion"
                          name="motivo_anulacion"
                          class="form-control mb-3"
                          rows="3"
                          maxlength="1000"
                          required>{{ old('motivo_anulacion') }}</textarea>

                @error('motivo_anulacion')

                    <div class="text-danger mb-3">
                        {{ $message }}
                    </div>

                @enderror


                <button type="submit"
                        class="btn btn-danger"
                        onclick="return confirm(
                            '¿Confirma la anulación de este vale? El número no podrá reutilizarse.'
                        )">

                    Confirmar anulación

                </button>

            </form>


        {{-- =================================================
             GAMS - COMPLETADO
        ================================================== --}}
        @elseif(
            !$valeCombustible->entrega_policia_id &&
            $valeCombustible->estado === 'COMPLETADO'
        )

            <a target="_blank"
               rel="noopener"
               href="{{ route(
                    'vales-combustible.imprimir',
                    $valeCombustible
               ) }}"
               class="btn btn-outline-primary">

                <i class="bi bi-printer me-1"></i>
                Imprimir

            </a>


        {{-- =================================================
             POLICÍA - ENTREGADO
        ================================================== --}}
        @elseif(
            $valeCombustible->entrega_policia_id &&
            $valeCombustible->estado === 'ENTREGADO'
        )

            <div class="d-flex gap-2 flex-wrap">

                {{-- REGISTRAR USO --}}
                <a href="{{ route(
                    'vales-combustible.edit',
                    $valeCombustible
                ) }}"
                   class="btn btn-success">

                    <i class="bi bi-pencil-square me-1"></i>
                    Registrar uso

                </a>


                {{-- ANULAR --}}
                <button type="button"
                        class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAnularPolicia">

                    <i class="bi bi-x-circle me-1"></i>
                    Anular

                </button>


                {{-- IMPRIMIR --}}
                <a target="_blank"
                   rel="noopener"
                   href="{{ route(
                        'vales-combustible.imprimir',
                        $valeCombustible
                   ) }}"
                   class="btn btn-outline-primary">

                    <i class="bi bi-printer me-1"></i>
                    Imprimir

                </a>

            </div>


            {{-- =============================================
                 MODAL ANULAR POLICÍA
            ============================================== --}}
            <div class="modal fade"
                 id="modalAnularPolicia"
                 tabindex="-1"
                 aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">

                        <form method="POST"
                              action="{{ route(
                                  'entregas-policia.cerrar',
                                  $valeCombustible
                              ) }}">

                            @csrf

                            <div class="modal-header">

                                <h5 class="modal-title">

                                    Anular vale N.º
                                    {{ sprintf(
                                        '%06d',
                                        (int) $valeCombustible->numero_vale
                                    ) }}

                                </h5>

                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Cerrar">
                                </button>

                            </div>


                            <div class="modal-body">

                                <div class="alert alert-warning">

                                    <i class="bi bi-exclamation-triangle me-1"></i>

                                    El vale será anulado y su número
                                    se conservará.

                                    <strong>
                                        No podrá reutilizarse.
                                    </strong>

                                </div>


                                <input type="hidden"
                                       name="estado"
                                       value="ANULADO">


                                <div class="mb-3">

                                    <label for="motivoPolicia"
                                           class="form-label fw-semibold">

                                        Motivo de anulación *

                                    </label>

                                    <textarea name="motivo"
                                              id="motivoPolicia"
                                              class="form-control"
                                              rows="4"
                                              maxlength="1000"
                                              placeholder="Ingrese el motivo de la anulación..."
                                              required></textarea>

                                </div>

                            </div>


                            <div class="modal-footer">

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">

                                    Cancelar

                                </button>


                                <button type="submit"
                                        class="btn btn-danger">

                                    <i class="bi bi-x-circle me-1"></i>
                                    Confirmar anulación

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>


        {{-- =================================================
             POLICÍA - UTILIZADO
        ================================================== --}}
        @elseif(
            $valeCombustible->entrega_policia_id &&
            $valeCombustible->estado === 'UTILIZADO'
        )

            <div class="d-flex gap-2 flex-wrap">

                {{-- MODIFICAR USO --}}
                <a href="{{ route(
                    'vales-combustible.edit',
                    $valeCombustible
                ) }}"
                   class="btn btn-warning">

                    <i class="bi bi-pencil-square me-1"></i>
                    Modificar uso

                </a>


                {{-- IMPRIMIR --}}
                <a target="_blank"
                   rel="noopener"
                   href="{{ route(
                        'vales-combustible.imprimir',
                        $valeCombustible
                   ) }}"
                   class="btn btn-outline-primary">

                    <i class="bi bi-printer me-1"></i>
                    Imprimir

                </a>

            </div>


        {{-- =================================================
             POLICÍA - ANULADO O DEVUELTO
        ================================================== --}}
        @elseif(
            $valeCombustible->entrega_policia_id &&
            in_array(
                $valeCombustible->estado,
                ['ANULADO', 'DEVUELTO']
            )
        )

            <a target="_blank"
               rel="noopener"
               href="{{ route(
                    'vales-combustible.imprimir',
                    $valeCombustible
               ) }}"
               class="btn btn-outline-primary">

                <i class="bi bi-printer me-1"></i>
                Imprimir

            </a>

        @endif

    </div>

</div>

@endsection
