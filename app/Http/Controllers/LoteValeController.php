<?php

namespace App\Http\Controllers;

use App\Models\LoteVale;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoteValeController extends Controller
{
    public function index()
    {
        $lotes = LoteVale::with('periodo')
            ->withMax('valesCombustible as ultimo_numero', 'numero_vale')
            ->orderByDesc('periodo_id')
            ->orderBy('numero_inicial')
            ->get();

        return view('lotes_vales.index', compact('lotes'));
    }

    public function create()
    {
        $periodos = Periodo::where('estado', true)
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->get();

        return view('lotes_vales.create', compact('periodos'));
    }

    private function guardar(Request $request, ?LoteVale $lote = null): LoteVale
    {
        $datos = $request->validate([
            'numero_inicial' => 'required|integer|min:1|max:999999',
            'numero_final' => 'required|integer|gte:numero_inicial|max:999999',
            'fecha_recepcion' => 'required|date',
            'periodo_id' => 'required|exists:periodos,id',
            'estado' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($datos, $lote) {

            $referencia = Periodo::findOrFail($datos['periodo_id']);

            \App\Models\NumeracionVale::bloquear(
                (int) $referencia->anio
            );

            $periodo = Periodo::lockForUpdate()
                ->findOrFail($datos['periodo_id']);

            if ($lote) {
                $lote = LoteVale::lockForUpdate()
                    ->findOrFail($lote->id);

                if (
                    $lote->valesCombustible()->exists()
                    && (
                        $datos['periodo_id'] != $lote->periodo_id
                        || $datos['numero_inicial'] != $lote->numero_inicial
                        || $datos['numero_final'] != $lote->numero_final
                    )
                ) {
                    throw ValidationException::withMessages([
                        'numero_inicial' =>
                            'No se puede cambiar el período ni el rango de un lote con vales emitidos.',
                    ]);
                }
            }

            if (
                !$periodo->estado
                && (
                    !$lote
                    || $lote->periodo_id != $periodo->id
                    || $datos['estado']
                )
            ) {
                throw ValidationException::withMessages([
                    'periodo_id' => 'Seleccione un período activo.',
                ]);
            }

            $solapado = LoteVale::whereHas(
                'periodo',
                fn ($q) => $q->where('anio', $periodo->anio)
            )
                ->when(
                    $lote,
                    fn ($q) => $q->where('id', '!=', $lote->id)
                )
                ->where('numero_inicial', '<=', $datos['numero_final'])
                ->where('numero_final', '>=', $datos['numero_inicial'])
                ->exists();

            if ($solapado) {
                throw ValidationException::withMessages([
                    'numero_inicial' =>
                        'El rango se superpone con otro lote del mismo año.',
                ]);
            }

            if ($lote) {
                $lote->update($datos);

                return $lote;
            }

            return LoteVale::create($datos);

        }, 3);
    }

    public function store(Request $request)
    {
        $lote = $this->guardar($request);

        return redirect()
            ->route('lotes-vales.show', $lote)
            ->with('success', 'Lote registrado correctamente.');
    }

public function show(LoteVale $loteVale)
{
    $loteVale->load('periodo');

    $total = $loteVale->numero_final - $loteVale->numero_inicial + 1;

    $cantidadVales = $loteVale->valesCombustible()->count();

    $disponibles = $loteVale->disponibles;

    $entregaPolicia = DB::table('entregas_policia')
        ->where('lote_vale_id', $loteVale->id)
        ->latest('id')
        ->first();

    return view(
        'lotes_vales.show',
        compact(
            'loteVale',
            'total',
            'cantidadVales',
            'disponibles',
            'entregaPolicia'
        )
    );
}

    public function edit(LoteVale $loteVale)
    {
        $periodos = Periodo::where('estado', true)
            ->orWhere('id', $loteVale->periodo_id)
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->get();

        return view(
            'lotes_vales.edit',
            compact('loteVale', 'periodos')
        );
    }

    public function update(Request $request, LoteVale $loteVale)
    {
        $this->guardar($request, $loteVale);

        return redirect()
            ->route('lotes-vales.show', $loteVale)
            ->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy(LoteVale $loteVale)
{
    return DB::transaction(function () use ($loteVale) {

        $lote = LoteVale::lockForUpdate()
            ->findOrFail($loteVale->id);

        // Eliminar los vales relacionados con el lote
        $lote->valesCombustible()->delete();

        // Eliminar las asignaciones/entregas a Policía relacionadas
        DB::table('entregas_policia')
            ->where('lote_vale_id', $lote->id)
            ->delete();

        // Eliminar el lote
        $lote->delete();

        return redirect()
            ->route('lotes-vales.index')
            ->with(
                'success',
                'Lote eliminado correctamente.'
            );
    });

    }
}