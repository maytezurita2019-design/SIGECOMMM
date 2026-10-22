<?php

namespace App\Http\Controllers;

use App\Models\ParteDiario;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class ParteDiarioController extends Controller
{
    /**
     * Mostrar pantalla principal e historial.
     */
   public function index(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS DISPONIBLES
    |--------------------------------------------------------------------------
    */

    $vehiculos = Vehiculo::with([
        'unidad',
    ])
        ->where('estado', true)
        ->orderBy('codigo')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | VEHÍCULO SELECCIONADO
    |--------------------------------------------------------------------------
    */

    $vehiculo = null;

    $vehiculoId = $request->input('vehiculo_id');

    if ($vehiculoId) {

        $vehiculo = Vehiculo::with([
            'unidad',
        ])
            ->where('id', $vehiculoId)
            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DE PARTES DIARIOS
    |--------------------------------------------------------------------------
    */

    $partes = ParteDiario::with([
        'vehiculo.unidad',
    ])
        ->orderByDesc('fecha')
        ->orderByDesc('id')
        ->paginate(15);


    return view(
        'partes-diarios.index',
        compact(
            'vehiculos',
            'vehiculo',
            'partes'
        )
    );
    }

    /**
     * Registrar si presentó o no el parte diario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id' => [
                'required',
                'exists:vehiculos,id',
            ],

            'fecha' => [
                'required',
                'date',
            ],

            'presentado' => [
                'required',
                'boolean',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'vehiculo_id.required' =>
                'Debe seleccionar un vehículo.',

            'vehiculo_id.exists' =>
                'El vehículo seleccionado no existe.',

            'fecha.required' =>
                'Debe ingresar la fecha del parte diario.',

            'fecha.date' =>
                'La fecha ingresada no es válida.',

            'presentado.required' =>
                'Debe indicar si presentó o no el parte diario.',

            'presentado.boolean' =>
                'El valor de presentación no es válido.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EVITAR DUPLICADOS
        |--------------------------------------------------------------------------
        | Un vehículo solamente puede tener un registro por fecha.
        */

        $existe = ParteDiario::where(
                'vehiculo_id',
                $request->vehiculo_id
            )
            ->whereDate(
                'fecha',
                $request->fecha
            )
            ->exists();

        if ($existe) {



           return redirect()
    ->route('partes-diarios.index', [
        'vehiculo_id' => $request->vehiculo_id
    ])
                ->with(
                    'error',
                    'Este vehículo ya tiene registrado un parte diario para esa fecha.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR PARTE DIARIO
        |--------------------------------------------------------------------------
        */

        ParteDiario::create([
            'vehiculo_id' =>
                $request->vehiculo_id,

            'fecha' =>
                $request->fecha,

            'presentado' =>
                $request->boolean('presentado'),

            'observacion' =>
                $request->observacion,
        ]);

        $vehiculo = Vehiculo::find(
            $request->vehiculo_id
        );

 return redirect()
    ->route('partes-diarios.index', [
        'vehiculo_id' => $request->vehiculo_id
    ])
            ->with(
                'success',
                'Parte diario registrado correctamente.'
            );
    }

    /**
     * Actualizar un registro.
     */
    public function update(
        Request $request,
        ParteDiario $parteDiario
    ) {
        $request->validate([
            'fecha' => [
                'required',
                'date',
            ],

            'presentado' => [
                'required',
                'boolean',
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | EVITAR DUPLICADOS AL EDITAR
        |--------------------------------------------------------------------------
        */

        $duplicado = ParteDiario::where(
                'vehiculo_id',
                $parteDiario->vehiculo_id
            )
            ->whereDate(
                'fecha',
                $request->fecha
            )
            ->where(
                'id',
                '!=',
                $parteDiario->id
            )
            ->exists();

        if ($duplicado) {
            return back()->with(
                'error',
                'Ya existe otro registro para este vehículo en esa fecha.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $parteDiario->update([
            'fecha' =>
                $request->fecha,

            'presentado' =>
                $request->boolean('presentado'),

            'observacion' =>
                $request->observacion,
        ]);

        return back()->with(
            'success',
            'Parte diario actualizado correctamente.'
        );
    }
}