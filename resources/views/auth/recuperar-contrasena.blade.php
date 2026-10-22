<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recuperar contraseña | GAM Sacaba</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f4f6f9;
            padding: 20px;
        }

        .tarjeta {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            padding: 80px 35px 35px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .circulo {
            position: absolute;
            width: 135px;
            height: 135px;
            border-radius: 50%;
            top: -67px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 5px solid #f4f6f9;

            display: flex;
            justify-content: center;
            align-items: center;

            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .circulo i {
            font-size: 45px;
        }

        .auto {
            margin-right: 5px;
        }

        .combustible {
            margin-left: 5px;
        }

        .titulo {
            text-align: center;
            margin-bottom: 25px;
        }

        .titulo h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .formulario {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
        }

        .btn-enviar {
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }

        .volver {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="tarjeta">

    <div class="circulo">
        <i class="bi bi-car-front-fill auto"></i>
        <i class="bi bi-fuel-pump-fill combustible"></i>
    </div>

    <div class="titulo">
        <h3>Recuperar contraseña</h3>

        <p class="text-muted mb-0">
            Sistema de Control de Combustible
        </p>
    </div>

    @if (session('estado'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('estado') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="formulario">

        <p class="text-muted text-center">
            Ingresa el correo registrado en el sistema.
            Te enviaremos un enlace para crear una nueva contraseña.
        </p>

        <form action="{{ route('password.email') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label for="correo" class="form-label">
                    Correo electrónico
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope-fill"></i>
                    </span>

                    <input
                        type="email"
                        name="correo"
                        id="correo"
                        class="form-control"
                        value="{{ old('correo') }}"
                        placeholder="Ingrese su correo"
                        required>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-primary btn-enviar">

                <i class="bi bi-send-fill me-1"></i>
                Enviar enlace de recuperación

            </button>

        </form>

    </div>

    <div class="volver">

        <a href="{{ route('login') }}"
           class="text-decoration-none">

            <i class="bi bi-arrow-left me-1"></i>
            Volver al inicio de sesión

        </a>

    </div>

</div>

</body>
</html>