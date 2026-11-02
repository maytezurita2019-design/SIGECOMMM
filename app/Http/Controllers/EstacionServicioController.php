<?php

namespace App\Http\Controllers;

use App\Models\EstacionServicio;
use Illuminate\Http\Request;

class EstacionServicioController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'in:activo,inactivo'],
        ]);

        $consulta = EstacionServicio::query()->orderBy('nombre');

        if (!empty($filtros['buscar'])) {
            $busqueda = trim($filtros['buscar']);

            $consulta->where(function ($subconsulta) use ($busqueda) {
                $coincidencia = '%' . $busqueda . '%';
                $subconsulta->where('nombre', 'ILIKE', $coincidencia)
                    ->orWhere('direccion', 'ILIKE', $coincidencia)
                    ->orWhere('telefono', 'ILIKE', $coincidencia);
            });
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $consulta->where('estado', $filtros['estado'] === 'activo');
        }

        $estaciones = $consulta->get();

        return view('estaciones_servicio.index', compact('estaciones'));
    }

    public function create()
    {
        return view('estaciones_servicio.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'estado' => 'required|boolean',
        ]);

        EstacionServicio::create($datos);

        return redirect()->route('estaciones-servicio.index')
            ->with('success', 'Estación de servicio registrada correctamente.');
    }

    public function show(EstacionServicio $estacionServicio)
    {
        return view(
            'estaciones_servicio.show',
            compact('estacionServicio')
        );
    }

    public function edit(EstacionServicio $estacionServicio)
    {
        return view(
            'estaciones_servicio.edit',
            compact('estacionServicio')
        );
    }

    public function update(
        Request $request,
        EstacionServicio $estacionServicio
    ) {
        $datos = $request->validate([
            'nombre' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'estado' => 'required|boolean',
        ]);

        $estacionServicio->update($datos);

        return redirect()->route('estaciones-servicio.index')
            ->with('success', 'Estación de servicio actualizada correctamente.');
    }


}