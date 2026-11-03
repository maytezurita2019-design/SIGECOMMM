<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class AutenticacionController extends Controller
{
    public function mostrarLogin()
    {
        Log::info('SIGECOM_DIAG_RETORNO', [
        'autenticado' => Auth::check(),
        'usuario_id' => Auth::id(),
        'sesion' => substr(hash('sha256', request()->session()->getId()), 0, 12),
        'driver' => config('session.driver'),
    ]);

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

        $usuario = Usuario::where('correo', $datos['correo'])->first();

        if (!$usuario) {
            return back()
                ->withErrors([
                    'correo' => 'El correo electrónico no está registrado.',
                ])
                ->onlyInput('correo');
        }

        $recordarme = $request->boolean('recordarme');

        if (Auth::attempt([
            'correo' => $datos['correo'],
            'password' => $datos['contrasena'],
            'estado' => true,
        ], $recordarme)) {

           $request->session()->regenerate();

Log::info('SIGECOM_DIAG_LOGIN', [
    'autenticado' => Auth::check(),
    'usuario_id' => Auth::id(),
    'sesion' => substr(hash('sha256', $request->session()->getId()), 0, 12),
    'driver' => config('session.driver'),
]);

return redirect()->route('dashboard');
        }

        return back()
            ->withErrors([
                'contrasena' => 'La contraseña es incorrecta.',
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