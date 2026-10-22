<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class InterfazController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard.index', ['titulo' => 'Resumen general']);
    }

    public function seccion(string $modulo, string $seccion): View
    {
        $datos = config('interfaz')[$modulo] ?? null;
        abort_unless($datos && isset($datos['secciones'][$seccion]), 404);

        return view('interfaz.seccion', [
            'titulo' => $datos['secciones'][$seccion], 'grupo' => $datos['titulo'],
            'icono' => $datos['icono'], 'modulo' => $modulo, 'secciones' => $datos['secciones'],
            'seccion' => $seccion,
        ]);
    }
}
