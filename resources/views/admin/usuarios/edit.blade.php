@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold tracking-[-0.3px]">Editar usuario</h1>
            <p class="mt-1 text-sm text-[#a0a09c]">{{ $usuario->nombre }} · {{ $usuario->correo }}</p>
        </div>
        <a href="{{ route('admin.usuarios.index') }}"
           class="rounded-lg border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#6b6b67] transition hover:bg-[#f5f5f3]">
            Volver a usuarios
        </a>
    </div>

    <section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
        <div class="border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Datos del usuario</p>
        </div>

        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            @method('PUT')

            <x-field name="nombre" label="Nombre" :model="$usuario" autocomplete="name" />
            <x-field name="correo" label="Correo" type="email" :model="$usuario" autocomplete="email" />
            <x-field name="rol" label="Rol" as="select" :options="$roles" :model="$usuario" />

            <div class="flex flex-col gap-1">
                <label for="contrasena" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Nueva contraseña</label>
                <input id="contrasena" type="password" name="contrasena" minlength="8" autocomplete="new-password"
                       placeholder="Dejar vacío para no cambiarla"
                       class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#1a1a18] outline-none transition focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
                @error('contrasena')
                    <p class="text-xs text-[#dc2626]">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label for="contrasena_confirmation" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Confirmar contraseña</label>
                <input id="contrasena_confirmation" type="password" name="contrasena_confirmation" minlength="8" autocomplete="new-password"
                       class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#1a1a18] outline-none transition focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="rounded-md bg-[#1d4ed8] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#1e40af]">
                    Guardar cambios
                </button>
            </div>
        </form>
    </section>

    @if (! $usuario->is(auth()->user()))
        <section class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <p class="text-sm text-[#dc2626]">Eliminar este usuario quitará su acceso al sistema.</p>
            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
                  onsubmit="return confirm('¿Eliminar este usuario?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded-md border border-red-200 bg-white px-4 py-2 text-sm text-[#dc2626] transition hover:bg-red-100">
                    Eliminar usuario
                </button>
            </form>
        </section>
    @endif
@endsection
