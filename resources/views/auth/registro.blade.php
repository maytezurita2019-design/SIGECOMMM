<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta | GAM Sacaba</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}"
          rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f4f6f9;
            padding: 30px 20px;
        }

        .tarjeta {
            width: 100%;
            max-width: 550px;
            background: white;
            border-radius: 20px;
            padding: 80px 35px 35px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            margin-top: 60px;
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

        .formulario {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
        }

        .btn-crear {
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

    @if ($errors->any())
        <div class="alert alert-danger">

            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach

        </div>
    @endif

    <div class="formulario">

        <form action="{{ route('registro.procesar') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label">
                    Nombre
                </label>

                <input type="text"
                       name="nombre"
                       id="nombre"
                       class="form-control"
                       value="{{ old('nombre') }}"
                       required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">
                    Apellido
                </label>

                <input type="text"
                       name="apellido"
                       id="apellido"
                       class="form-control"
                       value="{{ old('apellido') }}"
                       required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">
                    Teléfono
                </label>

                <input type="text"
                       name="telefono"
                       id="telefono"
                       class="form-control"
                       value="{{ old('telefono') }}">
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">
                    Correo electrónico
                </label>

                <input type="email"
                       name="correo"
                       id="correo"
                       class="form-control"
                       value="{{ old('correo') }}"
                       required>
            </div>

            <div class="mb-3">
                <label for="contrasena" class="form-label">
                    Contraseña
                </label>

                <input type="password"
                       name="contrasena"
                       id="contrasena"
                       class="form-control"
                       minlength="8"
                       required>

                <small class="text-muted">
                    Mínimo 8 caracteres.
                </small>
            </div>

            <div class="mb-3">
                <label for="contrasena_confirmation" class="form-label">
                    Confirmar contraseña
                </label>

                <input type="password"
                       name="contrasena_confirmation"
                       id="contrasena_confirmation"
                       class="form-control"
                       minlength="8"
                       required>
            </div>

            <button type="submit"
                    class="btn btn-primary btn-crear">

                <i class="bi bi-person-plus-fill me-1"></i>
                Crear cuenta

            </button>

        </form>

    </div>

    <div class="volver">

        <span class="text-muted">
            ¿Ya tienes una cuenta?
        </span>

        <a href="{{ route('login') }}"
           class="text-decoration-none fw-semibold">

            Iniciar sesión

        </a>

    </div>

</div>

</body>
</html>