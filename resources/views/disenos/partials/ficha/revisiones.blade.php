@php
    $aprobadas = $diseno->revisiones->where('estado', \App\Models\RevisionDiseno::APROBADA)->count();
    $observadas = $diseno->revisiones->where('estado', \App\Models\RevisionDiseno::OBSERVADA)->count();
@endphp

<section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Revisión EU</p>
            <span class="text-[11px] text-[#a0a09c]">Usuarios externos</span>
            <span class="rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-semibold text-green-700">✓ {{ $aprobadas }} aprobaron</span>
            <span class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-semibold text-[#dc2626]">✕ {{ $observadas }} con observaciones</span>
        </div>

        @can('eu')
            @if ($diseno->exists)
                <button type="button" data-toggle="#review-box" class="text-xs font-medium text-[#1d4ed8] hover:underline">+ Nueva revisión EU</button>
            @endif
        @endcan
    </div>

    @if (! $diseno->exists)
        <p class="px-4 py-6 text-center text-xs text-[#a0a09c]">Guarda el diseño para registrar revisiones.</p>
    @else
        <div class="flex flex-col gap-3 p-4">
            @can('eu')
                <div id="review-box" @class(['hidden' => ! $errors->has('estado') && ! $errors->has('comentario')])>
                    <div class="flex flex-col gap-2 rounded-lg border border-[#e2e2df] bg-[#fafaf9] p-3">
                        <select name="estado" form="review-form"
                                class="rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8]">
                            @foreach (\App\Models\RevisionDiseno::ESTADOS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <textarea name="comentario" form="review-form" rows="3" placeholder="Comentario de la revisión…"
                                  class="w-full rounded-md border border-[#e2e2df] bg-white px-3 py-2 text-sm outline-none focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20"></textarea>
                        <div class="flex justify-end">
                            <button type="submit" form="review-form"
                                    class="rounded-md bg-[#1d4ed8] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#1e40af]">Registrar revisión</button>
                        </div>
                    </div>
                </div>
            @endcan

            @forelse ($diseno->revisiones as $revision)
                @php $ok = $revision->estado === \App\Models\RevisionDiseno::APROBADA; @endphp
                <article @class([
                    'flex gap-3 rounded-lg border p-3',
                    'border-green-200 bg-green-50' => $ok,
                    'border-orange-200 bg-orange-50' => ! $ok,
                ])>
                    <span @class([
                        'flex size-8 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                        'bg-green-200 text-green-800' => $ok,
                        'bg-orange-200 text-orange-800' => ! $ok,
                    ])>{{ mb_strtoupper(mb_substr($revision->revisor, 0, 1)) }}</span>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-medium">
                                {{ $revision->revisor }}
                                <span class="ml-1 text-xs font-normal text-[#a0a09c]">{{ $revision->revisado_en->format('Y-m-d H:i') }}</span>
                            </p>
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-[10px] font-semibold',
                                'bg-green-100 text-green-700' => $ok,
                                'bg-orange-100 text-orange-700' => ! $ok,
                            ])>{{ $ok ? '✓' : '⚠' }} {{ \App\Models\RevisionDiseno::ESTADOS[$revision->estado] ?? $revision->estado }}</span>
                        </div>
                        <p class="mt-1 text-sm text-[#1a1a18]">{{ $revision->comentario }}</p>
                    </div>
                </article>
            @empty
                <p class="rounded-lg border border-dashed border-[#e2e2df] px-3 py-4 text-center text-xs text-[#a0a09c]">Aún no hay revisiones de EU.</p>
            @endforelse
        </div>
    @endif
</section>
