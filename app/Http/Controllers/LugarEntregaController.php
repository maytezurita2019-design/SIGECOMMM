<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\LugarEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LugarEntregaController extends Controller
{
    public function index()
    {
        $lugares = LugarEntrega::query()
            ->orderBy('nombre')
            ->paginate(20);

        return view('lugares_entrega.index', compact('lugares'));
    }

    public function store(Request $request)
    {
        $datos = $this->validarNombre($request);

        DB::transaction(function () use ($datos, $request) {
            $lugar = LugarEntrega::create([
                'nombre' => $datos['nombre'],
                'estado' => true,
            ]);

            Auditoria::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'CREAR',
                'tabla' => 'lugares_entrega',
                'registro_id' => $lugar->id,
                'datos_nuevos' => ['nombre' => $lugar->nombre, 'estado' => true],
                'ip' => $request->ip(),
                'descripcion' => 'Se creó el lugar de entrega "' . $lugar->nombre . '".',
            ]);
        });

        return redirect()->route('lugares-entrega.index')
            ->with('success', 'Lugar de entrega creado correctamente.');
    }

    public function update(Request $request, LugarEntrega $lugarEntrega)
    {
        abort_unless($lugarEntrega->estado, 409, 'No se puede modificar un lugar de entrega inactivo.');

        $datos = $this->validarNombre($request, $lugarEntrega);
        $anterior = $lugarEntrega->nombre;

        DB::transaction(function () use ($datos, $request, $lugarEntrega, $anterior) {
            $lugarEntrega->update(['nombre' => $datos['nombre']]);

            if ($lugarEntrega->wasChanged('nombre')) {
                Auditoria::create([
                    'usuario_id' => $request->user()->id,
                    'accion' => 'MODIFICAR',
                    'tabla' => 'lugares_entrega',
                    'registro_id' => $lugarEntrega->id,
                    'datos_anteriores' => ['nombre' => $anterior],
                    'datos_nuevos' => ['nombre' => $lugarEntrega->nombre],
                    'ip' => $request->ip(),
                    'descripcion' => 'Se modificó el lugar de entrega "' . $anterior . '".',
                ]);
            }
        });

        return redirect()->route('lugares-entrega.index')
            ->with('success', 'Lugar de entrega actualizado correctamente.');
    }

    public function desactivar(Request $request, LugarEntrega $lugarEntrega)
    {
        DB::transaction(function () use ($request, $lugarEntrega) {
            $lugar = LugarEntrega::query()
                ->lockForUpdate()
                ->findOrFail($lugarEntrega->id);

            abort_unless($lugar->estado, 409, 'El lugar de entrega ya está inactivo.');

            $lugar->update(['estado' => false]);

            Auditoria::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'DESACTIVAR',
                'tabla' => 'lugares_entrega',
                'registro_id' => $lugar->id,
                'datos_anteriores' => ['nombre' => $lugar->nombre, 'estado' => true],
                'datos_nuevos' => ['nombre' => $lugar->nombre, 'estado' => false],
                'ip' => $request->ip(),
                'descripcion' => 'Se desactivó el lugar de entrega "' . $lugar->nombre . '".',
            ]);
        });

        return redirect()->route('lugares-entrega.index')
            ->with('success', 'Lugar de entrega desactivado correctamente.');
    }

    public function reactivar(Request $request, LugarEntrega $lugarEntrega)
    {
        DB::transaction(function () use ($request, $lugarEntrega) {
            $lugar = LugarEntrega::query()
                ->lockForUpdate()
                ->findOrFail($lugarEntrega->id);

            abort_unless(!$lugar->estado, 409, 'El lugar de entrega ya está activo.');

            $lugar->update(['estado' => true]);

            Auditoria::create([
                'usuario_id' => $request->user()->id,
                'accion' => 'REACTIVAR',
                'tabla' => 'lugares_entrega',
                'registro_id' => $lugar->id,
                'datos_anteriores' => ['nombre' => $lugar->nombre, 'estado' => false],
                'datos_nuevos' => ['nombre' => $lugar->nombre, 'estado' => true],
                'ip' => $request->ip(),
                'descripcion' => 'Se reactivó el lugar de entrega "' . $lugar->nombre . '".',
            ]);
        });

        return redirect()->route('lugares-entrega.index')
            ->with('success', 'Lugar de entrega reactivado correctamente.');
    }

    private function validarNombre(Request $request, ?LugarEntrega $lugarEntrega = null): array
    {
        $request->merge([
            'nombre' => preg_replace('/\s+/u', ' ', trim((string) $request->input('nombre'))),
        ]);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
        ]);

        $nombreNormalizado = $this->normalizarNombre($datos['nombre']);
        $duplicado = LugarEntrega::query()
            ->when($lugarEntrega, fn ($query) => $query->where('id', '<>', $lugarEntrega->id))
            ->get(['id', 'nombre'])
            ->contains(fn (LugarEntrega $lugar) => $this->normalizarNombre($lugar->nombre) === $nombreNormalizado);

        if ($duplicado) {
            throw ValidationException::withMessages([
                'nombre' => 'Ya existe un lugar de entrega con ese nombre.',
            ]);
        }

        return $datos;
    }

    private function normalizarNombre(string $nombre): string
    {
        return mb_strtolower(
            preg_replace('/\s+/u', ' ', trim($nombre)),
            'UTF-8'
        );
    }
}
