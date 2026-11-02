<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnidadController extends Controller
{
    /**
     * Listado de unidades.
     */
    public function index()
    {
        $unidades = Unidad::orderBy('id', 'asc')->get();

        return view(
            'unidades.index',
            compact('unidades')
        );
    }

    /**
     * Formulario para registrar una unidad.
     */
    public function create()
    {
        return view('unidades.create');
    }

    /**
     * Registrar una nueva unidad.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR DATOS
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'nombre' => mb_strtoupper(
                trim((string) $request->nombre)
            ),

            'sigla' => mb_strtoupper(
                trim((string) $request->sigla)
            ),

            'tipo' => mb_strtoupper(
                trim((string) $request->tipo)
            ),

            'descripcion' => $request->descripcion
                ? trim((string) $request->descripcion)
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR
        |--------------------------------------------------------------------------
        */

        $datos = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:150',
                    Rule::unique('unidades', 'nombre'),
                ],

                'sigla' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('unidades', 'sigla'),
                ],

                'tipo' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'estado' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre de la unidad es obligatorio.',

                'nombre.unique' =>
                    'Ya existe una unidad con este nombre.',

                'sigla.required' =>
                    'La sigla de la unidad es obligatoria.',

                'sigla.unique' =>
                    'Ya existe una unidad con esta sigla.',

                'tipo.required' =>
                    'El tipo de unidad es obligatorio.',

                'estado.required' =>
                    'Seleccione el estado de la unidad.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | REGISTRAR
        |--------------------------------------------------------------------------
        */

        Unidad::create($datos);

        return redirect()
            ->route('unidades.index')
            ->with(
                'success',
                'Unidad registrada correctamente.'
            );
    }

    /**
     * Mostrar unidad.
     */
    public function show(Unidad $unidad)
    {
        return view(
            'unidades.show',
            compact('unidad')
        );
    }

    /**
     * Formulario para editar una unidad.
     */
    public function edit(Unidad $unidad)
    {
        return view(
            'unidades.edit',
            compact('unidad')
        );
    }

    /**
     * Actualizar unidad.
     */
    public function update(
        Request $request,
        Unidad $unidad
    ) {
        /*
        |--------------------------------------------------------------------------
        | NORMALIZAR DATOS
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'nombre' => mb_strtoupper(
                trim((string) $request->nombre)
            ),

            'sigla' => mb_strtoupper(
                trim((string) $request->sigla)
            ),

            'tipo' => mb_strtoupper(
                trim((string) $request->tipo)
            ),

            'descripcion' => $request->descripcion
                ? trim((string) $request->descripcion)
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR
        |--------------------------------------------------------------------------
        */

        $datos = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:150',

                    Rule::unique('unidades', 'nombre')
                        ->ignore($unidad->id),
                ],

                'sigla' => [
                    'required',
                    'string',
                    'max:50',

                    Rule::unique('unidades', 'sigla')
                        ->ignore($unidad->id),
                ],

                'tipo' => [
                    'required',
                    'string',
                    'max:50',
                ],

                'descripcion' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'estado' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'nombre.required' =>
                    'El nombre de la unidad es obligatorio.',

                'nombre.unique' =>
                    'Ya existe una unidad con este nombre.',

                'sigla.required' =>
                    'La sigla de la unidad es obligatoria.',

                'sigla.unique' =>
                    'Ya existe una unidad con esta sigla.',

                'tipo.required' =>
                    'El tipo de unidad es obligatorio.',

                'estado.required' =>
                    'Seleccione el estado de la unidad.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $unidad->update($datos);

        return redirect()
            ->route('unidades.index')
            ->with(
                'success',
                'Unidad actualizada correctamente.'
            );
    }

    /**
     * Eliminar unidad.
     */
    public function destroy(Unidad $unidad)
    {
        if ($unidad->vehiculos()->exists()) {

            return redirect()
                ->route('unidades.index')
                ->with(
                    'error',
                    'No se puede eliminar porque tiene vehículos asociados.'
                );
        }

        $unidad->delete();

        return redirect()
            ->route('unidades.index')
            ->with(
                'success',
                'Unidad eliminada correctamente.'
            );
    }
}