<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IniciarNuevoAnioSeriesTest extends TestCase
{
    private const SERIES_2026 = [
        'POL-GAS' => 100310,
        'GAMS-GAS' => 300131,
        'GAMS-DIE' => 400235,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
        });
        Schema::create('usuarios', function (Blueprint $table): void {
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
        Schema::create('auditorias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('usuario_id')->nullable();
            $table->string('accion');
            $table->string('tabla');
            $table->unsignedBigInteger('registro_id');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
        Schema::create('tipos_combustible', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
        });
        Schema::create('series_correlativas', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo_serie', 32);
            $table->string('institucion', 32);
            $table->string('combustible', 32);
            $table->unsignedBigInteger('ultimo_numero');
            $table->unsignedSmallInteger('anio');
            $table->string('estado', 20)->default('ACTIVA');
            $table->timestamps();
            $table->unique(['codigo_serie', 'anio']);
        });
        Schema::create('series_contadores', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo_serie', 16);
            $table->unsignedSmallInteger('anio');
            $table->unsignedBigInteger('ultimo_numero');
            $table->string('prefijo', 32);
            $table->timestamps();
            $table->unique(['codigo_serie', 'anio']);
        });
        Schema::create('vales_combustible', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('anio');
            $table->unsignedBigInteger('serie_correlativa_id')->nullable();
            $table->unsignedBigInteger('numero_vale');
            $table->unsignedBigInteger('tipo_combustible_id')->nullable();
            $table->unsignedBigInteger('entrega_policia_id')->nullable();
        });
        Schema::create('periodos', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('anio');
        });
        Schema::create('lotes_vales', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('serie_correlativa_id')->nullable();
        });

        foreach (self::SERIES_2026 as $codigo => $ultimoNumero) {
            $institucion = $codigo === 'POL-GAS' ? 'POLICIA' : 'GAMS';
            $combustible = $codigo === 'GAMS-DIE' ? 'DIESEL' : 'GASOLINA';
            DB::table('series_correlativas')->insert([
                'codigo_serie' => $codigo,
                'institucion' => $institucion,
                'combustible' => $combustible,
                'ultimo_numero' => $ultimoNumero,
                'anio' => 2026,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('series_contadores')->insert([
                'codigo_serie' => $codigo,
                'anio' => 2026,
                'ultimo_numero' => $ultimoNumero,
                'prefijo' => $codigo . '-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_tecnico_i_can_start_three_independent_series_and_audit_the_operation(): void
    {
        $usuario = $this->usuario('TECNICO_I');

        $this->actingAs($usuario)
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio([
                'primer_gams_gas' => '020701',
                'primer_pol_gas' => '000001',
                'primer_gams_die' => '0400233',
            ]))
            ->assertRedirect(route('series-correlativas.index', ['anio' => 2027]))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('series_correlativas', 6);
        $this->assertDatabaseCount('series_contadores', 6);
        foreach ([
            'GAMS-GAS' => 20700,
            'POL-GAS' => 0,
            'GAMS-DIE' => 400232,
        ] as $codigo => $ultimoNumero) {
            $this->assertDatabaseHas('series_correlativas', [
                'codigo_serie' => $codigo,
                'anio' => 2027,
                'ultimo_numero' => $ultimoNumero,
                'estado' => 'EN_CONFIGURACION',
            ]);
            $this->assertDatabaseHas('series_contadores', [
                'codigo_serie' => $codigo,
                'anio' => 2027,
                'ultimo_numero' => $ultimoNumero,
            ]);
        }

        $auditoria = DB::table('auditorias')->where('accion', 'INICIAR SERIES NUEVO AÑO')->first();
        $this->assertNotNull($auditoria);
        $this->assertSame($usuario->id, (int) $auditoria->usuario_id);
        $datosNuevos = json_decode($auditoria->datos_nuevos, true);
        $this->assertSame('TECNICO_I', $datosNuevos['rol']);
        $this->assertSame(2027, $datosNuevos['anio']);
        $this->assertSame(20701, $datosNuevos['series']['GAMS-GAS']['primer_numero']);
        $this->assertSame(400232, $datosNuevos['series']['GAMS-DIE']['ultimo_numero']);
        $this->assertSame('Inicio autorizado por Transporte', $datosNuevos['motivo']);

        $this->assertSeries2026Unchanged();
    }

    public function test_a_series_can_be_configured_independently_then_activated_and_locked(): void
    {
        $usuario = $this->usuario('TECNICO_I');
        $this->actingAs($usuario)
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertRedirect();

        $this->actingAs($usuario)
            ->post(route('series-correlativas.update', 'GAMS-GAS'), [
                'anio' => 2027,
                'primer_numero' => '020701',
                'motivo' => 'Continuar numeración física',
                'confirmacion' => 'CONFIGURAR GAMS-GAS 2027',
            ])
            ->assertRedirect(route('series-correlativas.index', ['anio' => 2027]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027, 'ultimo_numero' => 20700,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'POL-GAS', 'anio' => 2027, 'ultimo_numero' => 0,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-DIE', 'anio' => 2027, 'ultimo_numero' => 0,
        ]);

        $this->actingAs($usuario)
            ->post(route('series-correlativas.activar', 'GAMS-GAS'), [
                'anio' => 2027,
                'motivo' => 'Inicio autorizado para emitir',
                'confirmacion' => 'ACTIVAR GAMS-GAS 2027',
            ])
            ->assertRedirect(route('series-correlativas.index', ['anio' => 2027]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027,
            'ultimo_numero' => 20700, 'estado' => 'ACTIVA',
        ]);
        $this->assertDatabaseHas('auditorias', [
            'accion' => 'CONFIGURAR SERIE CORRELATIVA',
            'usuario_id' => $usuario->id,
        ]);
        $this->assertDatabaseHas('auditorias', [
            'accion' => 'ACTIVAR SERIE CORRELATIVA',
            'usuario_id' => $usuario->id,
        ]);

        $this->actingAs($usuario)
            ->post(route('series-correlativas.update', 'GAMS-GAS'), [
                'anio' => 2027,
                'primer_numero' => '000001',
                'motivo' => 'Intento de reinicio',
                'confirmacion' => 'CONFIGURAR GAMS-GAS 2027',
            ])
            ->assertSessionHasErrors('primer_numero');
        $this->actingAs($usuario)
            ->post(route('series-correlativas.update', 'GAMS-GAS'), [
                'anio' => 2027,
                'primer_numero' => '020702',
                'motivo' => 'Intento de adelanto manual',
                'confirmacion' => 'CONFIGURAR GAMS-GAS 2027',
            ])
            ->assertSessionHasErrors('primer_numero');
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027, 'ultimo_numero' => 20700,
        ]);

        DB::transaction(function (): void {
            $serie = \App\Models\NumeracionVale::bloquear(2027, 'GAMS-GAS');
            \App\Models\NumeracionVale::avanzar($serie, 20701);
        });
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027, 'ultimo_numero' => 20701,
        ]);
        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027, 'ultimo_numero' => 20701,
        ]);
        $this->assertSeries2026Unchanged();
    }

    public function test_only_tecnico_i_can_configure_or_activate_series(): void
    {
        $this->actingAs($this->usuario('TECNICO_I'))
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertRedirect();

        foreach (['TECNICO_II', 'ADMINISTRADOR'] as $rol) {
            $usuario = $this->usuario($rol);
            $datos = [
                'anio' => 2027,
                'primer_numero' => '000002',
                'motivo' => 'Intento no autorizado',
                'confirmacion' => 'CONFIGURAR GAMS-GAS 2027',
            ];
            $this->actingAs($usuario)
                ->post(route('series-correlativas.update', 'GAMS-GAS'), $datos)
                ->assertForbidden();
            $this->actingAs($usuario)
                ->post(route('series-correlativas.activar', 'GAMS-GAS'), [
                    'anio' => 2027,
                    'motivo' => 'Intento no autorizado',
                    'confirmacion' => 'ACTIVAR GAMS-GAS 2027',
                ])
                ->assertForbidden();
        }

        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027,
            'ultimo_numero' => 0, 'estado' => 'EN_CONFIGURACION',
        ]);
        $this->assertSeries2026Unchanged();
    }

    public function test_series_in_configuration_cannot_be_advanced(): void
    {
        $this->actingAs($this->usuario('TECNICO_I'))
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertRedirect();

        try {
            DB::transaction(function (): void {
                $serie = \App\Models\NumeracionVale::bloquear(2027, 'GAMS-GAS');
                \App\Models\NumeracionVale::avanzar($serie, 1);
            });
            $this->fail('Una serie EN_CONFIGURACION no debe permitir el bloqueo de emisión.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('EN_CONFIGURACION', $exception->getMessage());
        }

        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS', 'anio' => 2027, 'ultimo_numero' => 0,
        ]);
    }

    public function test_default_first_numbers_create_zero_counters_and_preview_with_padding(): void
    {
        $this->actingAs($this->usuario('TECNICO_I'))
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertRedirect();

        foreach (['GAMS-GAS', 'POL-GAS', 'GAMS-DIE'] as $codigo) {
            $this->assertDatabaseHas('series_contadores', [
                'codigo_serie' => $codigo,
                'anio' => 2027,
                'ultimo_numero' => 0,
            ]);
        }

        $this->actingAs($this->usuario('TECNICO_II'))
            ->getJson(route('vales-combustible.series.siguiente', ['anio' => 2027]))
            ->assertOk()
            ->assertJsonPath('series.GAMS-GAS.formateado', '000001')
            ->assertJsonPath('series.GAMS-DIE.formateado', '0000001');

        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS',
            'anio' => 2027,
            'ultimo_numero' => 0,
        ]);
    }

    public function test_preview_explains_when_a_year_has_not_been_initialized(): void
    {
        $this->actingAs($this->usuario('TECNICO_II'))
            ->getJson(route('vales-combustible.series.siguiente', ['anio' => 2027]))
            ->assertOk()
            ->assertJsonPath('series.GAMS-GAS.disponible', false)
            ->assertJsonPath(
                'series.GAMS-GAS.mensaje',
                'Las series correlativas del año seleccionado todavía no han sido configuradas. Solicite al TECNICO_I iniciar el nuevo año.'
            );
    }

    public function test_only_tecnico_i_can_initialize_the_new_year(): void
    {
        $usuarios = [
            $this->usuario('TECNICO_II'),
            $this->usuario('ADMINISTRADOR'),
        ];
        $auditoriasAntes = DB::table('auditorias')->count();

        foreach ($usuarios as $usuario) {
            $this->actingAs($usuario)
                ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
                ->assertForbidden();
        }

        $this->assertDatabaseCount('series_correlativas', 3);
        $this->assertDatabaseCount('series_contadores', 3);
        $this->assertSame($auditoriasAntes, DB::table('auditorias')->count());
    }

    public function test_duplicate_partial_and_2026_initializations_are_rejected_without_changing_existing_rows(): void
    {
        $usuario = $this->usuario('TECNICO_I');
        $this->actingAs($usuario)
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertRedirect();
        $this->actingAs($usuario)
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertSessionHasErrors('anio');

        $this->actingAs($usuario)
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio(['anio' => 2026, 'confirmacion' => 'INICIAR 2026']))
            ->assertSessionHasErrors('anio');
        $this->assertSeries2026Unchanged();
    }

    public function test_a_partially_configured_year_is_not_completed_or_overwritten(): void
    {
        DB::table('series_correlativas')->insert([
            'codigo_serie' => 'POL-GAS',
            'institucion' => 'POLICIA',
            'combustible' => 'GASOLINA',
            'ultimo_numero' => 12,
            'anio' => 2027,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->usuario('TECNICO_I'))
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertSessionHasErrors('anio');

        $this->assertDatabaseCount('series_correlativas', 4);
        $this->assertDatabaseCount('series_contadores', 3);
        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2027,
            'ultimo_numero' => 12,
        ]);
        $this->assertDatabaseMissing('series_contadores', ['anio' => 2027]);
        $this->assertSeries2026Unchanged();
    }

    public function test_invalid_first_numbers_and_year_usage_are_rejected(): void
    {
        $usuario = $this->usuario('TECNICO_I');

        foreach ([
            ['primer_gams_gas' => '1000000'],
            ['primer_pol_gas' => '-1'],
            ['primer_gams_die' => '1.5'],
            ['primer_gams_die' => '10000000'],
        ] as $datosInvalidos) {
            $this->actingAs($usuario)
                ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio($datosInvalidos))
                ->assertSessionHasErrors();
        }

        DB::table('vales_combustible')->insert([
            'anio' => 2027,
            'numero_vale' => 1,
        ]);
        $this->actingAs($usuario)
            ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio())
            ->assertSessionHasErrors('anio');
        $this->assertDatabaseCount('series_correlativas', 3);
    }

    public function test_audit_failure_rolls_back_all_six_new_rows(): void
    {
        DB::statement(
            "CREATE TRIGGER fail_new_year_audit BEFORE INSERT ON auditorias "
            . "BEGIN SELECT RAISE(ABORT, 'audit failure'); END"
        );
        $this->withoutExceptionHandling();

        try {
            $this->actingAs($this->usuario('TECNICO_I'))
                ->post(route('series-correlativas.iniciar-anio'), $this->datosInicio());
            $this->fail('Se esperaba que fallara la escritura de auditoría.');
        } catch (QueryException) {
            $this->assertDatabaseCount('series_correlativas', 3);
            $this->assertDatabaseCount('series_contadores', 3);
            $this->assertDatabaseCount('auditorias', 0);
        }
    }

    public function test_admin_can_view_the_panel_but_cannot_see_the_start_button(): void
    {
        $this->actingAs($this->usuario('ADMINISTRADOR'))
            ->get(route('series-correlativas.index', ['anio' => 2026]))
            ->assertOk()
            ->assertDontSee('Iniciar nuevo año');

        $this->actingAs($this->usuario('TECNICO_I'))
            ->get(route('series-correlativas.index', ['anio' => 2026]))
            ->assertOk()
            ->assertSee('Iniciar nuevo año')
            ->assertSee('GAMS-GAS')
            ->assertSee('300131');

        $this->actingAs($this->usuario('TECNICO_II'))
            ->get(route('series-correlativas.index', ['anio' => 2026]))
            ->assertOk()
            ->assertDontSee('Iniciar nuevo año');
    }

    private function usuario(string $rol): Usuario
    {
        $rolId = DB::table('roles')->insertGetId(['nombre' => $rol]);

        return Usuario::create([
            'nombre' => $rol,
            'correo' => strtolower($rol) . '@example.test',
            'contrasena' => 'password',
            'rol_id' => $rolId,
            'estado' => true,
        ]);
    }

    private function datosInicio(array $overrides = []): array
    {
        return array_merge([
            'anio' => 2027,
            'primer_gams_gas' => '000001',
            'primer_pol_gas' => '000001',
            'primer_gams_die' => '0000001',
            'motivo' => 'Inicio autorizado por Transporte',
            'confirmacion' => 'INICIAR 2027',
        ], $overrides);
    }

    private function assertSeries2026Unchanged(): void
    {
        foreach (self::SERIES_2026 as $codigo => $ultimoNumero) {
            $this->assertDatabaseHas('series_correlativas', [
                'codigo_serie' => $codigo,
                'anio' => 2026,
                'ultimo_numero' => $ultimoNumero,
            ]);
            $this->assertDatabaseHas('series_contadores', [
                'codigo_serie' => $codigo,
                'anio' => 2026,
                'ultimo_numero' => $ultimoNumero,
            ]);
        }
    }
}
