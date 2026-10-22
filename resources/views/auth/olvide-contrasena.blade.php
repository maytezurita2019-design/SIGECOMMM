<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>¿Olvidaste tu contraseña? - SIGECOM</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: #f4f6f9;

            font-family: Arial, sans-serif;
        }


        /* TARJETA PRINCIPAL */

        .recuperacion-card {
            position: relative;

            width: 100%;
            max-width: 430px;

            padding: 55px 35px 30px;

            background: #ffffff;

            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .12);
        }


        /* CÍRCULO SUPERIOR */

        .icono-superior {
            position: absolute;

            width: 82px;
            height: 82px;

            top: -41px;
            left: 50%;

            transform: translateX(-50%);

            display: flex;
            justify-content: center;
            align-items: center;

            background: #ffffff;

            border-radius: 50%;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, .12);

            font-size: 28px;

            color: #17211b;
        }


        /* ENCABEZADO */

        .titulo {
            text-align: center;

            font-size: 22px;

            font-weight: 700;

            margin-bottom: 5px;
        }

        .subtitulo {
            text-align: center;

            font-size: 12px;

            color: #6c757d;

            margin-bottom: 22px;
        }


        /* CUADRO INTERIOR */

        .contenido-recuperacion {
            background: #f7f8fa;

            border-radius: 12px;

            padding: 24px;
        }


        .icono-usuario {
            text-align: center;

            font-size: 30px;

            color: #17211b;

            margin-bottom: 8px;
        }


        .contenido-recuperacion h5 {
            text-align: center;

            font-size: 16px;

            font-weight: 700;

            margin-bottom: 7px;
        }


        .descripcion {
            text-align: center;

            color: #6c757d;

            font-size: 13px;

            line-height: 1.5;

            margin-bottom: 20px;
        }


        /* FORMULARIO */

        .form-label {
            font-size: 13px;

            font-weight: 600;

            color: #343a40;
        }


        .form-control {
            min-height: 44px;

            border-radius: 7px;
        }


        .btn-enviar {
            width: 100%;

            min-height: 44px;

            font-weight: 600;
        }


        .btn-volver {
            width: 100%;

            margin-top: 10px;
        }


        /* MENSAJES */

        .alert {
            font-size: 13px;

            padding: 10px 12px;
        }

    </style>

</head>


<body>


<div class="recuperacion-card">


    <!-- ICONO SUPERIOR -->

    <div class="icono-superior">

        <i class="bi bi-key-fill"></i>

    </div>


    <!-- TÍTULO -->

    <h1 class="titulo">
        ¿Olvidaste tu contraseña?
    </h1>


    <div class="subtitulo">
        Sistema de Gestión y Control de Combustible
    </div>


    <!-- MENSAJE DE SOLICITUD ENVIADA -->

    @if(session('estado'))

        <div class="alert alert-success">

            <i class="bi bi-check-circle-fill me-1"></i>

            {{ session('estado') }}

        </div>

    @endif


    <!-- ERRORES -->

    @if($errors->any())

        <div class="alert alert-danger">

            <i class="bi bi-exclamation-triangle-fill me-1"></i>

            Revise los datos ingresados.

        </div>

    @endif


    <div class="contenido-recuperacion">


        <!-- ICONO -->

        <div class="icono-usuario">

            <i class="bi bi-person-gear"></i>

        </div>


        <h5>
            Solicita el restablecimiento
        </h5>


        <p class="descripcion">

            Ingresa tu correo electrónico y tu CI.

            Si los datos corresponden a una cuenta activa,
            se enviará una solicitud al administrador.

        </p>


        <!-- FORMULARIO -->

        <form
            action="{{ route('password.solicitar') }}"
            method="POST">

            @csrf


            <!-- CORREO -->

            <div class="mb-3">

                <label
                    for="correo"
                    class="form-label">

                    Correo electrónico

                </label>


                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-envelope"></i>

                    </span>


                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        class="form-control @error('correo') is-invalid @enderror"
                        value="{{ old('correo') }}"
                        placeholder="Ingrese su correo"
                        required>

                </div>


                @error('correo')

                    <div class="text-danger small mt-1">

                        {{ $message }}

                    </div>

                @enderror

            </div>


            <!-- CI -->

            <div class="mb-3">

                <label
                    for="ci"
                    class="form-label">

                    Cédula de identidad (CI)

                </label>


                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-person-vcard"></i>

                    </span>


                    <input
                        type="text"
                        id="ci"
                        name="ci"
                        class="form-control @error('ci') is-invalid @enderror"
                        value="{{ old('ci') }}"
                        placeholder="Ingrese su CI"
                        required>

                </div>


                @error('ci')

                    <div class="text-danger small mt-1">

                        {{ $message }}

                    </div>

                @enderror

            </div>


            <!-- ENVIAR -->

            <button
                type="submit"
                class="btn btn-primary btn-enviar">

                <i class="bi bi-send-fill me-1"></i>

                Enviar solicitud

            </button>


            <!-- VOLVER -->

            <a
                href="{{ route('login') }}"
                class="btn btn-outline-secondary btn-volver">

                <i class="bi bi-arrow-left me-1"></i>

                Volver al inicio de sesión

            </a>


        </form>

    </div>

</div>


</body>

</html>