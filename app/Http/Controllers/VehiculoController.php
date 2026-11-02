<?php

namespace App\Http\Controllers;

use App\Models\AperturaProgramatica;
use App\Models\Unidad;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * Normaliza los datos de texto antes de validarlos.
     */
    private function normalizar(Request $request): void
    {
        $datos = [];

        foreach ([
            'codigo',
            'placa',
            'tipo',
            'industria',
            'marca',
            'color',
        ] as $campo) {

            if ($request->has($campo)) {

                $valor = preg_replace(
                    '/\s+/',
                    ' ',
                    trim((string) $request->input($campo))
                );

                $datos[$campo] = in_array(
                    $campo,
                    ['codigo', 'placa'],
                    true
                )
                    ? mb_strtoupper($valor)
                    : $valor;
            }
        }

        if ($request->has('detalle_asignacion_predeterminado')) {
            $datos['detalle_asignacion_predeterminado'] = trim(
                (string) $request->input('detalle_asignacion_predeterminado')
            );
        }

        $request->merge($datos);
    }

    /**
     * Reglas de validación.
     */
    private function reglas(?Vehiculo $vehiculo = null): array
    {
        return [

            'codigo' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9][A-Z0-9._\/-]*$/',

                Rule::unique('vehiculos', 'codigo')
                    ->ignore($vehiculo?->id),
            ],

            'placa' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9][A-Z0-9 .\/-]*$/',

                Rule::unique('vehiculos', 'placa')
                    ->ignore($vehiculo?->id),
            ],

            'tipo' => [
                'nullable',
                'string',
                'max:100',
            ],

            'industria' => [
                'nullable',
                'string',
                'max:100',
            ],

            'marca' => [
                'nullable',
                'string',
                'max:100',
            ],

            'color' => [
                'nullable',
                'string',
                'max:50',
            ],

            'detalle_asignacion_predeterminado' => [
                'nullable',
                'string',
            ],

            'unidad_id' => [
                'required',
                'integer',
                'exists:unidades,id',
            ],

            'apertura_programatica_id' => [
                'required',
                'integer',
                'exists:aperturas_programaticas,id',
            ],

            'estado' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     */
    private function mensajes(): array
    {
        return [

            'codigo.required' =>
                'El código del vehículo es obligatorio.',

            'codigo.unique' =>
                'Ya existe un vehículo con este código.',

            'codigo.regex' =>
                'El código contiene caracteres no permitidos.',

            'placa.required' =>
                'La placa es obligatoria.',

            'placa.unique' =>
                'Esta placa ya está registrada en otro vehículo.',

            'placa.regex' =>
                'La placa contiene caracteres no permitidos.',

            'unidad_id.required' =>
                'Seleccione la unidad responsable.',

            'unidad_id.exists' =>
                'La unidad seleccionada no existe.',

            'apertura_programatica_id.required' =>
                'Seleccione la asignación presupuestaria.',

            'apertura_programatica_id.exists' =>
                'La apertura programática seleccionada no existe.',
        ];
    }

    /**
     * Listado de vehículos.
     */
    public function index()
    {
        $vehiculos = Vehiculo::with([
            'unidad',
            'aperturaProgramatica.fuenteFinanciamiento',
            'aperturaProgramatica.organismoFinanciador',
        ])
            ->orderByDesc('id')
            ->get();

        return view(
            'vehiculos.index',
            compact('vehiculos')
        );
    }

    /**
     * Formulario para registrar vehículo.
     */
    public function create()
    {
        $unidades = Unidad::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $aperturas = AperturaProgramatica::with([
            'fuenteFinanciamiento',
            'organismoFinanciador',
        ])
            ->where('estado', true)
            ->orderBy('codigo')
            ->get();

        return view(
            'vehiculos.create',
            compact(
                'unidades',
                'aperturas'
            )
        );
    }

    /**
     * Registrar vehículo.
     */
    public function store(Request $request)
    {
        $this->normalizar($request);

        $datos = $request->validate(
            $this->reglas(),
            $this->mensajes()
        );

        Vehiculo::create($datos);

        return redirect()
            ->route('vehiculos.index')
            ->with(
                'success',
                'Vehículo registrado correctamente.'
            );
    }

    /**
     * Mostrar vehículo.
     */
    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load([
            'unidad',
            'aperturaProgramatica.fuenteFinanciamiento',
            'aperturaProgramatica.organismoFinanciador',
        ]);

        return view(
            'vehiculos.show',
            compact('vehiculo')
        );
    }

    /**
     * Formulario para editar vehículo.
     */
    public function edit(Vehiculo $vehiculo)
    {
        $unidades = Unidad::where('estado', true)
            ->orWhere('id', $vehiculo->unidad_id)
            ->orderBy('nombre')
            ->get();

        $aperturas = AperturaProgramatica::with([
            'fuenteFinanciamiento',
            'organismoFinanciador',
        ])
            ->where(function ($query) use ($vehiculo) {

                $query->where('estado', true);

                if ($vehiculo->apertura_programatica_id) {
                    $query->orWhere(
                        'id',
                        $vehiculo->apertura_programatica_id
                    );
                }
            })
            ->orderBy('codigo')
            ->get();

        return view(
            'vehiculos.edit',
            compact(
                'vehiculo',
                'unidades',
                'aperturas'
            )
        );
    }

    /**
     * Actualizar vehículo.
     */
    public function update(
        Request $request,
        Vehiculo $vehiculo
    ) {
        $this->normalizar($request);

        $datos = $request->validate(
            $this->reglas($vehiculo),
            $this->mensajes()
        );

        $vehiculo->update($datos);

        return redirect()
            ->route('vehiculos.index')
            ->with(
                'success',
                'Vehículo actualizado correctamente.'
            );
    }

    /**
     * Eliminar vehículo.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        if ($vehiculo->valesCombustible()->exists()) {

            return redirect()
                ->route('vehiculos.index')
                ->with(
                    'error',
                    'No se puede eliminar porque tiene vales registrados. Puede marcarlo como inactivo.'
                );
        }

        $vehiculo->delete();

        return redirect()
            ->route('vehiculos.index')
            ->with(
                'success',
                'Vehículo eliminado correctamente.'
            );
    }
}