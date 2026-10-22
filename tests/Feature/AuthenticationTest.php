<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_login_but_cannot_access_modules(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión')->assertSee('name="_token"', false);
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/interfaz/seguridad/usuarios')->assertRedirect('/login');
    }

    public function test_valid_credentials_restore_intended_page_and_remember_user(): void
    {
        $user = User::factory()->create();
        $this->get('/interfaz/seguridad/usuarios');
        $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1'])
            ->assertRedirect('/interfaz/seguridad/usuarios')->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
        $this->get('/login')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk()->assertSee($user->name)->assertSee('Cerrar sesión');
    }

    public function test_invalid_credentials_are_rejected_and_attempts_are_limited(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 5; $i++) {
            $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'incorrecta'])
                ->assertRedirect('/login')->assertSessionHasErrors('email');
            $this->assertGuest();
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->get('/login')->assertSee('Demasiados intentos');
        $this->assertGuest();
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_logout_invalidates_session_and_protects_dashboard(): void
    {
        $this->actingAs(User::factory()->create())->withSession(['private_value' => 'secret']);
        $this->post('/logout')->assertRedirect('/login')->assertSessionMissing('private_value');
        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
