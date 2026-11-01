@php $usuario = auth()->user(); @endphp
<header class="flex flex-wrap items-center justify-between gap-3 border-b border-[#e2e2df] bg-white px-6 py-4">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo-2.png') }}" alt="Logo" class="h-10 w-auto shrink-0">
        <div class="flex flex-col gap-0.5">
            <h1 class="text-lg font-semibold leading-7 tracking-[-0.45px] text-[#1a1a18]">Base de diseños y desarrollo de producto</h1>
            <p class="text-xs text-[#a0a09c]"></p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-[#6b6b67]">{{ $usuario->nombre }}</span>
            <span @class([
                'rounded-full px-2.5 py-0.5 text-[11px] font-semibold',
                'bg-[#f3e8ff] text-[#7e22ce]' => $usuario->isAdmin(),
                'bg-[#eff3ff] text-[#1d4ed8]' => $usuario->isEu() && ! $usuario->isAdmin(),
                'bg-[#f5f5f3] text-[#6b6b67]' => ! $usuario->isEu() && ! $usuario->isAdmin(),
            ])>{{ $usuario->etiquetaRol() }}</span>
        </div>

        @can('admin')
            <a href="{{ route('admin.usuarios.index') }}"
               class="rounded-lg border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#6b6b67] transition hover:bg-[#f5f5f3]">
                Administración
            </a>
        @endcan

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-lg border border-[#e2e2df] bg-white px-3 py-2 text-sm text-[#6b6b67] transition hover:bg-[#f5f5f3]">Salir</button>
        </form>

        @can('fabrica')
            <a href="{{ route('disenos.index', ['new' => 1]) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-[#1d4ed8] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#1e40af]">
                <span class="text-base leading-none">+</span> Nuevo diseño
            </a>
        @endcan
    </div>
</header>
