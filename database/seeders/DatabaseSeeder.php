<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuarios de prueba (cambia las contraseñas antes de publicar)
        Usuario::create([
            'nombre' => 'Maky Nelly',
            'correo' => 'fabrica@apukuntur.test',
            'contrasena' => 'password',
            'rol' => Usuario::ROL_FABRICA,
        ]);

        Usuario::create([
            'nombre' => 'Ana Gómez',
            'correo' => 'eu@apukuntur.test',
            'contrasena' => 'password',
            'rol' => Usuario::ROL_EU,
        ]);

        $this->call(AdminUserSeeder::class);
        $this->call(DesignSeeder::class);
    }
}
