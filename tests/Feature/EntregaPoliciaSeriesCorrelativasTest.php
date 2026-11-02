<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class EntregaPoliciaSeriesCorrelativasTest extends TestCase
{
    use RefreshDatabase;

    public function test_police_lot_assignment_advances_only_its_series_and_delivery_preserves_range(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-10-15'));

        $rolId = DB::table('roles')->insertGetId(['nombre' => 'TECNICO_I']);
        $usuario = Usuario::create([
            'nombre' => 'Técnico',
            'correo' => 'tecnico.pol@example.test',
            'contrasena' => 'password',
            'rol_id' => $rolId,
            'estado' => true,
        ]);
        $this->actingAs($usuario);

        $periodoId = DB::table('periodos')->insertGetId([
            'mes' => 10,
            'anio' => 2026,
            'fecha_inicio' => '2026-10-01',
            'fecha_fin' => '2026-10-31',
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $fuenteId = DB::table('fuentes_financiamiento')->insertGetId([
            'codigo' => 'TEST-FUENTE',
            'nombre' => 'Fuente de prueba',
        ]);
        $organismoId = DB::table('organismos_financiadores')->insertGetId([
            'codigo' => 'TEST-OF',
            'nombre' => 'Organismo de prueba',
        ]);
        $aperturaId = DB::table('aperturas_programaticas')->insertGetId([
            'codigo' => 'TEST-AP',
            'descripcion' => 'Apertura de prueba',
            'fuente_financiamiento_id' => $fuenteId,
            'organismo_financiador_id' => $organismoId,
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('tipos_combustible')->insert([
            ['nombre' => 'Gasolina', 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Diésel', 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $seriePol = DB::table('series_correlativas')
            ->where('codigo_serie', 'POL-GAS')
            ->where('anio', 2026)
            ->first();
        $serieGamsGas = DB::table('series_correlativas')
            ->where('codigo_serie', 'GAMS-GAS')
            ->where('anio', 2026)
            ->first();
        $serieGamsDie = DB::table('series_correlativas')
            ->where('codigo_serie', 'GAMS-DIE')
            ->where('anio', 2026)
            ->first();

        $this->assertSame('ACTIVA', $seriePol->estado);

        $this->post('/lotes-vales', [
            'fecha_recepcion' => '2026-10-01',
            'periodo_id' => $periodoId,
            'cantidad_vales' => 3,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $loteId = DB::table('lotes_vales')->value('id');
        $this->assertDatabaseHas('lotes_vales', [
            'id' => $loteId,
            'numero_inicial' => 100091,
            'numero_final' => 100093,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2026,
            'ultimo_numero' => 100093,
        ]);
        $datos = [
            'lote_vale_id' => $loteId,
            'contrato' => 'POL-TEST-2026',
            'dependencia' => 'Policía de prueba',
            'recibido_por' => 'Responsable de prueba',
            'fecha_entrega' => '2026-10-15',
            'apertura_programatica_id' => $aperturaId,
            'solicitud_token' => (string) Str::uuid(),
            'cantidad_gasolina' => 3,
            'cantidad_diesel' => 0,
        ];

        $this->post('/entregas-policia', $datos)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $entregaId = DB::table('entregas_policia')->value('id');
        $this->assertNotNull($entregaId);
        $this->assertSame(
            [100091, 100092, 100093],
            DB::table('vales_combustible')
                ->where('entrega_policia_id', $entregaId)
                ->orderBy('numero_vale')
                ->pluck('numero_vale')
                ->map(fn ($numero) => (int) $numero)
                ->all()
        );
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2026,
            'ultimo_numero' => 100093,
        ]);
        $this->assertDatabaseHas('series_correlativas', [
            'codigo_serie' => 'POL-GAS',
            'anio' => 2026,
            'ultimo_numero' => 100093,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-GAS',
            'anio' => 2026,
            'ultimo_numero' => $serieGamsGas->ultimo_numero,
        ]);
        $this->assertDatabaseHas('series_contadores', [
            'codigo_serie' => 'GAMS-DIE',
            'anio' => 2026,
            'ultimo_numero' => $serieGamsDie->ultimo_numero,
        ]);

        $this->post('/entregas-policia', $datos)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('entregas_policia', 1);
        $this->assertDatabaseCount('vales_combustible', 3);
    }
}
