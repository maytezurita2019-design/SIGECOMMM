<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function mostrarRegistro()
    {
        return view('auth.registro');
    }

    public function registrar(Request $request)
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
            'contrasena' => ['required', 'string', 'min:8'],
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingrese un correo válido.',
            'correo.unique' => 'Este correo ya está registrado.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // El usuario que se registra NO será administrador.
        $rol = Rol::where('nombre', 'Usuario')->first();

        if (!$rol) {
            return back()
                ->withErrors([
                    'registro' => 'No existe el rol Usuario. Primero debe crearlo el administrador.',
                ])
                ->withInput();
        }

        Usuario::create([
            'correo' => $datos['correo'],
            'contrasena' => $datos['contrasena'],
            'rol_id' => $rol->id,
            'estado' => true,
        ]);

        return redirect()
            ->route('login')
            ->with('estado', 'Cuenta creada correctamente. Ya puede iniciar sesión.');
    }
}