@php
    $rolActual = mb_strtoupper(auth()->user()->rol->nombre ?? '');

    $puedeOperar =
        str_contains($rolActual, 'ADMIN') ||
        str_contains($rolActual, 'AUX');

    $puedeEliminar =
        str_contains($rolActual, 'ADMIN') ||
        str_contains($rolActual, 'AUX');
@endphp


<div class="acciones-vale">

    {{-- VER --}}
    <a href="{{ route('vales-combustible.show', $vale) }}"
       class="btn btn-sm btn-sig-outline">

        <i class="bi bi-eye me-1"></i>
        Ver

    </a>


    {{-- ===================================================== --}}
    {{-- VALE GAMS PENDIENTE --}}
    {{-- ===================================================== --}}
    @if(
        $puedeOperar &&
        !$vale->entrega_policia_id &&
        $vale->estado === 'EMITIDO'
    )

        {{-- EDITAR --}}
        <a href="{{ route('vales-combustible.edit', $vale) }}"
           class="btn btn-sm btn-outline-primary"
           title="Editar vale">

            <i class="bi bi-pencil-square me-1"></i>
            Editar

        </a>


        {{-- COMPLETAR --}}
        <a href="{{ route('vales-combustible.completar.formulario', $vale) }}"
           class="btn btn-sm btn-sig">

            <i class="bi bi-check2-circle me-1"></i>
            Completar

        </a>


        {{-- IMPRIMIR --}}
        <a href="{{ route('vales-combustible.imprimir', $vale) }}"
           target="_blank"
           class="btn btn-sm btn-sig-outline">

            <i class="bi bi-printer me-1"></i>
            Imprimir

        </a>


        {{-- ELIMINAR --}}
        @if($puedeEliminar)

            <form
                action="{{ route('vales-combustible.destroy', $vale) }}"
                method="POST"
                class="d-inline"
                onsubmit="return confirm('¿Está seguro de eliminar este vale? Utilice esta opción únicamente cuando el conductor haya informado que ya no realizará la carga de combustible. El número del vale no volverá a utilizarse.');"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-sm btn-outline-danger"
                    title="Eliminar vale"
                >

                    <i class="bi bi-trash me-1"></i>
                    Eliminar

                </button>

            </form>

        @endif


    {{-- ===================================================== --}}
    {{-- VALE GAMS COMPLETADO --}}
    {{-- ===================================================== --}}
    @elseif(
        $puedeOperar &&
        !$vale->entrega_policia_id &&
        $vale->estado === 'COMPLETADO'
    )

        {{-- EDITAR --}}
        <a href="{{ route('vales-combustible.edit', $vale) }}"
           class="btn btn-sm btn-outline-primary"
           title="Corregir datos del vale">

            <i class="bi bi-pencil-square me-1"></i>
            Editar

        </a>


        {{-- IMPRIMIR --}}
        <a href="{{ route('vales-combustible.imprimir', $vale) }}"
           target="_blank"
           class="btn btn-sm btn-sig-outline">

            <i class="bi bi-printer me-1"></i>
            Imprimir

        </a>


    {{-- ===================================================== --}}
    {{-- VALE POLICÍA ENTREGADO --}}
    {{-- ===================================================== --}}
    @elseif(
        $puedeOperar &&
        $vale->entrega_policia_id &&
        $vale->estado === 'ENTREGADO'
    )

        <a href="{{ route('vales-combustible.edit', $vale) }}"
           class="btn btn-sm btn-success">

            <i class="bi bi-pencil-square me-1"></i>
            Registrar uso

        </a>


    {{-- ===================================================== --}}
    {{-- VALE POLICÍA UTILIZADO --}}
    {{-- ===================================================== --}}
    @elseif(
        $puedeOperar &&
        $vale->entrega_policia_id &&
        $vale->estado === 'UTILIZADO'
    )

        <a href="{{ route('vales-combustible.edit', $vale) }}"
           class="btn btn-sm btn-outline-primary"
           title="Corregir datos registrados">

            <i class="bi bi-pencil-square me-1"></i>
            Editar

        </a>

    @endif

</div>