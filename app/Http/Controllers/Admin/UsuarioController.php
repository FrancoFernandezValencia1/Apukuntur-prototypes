<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        return view('admin.usuarios.index', [
            'usuarios' => Usuario::orderBy('nombre')->get(),
            'roles' => Usuario::ROLES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,correo'],
            'rol' => ['required', Rule::in(array_keys(Usuario::ROLES))],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'nombre' => 'nombre',
            'correo' => 'correo',
            'rol' => 'rol',
            'contrasena' => 'contraseña',
        ]);

        $usuario = Usuario::create(Arr::only($datos, ['nombre', 'correo', 'rol', 'contrasena']));

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', "Usuario {$usuario->nombre} creado correctamente.");
    }

    public function edit(Usuario $usuario): View
    {
        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Usuario::ROLES,
        ]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'correo')->ignore($usuario->id)],
            'rol' => ['required', Rule::in(array_keys(Usuario::ROLES))],
            'contrasena' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [], [
            'nombre' => 'nombre',
            'correo' => 'correo',
            'rol' => 'rol',
            'contrasena' => 'contraseña',
        ]);

        if ($usuario->is($request->user()) && $datos['rol'] !== Usuario::ROL_ADMIN) {
            return back()
                ->withErrors(['rol' => 'No puedes quitarte el rol Admin a ti mismo.'])
                ->withInput();
        }

        $usuario->fill(Arr::only($datos, ['nombre', 'correo', 'rol']));

        if (filled($datos['contrasena'] ?? null)) {
            $usuario->contrasena = $datos['contrasena'];
        }

        $usuario->save();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', "Usuario {$usuario->nombre} actualizado correctamente.");
    }

    public function destroy(Request $request, Usuario $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return redirect()
                ->route('admin.usuarios.index')
                ->withErrors(['usuario' => 'No puedes eliminar tu propio usuario.']);
        }

        if ($usuario->isAdmin() && Usuario::where('rol', Usuario::ROL_ADMIN)->count() <= 1) {
            return redirect()
                ->route('admin.usuarios.index')
                ->withErrors(['usuario' => 'Debe existir al menos un usuario Admin.']);
        }

        $nombre = $usuario->nombre;
        $usuario->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', "Usuario {$nombre} eliminado.");
    }
}
