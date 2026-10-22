<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | GAM Sacaba</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        /* =====================================================
           CONFIGURACIÓN GENERAL
        ===================================================== */

        * {
            box-sizing: border-box;
        }

        :root {
            --verde: #08712f;
            --verde-medio: #159447;
            --verde-oscuro: #03451f;
            --verde-claro: #eaf7ee;
            --verde-muy-claro: #f4fbf6;
            --blanco: #ffffff;
            --gris: #667085;
            --borde: #d4e5da;
        }

        html,
        body {
            min-height: 100%;
        }

        /* =====================================================
           FONDO DEL PARQUE AUTOMOTOR
        ===================================================== */

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 105px 20px 50px;
            overflow-x: hidden;

            font-family: Arial, Helvetica, sans-serif;

            background-image:
                url("{{ asset('images/fondo-parque-automotor.png') }}");

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* =====================================================
           CAPA VERDE TRANSPARENTE
        ===================================================== */

        body::before {
            content: "";
            position: fixed;
            inset: 0;

            background:
                linear-gradient(
                    135deg,
                    rgba(0, 70, 30, .48),
                    rgba(8, 113, 47, .36),
                    rgba(0, 65, 28, .50)
                );

            pointer-events: none;
            z-index: 0;
        }

        /* =====================================================
           ILUMINACIÓN DETRÁS DEL LOGIN
        ===================================================== */

        body::after {
            content: "";
            position: fixed;

            width: 760px;
            height: 760px;

            top: 50%;
            left: 50%;

            transform: translate(-50%, -50%);
            border-radius: 50%;

            background:
                radial-gradient(
                    circle,
                    rgba(255, 255, 255, .20) 0%,
                    rgba(255, 255, 255, .10) 30%,
                    rgba(255, 255, 255, .03) 50%,
                    transparent 70%
                );

            pointer-events: none;
            z-index: 1;
        }

        /* =====================================================
           TARJETA PRINCIPAL
        ===================================================== */

        .login-card {
            position: relative;

            width: 100%;
            max-width: 535px;

            padding: 100px 45px 30px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255, 255, 255, .98),
                    rgba(246, 252, 248, .96)
                );

            border: 1px solid rgba(255, 255, 255, .95);
            border-radius: 28px;

            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            box-shadow:
                0 35px 75px rgba(0, 45, 20, .32),
                0 12px 30px rgba(0, 0, 0, .14),
                inset 1px 1px 2px rgba(255, 255, 255, 1),
                inset -1px -1px 2px rgba(8, 113, 47, .08);

            z-index: 10;
        }

        .login-card::after {
            content: "";

            position: absolute;

            left: 18%;
            bottom: -3px;

            width: 64%;
            height: 4px;

            border-radius: 50%;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    var(--verde),
                    transparent
                );

            box-shadow:
                0 0 12px rgba(8, 113, 47, .30);
        }

        /* =====================================================
           CÍRCULO SUPERIOR
        ===================================================== */

        .circulo {
            position: absolute;

            width: 145px;
            height: 145px;

            top: -73px;
            left: 50%;

            transform: translateX(-50%);

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background:
                radial-gradient(
                    circle at 35% 25%,
                    #ffffff 0%,
                    #ffffff 38%,
                    #eef8f1 72%,
                    #dcefe2 100%
                );

            border: 3px solid var(--verde);

            box-shadow:
                0 15px 30px rgba(0, 70, 30, .25),
                inset 8px 8px 15px rgba(255, 255, 255, 1),
                inset -8px -10px 18px rgba(8, 113, 47, .10),
                0 0 0 7px rgba(255, 255, 255, .72);
        }

        .circulo .auto {
            font-size: 47px;
            color: var(--verde-oscuro);

            filter:
                drop-shadow(0 3px 4px rgba(0, 0, 0, .12));
        }

        .circulo .combustible {
            margin-left: -5px;

            font-size: 41px;
            color: var(--verde-medio);

            filter:
                drop-shadow(0 3px 4px rgba(0, 0, 0, .10));
        }

        /* =====================================================
           ENCABEZADO
        ===================================================== */

        .encabezado {
            margin-bottom: 28px;
            text-align: center;
        }

        .encabezado h2 {
            margin: 0;

            color: var(--verde-oscuro);

            font-size: 30px;
            font-weight: 800;

            letter-spacing: .2px;
        }

        .encabezado p {
            margin: 8px 0 0;

            color: #65756b;
            font-size: 14px;
        }

        .encabezado small {
            display: block;

            margin-top: 4px;

            color: #718078;
            font-size: 12px;
        }

        .linea {
            width: 55px;
            height: 4px;

            margin: 15px auto 0;

            border-radius: 20px;

            background:
                linear-gradient(
                    90deg,
                    var(--verde-medio),
                    var(--verde)
                );

            box-shadow:
                0 3px 8px rgba(8, 113, 47, .20);
        }

        /* =====================================================
           FORMULARIO INTERIOR
        ===================================================== */

        .formulario {
            padding: 27px;

            background: rgba(255, 255, 255, .95);

            border: 1px solid #dce8e0;
            border-radius: 20px;

            box-shadow:
                0 10px 28px rgba(3, 69, 31, .08),
                inset 0 1px 1px #ffffff;
        }

        .campo {
            margin-bottom: 21px;
        }

        .campo label {
            display: block;

            margin-bottom: 8px;

            color: var(--verde-oscuro);

            font-size: 14px;
            font-weight: 700;
        }

        /* =====================================================
           CAMPOS
        ===================================================== */

        .input-group {
            border-radius: 11px;
            transition: .25s;
        }

        .input-group:focus-within {
            box-shadow:
                0 0 0 3px rgba(8, 113, 47, .10),
                0 5px 15px rgba(8, 113, 47, .08);
        }

        .input-group-text {
            width: 50px;

            display: flex;
            justify-content: center;

            color: var(--verde);

            background: var(--verde-muy-claro);

            border: 1px solid #c9ddd0;
        }

        .form-control {
            height: 51px;

            color: #26352b;

            background: white;

            border: 1px solid #c9ddd0;
            border-left: 0;
        }

        .form-control::placeholder {
            color: #98a39c;
        }

        .form-control:focus {
            color: #26352b;

            background: white;

            border-color: var(--verde-medio);

            box-shadow: none;
        }

        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #26352b;

            -webkit-box-shadow:
                0 0 0 1000px white inset;

            transition:
                background-color 9999s ease-in-out 0s;
        }

        /* =====================================================
           MOSTRAR CONTRASEÑA
        ===================================================== */

        .mostrar-contrasena {
            width: 50px;

            color: var(--verde);

            background: var(--verde-muy-claro);

            border: 1px solid #c9ddd0;
            border-left: 0;

            border-radius: 0 8px 8px 0;

            transition: .2s;
        }

        .mostrar-contrasena:hover {
            color: white;
            background: var(--verde);
        }

        /* =====================================================
           ENLACES
        ===================================================== */

        .enlaces {
            display: flex;
            justify-content: space-between;

            gap: 15px;

            margin: 5px 0 22px;
        }

        .enlaces a {
            color: var(--verde);

            font-size: 13px;
            font-weight: 700;

            text-decoration: none;

            transition: .2s;
        }

        .enlaces a:hover {
            color: var(--verde-oscuro);
            text-decoration: underline;
        }

        /* =====================================================
           RECORDAR SESIÓN
        ===================================================== */

        .recordar {
            margin-bottom: 20px;
        }

        .form-check-label {
            color: #48574e;
            font-size: 14px;
        }

        .form-check-input {
            border-color: #a9c5b2;
        }

        .form-check-input:checked {
            background-color: var(--verde);
            border-color: var(--verde);
        }

       
       

        /* =====================================================
           BOTÓN INICIAR SESIÓN
        ===================================================== */

        .btn-login {
            width: 100%;
            height: 50px;

            color: white;

            border: 1px solid #056027;
            border-radius: 12px;

            font-weight: 700;

            background:
                linear-gradient(
                    135deg,
                    #159447,
                    #08712f,
                    #056027
                );

            box-shadow:
                0 9px 20px rgba(8, 113, 47, .28),
                inset 0 1px 1px rgba(255, 255, 255, .30);

            transition:
                transform .2s,
                box-shadow .2s,
                background .2s;
        }

        .btn-login:hover {
            color: white;

            transform: translateY(-2px);

            background:
                linear-gradient(
                    135deg,
                    #20a653,
                    #08712f
                );

            box-shadow:
                0 12px 25px rgba(8, 113, 47, .35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* =====================================================
           ALERTAS
        ===================================================== */

        .alert {
            border-radius: 12px;
            font-size: 13px;
        }

        /* =====================================================
           PIE
        ===================================================== */

        .pie-login {
            margin-top: 27px;
            padding-top: 20px;

            text-align: center;

            border-top: 1px solid #d6e5db;

            color: #718078;

            font-size: 12px;
        }

        .pie-login strong {
            display: block;

            margin-bottom: 5px;

            color: var(--verde-oscuro);

            font-size: 13px;
        }

        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 768px) {

            body {
                padding: 100px 15px 35px;
                background-attachment: scroll;
            }

            body::before {
                background: rgba(0, 75, 32, .50);
            }

            .login-card {
                max-width: 500px;
            }
        }

        @media (max-width: 576px) {

            body {
                align-items: flex-start;

                padding:
                    100px 15px 30px;
            }

            .login-card {
                padding:
                    90px 20px 25px;

                border-radius: 22px;
            }

            .circulo {
                width: 125px;
                height: 125px;

                top: -63px;
            }

            .circulo .auto {
                font-size: 40px;
            }

            .circulo .combustible {
                font-size: 35px;
            }

            .encabezado h2 {
                font-size: 25px;
            }

            .encabezado p {
                font-size: 12px;
            }

            .formulario {
                padding:
                    22px 17px;
            }

            .enlaces {
                flex-direction: column;
                gap: 9px;
            }

            
        }
    </style>





   
</head>

<body>

    <!-- =====================================================
         TARJETA LOGIN
    ====================================================== -->

    <div class="login-card">

        <!-- CÍRCULO SUPERIOR -->
        <div class="circulo">
            <i class="bi bi-car-front-fill auto"></i>
            <i class="bi bi-fuel-pump-fill combustible"></i>
        </div>

        <!-- ENCABEZADO -->
        <div class="encabezado">

            <h2>SIGECOM</h2>

            <p>
                Sistema de Gestión y Control de Combustible
            </p>

            <small>
                Gobierno Autónomo Municipal de Sacaba
            </small>

            <div class="linea"></div>
        </div>

        <!-- =====================================================
             MENSAJE CORRECTO
        ====================================================== -->

        @if (session('estado'))
            <div class="alert alert-success">

                <i class="bi bi-check-circle-fill me-1"></i>

                {{ session('estado') }}

            </div>
        @endif

        <!-- =====================================================
             ERRORES
        ====================================================== -->

        @if ($errors->any())

            <div class="alert alert-danger">

                <div class="fw-semibold mb-1">

                    <i class="bi bi-exclamation-triangle-fill me-1"></i>

                    No se pudo iniciar sesión

                </div>

                @foreach ($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif

        <!-- =====================================================
             FORMULARIO
        ====================================================== -->

        <div class="formulario">

            <form
                action="{{ route('login.procesar') }}"
                method="POST">

                @csrf

                <!-- =====================================================
                     CORREO
                ====================================================== -->

                <div class="campo">

                    <label for="correo">

                        <i class="bi bi-envelope me-1"></i>

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
                            class="form-control"
                            value="{{ old('correo') }}"
                            placeholder="Ingrese su correo"
                            autocomplete="email"
                            required>

                    </div>

                </div>

                <!-- =====================================================
                     CONTRASEÑA
                ====================================================== -->

                <div class="campo">

                    <label for="contrasena">

                        <i class="bi bi-lock me-1"></i>

                        Contraseña

                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-lock"></i>

                        </span>

                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            class="form-control"
                            placeholder="Ingrese su contraseña"
                            autocomplete="current-password"
                            required>

                        <button
                            type="button"
                            class="mostrar-contrasena"
                            id="btnMostrarContrasena"
                            title="Mostrar contraseña">

                            <i
                                class="bi bi-eye"
                                id="iconoContrasena">
                            </i>

                        </button>

                    </div>

                </div>

                <!-- =====================================================
                     ENLACES
                ====================================================== -->

                <div class="enlaces">

                    <a href="{{ route('password.request') }}">

                        <i class="bi bi-key me-1"></i>

                        ¿Olvidaste tu contraseña?

                    </a>



                </div>

                <!-- =====================================================
                     RECORDAR SESIÓN
                ====================================================== -->

                <div class="recordar">

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="recordarme"
                            name="recordarme">

                        <label
                            class="form-check-label"
                            for="recordarme">

                            Recordarme

                        </label>

                    </div>

                </div>

            

                <!-- =====================================================
                     INICIAR SESIÓN
                ====================================================== -->

                <button
                    type="submit"
                    class="btn btn-login">

                    <i class="bi bi-box-arrow-in-right me-2"></i>

                    Iniciar sesión

                </button>

            </form>

        </div>

        <!-- =====================================================
             PIE
        ====================================================== -->

        <div class="pie-login">

            <strong>
                Gobierno Autónomo Municipal de Sacaba
            </strong>

            Sacaba, Cochabamba - Bolivia

        </div>

    </div>

    <!-- =====================================================
         MOSTRAR / OCULTAR CONTRASEÑA
    ====================================================== -->

    <script>

        const boton =
            document.getElementById('btnMostrarContrasena');

        const contrasena =
            document.getElementById('contrasena');

        const icono =
            document.getElementById('iconoContrasena');

        boton.addEventListener('click', function () {

            if (contrasena.type === 'password') {

                contrasena.type = 'text';

                icono.classList.remove('bi-eye');
                icono.classList.add('bi-eye-slash');

                boton.title =
                    'Ocultar contraseña';

            } else {

                contrasena.type = 'password';

                icono.classList.remove('bi-eye-slash');
                icono.classList.add('bi-eye');

                boton.title =
                    'Mostrar contraseña';
            }

        });

    </script>

</body>

</html>