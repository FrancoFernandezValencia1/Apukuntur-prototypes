@php $autores = $diseno->historialNotas->pluck('autor')->unique()->values(); @endphp

<section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white" data-history>
    <div class="flex items-center justify-between gap-3 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
        <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">
            Historial de observaciones de la ficha
            <span class="rounded-full bg-[#e2e2df] px-2 py-0.5 text-[10px] font-semibold">{{ $diseno->historialNotas->count() }}</span>
        </p>
    </div>

    @if (! $diseno->exists)
        <p class="px-4 py-6 text-center text-xs text-[#a0a09c]">Guarda el diseño para ver el historial.</p>
    @elseif ($diseno->historialNotas->isEmpty())
        <p class="px-4 py-6 text-center text-xs text-[#a0a09c]">Aún no hay observaciones guardadas en la ficha.</p>
    @else
        <div class="flex flex-col gap-4 p-4">
            {{-- Filtros (solo en pantalla, no se envían) --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto_auto]" data-nodirty>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Filtrar por persona</label>
                    <select data-filter-who class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8]">
                        <option value="">Todas las personas</option>
                        @foreach ($autores as $autor)
                            <option value="{{ $autor }}">{{ $autor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Desde</label>
                    <input type="date" data-filter-from class="rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8]">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">Hasta</label>
                    <input type="date" data-filter-to class="rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8]">
                </div>
            </div>

            <ul class="flex flex-col gap-2" data-history-list>
                @foreach ($diseno->historialNotas as $nota)
                    <li data-author="{{ $nota->autor }}" data-date="{{ $nota->observado_en->format('Y-m-d') }}"
                        data-tech-note-item class="rounded-lg border border-[#e2e2df] bg-white">
                        <div class="flex items-start gap-2">
                            <button type="button" data-expand class="flex min-w-0 flex-1 items-start gap-3 px-3 py-2.5 text-left">
                                <span class="mt-0.5 rounded bg-[#eff3ff] px-1.5 py-0.5 text-[10px] font-bold text-[#1d4ed8]">FICHA</span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex flex-wrap items-baseline gap-2">
                                        <span class="text-sm font-medium">{{ $nota->autor }}</span>
                                        <span class="text-xs text-[#a0a09c]">{{ $nota->observado_en->format('Y-m-d H:i') }}</span>
                                    </span>
                                    <span data-comment class="mt-0.5 line-clamp-1 block text-xs text-[#6b6b67]">{{ $nota->comentario ?: '—' }}</span>
                                </span>
                                <span data-arrow class="mt-1 text-[10px] text-[#a0a09c] transition">▶</span>
                            </button>
                            <button type="button" data-tech-note-select aria-pressed="false"
                                    class="mr-3 mt-2 shrink-0 rounded-md border border-[#e2e2df] bg-white px-2.5 py-1 text-[11px] font-medium text-[#1d4ed8] transition hover:bg-[#eff3ff] aria-pressed:border-[#1d4ed8] aria-pressed:bg-[#eff3ff]">
                                Seleccionar
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
