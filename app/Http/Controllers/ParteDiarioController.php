<?php

namespace App\Http\Controllers;

use App\Models\ParteDiario;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ParteDiarioController extends Controller
{
    public function index(Request $request)
    {
        $vehiculos = Vehiculo::with('unidad')
            ->where('estado', true)
            ->orderBy('codigo')
            ->get();

        $vehiculo = null;

        if ($request->filled('vehiculo_id')) {
            $vehiculo = Vehiculo::with('unidad')
                ->where('estado', true)
                ->find($request->input('vehiculo_id'));
        }

        $partes = ParteDiario::with('vehiculo.unidad')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15);

        return view('partes-diarios.index', compact(
            'vehiculos',
            'vehiculo',
            'partes'
        ));
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request, true);

        // No permitir dos partes del mismo vehículo en la misma fecha.
        $existe = ParteDiario::where('vehiculo_id', $datos['vehiculo_id'])
            ->whereDate('fecha', $datos['fecha'])
            ->exists();

        if ($existe) {
            return redirect()
                ->route('partes-diarios.index')
                ->with('error', 'Este vehículo ya tiene registrado un Parte Diario para esa fecha.');
        }

        ParteDiario::create($this->prepararDatos($request, $datos));

        // Volver al buscador sin conservar el vehículo seleccionado.
        return redirect()
            ->route('partes-diarios.index')
            ->with('success', 'Parte Diario registrado correctamente.');
    }

    public function update(Request $request, ParteDiario $parteDiario)
    {
        $datos = $this->validarDatos($request);

        // Excluir el registro actual al comprobar duplicados.
        $duplicado = ParteDiario::where('vehiculo_id', $parteDiario->vehiculo_id)
            ->whereDate('fecha', $datos['fecha'])
            ->where('id', '!=', $parteDiario->id)
            ->exists();

        if ($duplicado) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe otro registro para este vehículo en esa fecha.');
        }

        $parteDiario->update($this->prepararDatos($request, $datos));

        return redirect()
            ->route('partes-diarios.index')
            ->with('success', 'Parte Diario actualizado correctamente.');
    }

    private function validarDatos(Request $request, bool $crear = false): array
    {
        $reglas = [
            'fecha' => ['required', 'date'],
            'presentado' => ['required', 'boolean'],
            'odometro_funciona' => ['required_if:presentado,1', 'nullable', 'boolean'],
            'km_inicial' => ['nullable', 'integer', 'min:0'],
            'km_final' => ['nullable', 'integer', 'min:0'],
            'observacion' => ['nullable', 'string', 'max:1000'],
        ];

        if ($crear) {
            $reglas['vehiculo_id'] = ['required', 'exists:vehiculos,id'];
        }

        $datos = $request->validate($reglas, [
            'vehiculo_id.required' => 'Debe seleccionar un vehículo.',
            'fecha.required' => 'Debe ingresar la fecha del Parte Diario.',
            'presentado.required' => 'Debe indicar si presentó el Parte Diario.',
            'odometro_funciona.required_if' => 'Debe indicar si el odómetro funciona.',
            'km_inicial.integer' => 'El kilometraje inicial debe ser un número entero.',
            'km_final.integer' => 'El kilometraje final debe ser un número entero.',
        ]);

        if ($request->boolean('presentado') && $request->boolean('odometro_funciona')) {
            if (
                ($datos['km_inicial'] ?? null) === null ||
                ($datos['km_final'] ?? null) === null
            ) {
                throw ValidationException::withMessages([
                    'km_inicial' => 'Debe ingresar el kilometraje inicial y final cuando el odómetro funciona.',
                ]);
            }

            if ($datos['km_final'] < $datos['km_inicial']) {
                throw ValidationException::withMessages([
                    'km_final' => 'El kilometraje final no puede ser menor al inicial.',
                ]);
            }
        }

        return $datos;
    }

    private function prepararDatos(Request $request, array $datos): array
    {
        $presentado = $request->boolean('presentado');
    
        $odometroFunciona = $presentado
            ? $request->boolean('odometro_funciona')
            : null;

        $funciona = $presentado && $odometroFunciona === true;

        return [
            'fecha' => $datos['fecha'],
            'presentado' => $presentado,
            'odometro_funciona' => $odometroFunciona,
            'km_inicial' => $funciona ? $datos['km_inicial'] : null,
            'km_final' => $funciona ? $datos['km_final'] : null,
            'observacion' => $datos['observacion'] ?? null,
        ] + (isset($datos['vehiculo_id'])
            ? ['vehiculo_id' => $datos['vehiculo_id']]
            : []);
    }
}

