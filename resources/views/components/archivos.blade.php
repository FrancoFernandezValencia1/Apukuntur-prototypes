@props(['seccion', 'label', 'diseno'])
@php $existentes = $diseno->fotosSeccion($seccion); @endphp

<div class="flex flex-col gap-1">
    <span class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">{{ $label }}</span>

    <label data-dropzone class="flex cursor-pointer flex-wrap items-center gap-3 rounded-md border border-dashed border-[#e2e2df] bg-white px-3 py-2 transition hover:border-[#1d4ed8]/40">
        <span class="rounded-md bg-[#f5f5f3] px-3 py-1 text-xs font-medium text-[#1a1a18]">Elegir archivos</span>
        <span class="text-xs text-[#a0a09c]" data-file-label>
            {{ count($existentes) ? count($existentes) . ' archivo(s) guardado(s)' : 'Sin archivos seleccionados' }}
        </span>
        <span class="text-[11px] text-[#a0a09c]">o arrastra fotos aquí</span>
        <input type="file" name="fotos[{{ $seccion }}][]" accept="image/*" multiple class="sr-only" data-file-input>
    </label>

    @if ($existentes)
        <div class="flex flex-wrap gap-2 pt-1" data-photo-album>
            @foreach ($existentes as $ruta)
                @php $url = \Illuminate\Support\Facades\Storage::disk('public')->url($ruta); @endphp
                <img src="{{ $url }}" alt="" title="Ampliar foto" data-photo-src="{{ $url }}"
                     tabindex="0" role="button" aria-label="Ampliar foto"
                     class="size-12 cursor-zoom-in rounded-md border border-[#e2e2df] object-cover transition hover:ring-2 hover:ring-[#1d4ed8]/40 focus-visible:ring-2 focus-visible:ring-[#1d4ed8]/60">
            @endforeach
        </div>
    @endif
</div>
