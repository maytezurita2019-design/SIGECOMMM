<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\LugarEntrega;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LugaresEntregaCatalogoTest extends TestCase
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

        Schema::create('lugares_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->boolean('estado')->default(true);
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
    }

    public function test_tecnico_i_can_create_modify_deactivate_and_reactivate_with_audit(): void
    {
        $this->actingAs($this->userWithRole('TECNICO_I'));

        $this->post(route('lugares-entrega.store'), ['nombre' => '  Patio   Norte  '])
            ->assertRedirect(route('lugares-entrega.index'));

        $lugar = LugarEntrega::query()->sole();
        $this->assertSame('Patio Norte', $lugar->nombre);
        $this->assertTrue((bool) $lugar->estado);

        $this->get(route('lugares-entrega.index'))
            ->assertOk()
            ->assertSee('Ej. Posta Municipal')
            ->assertSee('Agregar lugar')
            ->assertSee('Editar')
            ->assertSee('Guardar cambios')
            ->assertSee('class="fw-semibold">Patio Norte</td>', false)
            ->assertDontSee('value="Patio Norte"', false);

        $this->put(route('lugares-entrega.update', $lugar), [
            'nombre' => 'Patio Central',
            'lugar_entrega_id' => $lugar->id,
        ])->assertRedirect(route('lugares-entrega.index'));

        $this->patch(route('lugares-entrega.desactivar', $lugar))
            ->assertRedirect(route('lugares-entrega.index'));

        $this->assertFalse($lugar->fresh()->estado);
        $idOriginal = $lugar->id;
        $nombreOriginal = 'Patio Central';

        $this->patch(route('lugares-entrega.reactivar', $lugar))
            ->assertRedirect(route('lugares-entrega.index'));

        $lugar->refresh();
        $this->assertSame($idOriginal, $lugar->id);
        $this->assertSame($nombreOriginal, $lugar->nombre);
        $this->assertTrue($lugar->estado);
        $this->assertSame(
            ['CREAR', 'MODIFICAR', 'DESACTIVAR', 'REACTIVAR'],
            Auditoria::query()->where('tabla', 'lugares_entrega')->orderBy('id')->pluck('accion')->all()
        );

        $auditoriaReactivacion = Auditoria::query()
            ->where('tabla', 'lugares_entrega')
            ->where('accion', 'REACTIVAR')
            ->sole();
        $this->assertSame(auth()->id(), $auditoriaReactivacion->usuario_id);
        $this->assertNotNull($auditoriaReactivacion->created_at);
        $this->assertSame(
            ['nombre' => 'Patio Central', 'estado' => false],
            $auditoriaReactivacion->datos_anteriores
        );
        $this->assertSame(
            ['nombre' => 'Patio Central', 'estado' => true],
            $auditoriaReactivacion->datos_nuevos
        );
    }

    public function test_duplicate_names_are_rejected_without_overwriting_input(): void
    {
        $this->actingAs($this->userWithRole('TECNICO_I'));
        LugarEntrega::create(['nombre' => 'Patio Norte', 'estado' => false]);

        $this->from(route('lugares-entrega.index'))
            ->post(route('lugares-entrega.store'), ['nombre' => ' patio norte '])
            ->assertRedirect(route('lugares-entrega.index'))
            ->assertSessionHasErrors('nombre')
            ->assertSessionHas('_old_input.nombre', 'patio norte');

        $this->assertSame(1, LugarEntrega::query()->count());
        $this->assertSame(0, Auditoria::query()->where('tabla', 'lugares_entrega')->count());
    }

    public function test_whitespace_only_names_are_rejected_and_repeated_spaces_are_normalized_for_duplicates(): void
    {
        $this->actingAs($this->userWithRole('TECNICO_I'));
        LugarEntrega::create(['nombre' => 'Posta Municipal', 'estado' => true]);

        $this->from(route('lugares-entrega.index'))
            ->post(route('lugares-entrega.store'), ['nombre' => " \t\n "])
            ->assertRedirect(route('lugares-entrega.index'))
            ->assertSessionHasErrors('nombre');

        $this->from(route('lugares-entrega.index'))
            ->post(route('lugares-entrega.store'), ['nombre' => ' Posta   Municipal '])
            ->assertRedirect(route('lugares-entrega.index'))
            ->assertSessionHasErrors('nombre');

        $this->assertSame(1, LugarEntrega::query()->count());
    }

    public function test_administrator_can_consult_but_cannot_manage_places(): void
    {
        $this->actingAs($this->userWithRole('ADMINISTRADOR'));
        LugarEntrega::create(['nombre' => 'Patio Norte', 'estado' => false]);

        $this->get(route('lugares-entrega.index'))
            ->assertOk()
            ->assertSee('Patio Norte')
            ->assertDontSee('Registrar lugar')
            ->assertDontSee('Desactivar')
            ->assertDontSee('Reactivar');
        $this->post(route('lugares-entrega.store'), ['nombre' => 'Patio Norte'])
            ->assertForbidden();
        $this->patch(route('lugares-entrega.reactivar', 1))->assertForbidden();
    }

    public function test_tecnico_ii_can_consult_but_cannot_manage_places(): void
    {
        $this->actingAs($this->userWithRole('TECNICO_II'));
        LugarEntrega::create(['nombre' => 'Patio Norte', 'estado' => false]);

        $this->get(route('lugares-entrega.index'))
            ->assertOk()
            ->assertSee('Patio Norte')
            ->assertDontSee('Registrar lugar')
            ->assertDontSee('Desactivar')
            ->assertDontSee('Reactivar');
        $this->post(route('lugares-entrega.store'), ['nombre' => 'Patio Norte'])
            ->assertForbidden();
        $this->put(route('lugares-entrega.update', 1), ['nombre' => 'Nuevo'])
            ->assertForbidden();
        $this->patch(route('lugares-entrega.desactivar', 1))->assertForbidden();
        $this->patch(route('lugares-entrega.reactivar', 1))->assertForbidden();
    }

    private function userWithRole(string $nombre): Usuario
    {
        $rol = Rol::create([
            'nombre' => $nombre,
            'descripcion' => $nombre,
            'estado' => true,
        ]);

        return Usuario::create([
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'correo' => strtolower($nombre) . '@example.test',
            'contrasena' => 'test-password',
            'rol_id' => $rol->id,
            'estado' => true,
        ]);
    }
}
