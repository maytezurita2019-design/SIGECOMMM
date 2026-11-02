
@php
    $rolActual = mb_strtoupper(trim(auth()->user()->rol->nombre ?? ''));

    $puedeOperar = in_array(
        $rolActual,
        ['TECNICO_I', 'TECNICO_II'],
        true
    );

    $puedeConsultar = in_array(
        $rolActual,
        ['ADMINISTRADOR', 'TECNICO_I', 'TECNICO_II'],
        true
    );

    $esGams = !$vale->entrega_policia_id;
    $esPolicia = (bool) $vale->entrega_policia_id;
@endphp

<div class="acciones-vale">

    {{-- VER --}}
    @if($puedeConsultar)
        <a href="{{ route('vales-combustible.show', $vale) }}"
           class="btn btn-sm btn-sig-outline">
            <i class="bi bi-eye me-1"></i>
            Ver
        </a>
    @endif

    {{-- IMPRIMIR: disponible para los tres roles --}}
    @if(
        $puedeConsultar &&
        $esGams &&
        in_array($vale->estado, ['EMITIDO', 'COMPLETADO'], true)
    )
        <a href="{{ route('vales-combustible.imprimir', $vale) }}"
           target="_blank"
           class="btn btn-sm btn-sig-outline">
            <i class="bi bi-printer me-1"></i>
            Imprimir
        </a>
    @endif

    {{-- ACCIONES EXCLUSIVAS DE LOS TÉCNICOS --}}
    @if($puedeOperar)

        {{-- VALE GAMS EMITIDO --}}
        @if($esGams && $vale->estado === 'EMITIDO')

            <a href="{{ route('vales-combustible.edit', $vale) }}"
               class="btn btn-sm btn-outline-primary"
               title="Editar vale">
                <i class="bi bi-pencil-square me-1"></i>
                Editar
            </a>

            <a href="{{ route('vales-combustible.completar.formulario', $vale) }}"
               class="btn btn-sm btn-sig">
                <i class="bi bi-check2-circle me-1"></i>
                Completar
            </a>

        {{-- VALE GAMS COMPLETADO --}}
        @elseif($esGams && $vale->estado === 'COMPLETADO')

            <a href="{{ route('vales-combustible.edit', $vale) }}"
               class="btn btn-sm btn-outline-primary"
               title="Corregir datos del vale">
                <i class="bi bi-pencil-square me-1"></i>
                Editar
            </a>

        {{-- VALE POLICÍA ENTREGADO --}}
        @elseif($esPolicia && $vale->estado === 'ENTREGADO')

            <a href="{{ route('vales-combustible.edit', $vale) }}"
               class="btn btn-sm btn-success">
                <i class="bi bi-pencil-square me-1"></i>
                Registrar uso
            </a>

        {{-- VALE POLICÍA UTILIZADO --}}
        @elseif($esPolicia && $vale->estado === 'UTILIZADO')

            <a href="{{ route('vales-combustible.edit', $vale) }}"
               class="btn btn-sm btn-outline-primary"
               title="Corregir datos registrados">
                <i class="bi bi-pencil-square me-1"></i>
                Editar
            </a>

        @endif

    @endif

</div>
