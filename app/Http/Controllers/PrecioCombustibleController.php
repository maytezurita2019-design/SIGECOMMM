<?php

namespace App\Http\Controllers;

use App\Models\PrecioCombustible;
use App\Models\TipoCombustible;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrecioCombustibleController extends Controller
{
    public function index()
    {
        $tipos = TipoCombustible::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $precios = PrecioCombustible::with(['tipoCombustible', 'usuario'])
            ->orderByDesc('fecha_inicio')
            ->get();

        $vigentes = [];

        foreach ($tipos as $tipo) {
            $vigentes[$tipo->id] = PrecioCombustible::vigenteEnFecha($tipo->id, now());
        }

        return view('precios_combustible.index', [
            'tipos' => $tipos,
            'vigentes' => $vigentes,
            'precios' => $precios,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'precio_unitario' => str_replace(',', '.', (string) ($request->input('precio_unitario') ?? '')),
        ]);

        $datos = $request->validate([
            'tipo_combustible_id' => 'required|integer|exists:tipos_combustible,id',
            'precio_unitario' => ['required', 'numeric', 'min:0.0001', 'max:9999999999.9999'],
            'fecha_inicio' => 'required|date',
            'motivo' => 'nullable|string|max:255',
        ]);

        $tipo = TipoCombustible::findOrFail($datos['tipo_combustible_id']);

        if (!$tipo->estado) {
            throw ValidationException::withMessages([
                'tipo_combustible_id' => 'El tipo de combustible seleccionado no está activo.',
            ]);
        }

        $fechaInicio = Carbon::parse($datos['fecha_inicio'])->startOfDay();
        $vigenteExistente = PrecioCombustible::where('tipo_combustible_id', $tipo->id)
            ->where(function ($q) use ($fechaInicio) {
                $q->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '>=', $fechaInicio->toDateString());
            })
            ->whereDate('fecha_inicio', '<=', $fechaInicio->toDateString())
            ->first();

        if ($vigenteExistente && $vigenteExistente->fecha_inicio->lt($fechaInicio)) {
            throw ValidationException::withMessages([
                'fecha_inicio' => 'Ya existe un precio vigente para este combustible en la fecha indicada. No se permiten solapamientos.',
            ]);
        }

        $precio = PrecioCombustible::registrar([
            'tipo_combustible_id' => $datos['tipo_combustible_id'],
            'precio_unitario' => $datos['precio_unitario'],
            'fecha_inicio' => $fechaInicio->toDateString(),
            'motivo' => $datos['motivo'] ?? null,
            'usuario_id' => auth()->id(),
        ]);

        return redirect()->route('precios-combustible.index')
            ->with('success', 'Precio de combustible actualizado correctamente.');
    }
}
