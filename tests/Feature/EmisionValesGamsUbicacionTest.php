<?php

namespace Tests\Feature;

use App\Models\EstacionServicio;
use App\Models\LugarEntrega;
use App\Models\Rol;
use App\Models\TipoCombustible;
use App\Models\Usuario;
use App\Models\ValeCombustible;
use App\Models\Vehiculo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmisionValesGamsUbicacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('apellido')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->unique();
            $table->string('contrasena');
            $table->foreignId('rol_id');
            $table->boolean('estado')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable();
            $table->string('accion', 50);
            $table->string('tabla', 100);
            $table->unsignedBigInteger('registro_id');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('sigla')->nullable();
            $table->string('tipo')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('fuentes_financiamiento', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('organismos_financiadores', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('aperturas_programaticas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('fuente_financiamiento_id')->nullable();
            $table->unsignedBigInteger('organismo_financiador_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('placa')->nullable();
            $table->string('tipo')->nullable();
            $table->unsignedBigInteger('unidad_id')->nullable();
            $table->unsignedBigInteger('apertura_programatica_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('tipos_combustible', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad_medida')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('estaciones_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('lugares_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
        Schema::create('series_contadores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_serie', 16);
            $table->unsignedSmallInteger('anio');
            $table->unsignedBigInteger('ultimo_numero');
            $table->string('prefijo', 32);
            $table->timestamps();
        });
        Schema::create('series_correlativas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_serie', 32);
            $table->string('institucion', 32);
            $table->string('combustible', 32);
            $table->unsignedBigInteger('ultimo_numero');
            $table->unsignedSmallInteger('anio');
            $table->timestamps();
        });
        Schema::create('vales_combustible', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serie_vale_id')->nullable();
            $table->unsignedBigInteger('serie_correlativa_id')->nullable();
            $table->unsignedBigInteger('entrega_policia_id')->nullable();
            $table->unsignedBigInteger('numero_vale');
            $table->unsignedSmallInteger('anio');
            $table->date('fecha');
            $table->unsignedBigInteger('lote_vale_id')->nullable();
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('apertura_programatica_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('numero_factura')->nullable();
            $table->decimal('cantidad_litros', 21, 13)->nullable();
            $table->decimal('precio_unitario', 12, 4)->nullable();
            $table->decimal('total', 14, 2)->nullable();
            $table->string('kilometraje', 50)->nullable();
            $table->text('observacion')->nullable();
            $table->text('detalle_asignacion')->nullable();
            $table->unsignedBigInteger('estacion_servicio_id')->nullable();
            $table->unsignedBigInteger('lugar_entrega_id')->nullable();
            $table->unsignedBigInteger('tipo_combustible_id')->nullable();
            $table->unsignedBigInteger('periodo_id')->nullable();
            $table->string('estado', 20)->default('EMITIDO');
            $table->text('motivo_anulacion')->nullable();
            $table->timestamp('fecha_anulacion')->nullable();
            $table->string('documento_respaldo')->nullable();
            $table->unsignedBigInteger('uso_registrado_por')->nullable();
            $table->timestamp('uso_registrado_en')->nullable();
            $table->timestamps();
        });

        $role = Rol::create(['nombre' => 'TECNICO_II', 'estado' => true]);
        $user = Usuario::create([
            'nombre' => 'Técnico',
            'apellido' => 'Prueba',
            'correo' => 'tecnico@example.test',
            'contrasena' => 'test-password',
            'rol_id' => $role->id,
            'estado' => true,
        ]);
        $this->actingAs($user);

        $aperturaId = DB::table('aperturas_programaticas')->insertGetId([
            'codigo' => 'AP-01',
            'estado' => true,
        ]);
        Vehiculo::create([
            'codigo' => 'VEH-01',
            'apertura_programatica_id' => $aperturaId,
            'estado' => true,
        ]);

        foreach ([
            ['GAMS-GAS', 10],
            ['GAMS-DIE', 20],
            ['POL-GAS', 30],
        ] as [$codigo, $ultimoNumero]) {
            DB::table('series_correlativas')->insert([
                'codigo_serie' => $codigo,
                'institucion' => $codigo === 'POL-GAS' ? 'POLICIA' : 'GAMS',
                'combustible' => str_ends_with($codigo, 'DIE') ? 'DIESEL' : 'GASOLINA',
                'ultimo_numero' => $ultimoNumero,
                'anio' => (int) now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('series_contadores')->insert([
                'codigo_serie' => $codigo,
                'anio' => (int) now()->year,
                'ultimo_numero' => $ultimoNumero,
                'prefijo' => $codigo . '-' . now()->year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_gasoline_and_diesel_use_their_required_location_data_without_crossing_series(): void
    {
        $gasolina = TipoCombustible::create(['nombre' => 'GASOLINA', 'estado' => true]);
        $diesel = TipoCombustible::create(['nombre' => 'DIESEL OIL', 'estado' => true]);
        $estacion = EstacionServicio::create(['nombre' => 'YPFB', 'estado' => true]);
        $lugar = LugarEntrega::create(['nombre' => 'Patio Norte', 'estado' => true]);

        $this->get(route('vales-combustible.create'))
            ->assertOk()
            ->assertSee('Estación de servicio')
            ->assertSee('Lugar de entrega')
            ->assertSee('data-ubicacion="diesel"', false)
            ->assertSee('YPFB')
            ->assertSee('Patio Norte');

        $this->post(route('vales-combustible.store'), $this->datosVale($gasolina->id, [
            'estacion_servicio_id' => $estacion->id,
        ]))->assertRedirect();

        $valeGasolina = ValeCombustible::query()->sole();
        $this->assertSame(11, (int) $valeGasolina->numero_vale);
        $this->assertSame($estacion->id, $valeGasolina->estacion_servicio_id);
        $this->assertNull($valeGasolina->lugar_entrega_id);
        $this->assertSame('GAMS-GAS', $valeGasolina->serieCorrelativa->codigo_serie);
        $this->get(route('vales-combustible.show', $valeGasolina))
            ->assertOk()
            ->assertSee('Vale N.º 000011')
            ->assertSee('YPFB')
            ->assertDontSee('Lugar de entrega');
        $this->get(route('vales-combustible.edit', $valeGasolina))
            ->assertOk()
            ->assertSee('000011');
        $this->get(route('vales-combustible.index', ['tipo' => 'GAMS']))
            ->assertOk()
            ->assertSee('000011');
        $this->get(route('vales-combustible.imprimir', $valeGasolina))
            ->assertOk()
            ->assertSee('N.º 000011')
            ->assertSee('YPFB');

        $this->post(route('vales-combustible.store'), $this->datosVale($diesel->id, [
            'estacion_servicio_id' => $estacion->id,
            'lugar_entrega_id' => $lugar->id,
        ]))->assertRedirect();

        $valeDiesel = ValeCombustible::query()->orderByDesc('id')->firstOrFail();
        $this->assertSame(21, (int) $valeDiesel->numero_vale);
        $this->assertSame($estacion->id, $valeDiesel->estacion_servicio_id);
        $this->assertSame($lugar->id, $valeDiesel->lugar_entrega_id);
        $this->assertSame('GAMS-DIE', $valeDiesel->serieCorrelativa->codigo_serie);
        $this->assertSame(11, (int) DB::table('series_contadores')->where('codigo_serie', 'GAMS-GAS')->value('ultimo_numero'));
        $this->assertSame(21, (int) DB::table('series_contadores')->where('codigo_serie', 'GAMS-DIE')->value('ultimo_numero'));
        $this->assertSame(30, (int) DB::table('series_contadores')->where('codigo_serie', 'POL-GAS')->value('ultimo_numero'));

        $this->get(route('vales-combustible.show', $valeDiesel))
            ->assertOk()
            ->assertSee('Vale N.º 0000021')
            ->assertSee('YPFB')
            ->assertSee('Patio Norte');
        $this->get(route('vales-combustible.edit', $valeDiesel))
            ->assertOk()
            ->assertSee('0000021');
        $this->get(route('vales-combustible.completar.formulario', $valeDiesel))
            ->assertOk()
            ->assertSee('0000021');
        $this->get(route('vales-combustible.index', ['tipo' => 'GAMS']))
            ->assertOk()
            ->assertSee('0000021');
        $this->get(route('vales-combustible.imprimir', $valeDiesel))
            ->assertOk()
            ->assertSee('N.º 0000021')
            ->assertSee('Patio Norte')
            ->assertDontSee('YPFB')
            ->assertDontSee('Estación de servicio:');
    }

    public function test_gasoline_preview_is_zero_padded_without_reserving_a_number(): void
    {
        TipoCombustible::create(['nombre' => 'GASOLINA', 'estado' => true]);
        TipoCombustible::create(['nombre' => 'DIESEL', 'estado' => true]);
        DB::table('series_contadores')->where('codigo_serie', 'GAMS-GAS')->update(['ultimo_numero' => 0]);
        DB::table('series_correlativas')->where('codigo_serie', 'GAMS-GAS')->update(['ultimo_numero' => 0]);

        $this->get(route('vales-combustible.create'))
            ->assertOk()
            ->assertSee('N.º 000001')
            ->assertSee('data-siguiente="1"', false)
            ->assertSee('data-siguiente="21"', false);

        $this->assertSame(
            0,
            (int) DB::table('series_contadores')->where('codigo_serie', 'GAMS-GAS')->value('ultimo_numero')
        );
        $this->assertSame(
            0,
            (int) DB::table('series_correlativas')->where('codigo_serie', 'GAMS-GAS')->value('ultimo_numero')
        );
    }

    public function test_gams_emission_rejects_missing_inactive_and_nonexistent_locations(): void
    {
        $diesel = TipoCombustible::create(['nombre' => 'DIESEL', 'estado' => true]);
        $estacion = EstacionServicio::create(['nombre' => 'YPFB', 'estado' => true]);
        $inactivo = LugarEntrega::create(['nombre' => 'Cerrado', 'estado' => false]);

        $this->get(route('vales-combustible.create'))
            ->assertOk()
            ->assertDontSee('Cerrado');

        $this->from(route('vales-combustible.create'))
            ->post(route('vales-combustible.store'), $this->datosVale($diesel->id))
            ->assertSessionHasErrors('estacion_servicio_id');

        $this->from(route('vales-combustible.create'))
            ->post(route('vales-combustible.store'), $this->datosVale($diesel->id, [
                'estacion_servicio_id' => $estacion->id,
            ]))
            ->assertSessionHasErrors('lugar_entrega_id');

        $estacionInactiva = EstacionServicio::create(['nombre' => 'YPFB inactiva', 'estado' => false]);
        $lugarActivo = LugarEntrega::create(['nombre' => 'Posta Municipal', 'estado' => true]);
        $this->from(route('vales-combustible.create'))
            ->post(route('vales-combustible.store'), $this->datosVale($diesel->id, [
                'estacion_servicio_id' => $estacionInactiva->id,
                'lugar_entrega_id' => $lugarActivo->id,
            ]))
            ->assertSessionHasErrors('estacion_servicio_id');

        $this->from(route('vales-combustible.create'))
            ->post(route('vales-combustible.store'), $this->datosVale($diesel->id, [
                'estacion_servicio_id' => $estacion->id,
                'lugar_entrega_id' => $inactivo->id,
            ]))
            ->assertSessionHasErrors('lugar_entrega_id');

        $this->from(route('vales-combustible.create'))
            ->post(route('vales-combustible.store'), $this->datosVale($diesel->id, [
                'estacion_servicio_id' => $estacion->id,
                'lugar_entrega_id' => 999,
            ]))
            ->assertSessionHasErrors('lugar_entrega_id');

        $this->from(route('vales-combustible.create'))
            ->post(route('vales-combustible.store'), $this->datosVale($diesel->id, [
                'estacion_servicio_id' => 999,
                'lugar_entrega_id' => $lugarActivo->id,
            ]))
            ->assertSessionHasErrors('estacion_servicio_id');

        $this->assertSame(0, ValeCombustible::query()->count());
        $this->assertSame(20, (int) DB::table('series_contadores')->where('codigo_serie', 'GAMS-DIE')->value('ultimo_numero'));
    }

    public function test_completed_voucher_type_cannot_be_changed(): void
    {
        $gasolina = TipoCombustible::create(['nombre' => 'GASOLINA', 'estado' => true]);
        $diesel = TipoCombustible::create(['nombre' => 'DIESEL', 'estado' => true]);
        $serieDieselId = DB::table('series_correlativas')->where('codigo_serie', 'GAMS-DIE')->value('id');
        $vale = ValeCombustible::create([
            'serie_correlativa_id' => $serieDieselId,
            'numero_vale' => 20,
            'anio' => (int) now()->year,
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'apertura_programatica_id' => 1,
            'usuario_id' => auth()->id(),
            'tipo_combustible_id' => $diesel->id,
            'estado' => 'COMPLETADO',
        ]);

        $this->put(route('vales-combustible.update', $vale), [
            'tipo_combustible_id' => $gasolina->id,
        ])->assertSessionHasErrors('tipo_combustible_id');

        $this->assertSame($diesel->id, $vale->fresh()->tipo_combustible_id);
    }

    public function test_completed_voucher_without_type_cannot_be_assigned_a_fuel_from_another_series(): void
    {
        $gasolina = TipoCombustible::create(['nombre' => 'GASOLINA', 'estado' => true]);
        $serieDieselId = DB::table('series_correlativas')->where('codigo_serie', 'GAMS-DIE')->value('id');
        $vale = ValeCombustible::create([
            'serie_correlativa_id' => $serieDieselId,
            'numero_vale' => 22,
            'anio' => (int) now()->year,
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'apertura_programatica_id' => 1,
            'usuario_id' => auth()->id(),
            'estado' => 'COMPLETADO',
        ]);

        $this->put(route('vales-combustible.update', $vale), [
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'tipo_combustible_id' => $gasolina->id,
            'numero_factura' => 'F-1',
            'cantidad_litros' => '1',
            'precio_unitario' => '1',
        ])->assertSessionHasErrors('tipo_combustible_id');

        $this->assertNull($vale->fresh()->tipo_combustible_id);
    }

    public function test_diesel_edit_accepts_only_active_places_and_keeps_historical_null(): void
    {
        $diesel = TipoCombustible::create(['nombre' => 'DIESEL', 'estado' => true]);
        $estacionAnterior = EstacionServicio::create(['nombre' => 'YPFB', 'estado' => true]);
        $estacionNueva = EstacionServicio::create(['nombre' => 'Estación nueva', 'estado' => true]);
        $estacionInactiva = EstacionServicio::create(['nombre' => 'Estación inactiva', 'estado' => false]);
        $lugarAnterior = LugarEntrega::create(['nombre' => 'Patio antiguo', 'estado' => true]);
        $lugarNuevo = LugarEntrega::create(['nombre' => 'Patio nuevo', 'estado' => true]);
        $inactivo = LugarEntrega::create(['nombre' => 'Cerrado', 'estado' => false]);
        $serieId = DB::table('series_correlativas')->where('codigo_serie', 'GAMS-DIE')->value('id');

        $vale = ValeCombustible::create([
            'serie_correlativa_id' => $serieId,
            'numero_vale' => 20,
            'anio' => (int) now()->year,
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'apertura_programatica_id' => 1,
            'usuario_id' => auth()->id(),
            'tipo_combustible_id' => $diesel->id,
            'estacion_servicio_id' => $estacionAnterior->id,
            'lugar_entrega_id' => $lugarAnterior->id,
            'estado' => 'EMITIDO',
        ]);

        $this->get(route('vales-combustible.edit', $vale))
            ->assertOk()
            ->assertSee('Estación de servicio')
            ->assertSee('YPFB')
            ->assertSee('Lugar de entrega')
            ->assertSee('Patio antiguo');

        $this->put(route('vales-combustible.update', $vale), [
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'estacion_servicio_id' => $estacionInactiva->id,
            'lugar_entrega_id' => $lugarAnterior->id,
        ])->assertSessionHasErrors('estacion_servicio_id');
        $this->assertSame($estacionAnterior->id, $vale->fresh()->estacion_servicio_id);

        $this->put(route('vales-combustible.update', $vale), [
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'estacion_servicio_id' => $estacionAnterior->id,
            'lugar_entrega_id' => $inactivo->id,
        ])->assertSessionHasErrors('lugar_entrega_id');
        $this->assertSame($lugarAnterior->id, $vale->fresh()->lugar_entrega_id);

        $this->put(route('vales-combustible.update', $vale), [
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'estacion_servicio_id' => $estacionNueva->id,
            'lugar_entrega_id' => $lugarNuevo->id,
        ])->assertRedirect(route('vales-combustible.show', $vale));
        $this->assertSame($estacionNueva->id, $vale->fresh()->estacion_servicio_id);
        $this->assertSame($lugarNuevo->id, $vale->fresh()->lugar_entrega_id);
        $this->get(route('vales-combustible.show', $vale))
            ->assertOk()
            ->assertSee('Estación nueva')
            ->assertSee('Patio nuevo');

        $historico = ValeCombustible::create([
            'serie_correlativa_id' => $serieId,
            'numero_vale' => 21,
            'anio' => (int) now()->year,
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'apertura_programatica_id' => 1,
            'usuario_id' => auth()->id(),
            'tipo_combustible_id' => $diesel->id,
            'estado' => 'EMITIDO',
        ]);

        $this->put(route('vales-combustible.update', $historico), [
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
        ])->assertRedirect(route('vales-combustible.show', $historico));
        $this->assertNull($historico->fresh()->lugar_entrega_id);
        $this->get(route('vales-combustible.imprimir', $historico))
            ->assertOk()
            ->assertSee('N.º 0000021');
    }

    public function test_deactivating_and_reactivating_places_preserves_historical_vouchers_and_series(): void
    {
        $role = Rol::create(['nombre' => 'TECNICO_I', 'estado' => true]);
        $user = Usuario::create([
            'nombre' => 'Técnico',
            'apellido' => 'Primero',
            'correo' => 'tecnico-primero@example.test',
            'contrasena' => 'test-password',
            'rol_id' => $role->id,
            'estado' => true,
        ]);
        $this->actingAs($user);

        $diesel = TipoCombustible::create(['nombre' => 'DIESEL', 'estado' => true]);
        $lugar = LugarEntrega::create(['nombre' => 'Patio histórico', 'estado' => true]);
        $serieId = DB::table('series_correlativas')->where('codigo_serie', 'GAMS-DIE')->value('id');
        $vale = ValeCombustible::create([
            'serie_correlativa_id' => $serieId,
            'numero_vale' => 20,
            'anio' => (int) now()->year,
            'fecha' => now()->toDateString(),
            'vehiculo_id' => 1,
            'apertura_programatica_id' => 1,
            'usuario_id' => $user->id,
            'tipo_combustible_id' => $diesel->id,
            'lugar_entrega_id' => $lugar->id,
            'estado' => 'EMITIDO',
        ]);

        $contadorAntes = DB::table('series_contadores')
            ->orderBy('codigo_serie')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
        $serieAntes = DB::table('series_correlativas')
            ->orderBy('codigo_serie')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
        $valeAntes = $vale->getAttributes();

        $this->patch(route('lugares-entrega.desactivar', $lugar))
            ->assertRedirect(route('lugares-entrega.index'));
        $this->assertFalse($lugar->fresh()->estado);
        $this->assertSame($lugar->id, $vale->fresh()->lugar_entrega_id);
        $this->get(route('vales-combustible.show', $vale))
            ->assertOk()
            ->assertSee('Patio histórico');
        $this->get(route('vales-combustible.imprimir', $vale))
            ->assertOk()
            ->assertSee('Patio histórico');

        $this->patch(route('lugares-entrega.reactivar', $lugar))
            ->assertRedirect(route('lugares-entrega.index'));
        $this->assertTrue($lugar->fresh()->estado);
        $this->assertSame($lugar->id, $vale->fresh()->lugar_entrega_id);
        $this->assertSame($valeAntes['numero_vale'], $vale->fresh()->numero_vale);
        $contadorDespues = DB::table('series_contadores')
            ->orderBy('codigo_serie')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
        $serieDespues = DB::table('series_correlativas')
            ->orderBy('codigo_serie')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
        $this->assertSame($contadorAntes, $contadorDespues);
        $this->assertSame($serieAntes, $serieDespues);
        $this->assertSame(
            ['DESACTIVAR', 'REACTIVAR'],
            DB::table('auditorias')
                ->where('tabla', 'lugares_entrega')
                ->orderBy('id')
                ->pluck('accion')
                ->all()
        );
    }

    private function datosVale(int $tipoId, array $extras = []): array
    {
        return array_merge([
            'fecha' => now()->toDateString(),
            'tipo_combustible_id' => $tipoId,
            'vehiculo_id' => 1,
            'detalle_asignacion' => 'Trabajo municipal',
        ], $extras);
    }
}
