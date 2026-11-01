@php $autores = $diseno->observaciones->pluck('autor')->unique()->values(); @endphp

<section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white" data-history>
    <div class="flex items-center justify-between gap-3 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
        <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">
            Historial de observaciones
            <span class="rounded-full bg-[#e2e2df] px-2 py-0.5 text-[10px] font-semibold">{{ $diseno->observaciones->count() }}</span>
        </p>
        @if ($diseno->exists)
            <button type="button" data-toggle="#obs-box" class="text-xs font-medium text-[#1d4ed8] hover:underline">+ Agregar observación</button>
        @endif
    </div>

    @if (! $diseno->exists)
        <p class="px-4 py-6 text-center text-xs text-[#a0a09c]">Guarda el diseño para registrar observaciones.</p>
    @else
        <div class="flex flex-col gap-4 p-4">
            {{-- Nueva observación (formulario independiente: obs-form) --}}
            <div id="obs-box" @class(['hidden' => ! $errors->has('comentario')])>
                <div class="flex flex-col gap-2 rounded-lg border border-[#e2e2df] bg-[#fafaf9] p-3">
                    <textarea name="comentario" form="obs-form" rows="3" placeholder="Escribe la observación…"
                              class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">{{ old('comentario') }}</textarea>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-[#a0a09c]">Se guardará como {{ auth()->user()->isAdmin() ? 'Admin' : (auth()->user()->isEu() ? 'EU' : 'INT') }} · {{ auth()->user()->nombre }}</span>
                        <button type="submit" form="obs-form"
                                class="rounded-md bg-[#1d4ed8] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#1e40af]">Guardar observación</button>
                    </div>
                </div>
            </div>

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
                @forelse ($diseno->observaciones as $observacion)
                    <li data-author="{{ $observacion->autor }}" data-date="{{ $observacion->observado_en->format('Y-m-d') }}"
                        class="rounded-lg border border-[#e2e2df] bg-white">
                        <button type="button" data-expand class="flex w-full items-start gap-3 px-3 py-2.5 text-left">
                            <span @class([
                                'mt-0.5 rounded px-1.5 py-0.5 text-[10px] font-bold',
                                'bg-[#f3e8ff] text-[#7e22ce]' => in_array($observacion->rol, ['eu', 'admin'], true),
                                'bg-[#eff3ff] text-[#1d4ed8]' => ! in_array($observacion->rol, ['eu', 'admin'], true),
                            ])>{{ $observacion->etiqueta() }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-baseline gap-2">
                                    <span class="text-sm font-medium">{{ $observacion->autor }}</span>
                                    <span class="text-xs text-[#a0a09c]">{{ $observacion->observado_en->format('Y-m-d H:i') }}</span>
                                </span>
                                <span data-comment class="mt-0.5 line-clamp-1 block text-xs text-[#6b6b67]">{{ $observacion->comentario }}</span>
                            </span>
                            <span data-arrow class="mt-1 text-[10px] text-[#a0a09c] transition">▶</span>
                        </button>
                    </li>
                @empty
                    <li class="rounded-lg border border-dashed border-[#e2e2df] px-3 py-4 text-center text-xs text-[#a0a09c]">Aún no hay observaciones.</li>
                @endforelse
            </ul>
        </div>
    @endif
</section>
