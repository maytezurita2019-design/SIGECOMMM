<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $q = Auditoria::with([
            'usuario.rol'
        ])->latest('created_at');


        /*
        |--------------------------------------------------------------------------
        | BUSCADOR GENERAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('buscar')) {

            $buscar = trim($request->buscar);

            $q->where(function ($consulta) use ($buscar) {

                $consulta
                    ->where('accion', 'like', "%{$buscar}%")
                    ->orWhere('tabla', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('ip', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($usuario) use ($buscar) {

                        $usuario
                            ->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('apellido', 'like', "%{$buscar}%");

                    });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR USUARIO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('usuario_id')) {

            $q->where(
                'usuario_id',
                $request->usuario_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR ACCIÓN
        |--------------------------------------------------------------------------
        */

        if ($request->filled('accion')) {

            $q->where(
                'accion',
                $request->accion
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR MÓDULO / TABLA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('tabla')) {

            $q->where(
                'tabla',
                $request->tabla
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA DESDE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha_desde')) {

            $q->whereDate(
                'created_at',
                '>=',
                $request->fecha_desde
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA HASTA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha_hasta')) {

            $q->whereDate(
                'created_at',
                '<=',
                $request->fecha_hasta
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINACIÓN
        |--------------------------------------------------------------------------
        */

        $auditorias = $q
            ->paginate(30)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | DATOS PARA FILTROS
        |--------------------------------------------------------------------------
        */

        $usuarios = Usuario::orderBy('nombre')
            ->orderBy('apellido')
            ->get();


        $acciones = Auditoria::query()
            ->whereNotNull('accion')
            ->select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');


        $tablas = Auditoria::query()
            ->whereNotNull('tabla')
            ->select('tabla')
            ->distinct()
            ->orderBy('tabla')
            ->pluck('tabla');


        return view(
            'auditoria.index',
            compact(
                'auditorias',
                'usuarios',
                'acciones',
                'tablas'
            )
        );
    }
}
