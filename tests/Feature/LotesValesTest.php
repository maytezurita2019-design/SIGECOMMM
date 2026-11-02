<?php

namespace Tests\Feature;

use App\Models\{Usuario, Periodo, LoteVale, ValeCombustible};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            'nombre' => 'TECNICO_I',
            'estado' => true,
        ]);
        $usuario = Usuario::create(['nombre' => 'Prueba', 'apellido' => 'Local', 'ci' => 'TEST', 'correo' => 'test@example.test', 'contrasena' => bcrypt('test'), 'rol_id' => $rol, 'estado' => true]);
        $this->actingAs($usuario);
        $this->periodo = Periodo::create(['mes' => 10, 'anio' => 2026, 'fecha_inicio' => '2026-10-01', 'fecha_fin' => '2026-10-31', 'estado' => true]);
        $unidad = DB::table('unidades')->insertGetId(['nombre' => 'Obras']);
        $vehiculo = DB::table('vehiculos')->insertGetId(['codigo' => 'V1', 'placa' => 'TEST', 'unidad_id' => $unidad]);
        $fuente = DB::table('fuentes_financiamiento')->insertGetId(['codigo' => '1', 'nombre' => 'Fuente']);
        $organismo = DB::table('organismos_financiadores')->insertGetId(['codigo' => '1', 'nombre' => 'Organismo']);
        $apertura = DB::table('aperturas_programaticas')->insertGetId(['codigo' => '1', 'fuente_financiamiento_id' => $fuente, 'organismo_financiador_id' => $organismo]);
        DB::table('vehiculos')->where('id', $vehiculo)->update([
            'apertura_programatica_id' => $apertura,
        ]);
        $this->emision = ['fecha' => '2026-10-01', 'vehiculo_id' => $vehiculo, 'apertura_programatica_id' => $apertura, 'kilometraje' => '100'];
    }

    private function prepararEmisionGasolina(): array
    {
        $tipo = DB::table('tipos_combustible')->where('nombre', 'Gasolina')->value('id');
        $tipo ??= DB::table('tipos_combustible')->insertGetId([
            'nombre' => 'Gasolina', 'estado' => true,
        ]);
        $estacion = DB::table('estaciones_servicio')->where('nombre', 'Estación de prueba')->value('id');
        $estacion ??= DB::table('estaciones_servicio')->insertGetId([
            'nombre' => 'Estación de prueba', 'estado' => true,
        ]);

        return array_merge($this->emision, [
            'tipo_combustible_id' => $tipo,
            'estacion_servicio_id' => $estacion,
            'detalle_asignacion' => 'Asignación de prueba',
        ]);
    }

    private function prepararEmisionDiesel(): array
    {
        $tipo = DB::table('tipos_combustible')->where('nombre', 'Diésel')->value('id');
        $tipo ??= DB::table('tipos_combustible')->insertGetId([
            'nombre' => 'Diésel', 'estado' => true,
        ]);
        $estacion = DB::table('estaciones_servicio')->where('nombre', 'Estación de prueba')->value('id');
        $estacion ??= DB::table('estaciones_servicio')->insertGetId([
            'nombre' => 'Estación de prueba', 'estado' => true,
        ]);
        $lugar = DB::table('lugares_entrega')->insertGetId([
            'nombre' => 'Patio de prueba',
            'estado' => true,
        ]);

        return array_merge($this->emision, [
            'tipo_combustible_id' => $tipo,
            'estacion_servicio_id' => $estacion,
            'lugar_entrega_id' => $lugar,
            'detalle_asignacion' => 'Asignación de prueba',
        ]);
    }

    private function datosLote(): array
    {
        return ['numero_inicial' => 1, 'numero_final' => 2, 'fecha_recepcion' => '2026-10-01', 'periodo_id' => $this->periodo->id, 'estado' => 1];
    }

    public function test_calcula_total_con_coma_y_litros_de_alta_precision(): void
    {
        $datos = $this->prepararEmisionGasolina() + [
            'cantidad_litros' => '53,0172413793103',
            'precio_unitario' => '6,96',
        ];
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $vale = ValeCombustible::firstOrFail();
        $this->assertNull($vale->lote_vale_id);
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
        $periodoNoviembre = Periodo::create([
            'mes' => 11,
            'anio' => 2026,
            'fecha_inicio' => '2026-11-01',
            'fecha_fin' => '2026-11-30',
            'estado' => true,
        ]);
        DB::table('tipos_combustible')->insert([
            'nombre' => 'Gasolina',
            'estado' => true,
        ]);
        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-11-03',
            'periodo_id' => $periodoNoviembre->id,
            'cantidad_vales' => 10,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $lote = LoteVale::latest('id')->firstOrFail();
        $inicio = (int) $lote->numero_inicial;
        $fin = (int) $lote->numero_final;
        $datos = [
            'lote_vale_id' => $lote->id,
            'contrato' => 'POL-2026',
            'dependencia' => 'Policía Sacaba',
            'recibido_por' => 'Responsable de Policía',
            'fecha_entrega' => '2026-11-03',
            'cantidad_gasolina' => $lote->numero_final - $lote->numero_inicial + 1,
            'cantidad_diesel' => 0,
            'apertura_programatica_id' => $this->emision['apertura_programatica_id'],
            'solicitud_token' => (string) \Illuminate\Support\Str::uuid(),
        ];
        $this->get('/entregas-policia/create')->assertOk();
        $this->post('/entregas-policia', $datos)->assertSessionHasNoErrors()->assertRedirect();
        $entrega = \App\Models\EntregaPolicia::firstOrFail();
        $this->post('/entregas-policia', $datos)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('entregas_policia', 1);
        $this->assertDatabaseCount('vales_combustible', 10);
        $this->assertSame(
            range($inicio, $fin),
            $entrega->vales()->orderBy('numero_vale')->pluck('numero_vale')
                ->map(fn ($numero) => (int) $numero)->all()
        );
        $this->assertSame(100090, (int) DB::table('series_contadores')
            ->where('codigo_serie', 'POL-GAS')->where('anio', 2026)->value('ultimo_numero') - 10);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2026,
            'ultimo_numero' => $fin,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS',
            'anio' => 2026,
            'ultimo_numero' => 300130,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-DIE',
            'anio' => 2026,
            'ultimo_numero' => 400230,
        ]);
        $vale = $entrega->vales()->firstOrFail();
        $this->assertNull($vale->vehiculo_id);
        $this->assertNull($vale->fecha);
        $this->assertNull($vale->total);
        $this->assertSame(0, ValeCombustible::conConsumo()->count());
        $this->get('/entregas-policia')->assertOk()->assertSee('POL-2026');
        $this->get('/entregas-policia/'.$entrega->id.'?numero=' . $inicio)->assertOk()
            ->assertSee(sprintf('%06d', $inicio));
        $this->get('/entregas-policia/'.$entrega->id.'/imprimir')->assertOk()
            ->assertSee(sprintf('%06d', $inicio))
            ->assertSee(sprintf('%06d', $fin))
            ->assertDontSee('Número de factura')
            ->assertDontSee('Apertura programática');
        $this->get('/vales-combustible/'.$vale->id.'/imprimir')->assertOk();
        $this->get('/lotes-vales/'.$lote->id)->assertOk();
        $this->get('/vales-combustible')->assertOk();
        foreach (['consumo', 'vehiculos', 'aperturas'] as $reporte) {
            $this->get('/reportes/'.$reporte)->assertOk();
        }
        $this->assertDatabaseCount('entregas_policia', 1);
        $this->assertDatabaseCount('vales_combustible', 10);
        $this->travelBack();
    }

    public function test_nueva_solicitud_guarda_datos_y_calcula_total_en_servidor(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-10-15'));
        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-10-01',
            'periodo_id' => $this->periodo->id,
            'cantidad_vales' => 1,
        ])
            ->assertSessionHasNoErrors();
        $lote = LoteVale::firstOrFail();
        $datos = $this->prepararEmisionGasolina() + [
            'cantidad_litros' => '65.37',
            'precio_unitario' => '6.96',
            'total' => 1, 'numero_factura' => 'FACT-229830', 'observacion' => 'Camioneta de Educación'];
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('vales_combustible', ['numero_vale' => '300131',
            'lote_vale_id' => null,
            'cantidad_litros' => null, 'precio_unitario' => null, 'total' => null,
            'numero_factura' => null, 'estado' => 'EMITIDO', 'observacion' => null]);
        $vale = ValeCombustible::firstOrFail();
        $this->assertNotNull($lote);
        $this->post('/vales-combustible/'.$vale->id.'/completar', [
            'numero_factura' => 'FACT-229830', 'cantidad_litros' => '65.37', 'precio_unitario' => '6.96',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['id' => $vale->id, 'total' => 454.98, 'estado' => 'COMPLETADO']);
        $this->post('/vales-combustible', $this->prepararEmisionGasolina())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('vales_combustible', 2);
        $this->assertNull(ValeCombustible::orderByDesc('id')->first()->total);
        $this->travelBack();
    }

    public function test_solicitud_pendiente_se_completa_con_factura_que_no_se_imprime(): void
    {
        $datos = $this->prepararEmisionGasolina() + ['cantidad_litros' => '',
            'precio_unitario' => '', 'numero_factura' => ''];
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $vale = ValeCombustible::firstOrFail();
        $this->assertNull($vale->total);
        $this->assertNull($vale->numero_factura);
        $this->get('/vales-combustible/'.$vale->id)->assertOk()->assertSee('Pendiente');
        $this->get('/vales-combustible/'.$vale->id.'/edit')->assertOk();
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
        $this->travelTo(\Carbon\Carbon::parse('2026-10-15'));
        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-10-01',
            'periodo_id' => $this->periodo->id,
            'cantidad_vales' => 2,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('lotes_vales', [
            'numero_inicial' => 100091,
            'numero_final' => 100092,
        ]);
        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-10-01',
            'periodo_id' => $this->periodo->id,
            'cantidad_vales' => 3,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('lotes_vales', [
            'numero_inicial' => 100093,
            'numero_final' => 100095,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2026,
            'ultimo_numero' => 100095,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS',
            'anio' => 2026,
            'ultimo_numero' => 300130,
        ]);
        $this->travelBack();
        $this->get('/lotes-vales')->assertOk()->assertSee('10/2026');
        $this->get('/lotes-vales/create')->assertOk()
            ->assertSee('name="cantidad_vales"', false);
    }

    public function test_emision_correlativa_agotamiento_e_impresion(): void
    {
        $datos = $this->prepararEmisionGasolina();
        $this->get('/vales-combustible/create')->assertOk()->assertSee('N.º 300131');
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->assertSame([300131, 300132], ValeCombustible::orderBy('numero_vale')
            ->pluck('numero_vale')->map(fn ($numero) => (int) $numero)->all());
        $vale = ValeCombustible::where('numero_vale', 300131)->firstOrFail();
        $this->get('/vales-combustible/'.$vale->id)->assertOk()->assertSee('Imprimir');
        $this->get('/vales-combustible/'.$vale->id.'/imprimir')->assertOk()
            ->assertSee('N.º 300131');
        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2026, 'ultimo_numero' => 300132,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2026, 'ultimo_numero' => 300132,
        ]);
    }

    public function test_emision_gams_no_necesita_lote_y_no_crea_series_para_anios_sin_configuracion(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-03'));
        $datos = $this->prepararEmisionGasolina();

        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $vale = ValeCombustible::firstOrFail();
        $this->assertSame(300131, (int) $vale->numero_vale);
        $this->assertNull($vale->lote_vale_id);
        $columnaLote = collect(Schema::getColumns('vales_combustible'))
            ->firstWhere('name', 'lote_vale_id');
        $this->assertTrue($columnaLote['nullable']);
        $this->assertTrue(collect(DB::select('PRAGMA foreign_key_list(vales_combustible)'))
            ->contains(fn ($foreignKey) => $foreignKey->from === 'lote_vale_id' && $foreignKey->table === 'lotes_vales'));

        $this->travelTo(\Carbon\Carbon::parse('2027-01-03'));
        $this->post('/vales-combustible', array_replace($datos, [
            'fecha' => '2027-01-01',
        ]))->assertSessionHasErrors('fecha');

        $this->assertDatabaseCount('vales_combustible', 1);
        $this->assertDatabaseMissing('series_correlativas', [
            'codigo_serie' => 'GAMS-GAS',
            'anio' => 2027,
        ]);
        $this->travelBack();
    }

    public function test_consumo_por_vale_y_numero_inmutable(): void
    {
        $datosEmision = $this->prepararEmisionDiesel();
        $tipo = $datosEmision['tipo_combustible_id'];
        $estacion = $datosEmision['estacion_servicio_id'];
        $this->post('/vales-combustible', $datosEmision)->assertSessionHasNoErrors();
        $vale = ValeCombustible::firstOrFail();
        $this->assertNull($vale->lote_vale_id);
        $datos = ['numero_factura' => 'FACT-1', 'cantidad_litros' => 20, 'precio_unitario' => 5];
        $this->post('/vales-combustible/'.$vale->id.'/completar', $datos)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['id' => $vale->id, 'total' => 100, 'tipo_combustible_id' => $tipo, 'estacion_servicio_id' => $estacion]);
        $this->put('/vales-combustible/'.$vale->id, array_replace($datosEmision, [
            'numero_vale' => '000002',
            'numero_factura' => 'FACT-1',
            'cantidad_litros' => 20,
            'precio_unitario' => 5,
        ]))->assertSessionHasNoErrors();
        $this->assertSame(400231, (int) $vale->fresh()->numero_vale);
        $this->get('/reportes/consumo')->assertOk()->assertSee('Diésel');
    }

    public function test_flujo_normal_completa_o_anula_y_conserva_siempre_el_numero(): void
    {
        $datosEmision = $this->prepararEmisionGasolina();
        $this->post('/vales-combustible', $datosEmision + [
            'cantidad_litros' => 20,
            'precio_unitario' => 5,
            'numero_factura' => 'NO-ACEPTAR-EN-EMISION',
            'total' => 1,
        ])->assertSessionHasNoErrors();

        $primero = ValeCombustible::where('numero_vale', 300131)->firstOrFail();
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
        $this->assertSame(300131, (int) $primero->numero_vale);
        $this->assertSame('87.00', $primero->total);
        $this->get('/vales-combustible/'.$primero->id.'/edit')->assertOk();
        $this->get('/vales-combustible/'.$primero->id.'/completar')->assertStatus(409);
        $this->post('/vales-combustible/'.$primero->id.'/anular', ['motivo_anulacion' => 'No corresponde'])
            ->assertStatus(409);

        $this->post('/vales-combustible', $datosEmision)->assertSessionHasNoErrors();
        $segundo = ValeCombustible::where('numero_vale', 300132)->firstOrFail();
        $this->post('/vales-combustible/'.$segundo->id.'/anular', ['motivo_anulacion' => 'Vale emitido por error'])
            ->assertSessionHasNoErrors();
        $segundo->refresh();
        $this->assertSame('ANULADO', $segundo->estado);
        $this->assertSame(300132, (int) $segundo->numero_vale);
        $this->assertSame('Vale emitido por error', $segundo->motivo_anulacion);
        $this->assertNotNull($segundo->fecha_anulacion);
        $this->assertDatabaseHas('auditorias', [
            'tabla' => 'vales_combustible',
            'registro_id' => $segundo->id,
            'usuario_id' => auth()->id(),
            'accion' => 'ANULAR',
        ]);
        $this->get('/vales-combustible/'.$segundo->id.'/completar')->assertStatus(409);
        $this->put('/vales-combustible/'.$segundo->id, [])->assertStatus(409);
        $this->post('/vales-combustible/'.$segundo->id.'/anular', ['motivo_anulacion' => 'Otra vez'])
            ->assertStatus(409);
        $this->get('/vales-combustible/'.$segundo->id.'/imprimir')->assertStatus(409);

        $this->post('/vales-combustible', $datosEmision)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', ['numero_vale' => '300133', 'estado' => 'EMITIDO']);
        $this->delete('/vales-combustible/'.$segundo->id)->assertStatus(405);
        $this->assertDatabaseHas('vales_combustible', ['id' => $segundo->id, 'numero_vale' => '300132', 'estado' => 'ANULADO']);
        $this->get('/vales-combustible')->assertOk()
            ->assertSee('Pendientes')->assertSee('Completados')->assertSee('Anulados');
    }

    public function test_emision_gams_no_necesita_lotes_activos_ni_periodos(): void
    {
        $lote = LoteVale::create($this->datosLote());
        $lote->update(['estado' => false]);
        $this->periodo->update(['estado' => false]);
        $this->post('/vales-combustible', $this->prepararEmisionGasolina())
            ->assertSessionHasNoErrors();
        $this->assertNull(ValeCombustible::firstOrFail()->lote_vale_id);
        $this->assertDatabaseHas('lotes_vales', [
            'id' => $lote->id,
            'estado' => false,
        ]);
    }

    public function test_continua_entre_meses_y_guarda_seis_digitos_en_base_de_datos(): void
    {
        $datos = $this->prepararEmisionGasolina();
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->post('/vales-combustible', $datos)->assertSessionHasNoErrors();
        $this->travelTo(\Carbon\Carbon::parse('2026-11-03'));
        $this->post('/vales-combustible', array_replace($datos, [
            'fecha' => '2026-11-01',
        ]))->assertSessionHasNoErrors();

        $this->assertSame([300131, 300132, 300133], DB::table('vales_combustible')
            ->orderBy('id')->pluck('numero_vale')->map(fn ($numero) => (int) $numero)->all());
        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2026, 'ultimo_numero' => 300133,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2026, 'ultimo_numero' => 300133,
        ]);
        $this->travelBack();
    }

    public function test_no_salta_numeros_y_rechaza_mas_de_seis_digitos(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-10-15'));
        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-10-01',
            'periodo_id' => $this->periodo->id,
            'cantidad_vales' => 5,
        ])->assertSessionHasNoErrors();
        $this->post('/vales-combustible', $this->prepararEmisionGasolina())
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('vales_combustible', [
            'numero_vale' => 300131,
            'lote_vale_id' => null,
        ]);
        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-10-01',
            'periodo_id' => $this->periodo->id,
            'cantidad_vales' => 1000000,
        ])->assertSessionHasErrors('cantidad_vales');
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2026,
            'ultimo_numero' => 100095,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS',
            'anio' => 2026,
            'ultimo_numero' => 300131,
        ]);
        $this->travelBack();
    }
}
