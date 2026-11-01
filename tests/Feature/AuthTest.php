<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('disenos.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_with_correo_and_contrasena(): void
    {
        $this->seed();

        $this->post(route('login.attempt'), [
            'correo' => 'fabrica@apukuntur.test',
            'contrasena' => 'password',
        ])->assertRedirect(route('disenos.index'));

        $this->assertAuthenticatedAs(Usuario::where('correo', 'fabrica@apukuntur.test')->firstOrFail());
    }

    public function test_user_can_log_out(): void
    {
        $this->seed();

        $usuario = Usuario::where('correo', 'fabrica@apukuntur.test')->firstOrFail();

        $this->actingAs($usuario)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
