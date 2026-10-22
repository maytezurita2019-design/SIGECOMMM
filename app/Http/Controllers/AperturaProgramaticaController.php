<?php

namespace App\Http\Controllers;

use App\Models\AperturaProgramatica;
use App\Models\FuenteFinanciamiento;
use App\Models\OrganismoFinanciador;
use Illuminate\Http\Request;

class AperturaProgramaticaController extends Controller
{
    public function index()
    {
        $aperturas = AperturaProgramatica::with([
            'fuenteFinanciamiento',
            'organismoFinanciador'
        ])->orderBy('codigo')->get();

        return view('aperturas_programaticas.index', compact('aperturas'));
    }

    public function create()
    {
        $fuentes = FuenteFinanciamiento::where('estado', true)
            ->orderBy('codigo')
            ->get();

        $organismos = OrganismoFinanciador::where('estado', true)
            ->orderBy('codigo')
            ->get();

        return view(
            'aperturas_programaticas.create',
            compact('fuentes', 'organismos')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'fuente_financiamiento_id' => 'required|exists:fuentes_financiamiento,id',
            'organismo_financiador_id' => 'required|exists:organismos_financiadores,id',
            'estado' => 'required|boolean',
        ]);

        AperturaProgramatica::create($datos);

        return redirect()->route('aperturas-programaticas.index')
            ->with('success', 'Apertura programática registrada correctamente.');
    }

    public function show(AperturaProgramatica $aperturaProgramatica)
    {
        $aperturaProgramatica->load([
            'fuenteFinanciamiento',
            'organismoFinanciador'
        ]);

        return view(
            'aperturas_programaticas.show',
            compact('aperturaProgramatica')
        );
    }

    public function edit(AperturaProgramatica $aperturaProgramatica)
    {
        $fuentes = FuenteFinanciamiento::where('estado', true)->get();
        $organismos = OrganismoFinanciador::where('estado', true)->get();

        return view(
            'aperturas_programaticas.edit',
            compact('aperturaProgramatica', 'fuentes', 'organismos')
        );
    }

    public function update(
        Request $request,
        AperturaProgramatica $aperturaProgramatica
    ) {
        $datos = $request->validate([
            'codigo' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'fuente_financiamiento_id' => 'required|exists:fuentes_financiamiento,id',
            'organismo_financiador_id' => 'required|exists:organismos_financiadores,id',
            'estado' => 'required|boolean',
        ]);

        $aperturaProgramatica->update($datos);

        return redirect()->route('aperturas-programaticas.index')
            ->with('success', 'Apertura programática actualizada correctamente.');
    }

    
}