<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\SolicitudContrasena;
use Illuminate\Http\Request;

class SolicitudContrasenaController extends Controller
{
    /**
     * Mostrar formulario de "¿Olvidaste tu contraseña?"
     */
    public function mostrarFormulario()
    {
        return view('auth.olvide-contrasena');
    }

    /**
     * Registrar solicitud de cambio de contraseña.
     */
    public function enviarSolicitud(Request $request)
    {
        $datos = $request->validate([
            'correo' => [
                'required',
                'email',
                'max:150',
            ],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingrese un correo válido.',
            'correo.max' => 'El correo no puede superar los 150 caracteres.',
        ]);

        // Buscar únicamente una cuenta activa con ese correo.
        $usuario = Usuario::where('correo', $datos['correo'])
            ->where('estado', true)
            ->first();

        if ($usuario) {

            // Comprobar si ya tiene una solicitud pendiente.
            $solicitudPendiente = SolicitudContrasena::where(
                'usuario_id',
                $usuario->id
            )
                ->where('estado', 'pendiente')
                ->exists();

            // Solo crear otra solicitud si no existe una pendiente.
            if (!$solicitudPendiente) {

                SolicitudContrasena::create([
                    'usuario_id' => $usuario->id,
                    'estado' => 'pendiente',
                    'ip_solicitud' => $request->ip(),
                ]);
            }
        }

        /*
         * Mensaje genérico.
         * No indicamos si el correo existe o no,
         * para evitar revelar cuentas registradas.
         */
        return back()->with(
            'estado',
            'Si el correo corresponde a una cuenta activa, la solicitud fue registrada para revisión del administrador.'
        );
    }

    /**
     * Listado de solicitudes para el administrador.
     */
    public function index()
    {
        $solicitudes = SolicitudContrasena::with([
            'usuario',
            'atendidoPor',
        ])
            ->orderByRaw("
                CASE
                    WHEN estado = 'pendiente' THEN 1
                    WHEN estado = 'atendida' THEN 2
                    ELSE 3
                END
            ")
            ->orderByDesc('created_at')
            ->get();

        return view(
            'solicitudes_contrasena.index',
            compact('solicitudes')
        );
    }
}