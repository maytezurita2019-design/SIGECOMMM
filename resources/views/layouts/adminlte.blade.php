@extends('adminlte::page')

{{-- =========================================================
     TÍTULO
========================================================= --}}

@section('title')
    @yield('page_title', 'SIGECOM')
@endsection


{{-- =========================================================
     CONTENIDO PRINCIPAL
========================================================= --}}

@section('content')

    <div class="container-fluid py-3">

        {{-- MENSAJE DE ÉXITO --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">

                <i class="bi bi-check-circle-fill me-2"></i>

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
                </button>

            </div>
        @endif


        {{-- MENSAJE DE ERROR --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                <i class="bi bi-exclamation-circle-fill me-2"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
                </button>

            </div>
        @endif


        {{-- ERRORES DE VALIDACIÓN --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                <div class="fw-bold mb-1">

                    <i class="bi bi-exclamation-circle-fill me-1"></i>

                    Revise los datos ingresados

                </div>

                <ul class="mb-0 ps-3">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
                </button>

            </div>
        @endif


        {{-- CONTENIDO DE LAS VISTAS --}}
        @yield('sigecom_content')

    </div>

@endsection


{{-- =========================================================
     CSS
========================================================= --}}

@section('css')

    {{-- CSS EXISTENTE DE SIGECOM --}}
    <link
        href="{{ asset('css/institucional.css') }}"
        rel="stylesheet">

    <link
        href="{{ asset('css/sigecom-final.css') }}"
        rel="stylesheet">


    {{-- PERSONALIZACIÓN ADMINLTE --}}
    <style>

        :root {
            --sig-verde: #08712f;
            --sig-verde-hover: #065b27;
            --sig-verde-oscuro: #123524;
            --sig-verde-sidebar: #172d20;
            --sig-verde-claro: #67c487;
            --sig-fondo: #f4f6f9;
        }


        /* =====================================================
           FONDO GENERAL
        ===================================================== */

        body {
            background-color: var(--sig-fondo);
        }

        .app-content {
            background: var(--sig-fondo);
        }

        .app-content .container-fluid {
            max-width: 100%;
        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .app-sidebar {
            background:
                linear-gradient(
                    180deg,
                    #123524 0%,
                    #172d20 55%,
                    #10291b 100%
                ) !important;

            box-shadow:
                3px 0 12px rgba(0, 0, 0, .12);
        }


        /* =====================================================
           LOGO / MARCA
        ===================================================== */

        .sidebar-brand {
            min-height: 65px;

            border-bottom:
                1px solid rgba(255, 255, 255, .10) !important;
        }

        .sidebar-brand .brand-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

      .sidebar-brand .brand-image {
    width: 48px !important;
    height: 50px !important;
    object-fit: cover;
    background: #ffffff;
    padding: 1px;
    border-radius: 80% !important;
    flex-shrink: 0;
}

        .sidebar-brand .brand-text {
            color: #ffffff !important;
            font-weight: 800 !important;
            letter-spacing: .4px;
        }


        /* =====================================================
           MENÚ LATERAL
        ===================================================== */

        .sidebar-menu .nav-header {
            margin-top: 12px;
            padding-left: 16px;

            color: #8fb39a !important;

            font-size: 10px;
            font-weight: 800;
            letter-spacing: .8px;
        }

        .sidebar-menu .nav-link {
            margin: 2px 7px;
            border-radius: 7px;

            color: #d7e2da !important;

            transition:
                background-color .15s ease,
                color .15s ease;
        }

        .sidebar-menu .nav-link:hover {
            color: #ffffff !important;

            background:
                rgba(255, 255, 255, .08) !important;
        }

        .sidebar-menu .nav-link.active {
            color: #ffffff !important;

            background:
                var(--sig-verde) !important;

            box-shadow:
                0 3px 9px rgba(0, 0, 0, .18);
        }

        .sidebar-menu .nav-icon {
            color: var(--sig-verde-claro);
        }

        .sidebar-menu .nav-link.active .nav-icon {
            color: #ffffff;
        }

        .sidebar-menu .nav-treeview {
            padding-left: 4px;
        }

        .sidebar-menu .nav-treeview .nav-link {
            font-size: 13px;
        }


        /* =====================================================
           NAVBAR SUPERIOR
        ===================================================== */

        .app-header {
            min-height: 60px;

            background: #ffffff !important;

            border-bottom:
                1px solid #e3e7e4 !important;

            box-shadow:
                0 2px 7px rgba(0, 0, 0, .04);
        }

        .app-header .nav-link {
            color: #34463a;
        }

        .app-header .nav-link:hover {
            color: var(--sig-verde);
        }


        /* =====================================================
           TARJETAS
        ===================================================== */

        .card {
            border: 0;
            border-radius: 10px;

            box-shadow:
                0 1px 5px rgba(0, 0, 0, .07);
        }


        /* =====================================================
           BOTONES VERDES
        ===================================================== */

        .btn-success {
            background-color: var(--sig-verde);
            border-color: var(--sig-verde);
        }

        .btn-success:hover,
        .btn-success:focus {
            background-color: var(--sig-verde-hover);
            border-color: var(--sig-verde-hover);
        }

        .text-success {
            color: var(--sig-verde) !important;
        }


        /* =====================================================
           FORMULARIOS
        ===================================================== */

        .form-control:focus,
        .form-select:focus {
            border-color: #63b47f;

            box-shadow:
                0 0 0 .2rem rgba(8, 113, 47, .12);
        }


        /* =====================================================
           TABLAS
        ===================================================== */

        .table thead th {
            vertical-align: middle;
        }

        .table tbody td {
            vertical-align: middle;
        }


        /* =====================================================
           ALERTAS
        ===================================================== */

        .alert {
            border: 0;
            border-radius: 8px;

            box-shadow:
                0 1px 4px rgba(0, 0, 0, .06);
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 767.98px) {

            .app-content .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }

            .card {
                border-radius: 8px;
            }

        }

    </style>


    {{-- CSS PARTICULAR DE CADA VISTA --}}
    @stack('styles')

@endsection


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}

@section('js')


    {{-- CERRAR ALERTAS AUTOMÁTICAMENTE --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            document
                .querySelectorAll('.alert')
                .forEach(function (alerta) {

                    setTimeout(function () {

                        try {

                            const instancia =
                                bootstrap.Alert.getOrCreateInstance(alerta);

                            instancia.close();

                        } catch (error) {

                            console.log(error);

                        }

                    }, 3000);

                });

        });

    </script>


    {{-- VALIDACIONES Y BOTÓN PROCESANDO --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            document
                .querySelectorAll('form')
                .forEach(function (form) {

                    form.addEventListener('submit', function (e) {

                        if (!form.checkValidity()) {

                            e.preventDefault();
                            e.stopPropagation();

                            form.classList.add('was-validated');

                            return;
                        }


                        /*
                         * No modificar formularios pertenecientes
                         * al menú lateral de AdminLTE.
                         */

                        if (form.closest('.sidebar-menu')) {
                            return;
                        }


                        form
                            .querySelectorAll('button[type="submit"]')
                            .forEach(function (btn) {

                                btn.disabled = true;

                                if (!btn.dataset.original) {
                                    btn.dataset.original = btn.innerHTML;
                                }

                                btn.innerHTML =
                                    '<span class="spinner-border spinner-border-sm me-1"></span>' +
                                    'Procesando...';

                            });

                    });

                });


            /*
             * CAMPOS AUTOMÁTICAMENTE EN MAYÚSCULAS
             */

            document
                .querySelectorAll('[data-uppercase]')
                .forEach(function (el) {

                    el.addEventListener('input', function () {

                        const posicion = el.selectionStart;

                        el.value = el.value.toUpperCase();

                        try {

                            el.setSelectionRange(
                                posicion,
                                posicion
                            );

                        } catch (error) {}

                    });

                });

        });

    </script>


    {{-- JAVASCRIPT PARTICULAR DE CADA VISTA --}}
    @stack('scripts')

@endsection