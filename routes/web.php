<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\TipoCombustibleController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\FuenteFinanciamientoController;
use App\Http\Controllers\OrganismoFinanciadorController;
use App\Http\Controllers\AperturaProgramaticaController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\EstacionServicioController;
use App\Http\Controllers\LoteValeController;
use App\Http\Controllers\ValeCombustibleController;
use App\Http\Controllers\AutenticacionController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudContrasenaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ParteDiarioController;

/*
|--------------------------------------------------------------------------
| Inicio
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

// Login
Route::get('/login', [AutenticacionController::class, 'mostrarLogin'])
    ->name('login');

Route::post('/login', [AutenticacionController::class, 'login'])
    ->name('login.procesar');

// Cerrar sesión
Route::post('/logout', [AutenticacionController::class, 'logout'])
    ->name('logout');

    Route::get('/partes-diarios', [ParteDiarioController::class, 'index'])
    ->name('partes-diarios.index');

Route::post('/partes-diarios', [ParteDiarioController::class, 'store'])
    ->name('partes-diarios.store');

Route::put('/partes-diarios/{parteDiario}', [ParteDiarioController::class, 'update'])
    ->name('partes-diarios.update');


/*
|--------------------------------------------------------------------------
| Registro
|--------------------------------------------------------------------------
*/

Route::get('/registro', [RegistroController::class, 'mostrarRegistro'])
    ->name('registro');

Route::post('/registro', [RegistroController::class, 'registrar'])
    ->name('registro.procesar');


/*
|--------------------------------------------------------------------------
| Olvidé mi contraseña
|--------------------------------------------------------------------------
*/

Route::get(
    '/olvide-contrasena',
    [SolicitudContrasenaController::class, 'mostrarFormulario']
)->name('password.request');

Route::post(
    '/olvide-contrasena',
    [SolicitudContrasenaController::class, 'enviarSolicitud']
)->name('password.solicitar');



/*
|--------------------------------------------------------------------------
| Módulos protegidos
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
        /*
    |--------------------------------------------------------------------------
    | dashboard
    |--------------------------------------------------------------------------
    */

  
    
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    Route::resource('roles', RolController::class)
    ->middleware('rol:Administrador,ADMINISTRADOR')->parameters(['roles' => 'rol']);


    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/usuarios/{usuario}/restablecer-contrasena',
        [UsuarioController::class, 'mostrarRestablecerContrasena']
    )->middleware('rol:Administrador,ADMINISTRADOR')->name('usuarios.restablecer-contrasena');

    Route::put(
        '/usuarios/{usuario}/restablecer-contrasena',
        [UsuarioController::class, 'restablecerContrasena']
    )->middleware('rol:Administrador,ADMINISTRADOR')->name('usuarios.actualizar-contrasena');

    Route::resource('usuarios', UsuarioController::class)->middleware
    ('rol:Administrador,ADMINISTRADOR')->parameters(['usuarios' => 'usuario']);


    /*
    |--------------------------------------------------------------------------
    | Vehículos y unidades
    |--------------------------------------------------------------------------
    */

    // IMPORTANTE: las rutas /create deben registrarse antes que /{vehiculo}.
    // PostgreSQL intenta convertir "create" a BIGINT si la ruta show queda primero.
    Route::resource('vehiculos', VehiculoController::class)->only(['create','store','edit','update','destroy'])->middleware('rol:Administrador,ADMINISTRADOR')->parameters(['vehiculos'=>'vehiculo']);
    Route::resource('vehiculos', VehiculoController::class)->only(['index','show'])->parameters(['vehiculos'=>'vehiculo']);

    Route::resource('unidades', UnidadController::class)->only(['create','store','edit','update','destroy'])->middleware('rol:Administrador,ADMINISTRADOR')->parameters(['unidades'=>'unidad']);
    Route::resource('unidades', UnidadController::class)->only(['index','show'])->parameters(['unidades'=>'unidad']);


    /*
    |--------------------------------------------------------------------------
    | Gestión de vales
    |--------------------------------------------------------------------------
    */

    Route::get('entregas-policia/{entregaPolicia}/imprimir', [\App\Http\Controllers\EntregaPoliciaController::class, 'imprimir'])->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')->name('entregas-policia.imprimir');
    Route::post('vales-combustible/{valeCombustible}/cerrar-entrega', [\App\Http\Controllers\EntregaPoliciaController::class, 'cerrar'])->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')->name('entregas-policia.cerrar');
    Route::resource('entregas-policia', \App\Http\Controllers\EntregaPoliciaController::class)->only(['index', 'create', 'store', 'show'])->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')->parameters(['entregas-policia' => 'entregaPolicia']);

    Route::resource('lotes-vales', LoteValeController::class)->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')->parameters(['lotes-vales'=>'loteVale']);

    Route::get('/vales-combustible/{valeCombustible}/imprimir', [ValeCombustibleController::class, 'imprimir'])->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')
        ->name('vales-combustible.imprimir');

    // Registrar primero /create para evitar que el binding de {valeCombustible}
    // interprete literalmente "create" como un id BIGINT en PostgreSQL.
    Route::resource('vales-combustible', ValeCombustibleController::class)->only(['create','store','edit','update','destroy'])->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')->parameters(['vales-combustible' => 'valeCombustible']);
    Route::resource('vales-combustible', ValeCombustibleController::class)->only(['index','show'])->parameters(['vales-combustible' => 'valeCombustible']);
    Route::get('/vales-combustible/{valeCombustible}/completar', [ValeCombustibleController::class, 'completarFormulario'])
        ->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')
        ->name('vales-combustible.completar.formulario');
    Route::post('/vales-combustible/{valeCombustible}/completar', [ValeCombustibleController::class, 'completar'])
        ->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')
        ->name('vales-combustible.completar');
    Route::post('/vales-combustible/{valeCombustible}/anular', [ValeCombustibleController::class, 'anular'])
        ->middleware('rol:Administrador,ADMINISTRADOR,Auxiliar,AUXILIAR')
        ->name('vales-combustible.anular');


    /*
    |--------------------------------------------------------------------------
    | Configuración de combustible
    |--------------------------------------------------------------------------
    */

    Route::resource('periodos', PeriodoController::class)->middleware('rol:Administrador,ADMINISTRADOR')
        ->parameters(['periodos' => 'periodo']);

    Route::resource('tipos-combustible', TipoCombustibleController::class)->middleware('rol:Administrador,ADMINISTRADOR')
        ->parameters(['tipos-combustible' => 'tipoCombustible']);

    Route::resource('estaciones-servicio', EstacionServicioController::class)
    ->middleware('rol:Administrador,ADMINISTRADOR')->except(['destroy'])
    ->parameters(['estaciones-servicio' => 'estacionServicio']);


    /*
    |--------------------------------------------------------------------------
    | Aperturas y financiamiento
    |--------------------------------------------------------------------------
    */

    Route::resource(
    'aperturas-programaticas',
    AperturaProgramaticaController::class
)->middleware('rol:Administrador,ADMINISTRADOR')->except(['destroy'])
  ->parameters([
      'aperturas-programaticas' => 'aperturaProgramatica'
  ]);

Route::resource('fuentes-financiamiento', FuenteFinanciamientoController::class)
    ->middleware('rol:Administrador,ADMINISTRADOR')->except(['destroy'])
    ->parameters(['fuentes-financiamiento' => 'fuenteFinanciamiento']);

    Route::resource('organismos-financiadores', OrganismoFinanciadorController::class)
    ->middleware('rol:Administrador,ADMINISTRADOR')->except(['destroy'])
    ->parameters(['organismos-financiadores' => 'organismoFinanciador']);

    Route::get(
    '/solicitudes-contrasena',
    [SolicitudContrasenaController::class, 'index']
)->middleware('rol:Administrador,ADMINISTRADOR')->name('solicitudes-contrasena.index');

Route::get('/auditoria', [AuditoriaController::class, 'index'])->middleware('rol:Administrador,ADMINISTRADOR')->name('auditoria.index');

Route::get('/reportes/consumo/excel', [ReporteController::class, 'consumoExcel'])->name('reportes.consumo.excel');
Route::get('/reportes/vehiculos/excel', [ReporteController::class, 'vehiculosExcel'])->name('reportes.vehiculos.excel');
Route::get('/reportes/aperturas/excel', [ReporteController::class, 'aperturasExcel'])->name('reportes.aperturas.excel');

Route::get('/reportes/consumo', [ReporteController::class, 'consumo'])
    ->name('reportes.consumo');

    Route::get('/reportes/vehiculos', [ReporteController::class, 'porVehiculo'])
    ->name('reportes.vehiculos');

    Route::get('/reportes/aperturas', [ReporteController::class, 'porApertura'])
    ->name('reportes.aperturas');

    Route::get('/reportes/aperturas/pdf', [ReporteController::class, 'aperturasPdf'])
    ->name('reportes.aperturas.pdf');

    Route::get(
    '/reportes/vehiculos/pdf',
    [ReporteController::class, 'vehiculosPdf']
)->name('reportes.vehiculos.pdf');

Route::get(
    '/reportes/vehiculo-individual',
    [ReporteController::class, 'vehiculoIndividual']
)->name('reportes.vehiculo-individual');

Route::get(
    '/reportes/vehiculo-individual/pdf',
    [ReporteController::class, 'vehiculoIndividualPdf']
)->name('reportes.vehiculo-individual.pdf');
Route::get(
    '/reportes/vehiculo-individual/excel',
    [ReporteController::class, 'vehiculoIndividualExcel']
)->name('reportes.vehiculo-individual.excel');

Route::get('/reportes/consumo/pdf', [ReporteController::class, 'consumoPdf'])
    ->name('reportes.consumo.pdf');

});
