<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::where('nombre', 'like', 'Juan Pfu%')->first();

        if ($admin) {
            $admin->update(['rol' => Usuario::ROL_ADMIN]);

            return;
        }

        Usuario::create([
            'nombre' => 'Juan Pfuño',
            'correo' => 'admin@apukuntur.test',
            'contrasena' => 'password',
            'rol' => Usuario::ROL_ADMIN,
        ]);
    }
}
