<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        app()->setLocale('es');


        /*
        |--------------------------------------------------------------------------
        | PERMISOS DE INTERFAZ
        |--------------------------------------------------------------------------
        |
        | Se utilizan para controlar qué opciones muestra AdminLTE.
        | No modifican las rutas, controladores ni la base de datos.
        |
        */

        Gate::define('administrador', function ($usuario) {

            $rol = mb_strtoupper(
                $usuario->rol->nombre ?? ''
            );

            return str_contains($rol, 'ADMIN');
        });


        Gate::define('puede-operar', function ($usuario) {

            $rol = mb_strtoupper(
                $usuario->rol->nombre ?? ''
            );

            return str_contains($rol, 'ADMIN')
                || str_contains($rol, 'AUX');
        });

Event::listen(BuildingMenu::class, function (BuildingMenu $event) {

    if (!auth()->check()) {
        return;
    }

    $rol = mb_strtoupper(auth()->user()->rol->nombre ?? '');

    if (!str_contains($rol, 'ADMIN')) {
        return;
    }

    $pendientes = 0;

    if (Schema::hasTable('solicitudes_contrasena')) {
        $pendientes = \App\Models\SolicitudContrasena::where(
            'estado',
            'pendiente'
        )->count();
    }

    $item = [
        'text' => 'Solicitudes',
        'route' => 'solicitudes-contrasena.index',
        'icon' => 'bi bi-bell-fill',
        'topnav_right' => true,
    ];

    if ($pendientes > 0) {
        $item['label'] = $pendientes;
        $item['label_color'] = 'danger';
    }

    $event->menu->add($item);
});
        /*
        |--------------------------------------------------------------------------
        | AUDITORÍA
        |--------------------------------------------------------------------------
        */

        $modelosAuditados = [

            \App\Models\Vehiculo::class,
            \App\Models\Unidad::class,
            \App\Models\Periodo::class,
            \App\Models\TipoCombustible::class,
            \App\Models\EstacionServicio::class,
            \App\Models\FuenteFinanciamiento::class,
            \App\Models\OrganismoFinanciador::class,
            \App\Models\AperturaProgramatica::class,
            \App\Models\LoteVale::class,
            \App\Models\ValeCombustible::class,
            \App\Models\Usuario::class,
            \App\Models\Rol::class,

        ];


        $limpiar = static function (array $datos): array {

            foreach (
                [
                    'contrasena',
                    'password',
                    'remember_token'
                ] as $sensible
            ) {

                if (array_key_exists($sensible, $datos)) {

                    $datos[$sensible] = '[PROTEGIDO]';

                }

            }

            return $datos;
        };


        foreach ($modelosAuditados as $modelo) {

            /*
            |--------------------------------------------------------------------------
            | CREAR
            |--------------------------------------------------------------------------
            */

            $modelo::created(function ($m) use ($limpiar) {

                if (
                    !\Illuminate\Support\Facades\Schema::hasTable(
                        'auditorias'
                    )
                ) {
                    return;
                }


                \App\Models\Auditoria::create([

                    'usuario_id' => auth()->id(),

                    'accion' => 'CREAR',

                    'tabla' => $m->getTable(),

                    'registro_id' => $m->getKey(),

                    'datos_nuevos' => $limpiar(
                        $m->getAttributes()
                    ),

                    'ip' => request()?->ip(),

                    'descripcion' => 'Registro creado',

                ]);

            });


            /*
            |--------------------------------------------------------------------------
            | ACTUALIZAR
            |--------------------------------------------------------------------------
            */

            $modelo::updated(function ($m) use ($limpiar) {

                if (
                    !\Illuminate\Support\Facades\Schema::hasTable(
                        'auditorias'
                    )
                ) {
                    return;
                }


                $cambios = $m->getChanges();


                if (!$cambios) {
                    return;
                }


                $anteriores = [];


                foreach (array_keys($cambios) as $k) {

                    $anteriores[$k] =
                        $m->getOriginal($k);

                }


                \App\Models\Auditoria::create([

                    'usuario_id' => auth()->id(),

                    'accion' => 'ACTUALIZAR',

                    'tabla' => $m->getTable(),

                    'registro_id' => $m->getKey(),

                    'datos_anteriores' => $limpiar(
                        $anteriores
                    ),

                    'datos_nuevos' => $limpiar(
                        $cambios
                    ),

                    'ip' => request()?->ip(),

                    'descripcion' => 'Registro actualizado',

                ]);

            });


            /*
            |--------------------------------------------------------------------------
            | ELIMINAR
            |--------------------------------------------------------------------------
            */

            $modelo::deleted(function ($m) use ($limpiar) {

                if (
                    !\Illuminate\Support\Facades\Schema::hasTable(
                        'auditorias'
                    )
                ) {
                    return;
                }


                \App\Models\Auditoria::create([

                    'usuario_id' => auth()->id(),

                    'accion' => 'ELIMINAR',

                    'tabla' => $m->getTable(),

                    'registro_id' => $m->getKey(),

                    'datos_anteriores' => $limpiar(
                        $m->getOriginal()
                    ),

                    'ip' => request()?->ip(),

                    'descripcion' => 'Registro eliminado',

                ]);

            });

        }
    }
}
