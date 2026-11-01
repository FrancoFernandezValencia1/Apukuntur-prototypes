<aside class="w-full shrink-0 border-b border-[#e2e2df] bg-white lg:w-56 lg:border-b-0 lg:border-r">
    <div class="flex flex-col gap-2 p-3">
        <p class="px-2 text-[10px] font-semibold uppercase tracking-[1px] text-[#a0a09c]">Base de datos</p>

        <nav class="flex gap-1 overflow-x-auto lg:flex-col lg:overflow-visible">
            @forelse ($disenos as $item)
                @php $activo = $diseno->exists && $diseno->is($item); @endphp
                <a href="{{ route('disenos.index', ['diseno' => $item->id]) }}"
                   @class([
                       'flex min-w-48 items-center gap-3 rounded-lg border px-2 py-2.5 transition lg:min-w-0',
                       'border-[#1d4ed8]/20 bg-[#eff3ff]' => $activo,
                       'border-transparent hover:bg-[#f5f5f3]' => ! $activo,
                   ])>
                    <span class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-md border border-[#e2e2df] bg-[#f5f5f3] text-xs font-semibold text-[#a0a09c]">
                        @if ($item->urlMiniatura)
                            <img src="{{ $item->urlMiniatura }}" alt="" class="size-full object-cover">
                        @else
                            {{ mb_strtoupper(mb_substr($item->nombre, 0, 1)) }}
                        @endif
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-medium text-[#1a1a18]">{{ $item->nombre }}</span>
                        <span class="block truncate font-['DM_Mono'] text-xs text-[#a0a09c]">{{ $item->codigo }} · V{{ $item->version }}</span>
                    </span>
                </a>
            @empty
                <p class="px-2 py-3 text-xs text-[#a0a09c]">Aún no hay diseños. Crea el primero con «Nuevo diseño».</p>
            @endforelse
        </nav>
    </div>
</aside>
