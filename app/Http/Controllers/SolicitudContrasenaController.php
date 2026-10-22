<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\SolicitudContrasena;
use Illuminate\Http\Request;

class SolicitudContrasenaController extends Controller
{
    public function mostrarFormulario()
    {
        return view('auth.olvide-contrasena');
    }

    public function enviarSolicitud(Request $request)
    {
        $datos = $request->validate([
            'correo' => ['required', 'email'],
            'ci' => ['required', 'string', 'max:30'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingrese un correo válido.',
            'ci.required' => 'El CI es obligatorio.',
        ]);

        $usuario = Usuario::where('correo', $datos['correo'])
            ->where('ci', $datos['ci'])
            ->where('estado', true)
            ->first();

        if ($usuario) {
            $solicitudPendiente = SolicitudContrasena::where(
                'usuario_id',
                $usuario->id
            )
                ->where('estado', 'pendiente')
                ->exists();

            if (!$solicitudPendiente) {
                SolicitudContrasena::create([
                    'usuario_id' => $usuario->id,
                    'estado' => 'pendiente',
                    'ip_solicitud' => $request->ip(),
                ]);
            }
        }

        // Mensaje genérico para no revelar si la cuenta existe.
        return back()->with(
            'estado',
            'Si los datos corresponden a una cuenta activa, la solicitud fue registrada para revisión del administrador.'
        );
    }

    public function index()
    {
        $solicitudes = SolicitudContrasena::with('usuario')
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