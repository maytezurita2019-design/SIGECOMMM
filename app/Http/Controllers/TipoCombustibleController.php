<?php

namespace App\Http\Controllers;

use App\Models\TipoCombustible;
use App\Models\ValeCombustible;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoCombustibleController extends Controller
{
    public function index()
    {
        $tiposCombustible = TipoCombustible::orderBy('nombre')->get();

        return view('tipos_combustible.index', compact('tiposCombustible'));
    }

    public function create()
    {
        return view('tipos_combustible.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_combustible,nombre',
            'unidad_medida' => 'required|string|max:20',
            'estado' => 'required|boolean',
        ]);

        TipoCombustible::create($datos);

        return redirect()->route('tipos-combustible.index')
            ->with('success', 'Tipo de combustible registrado correctamente.');
    }

    public function show(TipoCombustible $tipoCombustible)
    {
        return view('tipos_combustible.show', compact('tipoCombustible'));
    }

    public function edit(TipoCombustible $tipoCombustible)
    {
        return view('tipos_combustible.edit', compact('tipoCombustible'));
    }

    public function update(Request $request, TipoCombustible $tipoCombustible)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tipos_combustible', 'nombre')
                    ->ignore($tipoCombustible->id),
            ],
            'unidad_medida' => 'required|string|max:20',
            'estado' => 'required|boolean',
        ]);

        $tipoCombustible->update($datos);

        return redirect()->route('tipos-combustible.index')
            ->with('success', 'Tipo de combustible actualizado correctamente.');
    }

    public function destroy(TipoCombustible $tipoCombustible)
    {
        $tieneVales = ValeCombustible::where(
            'tipo_combustible_id',
            $tipoCombustible->id
        )->exists();

        if ($tieneVales) {
            return redirect()->route('tipos-combustible.index')
                ->with(
                    'error',
                    'No se puede eliminar porque está siendo utilizado en vales de combustible.'
                );
        }

        $tipoCombustible->delete();

        return redirect()->route('tipos-combustible.index')
            ->with('success', 'Tipo de combustible eliminado correctamente.');
    }
}