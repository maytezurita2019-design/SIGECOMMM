<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\SolicitudContrasena;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')
            ->orderBy('nombre')
            ->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',

            'telefono' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]+$/',
            ],

            'contrasena' => 'required|string|min:8|confirmed',
            'rol_id' => 'required|exists:roles,id',
            'estado' => 'required|boolean',
        ], [
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'telefono.regex' => 'El teléfono solo puede contener números, espacios, + y -.',
        ]);

        Usuario::create($datos);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function show(Usuario $usuario)
    {
        $usuario->load('rol');

        return view('usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario)
    {
        $roles = Rol::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, Usuario $usuario)
{
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido' => 'required|string|max:100',

        'correo' => [
            'required',
            'email',
            'max:150',
            Rule::unique('usuarios', 'correo')->ignore($usuario->id),
        ],

        'telefono' => [
            'required',
            'string',
            'max:20',
            'regex:/^[0-9+\-\s]+$/',
        ],

        'rol_id' => 'required|exists:roles,id',
        'estado' => 'required|boolean',

        'contrasena' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
        ],
    ], [
        'telefono.required' =>
            'El teléfono es obligatorio.',

        'telefono.max' =>
            'El teléfono no puede tener más de 20 caracteres.',

        'telefono.regex' =>
            'El teléfono solo puede contener números, espacios, + y -.',

        'contrasena.min' =>
            'La nueva contraseña debe tener al menos 8 caracteres.',

        'contrasena.confirmed' =>
            'Las contraseñas no coinciden.',
    ]);

    // Si no escribió una nueva contraseña,
    // conservar la contraseña actual.
    if (empty($datos['contrasena'])) {
        unset($datos['contrasena']);
    }

    $usuario->update($datos);

    return redirect()
        ->route('usuarios.index')
        ->with('success', 'Usuario actualizado correctamente.');
}

    public function mostrarRestablecerContrasena(Usuario $usuario)
    {
        return view(
            'usuarios.restablecer-contrasena',
            compact('usuario')
        );
    }

    public function restablecerContrasena(
        Request $request,
        Usuario $usuario
    ) {
        $datos = $request->validate([
            'contrasena' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'contrasena.required' =>
                'La nueva contraseña es obligatoria.',

            'contrasena.min' =>
                'La contraseña debe tener al menos 8 caracteres.',

            'contrasena.confirmed' =>
                'Las contraseñas no coinciden.',
        ]);

        $usuario->contrasena = $datos['contrasena'];
        $usuario->save();

        SolicitudContrasena::where('usuario_id', $usuario->id)
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'atendida',
                'fecha_atencion' => now(),
                'atendido_por' => auth()->id(),
            ]);

       return redirect()
    ->route('solicitudes-contrasena.index')
    ->with(
        'success',
        'La contraseña de '.$usuario->nombre.
        ' fue restablecida correctamente.'
    );
           
    }
    public function destroy(Usuario $usuario)
{
    // No permitir que el administrador elimine su propia cuenta.
    if ((int) auth()->id() === (int) $usuario->id) {
        return redirect()
            ->route('usuarios.index')
            ->with(
                'error',
                'No puede eliminar su propia cuenta.'
            );
    }

    // Verificar si el usuario tiene registros históricos.
    $tieneRegistros =
        DB::table('vales_combustible')
            ->where('usuario_id', $usuario->id)
            ->exists()

        || DB::table('entregas_policia')
            ->where('usuario_id', $usuario->id)
            ->exists()

        || DB::table('auditorias')
            ->where('usuario_id', $usuario->id)
            ->exists()

        || DB::table('solicitudes_contrasena')
            ->where('usuario_id', $usuario->id)
            ->exists()

        || DB::table('solicitudes_contrasena')
            ->where('atendido_por', $usuario->id)
            ->exists();

    if ($tieneRegistros) {
        return redirect()
            ->route('usuarios.index')
            ->with(
                'error',
                'No se puede eliminar este usuario porque tiene registros asociados. Puede desactivarlo desde Editar usuario.'
            );
    }

    $nombre = trim(
        $usuario->nombre . ' ' . $usuario->apellido
    );

    $usuario->delete();

    return redirect()
        ->route('usuarios.index')
        ->with(
            'success',
            'El usuario '.$nombre.' fue eliminado correctamente.'
        );
}
}