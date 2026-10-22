<?php

namespace Tests\Feature;

use App\Models\{Usuario, Periodo, LoteVale, ValeCombustible};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LotesValesTest extends TestCase
{
    use RefreshDatabase;

    private Periodo $periodo;
    private array $emision;

    protected function setUp(): void
    {
        parent::setUp();
        $rol = DB::table('roles')->insertGetId([
    'nombre' => 'AUXILIAR',
    'estado' => true
]);
        $usuario = Usuario::create(['nombre' => 'Prueba', 'apellido' => 'Local', 'ci' => 'TEST', 'correo' => 'test@example.test', 'contrasena' => bcrypt('test'), 'rol_id' => $rol, 'estado' => true]);
        $this->actingAs($usuario);
        $this->periodo = Periodo::create(['mes' => 10, 'anio' => 2026, 'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2026-10-31', 'estado' => true]);
        $unidad = DB::table('unidades')->insertGetId(['nombre' => 'Obras']);
        $vehiculo = DB::table('vehiculos')->insertGetId(['codigo' => 'V1', 'placa' => 'TEST', 'unidad_id' => $unidad]);
        $fuente = DB::table('fuentes_financiamiento')->insertGetId(['codigo' => '1', 'nombre' => 'Fuente']);
        $organismo = DB::table('organismos_financiadores')->insertGetId(['codigo' => '1', 'nombre' => 'Organismo']);
        $apertura = DB::table('aperturas_programaticas')->insertGetId(['codigo' => '1', 'fuente_financiamiento_id' => $fuente, 'organismo_financiador_id' => $organismo]);
        $this->emision = ['fecha' => '2026-10-01', 'vehiculo_id' => $vehiculo, 'apertura_programatica_id' => $apertura, 'kilometraje' => 100];
    }

    private function datosLote(): array
    {
        return ['numero_inicial' => 1, 'numero_final' => 2, 'fecha_recepcion' => '2026-10-01', 'periodo_id' => $this->periodo->id, 'estado' => 1];
    }

    public function test_calcula_total_con_coma_y_litros_de_alta_precision(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $datos = $this->emision + ['lote_vale_id' => $lote->id, 'cantidad_litros' => '53,0172413793103', 'precio_unitario' => '6,96'];
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $vale = ValeCombustible::firstOrFail();
        $this->assertSame('EMITIDO', $vale->estado);
        $this->assertNull($vale->total);
        $datosCompletado = ['numero_factura' => 'FACT-1', 'cantidad_litros' => '53,0172413793103', 'precio_unitario' => '6,96'];
        $this->post('/vales-combustible/'.$vale->id.'/completar', $datosCompletado)->assertSessionHasNoErrors();
        $vale->refresh();
        $this->assertSame('COMPLETADO', $vale->estado);
        $this->assertSame('369.00', $vale->total);
        // SQLite usa punto flotante; PostgreSQL conserva los 13 decimales del campo NUMERIC.
        $this->assertEqualsWithDelta(53.0172413793103, (float) $vale->cantidad_litros, 0.000000000001);
        $this->get('/vales-combustible/'.$vale->id)->assertOk()->assertSee('369,00');
        $this->post('/vales-combustible/'.$vale->id.'/completar', array_replace($datosCompletado, ['precio_unitario' => '6,90']))
            ->assertStatus(409);
        $this->post('/vales-combustible/'.$vale->id.'/completar', array_replace($datosCompletado, ['cantidad_litros' => '1.234,56']))
            ->assertSessionHasErrors('cantidad_litros');
        $this->post('/vales-combustible/'.$vale->id.'/completar', array_replace($datosCompletado, ['cantidad_litros' => '1.00000000000001']))
            ->assertSessionHasErrors('cantidad_litros');
    }

    public function test_entrega_policia_reserva_imprime_y_registra_sin_renumerar(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-03'));
        $lote = LoteVale::create(array_replace($this->datosLote(), ['numero_final' => 10]));
        $datos = ['lote_vale_id' => $lote->id, 'contrato' => 'POL-2026', 'dependencia' => 'Policía Sacaba',
            'recibido_por' => 'Responsable de Policía', 'fecha_entrega' => '2026-10-01', 'cantidad' => 3,
            'apertura_programatica_id' => $this->emision['apertura_programatica_id'], 'solicitud_token' => (string) \Illuminate\Support\Str::uuid()];
        $this->get('/entregas-policia/create')->assertOk();
        $this->post('/entregas-policia', $datos)->assertSessionHasNoErrors()->assertRedirect();
        $entrega = \App\Models\EntregaPolicia::firstOrFail();
        $this->post('/entregas-policia', $datos)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('entregas_policia', 1);
        $this->assertDatabaseCount('vales_combustible', 3);
        $this->assertSame(['000001', '000002', '000003'], $entrega->vales()->orderBy('numero_vale')->pluck('numero_vale')->all());
        $vale = $entrega->vales()->firstOrFail();
        $this->assertNull($vale->vehiculo_id);
        $this->assertNull($vale->fecha);
        $this->assertNull($vale->total);
        $this->assertSame(0, ValeCombustible::conConsumo()->count());
        $this->get('/entregas-policia')->assertOk()->assertSee('POL-2026');
        $this->get('/entregas-policia/'.$entrega->id.'?numero=2')->assertOk()->assertSee('000002');
        $this->get('/entregas-policia/'.$entrega->id.'/imprimir')->assertOk()->assertSee('000001')->assertSee('000003')->assertDontSee('Número de factura')->assertDontSee('Apertura programática');
        $this->get('/vales-combustible/'.$vale->id.'/imprimir')->assertOk();
        $this->get('/lotes-vales/'.$lote->id)->assertOk();
        $this->get('/vales-combustible')->assertOk();
        foreach (['consumo', 'vehiculos', 'aperturas'] as $reporte) {
            $this->get('/reportes/'.$reporte)->assertOk();
        }
        $this->get('/vales-combustible/'.$vale->id.'/edit')->assertOk()->assertSee('Fecha de uso');
        $this->put('/vales-combustible/'.$vale->id, array_replace($this->emision, ['fecha' => '2026-11-02',
            'lote_vale_id' => $lote->id, 'numero_vale' => $vale->numero_vale, 'cantidad_litros' => 10, 'precio_unitario' => 6.96, 'numero_factura' => 'POL-FACT-1']))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['id' => $vale->id, 'numero_vale' => '000001', 'estado' => 'UTILIZADO', 'total' => 69.6]);
        $this->assertSame(1, ValeCombustible::conConsumo()->count());
        $segundo = $entrega->vales()->where('numero_vale', '000002')->firstOrFail();
        $this->post('/vales-combustible/'.$segundo->id.'/cerrar-entrega', ['estado' => 'DEVUELTO', 'motivo' => 'Sin utilizar'])->assertSessionHasNoErrors();
        $this->put('/vales-combustible/'.$segundo->id, $this->emision + ['lote_vale_id' => $lote->id, 'numero_vale' => '000002'])->assertStatus(409);
        $this->get('/entregas-policia/'.$entrega->id.'/imprimir')->assertSee('000003')->assertDontSee('N.º 000001')->assertDontSee('N.º 000002');
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['numero_vale' => '000004']);
        $this->post('/entregas-policia', array_replace($datos, ['cantidad' => 100, 'solicitud_token' => (string) \Illuminate\Support\Str::uuid()]))->assertSessionHasErrors('cantidad');
        $this->assertDatabaseCount('entregas_policia', 1);
        $this->assertDatabaseCount('vales_combustible', 4);
        $this->travelBack();
    }

    public function test_nueva_solicitud_guarda_datos_y_calcula_total_en_servidor(): void
    {
        $tipo = DB::table('tipos_combustible')->insertGetId(['nombre' => 'Gasolina', 'estado' => true]);
        $estacion = DB::table('estaciones_servicio')->insertGetId(['nombre' => 'Estación prueba', 'estado' => true]);
        $this->post('/lotes-vales', $this->datosLote() + ['tipo_combustible_id' => $tipo, 'estacion_servicio_id' => $estacion])
            ->assertSessionHasNoErrors();
        $lote = LoteVale::firstOrFail();
        $this->get('/vales-combustible/create')->assertOk()->assertSee('Nueva solicitud')
            ->assertSee('Estación del lote')->assertSee('Combustible del lote')
            ->assertSee('Código:')->assertSee('Placa:')->assertSee('Unidad:');
        $datos = $this->emision + ['lote_vale_id' => $lote->id, 'tipo_combustible_id' => $tipo,
            'estacion_servicio_id' => $estacion, 'cantidad_litros' => '65.37', 'precio_unitario' => '6.96',
            'total' => 1, 'numero_factura' => 'FACT-229830', 'observacion' => 'Camioneta de Educación'];
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('vales_combustible', ['numero_vale' => '000001',
            'estacion_servicio_id' => $estacion, 'tipo_combustible_id' => $tipo,
            'cantidad_litros' => null, 'precio_unitario' => null, 'total' => null,
            'numero_factura' => null, 'estado' => 'EMITIDO', 'observacion' => 'Camioneta de Educación']);
        $vale = ValeCombustible::firstOrFail();
        $this->post('/vales-combustible/'.$vale->id.'/completar', [
            'numero_factura' => 'FACT-229830', 'cantidad_litros' => '65.37', 'precio_unitario' => '6.96',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['id' => $vale->id, 'total' => 454.98, 'estado' => 'COMPLETADO']);
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('vales_combustible', 2);
        $this->assertNull(ValeCombustible::orderByDesc('id')->first()->total);
    }

    public function test_solicitud_pendiente_se_completa_con_factura_que_no_se_imprime(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $datos = $this->emision + ['lote_vale_id' => $lote->id, 'cantidad_litros' => '',
            'precio_unitario' => '', 'numero_factura' => ''];
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $vale = ValeCombustible::firstOrFail();
        $this->assertNull($vale->total);
        $this->assertNull($vale->numero_factura);
        $this->get('/vales-combustible/'.$vale->id)->assertOk()->assertSee('Pendiente');
        $this->get('/vales-combustible/'.$vale->id.'/edit')->assertStatus(409);
        $this->put('/vales-combustible/'.$vale->id, $this->emision + ['lote_vale_id' => $lote->id])->assertStatus(409);
        $this->get('/vales-combustible/'.$vale->id.'/completar')->assertOk();
        $this->post('/vales-combustible/'.$vale->id.'/completar', ['cantidad_litros' => '53.0172'])
            ->assertSessionHasErrors(['numero_factura', 'precio_unitario']);
        $datos = ['numero_factura' => 'FACT-229830', 'cantidad_litros' => '53.0172', 'precio_unitario' => '6.96'];
        $this->post('/vales-combustible/'.$vale->id.'/completar', $datos)->assertSessionHasNoErrors();
        $this->assertSame('COMPLETADO', $vale->fresh()->estado);
        $this->assertDatabaseHas('vales_combustible', ['id' => $vale->id, 'numero_factura' => 'FACT-229830', 'total' => 369.00]);
        $this->get('/vales-combustible/'.$vale->id)->assertSee('FACT-229830');
        $this->get('/vales-combustible/'.$vale->id.'/imprimir')->assertOk()
            ->assertDontSee('Número de factura')->assertDontSee('FACT-229830')
            ->assertDontSee('Apertura programática')->assertSee('369.00');
    }

    public function test_lotes_sin_combustible_ni_estacion_y_rangos_por_periodo(): void
    {
        $this->post('/lotes-vales', $this->datosLote())->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('lotes_vales', ['numero_inicial' => 1, 'tipo_combustible_id' => null, 'estacion_servicio_id' => null]);
        $this->post('/lotes-vales', array_replace($this->datosLote(), ['numero_inicial' => 2, 'numero_final' => 10]))->assertSessionHasErrors('numero_inicial');
        $otro = Periodo::create(['mes' => 1, 'anio' => 2027, 'fecha_inicio' => '2027-01-01', 'fecha_fin' => '2027-01-31', 'estado' => true]);
        $this->post('/lotes-vales', array_replace($this->datosLote(), ['periodo_id' => $otro->id]))->assertSessionHasNoErrors();
        $this->get('/lotes-vales')->assertOk()->assertSee('10/2026');
        $this->get('/lotes-vales/create')->assertOk()->assertSee('name="tipo_combustible_id"', false)
            ->assertSee('name="estacion_servicio_id"', false);
    }

    public function test_emision_correlativa_agotamiento_e_impresion(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $datos = $this->emision + ['lote_vale_id' => $lote->id];
        $this->get('/vales-combustible/create')->assertOk()->assertSee('Próximo: 000001');
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->assertSame(['000001', '000002'], ValeCombustible::orderBy('numero_vale')->pluck('numero_vale')->all());
        $this->post('/vales-combustible', $datos)->assertSessionHasErrors('lote_vale_id');
        $vale = ValeCombustible::first();
        $this->get('/vales-combustible/'.$vale->id)->assertOk()->assertSee('Imprimir');
        $this->get('/vales-combustible/'.$vale->id.'/imprimir')->assertOk()->assertSee('Vale de combustible N.º 000001')->assertSee('10/2026');
        $this->get('/lotes-vales/'.$lote->id)->assertOk()->assertSee('No disponible para emisión');
        $this->put('/lotes-vales/'.$lote->id, array_replace($this->datosLote(), ['numero_final' => 110]))->assertSessionHasErrors('numero_inicial');
        $this->delete('/vales-combustible/'.$vale->id)->assertSessionHas('error');
        $this->assertDatabaseCount('vales_combustible', 2);
        $this->assertDatabaseCount('auditorias', 2);
    }

    public function test_mismo_numero_en_otro_periodo_y_fecha_fuera_de_periodo(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasNoErrors();
        $otro = Periodo::create(['mes' => 1, 'anio' => 2027, 'fecha_inicio' => '2027-01-01', 'fecha_fin' => '2027-01-31', 'estado' => true]);
        $lote2 = LoteVale::create(array_replace($this->datosLote(), ['periodo_id' => $otro->id]));
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote2->id])->assertSessionHasErrors('fecha');
        $this->post('/vales-combustible', array_replace($this->emision, ['fecha' => '2027-01-01', 'lote_vale_id' => $lote2->id]))->assertSessionHasNoErrors();
        $this->assertSame(['000001', '000001'], ValeCombustible::pluck('numero_vale')->all());
    }

    public function test_consumo_por_vale_y_numero_inmutable(): void
    {
        $tipo = DB::table('tipos_combustible')->insertGetId(['nombre' => 'Diésel']);
        $estacion = DB::table('estaciones_servicio')->insertGetId(['nombre' => 'Estación prueba']);
        $lote = LoteVale::create($this->datosLote() + ['tipo_combustible_id' => $tipo, 'estacion_servicio_id' => $estacion]);
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id]);
        $vale = ValeCombustible::firstOrFail();
        $datos = ['numero_factura' => 'FACT-1', 'cantidad_litros' => 20, 'precio_unitario' => 5];
        $this->post('/vales-combustible/'.$vale->id.'/completar', $datos)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['id' => $vale->id, 'total' => 100, 'tipo_combustible_id' => $tipo, 'estacion_servicio_id' => $estacion]);
        $this->put('/vales-combustible/'.$vale->id, array_replace($datos, ['numero_vale' => '000002']))->assertStatus(409);
        $this->get('/reportes/consumo')->assertOk()->assertSee('Diésel');
    }

    public function test_flujo_normal_completa_o_anula_y_conserva_siempre_el_numero(): void
    {
        $lote = LoteVale::create(array_replace($this->datosLote(), ['numero_final' => 3]));
        $this->post('/vales-combustible', $this->emision + [
            'lote_vale_id' => $lote->id,
            'cantidad_litros' => 20,
            'precio_unitario' => 5,
            'numero_factura' => 'NO-ACEPTAR-EN-EMISION',
            'total' => 1,
        ])->assertSessionHasNoErrors();

        $primero = ValeCombustible::where('numero_vale', '000001')->firstOrFail();
        $this->assertSame('EMITIDO', $primero->estado);
        $this->assertNull($primero->cantidad_litros);
        $this->assertNull($primero->precio_unitario);
        $this->assertNull($primero->numero_factura);
        $this->assertNull($primero->total);

        $this->post('/vales-combustible/'.$primero->id.'/completar', [])->assertSessionHasErrors([
            'numero_factura', 'cantidad_litros', 'precio_unitario',
        ]);
        $this->post('/vales-combustible/'.$primero->id.'/completar', [
            'numero_factura' => 'FACT-1', 'cantidad_litros' => '12,5', 'precio_unitario' => '6,96',
            'total' => 0, 'estado' => 'ANULADO',
        ])->assertSessionHasNoErrors();
        $primero->refresh();
        $this->assertSame('COMPLETADO', $primero->estado);
        $this->assertSame('000001', $primero->numero_vale);
        $this->assertSame('87.00', $primero->total);
        $this->get('/vales-combustible/'.$primero->id.'/edit')->assertStatus(409);
        $this->put('/vales-combustible/'.$primero->id, [])->assertStatus(409);
        $this->get('/vales-combustible/'.$primero->id.'/completar')->assertStatus(409);
        $this->post('/vales-combustible/'.$primero->id.'/anular', ['motivo_anulacion' => 'No corresponde'])
            ->assertStatus(409);

        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasNoErrors();
        $segundo = ValeCombustible::where('numero_vale', '000002')->firstOrFail();
        $this->post('/vales-combustible/'.$segundo->id.'/anular', ['motivo_anulacion' => 'Vale emitido por error'])
            ->assertSessionHasNoErrors();
        $segundo->refresh();
        $this->assertSame('ANULADO', $segundo->estado);
        $this->assertSame('000002', $segundo->numero_vale);
        $this->assertSame('Vale emitido por error', $segundo->motivo_anulacion);
        $this->assertNotNull($segundo->fecha_anulacion);
        $this->assertDatabaseHas('auditorias', [
            'tabla' => 'vales_combustible',
            'registro_id' => $segundo->id,
            'usuario_id' => auth()->id(),
            'accion' => 'MODIFICAR',
        ]);
        $this->get('/vales-combustible/'.$segundo->id.'/completar')->assertStatus(409);
        $this->put('/vales-combustible/'.$segundo->id, [])->assertStatus(409);
        $this->post('/vales-combustible/'.$segundo->id.'/anular', ['motivo_anulacion' => 'Otra vez'])
            ->assertStatus(409);
        $this->get('/vales-combustible/'.$segundo->id.'/imprimir')->assertStatus(409);

        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['numero_vale' => '000003', 'estado' => 'EMITIDO']);
        $this->delete('/vales-combustible/'.$segundo->id)->assertSessionHas('error');
        $this->assertDatabaseHas('vales_combustible', ['id' => $segundo->id, 'numero_vale' => '000002', 'estado' => 'ANULADO']);
        $this->get('/vales-combustible')->assertOk()
            ->assertSee('EMITIDO / PENDIENTE')->assertSee('COMPLETADO')->assertSee('ANULADO');
    }

    public function test_lotes_y_periodos_inactivos_no_emiten_y_no_se_borra_lote_usado(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $datos = $this->emision + ['lote_vale_id' => $lote->id];
        $lote->update(['estado' => false]);
        $this->post('/vales-combustible', $datos)->assertSessionHasErrors('lote_vale_id');
        $lote->update(['estado' => true]);
        $this->periodo->update(['estado' => false]);
        $this->post('/vales-combustible', $datos)->assertSessionHasErrors('lote_vale_id');
        $this->periodo->update(['estado' => true]);
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->delete('/lotes-vales/'.$lote->id)->assertSessionHas('error');
        $this->get('/lotes-vales/'.$lote->id.'/edit')->assertOk();
        $this->get('/vales-combustible')->assertOk()->assertSee('10/2026')->assertSee('Imprimir');
        $this->assertDatabaseCount('lotes_vales', 1);
    }

    public function test_continua_entre_meses_y_guarda_seis_digitos_en_base_de_datos(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasNoErrors();
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasNoErrors();
        $noviembre = Periodo::create(['mes' => 11, 'anio' => 2026, 'fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-30', 'estado' => true]);
        $this->post('/lotes-vales', array_replace($this->datosLote(), ['periodo_id' => $noviembre->id]))->assertSessionHasErrors('numero_inicial');
        $this->post('/lotes-vales', array_replace($this->datosLote(), ['periodo_id' => $noviembre->id, 'numero_inicial' => 3, 'numero_final' => 10]))->assertSessionHasNoErrors();
        $lote2 = LoteVale::latest('id')->first();
        $this->post('/vales-combustible', array_replace($this->emision, ['fecha' => '2026-11-01', 'lote_vale_id' => $lote2->id]))->assertSessionHasNoErrors();
        $this->assertSame(['000001', '000002', '000003'], DB::table('vales_combustible')->orderBy('id')->pluck('numero_vale')->all());
        $this->assertDatabaseHas('numeraciones_vales', ['anio' => 2026, 'ultimo_numero' => 3]);
    }

    public function test_no_salta_numeros_y_rechaza_mas_de_seis_digitos(): void
    {
        $lote = LoteVale::create(array_replace($this->datosLote(), ['numero_inicial' => 50, 'numero_final' => 60]));
        $this->post('/vales-combustible', $this->emision + ['lote_vale_id' => $lote->id])->assertSessionHasErrors('lote_vale_id');
        $this->post('/lotes-vales', array_replace($this->datosLote(), ['numero_final' => 1000000]))->assertSessionHasErrors('numero_final');
        $this->assertDatabaseCount('vales_combustible', 0);
    }
}
