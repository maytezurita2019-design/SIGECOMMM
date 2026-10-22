<?php

namespace App\Http\Controllers;

use App\Models\OrganismoFinanciador;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganismoFinanciadorController extends Controller
{
    public function index()
    {
        $organismos = OrganismoFinanciador::orderBy('codigo')->get();

        return view('organismos_financiadores.index', compact('organismos'));
    }

    public function create()
    {
        return view('organismos_financiadores.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:20|unique:organismos_financiadores,codigo',
            'nombre' => 'required|string|max:150',
           
            'estado' => 'required|boolean',
        ]);

        OrganismoFinanciador::create($datos);

        return redirect()->route('organismos-financiadores.index')
            ->with('success', 'Organismo financiador registrado correctamente.');
    }

    public function show(OrganismoFinanciador $organismoFinanciador)
    {
        return view(
            'organismos_financiadores.show',
            compact('organismoFinanciador')
        );
    }

    public function edit(OrganismoFinanciador $organismoFinanciador)
    {
        return view(
            'organismos_financiadores.edit',
            compact('organismoFinanciador')
        );
    }

    public function update(
        Request $request,
        OrganismoFinanciador $organismoFinanciador
    ) {
        $datos = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('organismos_financiadores', 'codigo')
                    ->ignore($organismoFinanciador->id),
            ],
            'nombre' => 'required|string|max:150',
            
            'estado' => 'required|boolean',
        ]);

        $organismoFinanciador->update($datos);

        return redirect()->route('organismos-financiadores.index')
            ->with('success', 'Organismo financiador actualizado correctamente.');
    }

}