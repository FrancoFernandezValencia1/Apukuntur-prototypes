@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold tracking-[-0.3px]">Administración de usuarios</h1>
            <p class="mt-1 text-sm text-[#a0a09c]">Crea usuarios y administra sus nombres, roles y contraseñas.</p>
        </div>
        <a href="{{ route('disenos.index') }}"
           class="rounded-lg border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#6b6b67] transition hover:bg-[#f5f5f3]">
            Volver a diseños
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
        <div class="border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Nuevo usuario</p>
        </div>

        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf

            <x-field name="nombre" label="Nombre" :value="old('nombre')" autocomplete="name" />
            <x-field name="correo" label="Correo" type="email" :value="old('correo')" autocomplete="email" />
            <x-field name="rol" label="Rol" as="select" :options="$roles"
                     :value="old('rol', \App\Models\Usuario::ROL_FABRICA)" />

            <div class="flex flex-col gap-1">
                <label for="contrasena" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Contraseña</label>
                <input id="contrasena" type="password" name="contrasena" required minlength="8" autocomplete="new-password"
                       class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#1a1a18] outline-none transition focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
                @error('contrasena')
                    <p class="text-xs text-[#dc2626]">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label for="contrasena_confirmation" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Confirmar contraseña</label>
                <input id="contrasena_confirmation" type="password" name="contrasena_confirmation" required minlength="8" autocomplete="new-password"
                       class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#1a1a18] outline-none transition focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="rounded-md bg-[#1d4ed8] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#1e40af]">
                    Crear usuario
                </button>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
        <div class="flex items-center gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Usuarios</p>
            <span class="rounded-full bg-[#e2e2df] px-2 py-0.5 text-[10px] font-semibold">{{ $usuarios->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm">
                <thead>
                    <tr class="bg-[#fafaf9] text-left text-[11px] uppercase tracking-[0.3px] text-[#6b6b67]">
                        <th class="border-b border-[#e2e2df] px-4 py-2 font-semibold">Nombre</th>
                        <th class="border-b border-[#e2e2df] px-4 py-2 font-semibold">Correo</th>
                        <th class="border-b border-[#e2e2df] px-4 py-2 font-semibold">Rol</th>
                        <th class="border-b border-[#e2e2df] px-4 py-2 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr class="border-b border-[#f0f0ee] last:border-0">
                            <td class="px-4 py-3 font-medium">
                                {{ $usuario->nombre }}
                                @if ($usuario->is(auth()->user()))
                                    <span class="ml-1 rounded-full bg-[#eff3ff] px-2 py-0.5 text-[10px] font-semibold text-[#1d4ed8]">Tú</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-[#6b6b67]">{{ $usuario->correo }}</td>
                            <td class="px-4 py-3">
                                <span @class([
                                    'rounded-full px-2.5 py-0.5 text-[11px] font-semibold',
                                    'bg-[#f3e8ff] text-[#7e22ce]' => $usuario->isAdmin(),
                                    'bg-[#eff3ff] text-[#1d4ed8]' => $usuario->isEu(),
                                    'bg-[#f5f5f3] text-[#6b6b67]' => $usuario->isFactory(),
                                ])>{{ $usuario->etiquetaRol() }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-xs font-medium text-[#1d4ed8] hover:underline">
                                        Editar
                                    </a>

                                    @if (! $usuario->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                                              onsubmit="return confirm('¿Eliminar este usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-[#dc2626] hover:underline">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
