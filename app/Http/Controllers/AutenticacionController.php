<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutenticacionController extends Controller
{
    public function mostrarLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $datos = $request->validate(
            [
                'correo' => ['required', 'email'],
                'contrasena' => ['required', 'string'],
            ],
            [
                'correo.required' => 'El correo es obligatorio.',
                'correo.email' => 'Ingrese un correo válido.',
                'contrasena.required' => 'La contraseña es obligatoria.',
            ]
        );

        $recordarme = $request->boolean('recordarme');

        if (Auth::attempt([
            'correo' => $datos['correo'],
            'password' => $datos['contrasena'],
            'estado' => true,
        ], $recordarme)) {

            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors([
                'correo' => 'El correo o la contraseña son incorrectos.',
            ])
            ->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}