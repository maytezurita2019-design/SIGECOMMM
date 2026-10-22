<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_only_requests_email_and_password(): void
    {
        $this->get('/registro')
            ->assertOk()
            ->assertSee('name="correo"', false)
            ->assertSee('name="contrasena"', false)
            ->assertDontSee('name="nombre"', false)
            ->assertDontSee('name="apellido"', false)
            ->assertDontSee('name="ci"', false)
            ->assertDontSee('name="contrasena_confirmation"', false);
    }

    public function test_guest_can_register_with_only_email_and_password(): void
    {
        $rol = Rol::create(['nombre' => 'Usuario']);

        $this->post('/registro', [
            'correo' => 'ana@example.com',
            'contrasena' => 'ClaveSegura123!',
        ])->assertRedirect(route('login'))
            ->assertSessionHasNoErrors();

        $usuario = Usuario::where('correo', 'ana@example.com')->firstOrFail();

        $this->assertNull($usuario->nombre);
        $this->assertNull($usuario->apellido);
        $this->assertNull($usuario->ci);
        $this->assertSame($rol->id, $usuario->rol_id);
        $this->assertTrue(Hash::check('ClaveSegura123!', $usuario->contrasena));
        $this->assertDatabaseCount('usuarios', 1);
    }

    public function test_duplicate_email_and_short_password_are_rejected(): void
    {
        Rol::create(['nombre' => 'Usuario']);
        Usuario::create([
            'correo' => 'ana@example.com',
            'contrasena' => 'ClaveSegura123!',
            'rol_id' => Rol::where('nombre', 'Usuario')->value('id'),
            'estado' => true,
        ]);

        $this->post('/registro', [
            'correo' => 'ana@example.com',
            'contrasena' => '123',
        ])->assertSessionHasErrors(['correo', 'contrasena']);

        $this->assertDatabaseCount('usuarios', 1);
    }

    public function test_registration_requires_an_existing_user_role(): void
    {
        $this->post('/registro', [
            'correo' => 'ana@example.com',
            'contrasena' => 'ClaveSegura123!',
        ])->assertSessionHasErrors('registro');

        $this->assertDatabaseCount('usuarios', 0);
    }
}
