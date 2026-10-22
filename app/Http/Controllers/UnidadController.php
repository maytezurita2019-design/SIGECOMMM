<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $unidades = Unidad::orderBy('nombre')->get();

        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'sigla' => 'nullable|string|max:50',
            'tipo' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ]);

        Unidad::create($datos);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad registrada correctamente.');
    }

    public function show(Unidad $unidad)
    {
        return view('unidades.show', compact('unidad'));
    }

    public function edit(Unidad $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'sigla' => 'nullable|string|max:50',
            'tipo' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ]);

        $unidad->update($datos);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad actualizada correctamente.');
    }

    public function destroy(Unidad $unidad)
    {
        if ($unidad->vehiculos()->exists()) {
            return redirect()->route('unidades.index')
                ->with('error', 'No se puede eliminar porque tiene vehículos asociados.');
        }

        $unidad->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad eliminada correctamente.');
    }
}