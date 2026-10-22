@extends('layouts.app')

@section('title', 'Nuevo vehículo')

@section('content')

<h2>Nuevo vehículo</h2>
<p class="text-muted">Registrar vehículo del parque automotor</p>

<div class="card shadow-sm">
<div class="card-body">

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('vehiculos.store') }}" method="POST">
@csrf

<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">Código *</label>

        <input type="text"
               name="codigo"
               class="form-control"
               data-uppercase
               pattern="[A-Za-z0-9._/\-]+"
               maxlength="30"
               value="{{ old('codigo') }}"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Placa *</label>

        <input type="text"
               name="placa"
               class="form-control"
               data-uppercase
               pattern="[A-Za-z0-9 ./\-]+"
               maxlength="20"
               value="{{ old('placa') }}"
               required>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Tipo</label>

        <input type="text"
               name="tipo"
               class="form-control"
               value="{{ old('tipo') }}"
               placeholder="Ej.: CAMIONETA">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Industria</label>

        <input type="text"
               name="industria"
               class="form-control"
               value="{{ old('industria') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Marca</label>

        <input type="text"
               name="marca"
               class="form-control"
               value="{{ old('marca') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Color</label>

        <input type="text"
               name="color"
               class="form-control"
               value="{{ old('color') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Unidad *</label>

        <select name="unidad_id"
                class="form-select"
                required>

            <option value="">Seleccione...</option>

            @foreach($unidades as $unidad)
                <option value="{{ $unidad->id }}"
                    {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>

                    {{ $unidad->nombre }}

                    @if($unidad->sigla)
                        - {{ $unidad->sigla }}
                    @endif

                </option>
            @endforeach
        </select>
    </div>


    {{-- ASIGNACIÓN PRESUPUESTARIA --}}
    <div class="col-12 mt-2 mb-2">
        <hr>

        <h5 class="mb-1">
            Asignación presupuestaria
        </h5>

        <p class="text-muted small mb-3">
            Seleccione la Apertura Programática correspondiente al vehículo.
            La FF y OF se obtienen automáticamente de la configuración.
        </p>
    </div>


    <div class="col-md-8 mb-3">

        <label class="form-label">
            Apertura Programática / FF / OF *
        </label>

        <select name="apertura_programatica_id"
                id="apertura_programatica_id"
                class="form-select"
                required>

            <option value="">Seleccione...</option>

            @foreach($aperturas as $apertura)

                <option
                    value="{{ $apertura->id }}"

                    data-ap="{{ $apertura->codigo }}"

                    data-ff="{{ $apertura->fuenteFinanciamiento?->codigo ?? '' }}"

                    data-of="{{ $apertura->organismoFinanciador?->codigo ?? '' }}"

                    {{ old('apertura_programatica_id') == $apertura->id ? 'selected' : '' }}
                >

                    AP: {{ $apertura->codigo }}
                    |
                    FF: {{ $apertura->fuenteFinanciamiento?->codigo ?? 'Sin FF' }}
                    |
                    OF: {{ $apertura->organismoFinanciador?->codigo ?? 'Sin OF' }}

                    @if($apertura->descripcion)
                        | {{ $apertura->descripcion }}
                    @endif

                </option>

            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Resumen
        </label>

        <div class="border rounded p-2 bg-light">

            <div>
                <strong>AP:</strong>
                <span id="resumen_ap">-</span>
            </div>

            <div>
                <strong>FF:</strong>
                <span id="resumen_ff">-</span>
            </div>

            <div>
                <strong>OF:</strong>
                <span id="resumen_of">-</span>
            </div>

        </div>

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Estado *
        </label>

        <select name="estado"
                class="form-select"
                required>

            <option value="1"
                {{ old('estado', '1') == '1' ? 'selected' : '' }}>
                Activo
            </option>

            <option value="0"
                {{ old('estado') == '0' ? 'selected' : '' }}>
                Inactivo
            </option>

        </select>

    </div>

</div>


<button class="btn btn-primary">
    <i class="bi bi-save me-1"></i>
    Guardar
</button>

<a href="{{ route('vehiculos.index') }}"
   class="btn btn-secondary">
    Cancelar
</a>

</form>

</div>
</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const select =
        document.getElementById('apertura_programatica_id');

    const ap =
        document.getElementById('resumen_ap');

    const ff =
        document.getElementById('resumen_ff');

    const of =
        document.getElementById('resumen_of');


    function actualizarResumen() {

        const opcion =
            select.options[select.selectedIndex];

        if (!opcion || !opcion.value) {

            ap.textContent = '-';
            ff.textContent = '-';
            of.textContent = '-';

            return;
        }

        ap.textContent =
            opcion.dataset.ap || '-';

        ff.textContent =
            opcion.dataset.ff || '-';

        of.textContent =
            opcion.dataset.of || '-';
    }


    select.addEventListener(
        'change',
        actualizarResumen
    );

    actualizarResumen();

});

</script>

@endpush