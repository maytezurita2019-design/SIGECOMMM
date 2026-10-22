<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InterfazTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->make());
    }

    public function test_dashboard_renders_in_spanish_without_database_queries(): void
    {
        DB::shouldReceive('connection')->never();
        $this->get('/dashboard')->assertOk()
            ->assertSee('lang="es"', false)
            ->assertSee('12.480 L')->assertSee('Consumo mensual')
            ->assertSee('Los datos son ficticios');
    }

    public function test_all_menu_sections_are_available_without_database_queries(): void
    {
        DB::shouldReceive('connection')->never();
        foreach (config('interfaz') as $modulo => $datos) {
            foreach ($datos['secciones'] as $seccion => $titulo) {
                $this->get(route('interfaz.seccion', [$modulo, $seccion]))
                    ->assertOk()->assertSee($titulo)->assertSee($modulo === 'api-consulta' ? 'Pendiente de diseño' : 'Limpiar filtros');
            }
        }
    }

    public function test_unknown_sections_and_writes_are_not_available(): void
    {
        $this->get('/interfaz/inexistente/usuarios')->assertNotFound();
        $this->get('/interfaz/seguridad/inexistente')->assertNotFound();
        $this->post('/interfaz/seguridad/usuarios')->assertStatus(405);
    }
}
