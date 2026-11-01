<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Usuario
    {
        return Usuario::factory()->create(['rol' => Usuario::ROL_ADMIN]);
    }

    public function test_admin_can_view_users_panel(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertSee('Administración de usuarios')
            ->assertSee('Crear usuario')
            ->assertSee($admin->correo);

        $this->actingAs($admin)
            ->get(route('admin.usuarios.edit', $admin))
            ->assertOk()
            ->assertSee('Editar usuario')
            ->assertSee('Guardar cambios');
    }

    public function test_factory_and_eu_cannot_access_admin_panel(): void
    {
        $fabrica = Usuario::factory()->create(['rol' => Usuario::ROL_FABRICA]);
        $eu = Usuario::factory()->create(['rol' => Usuario::ROL_EU]);

        $this->actingAs($fabrica)->get(route('admin.usuarios.index'))->assertForbidden();
        $this->actingAs($eu)->get(route('admin.usuarios.index'))->assertForbidden();
        $this->actingAs($eu)->post(route('admin.usuarios.store'), [])->assertForbidden();
    }

    public function test_admin_can_create_user_with_role_and_password(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.usuarios.store'), [
            'nombre' => 'Nueva Persona',
            'correo' => 'nueva@apukuntur.test',
            'rol' => Usuario::ROL_FABRICA,
            'contrasena' => 'clave-segura',
            'contrasena_confirmation' => 'clave-segura',
        ]);

        $response->assertRedirect(route('admin.usuarios.index'));
        $response->assertSessionHas('status');

        $creado = Usuario::where('correo', 'nueva@apukuntur.test')->firstOrFail();
        $this->assertSame('Nueva Persona', $creado->nombre);
        $this->assertSame(Usuario::ROL_FABRICA, $creado->rol);
        $this->assertTrue(Hash::check('clave-segura', $creado->contrasena));
    }

    public function test_admin_can_update_user_role_and_password(): void
    {
        $admin = $this->admin();
        $usuario = Usuario::factory()->create(['rol' => Usuario::ROL_FABRICA]);

        $this->actingAs($admin)->put(route('admin.usuarios.update', $usuario), [
            'nombre' => 'Nombre Actualizado',
            'correo' => $usuario->correo,
            'rol' => Usuario::ROL_EU,
            'contrasena' => 'otra-clave-segura',
            'contrasena_confirmation' => 'otra-clave-segura',
        ])->assertRedirect(route('admin.usuarios.index'));

        $usuario->refresh();
        $this->assertSame('Nombre Actualizado', $usuario->nombre);
        $this->assertSame(Usuario::ROL_EU, $usuario->rol);
        $this->assertTrue(Hash::check('otra-clave-segura', $usuario->contrasena));
    }

    public function test_admin_cannot_demote_or_delete_self(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.usuarios.update', $admin), [
            'nombre' => $admin->nombre,
            'correo' => $admin->correo,
            'rol' => Usuario::ROL_EU,
        ])->assertSessionHasErrors('rol');

        $this->assertSame(Usuario::ROL_ADMIN, $admin->fresh()->rol);

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $admin))
            ->assertSessionHasErrors('usuario');

        $this->assertDatabaseHas('usuarios', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = $this->admin();
        $usuario = Usuario::factory()->create(['rol' => Usuario::ROL_FABRICA]);

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $usuario))
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseMissing('usuarios', ['id' => $usuario->id]);
    }

    public function test_admin_seeder_creates_juan_once_on_a_fresh_database(): void
    {
        $this->seed(\Database\Seeders\AdminUserSeeder::class);
        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $admins = Usuario::where('rol', Usuario::ROL_ADMIN)->get();

        $this->assertCount(1, $admins);
        $this->assertSame('Juan Pfuño', $admins->first()->nombre);
        $this->assertSame('admin@apukuntur.test', $admins->first()->correo);
    }

    public function test_full_seeder_keeps_one_user_per_role(): void
    {
        $this->seed();

        $this->assertDatabaseHas('usuarios', ['correo' => 'fabrica@apukuntur.test', 'rol' => Usuario::ROL_FABRICA]);
        $this->assertDatabaseHas('usuarios', ['correo' => 'eu@apukuntur.test', 'rol' => Usuario::ROL_EU]);
        $this->assertDatabaseHas('usuarios', ['correo' => 'admin@apukuntur.test', 'rol' => Usuario::ROL_ADMIN]);
    }
}
