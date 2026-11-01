@php
    $isFactory = $isFactory ?? ((auth()->user()?->isFactory() || auth()->user()?->isAdmin()) ?? false);
    $coloresMaterial = $diseno->coloresMaterial;
    $codeRows = $diseno->codigosTallaColor();
    $factoryCodeRows = $isFactory ? $diseno->codigosTallaColor(\App\Models\CodigoBarrasDiseno::ORIGEN_FABRICA) : [];
    $tallas = $tallas ?? \App\Models\Diseno::TALLAS;
    $ficha = $diseno->ficha;

    $currentColor = old('ficha.color_principal', $ficha?->color_principal);
    $currentHex = optional($coloresMaterial->firstWhere('color', $currentColor))->hex ?: '#d1d5db';
    $techPhoto = $diseno->fotosSeccion('ficha')[0]
        ?? ($diseno->fotosSeccion('final')[0] ?? ($diseno->ruta_miniatura ?: null));

    $supplyRows = array_values(old('insumos', $diseno->insumos->toArray()) ?? []);
    $colorRows = array_values(old('colores_material', $coloresMaterial->toArray()) ?? []);
    $measureRows = array_values(old('medidas_ficha', $diseno->medidasFicha->toArray()) ?? []);

    $supplyCols = [
        ['key' => 'nombre', 'label' => 'Insumo', 'w' => 'min-w-32'],
        ['key' => 'cantidad', 'label' => 'Cantidad', 'type' => 'number', 'w' => 'w-24'],
        ['key' => 'codigo', 'label' => 'Cód. insum.', 'w' => 'min-w-28'],
    ];
    $colorCols = [
        ['key' => 'color', 'label' => 'Color', 'w' => 'min-w-32'],
        ['key' => 'hex', 'label' => 'Ref.', 'type' => 'color', 'w' => 'w-14'],
        ['key' => 'codigo', 'label' => 'Cód.', 'w' => 'min-w-24'],
        ['key' => 'empresa', 'label' => 'Empresa', 'w' => 'min-w-28'],
        ['key' => 'cantidad_pedida', 'label' => 'Pedido', 'type' => 'number', 'w' => 'w-20'],
    ];
    $measureCols = [
        ['key' => 'nombre', 'label' => 'Medida', 'w' => 'min-w-56'],
        ['key' => 'incremento', 'label' => 'Inc', 'type' => 'number', 'w' => 'w-20'],
        ['key' => 's', 'label' => 'S', 'type' => 'number', 'w' => 'w-20'],
        ['key' => 'm', 'label' => 'M', 'type' => 'number', 'w' => 'w-20'],
        ['key' => 'l', 'label' => 'L', 'type' => 'number', 'w' => 'w-20'],
        ['key' => 'xl', 'label' => 'XL', 'type' => 'number', 'w' => 'w-20'],
        ['key' => 'xxl', 'label' => 'XXL', 'type' => 'number', 'w' => 'w-20'],
    ];

    $colorPhotos = [];
    foreach ($coloresMaterial as $c) {
        $ruta = $diseno->fotoFichaColor($c->color);
        $colorPhotos[$c->color] = $ruta
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($ruta)
            : null;
    }
    $generalPhotoUrl = $techPhoto
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($techPhoto)
        : null;
    $initialPhotoUrl = ($currentColor && ! empty($colorPhotos[$currentColor]))
        ? $colorPhotos[$currentColor]
        : $generalPhotoUrl;

    $techAlbum = [];
    foreach ($diseno->fotosSeccion('ficha') as $ruta) {
        $techAlbum[] = \Illuminate\Support\Facades\Storage::disk('public')->url($ruta);
    }
    foreach ($colorPhotos as $url) {
        if ($url) {
            $techAlbum[] = $url;
        }
    }
    if (! $techAlbum && $generalPhotoUrl) {
        $techAlbum[] = $generalPhotoUrl;
    }
    $techAlbum = array_values(array_unique($techAlbum));
@endphp

<div class="flex flex-col gap-6">
    {{-- 1. Especificaciones técnicas --}}
    <div class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Especificaciones técnicas</p>
            <div class="flex items-center gap-4">
                <x-field name="ficha.es_muestra" label="Muestra física" as="checkbox" title="Marca si la prenda es una muestra física" :model="$diseno" :disabled="$readonly" />
                <label class="inline-flex items-center gap-2 text-sm text-[#1a1a18]" title="La marca EU al aprobar la prenda para fabricación">
                    <input type="checkbox" disabled @checked($diseno->aprobado_para_produccion) class="size-4 rounded border-[#e2e2df] text-[#1d4ed8]">
                    Aprobada para producción
                </label>
                <button type="button" onclick="window.print()"
                        class="rounded-md border border-[#e2e2df] bg-white px-3 py-1.5 text-xs font-medium text-[#1a1a18] transition hover:bg-[#f7f7f5]">
                    Imprimir ficha
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="flex flex-col gap-4 p-4">
                <x-field name="ficha.modelo" label="Modelo" :model="$diseno" />
                <x-field name="ficha.codigo_modelo" label="Cód. modelo" :model="$diseno" />
                <x-field name="ficha.tipo_tejido" label="Tipo de tejido" :model="$diseno" />

                <div class="grid grid-cols-2 gap-4">
                    <x-field name="ficha.tipo_maquina" label="Máquina" :model="$diseno" />
                    <x-field name="ficha.tension" label="Tensión" type="number" step="any" min="0" :model="$diseno" />
                    <x-field name="ficha.galga" label="Galga" type="number" step="any" min="0" :model="$diseno" />
                    <x-field name="ficha.punto" label="Punto" :model="$diseno" />
                </div>

                <x-field name="ficha.nombre_maquina" label="Máquina (nombre)" :model="$diseno" />

                {{-- Insumos --}}
                <div class="flex flex-col gap-2" data-repeater data-next="{{ count($supplyRows) }}">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Insumos</p>
                        <button type="button" data-add class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-[#1d4ed8] hover:underline">
                            <span class="text-sm leading-none">+</span> Agregar
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-max text-xs">
                            <thead>
                                <tr>
                                    @foreach ($supplyCols as $col)
                                        <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase tracking-[0.3px] text-[#6b6b67]">{{ $col['label'] }}</th>
                                    @endforeach
                                    <th class="w-8 border-b border-[#e2e2df]"></th>
                                </tr>
                            </thead>
                            <tbody data-rows>
                                @foreach ($supplyRows as $i => $row)
                                    @include('disenos.partials.fila-repetidor', ['columns' => $supplyCols, 'name' => 'insumos', 'i' => $i, 'row' => $row])
                                @endforeach
                                <template data-template>
                                    @include('disenos.partials.fila-repetidor', ['columns' => $supplyCols, 'name' => 'insumos', 'i' => '__I__', 'row' => []])
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-field name="ficha.composicion" label="Composición" :model="$diseno" />
                    <x-field name="ficha.titulo_hilado" label="Título" :model="$diseno" />
                </div>
            </div>

            <div class="flex flex-col gap-4 border-t border-[#e2e2df] p-4 lg:border-l lg:border-t-0">
                <p class="text-center text-xs font-semibold uppercase tracking-[0.6px] text-[#6b6b67]">
                    {{ $diseno->nombre ?: 'Sin nombre' }}
                </p>

                <input type="hidden" name="ficha[color_principal]" id="tech-color-input" value="{{ $currentColor }}">

                <div data-dropzone="single" data-dropzone-for="foto-ficha-input" title="Arrastra una imagen aquí"
                     class="relative aspect-[3/4] w-full max-w-[12rem] overflow-hidden rounded-lg border border-[#e2e2df] bg-[#f5f5f3]">
                    <img id="foto-ficha" src="{{ $initialPhotoUrl }}" data-general="{{ $generalPhotoUrl }}" alt=""
                         data-photo-src="{{ $initialPhotoUrl }}" data-album="{{ json_encode($techAlbum) }}"
                         class="size-full cursor-zoom-in object-cover {{ $initialPhotoUrl ? '' : 'hidden' }}">
                    <span id="foto-ficha-vacia" class="flex size-full items-center justify-center text-xs text-[#a0a09c] {{ $initialPhotoUrl ? 'hidden' : '' }}">Sin imagen</span>
                    <span id="tech-pill" class="absolute bottom-2 left-2 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-semibold uppercase shadow {{ filled($currentColor) ? '' : 'hidden' }}">
                        <span id="tech-pill-dot" class="size-2.5 rounded-full" style="background: {{ $currentHex }}"></span><span id="tech-pill-label">{{ $currentColor }}</span>
                    </span>
                </div>

                <label data-dropzone="single" class="flex flex-wrap cursor-pointer items-center justify-center gap-2 rounded-md border border-dashed border-[#e2e2df] bg-white px-3 py-2 text-xs text-[#6b6b67] transition hover:border-[#1d4ed8]/40">
                    <span class="rounded-md bg-[#f5f5f3] px-2 py-1 font-medium text-[#1a1a18]">Subir foto</span>
                    <span data-tech-photo-label>{{ $currentColor ? 'para ' . $currentColor : 'general' }}</span>
                    <span class="text-[11px] text-[#a0a09c]">o arrastra aquí</span>
                    <input type="file" id="foto-ficha-input" name="fotos[ficha][]" accept="image/*" class="sr-only" @disabled($readonly)>
                </label>

                <x-field name="ficha.observaciones" label="Observaciones de la ficha" as="textarea" rows="4" :value="$ficha?->observaciones" />

                @if ($isFactory && $diseno->exists)
                    <div class="flex justify-end">
                        <button type="submit" data-tech-note-save
                                class="rounded-md bg-[#1d4ed8] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#1e40af]">
                            Guardar observación
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 1b. Historial de «Observaciones de la ficha» --}}
    @include('disenos.partials.ficha.historial-notas')

    {{-- 2. Colores material --}}
    <div id="tech-swatches" class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white" data-repeater data-next="{{ count($colorRows) }}">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Colores material</p>
            <button type="button" data-add class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-[#1d4ed8] hover:underline">
                <span class="text-sm leading-none">+</span> Agregar color
            </button>
        </div>

        @if ($coloresMaterial->isNotEmpty())
            <div class="flex flex-wrap gap-4 border-b border-[#e2e2df] px-4 py-3">
                @foreach ($coloresMaterial as $c)
                    <button type="button" data-tech-color data-color="{{ $c->color }}"
                            data-row-index="{{ $loop->index }}"
                            data-hex="{{ $c->hex ?: '#d1d5db' }}"
                            data-photo="{{ $colorPhotos[$c->color] ?? '' }}"
                            aria-pressed="{{ $c->color === $currentColor ? 'true' : 'false' }}"
                            class="flex flex-col items-center gap-1 rounded-md px-1 py-0.5 outline-none transition hover:bg-[#f5f5f3] focus-visible:ring-2 focus-visible:ring-[#1d4ed8]/40">
                        <span data-swatch-dot class="size-8 rounded-full border-2 border-white ring-1 ring-[#e2e2df]" style="background: {{ $c->hex ?: '#d1d5db' }}"></span>
                        <span data-swatch-label class="text-[10px] font-medium uppercase text-[#a0a09c]">{{ $c->color }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs">
                <thead class="bg-[#f5f5f3]">
                    <tr>
                        @foreach ($colorCols as $col)
                            <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase tracking-[0.3px] text-[#6b6b67]">{{ $col['label'] }}</th>
                        @endforeach
                        <th class="w-8 border-b border-[#e2e2df]"></th>
                    </tr>
                </thead>
                <tbody data-rows>
                    @foreach ($colorRows as $i => $row)
                        @include('disenos.partials.fila-repetidor', ['columns' => $colorCols, 'name' => 'colores_material', 'i' => $i, 'row' => $row])
                    @endforeach
                    <template data-template>
                        @include('disenos.partials.fila-repetidor', ['columns' => $colorCols, 'name' => 'colores_material', 'i' => '__I__', 'row' => []])
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. Códigos por talla y color + código activo --}}
    <section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Códigos por talla y color</p>
        </div>

        @if (! $diseno->exists || ! $codeRows)
            <p class="px-4 py-6 text-center text-xs text-[#a0a09c]">
                Agrega colores con su código en «Colores material» y guarda el diseño para generar la matriz.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-max text-xs">
                    <thead class="bg-[#f5f5f3]">
                        <tr>
                            <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase text-[#6b6b67]">Color</th>
                            @foreach ($tallas as $label)
                                <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase text-[#6b6b67]">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($codeRows as $row)
                            <tr data-code-color="{{ $row['color'] }}">
                                <td class="border-b border-[#e2e2df] px-3 py-1.5">
                                    <span class="inline-flex items-center gap-2 font-medium">
                                        <span data-code-dot class="size-3 rounded-full ring-1 ring-black/10" style="background: {{ $row['hex'] }}"></span>{{ $row['color'] }}
                                    </span>
                                </td>
                                @foreach ($row['celdas'] as $sizeKey => $cell)
                                    <td class="border-b border-[#e2e2df] px-1 py-1">
                                        <button type="button" aria-pressed="false"
                                                data-code="{{ $cell['codigo'] }}"
                                                data-key="{{ $row['codigo'] }}__{{ $sizeKey }}"
                                                data-color="{{ $row['color'] }}"
                                                data-hex="{{ $row['hex'] }}"
                                                data-color-code="{{ $row['codigo'] }}"
                                                data-size-key="{{ $sizeKey }}"
                                                data-size-label="{{ $cell['etiqueta'] }}"
                                                data-barcode="{{ $cell['barcode'] }}"
                                                data-barcode-code="{{ $cell['codigo_barras'] }}"
                                                class="inline-flex items-center gap-1.5 rounded px-2 py-1 font-['DM_Mono'] text-[11px] text-[#6b6b67] transition hover:bg-[#f5f5f3] aria-pressed:bg-[#1d4ed8] aria-pressed:text-white">
                                            {{ $cell['codigo'] }}
                                            @if ($cell['barcode'])
                                                <span class="size-1.5 rounded-full bg-[#16a34a]"></span>
                                            @endif
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="code-panel" class="flex flex-wrap items-center gap-6 border-t border-[#e2e2df] p-4"
                 data-preview-url="{{ route('disenos.codigos.preview', $diseno) }}">
                <div class="flex min-w-48 flex-col gap-1">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#a0a09c]">Código activo</p>
                    <div class="flex items-center gap-2">
                        <span id="ac-dot" class="size-3 rounded-full bg-[#d1d5db]"></span>
                        <span id="ac-color" class="text-sm font-medium">—</span>
                        <span id="ac-size" class="text-xs font-medium text-[#1d4ed8]"></span>
                    </div>
                    <p id="ac-code" class="font-['DM_Mono'] text-xs text-[#6b6b67]">—</p>
                </div>

                @can('eu')
                    <div class="flex min-w-60 flex-col gap-1">
                        <label for="ac-barcode-input" class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#a0a09c]">Código de barras</label>
                        <input type="text" id="ac-barcode-input" name="codigo" form="barcode-store-form"
                               maxlength="120" autocomplete="off" spellcheck="false" data-nodirty
                               placeholder="Escribe el código…"
                               class="w-60 rounded-md border border-[#e2e2df] bg-white px-3 py-2 font-['DM_Mono'] text-xs text-[#1a1a18] outline-none focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
                        <p class="text-[11px] text-[#a0a09c]">El código de barras se genera a partir de este texto.</p>
                    </div>
                @endcan

                <div id="ac-barcode-box" class="hidden rounded-lg border border-[#e2e2df] bg-white p-3">
                    <img id="ac-barcode" src="" alt="Código de barras" class="h-16 max-w-full">
                    <p id="ac-barcode-text" class="mt-1 text-center font-['DM_Mono'] text-[10px] text-[#1a1a18]"></p>
                </div>

                <p id="ac-none" class="text-xs text-[#a0a09c]">Selecciona una celda de la matriz.</p>

                @can('eu')
                    <div class="flex gap-2">
                        <button type="submit" form="barcode-store-form" id="ac-assign"
                                class="hidden rounded-md bg-[#1d4ed8] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#1e40af]">
                            Guardar código de barras
                        </button>
                        <button type="submit" form="barcode-delete-form" id="ac-remove"
                                class="hidden rounded-md border border-[#e2e2df] bg-white px-4 py-2 text-sm text-[#dc2626] transition hover:bg-red-50">
                            Quitar código
                        </button>
                    </div>
                @else
                    <p class="text-[11px] text-[#a0a09c]">Código de barras de EU (solo lectura).</p>
                @endcan
            </div>
        @endif
    </section>

    {{-- 3b. Códigos de barras de Fábrica (independientes de EU) --}}
    @if ($isFactory)
        <section class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
                <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Códigos de barras (Fábrica)</p>
                <p class="flex items-center gap-1.5 text-[11px] text-[#a0a09c]">
                    <span class="size-1.5 rounded-full bg-[#16a34a]"></span> código propio de Fábrica
                </p>
            </div>

            @if (! $diseno->exists || ! $factoryCodeRows)
                <p class="px-4 py-6 text-center text-xs text-[#a0a09c]">
                    Agrega colores con su código en «Colores material» y guarda el diseño para generar la matriz.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-max text-xs">
                        <thead class="bg-[#f5f5f3]">
                            <tr>
                                <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase text-[#6b6b67]">Color</th>
                                @foreach ($tallas as $label)
                                    <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase text-[#6b6b67]">{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($factoryCodeRows as $row)
                                <tr data-code-color="{{ $row['color'] }}">
                                    <td class="border-b border-[#e2e2df] px-3 py-1.5">
                                        <span class="inline-flex items-center gap-2 font-medium">
                                            <span data-code-dot class="size-3 rounded-full ring-1 ring-black/10" style="background: {{ $row['hex'] }}"></span>{{ $row['color'] }}
                                        </span>
                                    </td>
                                    @foreach ($row['celdas'] as $sizeKey => $cell)
                                        <td class="border-b border-[#e2e2df] px-1 py-1">
                                            <button type="button" aria-pressed="false"
                                                    data-fcode="{{ $cell['codigo'] }}"
                                                    data-fkey="{{ $row['codigo'] }}__{{ $sizeKey }}"
                                                    data-fcolor="{{ $row['color'] }}"
                                                    data-fhex="{{ $row['hex'] }}"
                                                    data-fcolor-code="{{ $row['codigo'] }}"
                                                    data-fsize-key="{{ $sizeKey }}"
                                                    data-fsize-label="{{ $cell['etiqueta'] }}"
                                                    data-fbarcode="{{ $cell['barcode'] }}"
                                                    data-fbarcode-code="{{ $cell['codigo_barras'] }}"
                                                    class="inline-flex items-center gap-1.5 rounded px-2 py-1 font-['DM_Mono'] text-[11px] text-[#6b6b67] transition hover:bg-[#f5f5f3] aria-pressed:bg-[#1d4ed8] aria-pressed:text-white">
                                                {{ $cell['codigo'] }}
                                                @if ($cell['barcode'])
                                                    <span class="size-1.5 rounded-full bg-[#16a34a]"></span>
                                                @endif
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="fc-code-panel" class="flex flex-wrap items-center gap-6 border-t border-[#e2e2df] p-4"
                     data-preview-url="{{ route('disenos.codigos.preview', $diseno) }}">
                    <div class="flex min-w-48 flex-col gap-1">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#a0a09c]">Código activo</p>
                        <div class="flex items-center gap-2">
                            <span id="fc-dot" class="size-3 rounded-full bg-[#d1d5db]"></span>
                            <span id="fc-color" class="text-sm font-medium">—</span>
                            <span id="fc-size" class="text-xs font-medium text-[#1d4ed8]"></span>
                        </div>
                        <p id="fc-code" class="font-['DM_Mono'] text-xs text-[#6b6b67]">—</p>
                    </div>

                    <div class="flex min-w-60 flex-col gap-1">
                        <label for="fc-barcode-input" class="text-[10px] font-semibold uppercase tracking-[0.8px] text-[#a0a09c]">Código de barras</label>
                        <input type="text" id="fc-barcode-input" name="codigo" form="factory-barcode-store-form"
                               maxlength="120" autocomplete="off" spellcheck="false" data-nodirty
                               placeholder="Escribe el código…"
                               class="w-60 rounded-md border border-[#e2e2df] bg-white px-3 py-2 font-['DM_Mono'] text-xs text-[#1a1a18] outline-none focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20">
                        <p class="text-[11px] text-[#a0a09c]">El código de barras se genera a partir de este texto.</p>
                    </div>

                    <div id="fc-barcode-box" class="hidden rounded-lg border border-[#e2e2df] bg-white p-3">
                        <img id="fc-barcode" src="" alt="Código de barras" class="h-16 max-w-full">
                        <p id="fc-barcode-text" class="mt-1 text-center font-['DM_Mono'] text-[10px] text-[#1a1a18]"></p>
                    </div>

                    <p id="fc-none" class="text-xs text-[#a0a09c]">Selecciona una celda de la matriz.</p>

                    <div class="flex gap-2">
                        <button type="submit" form="factory-barcode-store-form" id="fc-assign"
                                class="hidden rounded-md bg-[#1d4ed8] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#1e40af]">
                            Guardar código de barras
                        </button>
                        <button type="submit" form="factory-barcode-delete-form" id="fc-remove"
                                class="hidden rounded-md border border-[#e2e2df] bg-white px-4 py-2 text-sm text-[#dc2626] transition hover:bg-red-50">
                            Quitar código
                        </button>
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- 4. Medidas --}}
    <div id="tech-measures" class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white" data-repeater data-next="{{ count($measureRows) }}">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">Medidas (cm)</p>
            <button type="button" data-add class="inline-flex shrink-0 items-center gap-1 text-xs font-medium text-[#1d4ed8] hover:underline">
                <span class="text-sm leading-none">+</span> Agregar fila
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs">
                <thead class="bg-[#f5f5f3]">
                    <tr>
                        @foreach ($measureCols as $col)
                            <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase tracking-[0.3px] text-[#6b6b67]">{{ $col['label'] }}</th>
                        @endforeach
                        <th class="w-8 border-b border-[#e2e2df]"></th>
                    </tr>
                </thead>
                <tbody data-rows>
                    @foreach ($measureRows as $i => $row)
                        @include('disenos.partials.fila-repetidor', ['columns' => $measureCols, 'name' => 'medidas_ficha', 'i' => $i, 'row' => $row])
                    @endforeach
                    <template data-template>
                        @include('disenos.partials.fila-repetidor', ['columns' => $measureCols, 'name' => 'medidas_ficha', 'i' => '__I__', 'row' => []])
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- 5. Historial de observaciones --}}
    @include('disenos.partials.ficha.historial')

    {{-- 6. Revisión EU --}}
    @include('disenos.partials.ficha.revisiones')
</div>

{{-- Impresión: anverso (ficha) / reverso (códigos de barras) --}}
<div id="tech-print" class="hidden">
    <section class="tp-page tp-front">
        <header class="tp-head">
            <div>
                <h1 class="tp-title">FICHA TÉCNICA</h1>
                <p class="tp-sub">{{ $diseno->codigo }} · {{ $diseno->nombre }}</p>
            </div>
            <div class="tp-head-right">
                <p>Versión {{ $diseno->version }}</p>
                <p>Estado: {{ $diseno->etiquetaEstado }}</p>
                <p>Impresión: <span data-print-date>{{ now()->format('d/m/Y') }}</span></p>
            </div>
        </header>

        <div class="tp-specs">
            <div class="tp-specs-left">
                <h2 class="tp-h2">Especificaciones técnicas</h2>
                <dl class="tp-dl">
                    <div><dt>Modelo</dt><dd>{{ $ficha?->modelo }}</dd></div>
                    <div><dt>Cód. modelo</dt><dd>{{ $ficha?->codigo_modelo }}</dd></div>
                    <div><dt>Tipo de tejido</dt><dd>{{ $ficha?->tipo_tejido }}</dd></div>
                    <div><dt>Máquina</dt><dd>{{ $ficha?->tipo_maquina }}</dd></div>
                    <div><dt>Tensión</dt><dd>{{ $ficha?->tension }}</dd></div>
                    <div><dt>Galga</dt><dd>{{ $ficha?->galga }}</dd></div>
                    <div><dt>Punto</dt><dd>{{ $ficha?->punto }}</dd></div>
                    <div><dt>Máquina (nombre)</dt><dd>{{ $ficha?->nombre_maquina }}</dd></div>
                    <div><dt>Composición</dt><dd>{{ $ficha?->composicion }}</dd></div>
                    <div><dt>Título</dt><dd>{{ $ficha?->titulo_hilado }}</dd></div>
                    <div><dt>Color</dt><dd>{{ $ficha?->color_principal }}</dd></div>
                </dl>
            </div>

            @if ($generalPhotoUrl)
                <div class="tp-photo">
                    <img src="{{ $generalPhotoUrl }}" alt="">
                </div>
            @endif
        </div>

        <h2 class="tp-h2">Insumos</h2>
        <table class="tp-table">
            <thead>
                <tr><th>Insumo</th><th>Cantidad</th><th>Cód. insum.</th></tr>
            </thead>
            <tbody>
                @forelse ($diseno->insumos as $insumo)
                    <tr>
                        <td class="tp-left">{{ $insumo->nombre }}</td>
                        <td>{{ $insumo->cantidad }}</td>
                        <td>{{ $insumo->codigo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3">Sin insumos registrados</td></tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="tp-h2">Medidas (cm)</h2>
        <table class="tp-table">
            <thead>
                <tr>
                    <th>Medida</th>
                    <th>Inc</th>
                    <th>S</th>
                    <th>M</th>
                    <th>L</th>
                    <th>XL</th>
                    <th>XXL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($diseno->medidasFicha as $m)
                    <tr>
                        <td class="tp-left">{{ $m->nombre }}</td>
                        <td>{{ $m->incremento }}</td>
                        <td>{{ $m->s }}</td>
                        <td>{{ $m->m }}</td>
                        <td>{{ $m->l }}</td>
                        <td>{{ $m->xl }}</td>
                        <td>{{ $m->xxl }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">Sin medidas registradas</td></tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="tp-h2">Observaciones</h2>
        <p class="tp-note" data-print-tech-note>{{ $ficha?->observaciones ?: 'Sin observaciones.' }}</p>

        <footer class="tp-foot">
            <span>Fecha de actualización: {{ ($ficha?->actualizado_tecnica_en ?? $diseno->actualizado_en)?->format('d/m/Y') ?: '—' }}</span>
            <span>Modificado por: {{ $ficha?->modificado_por ?: '—' }}</span>
        </footer>
    </section>

    <section class="tp-page tp-back">
        <header class="tp-head">
            <div>
                <h1 class="tp-title">CÓDIGOS POR TALLA Y COLOR</h1>
                <p class="tp-sub">{{ $diseno->codigo }} · {{ $diseno->nombre }}</p>
            </div>
            <div class="tp-head-right">
                <p>Impresión: <span data-print-date>{{ now()->format('d/m/Y') }}</span></p>
            </div>
        </header>

        @if ($codeRows)
            <table class="tp-codes">
                <thead>
                    <tr>
                        <th class="tp-color-col">Color</th>
                        @foreach ($tallas as $label)
                            <th>{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($codeRows as $row)
                        <tr>
                            <td class="tp-color-cell">
                                <span class="tp-dot" style="background: {{ $row['hex'] }}"></span>
                                <span>{{ $row['color'] }}</span>
                            </td>
                            @foreach ($row['celdas'] as $cell)
                                <td class="tp-code-cell">
                                    <img src="{{ $cell['barcode'] ?: \App\Support\ImagenCodigoBarras::dataUri($cell['codigo']) }}" alt="">
                                    <span class="tp-code-text">{{ $cell['codigo_barras'] ?: $cell['codigo'] }}</span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="tp-note">Sin colores con código registrados.</p>
        @endif
    </section>
</div>

<style>
    #tech-swatches input[type="color"] {
        padding: 0;
        border: 0;
        background: transparent;
        border-radius: 9999px;
        -webkit-appearance: none;
        appearance: none;
        overflow: hidden;
    }
    #tech-swatches input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    #tech-swatches input[type="color"]::-webkit-color-swatch { border: 0; border-radius: 9999px; }
    #tech-swatches input[type="color"]::-moz-color-swatch { border: 0; border-radius: 9999px; }

    #tech-swatches [data-tech-color][aria-pressed="true"] > span:first-child {
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #1d4ed8;
        --tw-ring-color: transparent;
    }
    #tech-swatches [data-tech-color][aria-pressed="true"] > span:last-child { color: #1d4ed8; }

    #tech-measures th:not(:first-child),
    #tech-measures td:not(:first-child) { text-align: center; }
    #tech-measures td:not(:first-child) input { text-align: center; }
    #tech-measures th:first-child,
    #tech-measures td:first-child { text-align: left; }

    /* Impresión: anverso (ficha) y reverso (códigos de barras) */
    #tech-print { font-family: 'DM Sans', system-ui, -apple-system, sans-serif; color: #1a1a18; }
    .tp-page { width: 194mm; margin: 0 auto; }
    .tp-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #d1d5db; padding-bottom: 3mm; margin-bottom: 4mm; }
    .tp-title { font-size: 13pt; font-weight: 700; letter-spacing: .06em; margin: 0; }
    .tp-sub { font-size: 8pt; color: #4b5563; margin: 1mm 0 0; }
    .tp-head-right { text-align: right; font-size: 7.5pt; color: #4b5563; }
    .tp-head-right p { margin: 0; }
    .tp-specs { display: flex; gap: 6mm; margin-bottom: 4mm; }
    .tp-specs-left { flex: 1; min-width: 0; }
    .tp-photo img { width: 42mm; height: 56mm; object-fit: cover; border: 1px solid #e5e7eb; border-radius: 2mm; }
    .tp-h2 { font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 1mm; margin: 4mm 0 2mm; }
    .tp-dl { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5mm 6mm; font-size: 8pt; margin: 0; }
    .tp-dl dt { font-size: 6.5pt; text-transform: uppercase; color: #6b7280; }
    .tp-dl dd { margin: 0; font-weight: 500; }
    .tp-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
    .tp-table th, .tp-table td { border: 1px solid #d1d5db; padding: 1mm; text-align: center; }
    .tp-table th { background: #f3f4f6; text-transform: uppercase; font-size: 6.5pt; letter-spacing: .04em; }
    .tp-table td.tp-left { text-align: left; }
    .tp-note { font-size: 8pt; white-space: pre-line; border: 1px solid #e5e7eb; border-radius: 2mm; padding: 2mm; margin: 0; }
    .tp-foot { display: flex; justify-content: space-between; font-size: 7pt; color: #6b7280; margin-top: 6mm; border-top: 1px solid #e5e7eb; padding-top: 2mm; }
    .tp-codes { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .tp-codes th, .tp-codes td { border: 1px solid #d1d5db; padding: 1.2mm; vertical-align: top; }
    .tp-codes th { background: #f3f4f6; font-size: 7pt; text-transform: uppercase; }
    .tp-codes .tp-color-col { width: 24mm; text-align: left; }
    .tp-color-cell { font-size: 7pt; font-weight: 600; text-align: left; white-space: nowrap; }
    .tp-dot { display: inline-block; width: 3mm; height: 3mm; border-radius: 50%; border: 1px solid rgba(0, 0, 0, .15); vertical-align: middle; margin-right: 1.5mm; }
    .tp-code-cell { text-align: center; }
    .tp-code-cell img { width: 100%; height: 11mm; object-fit: fill; display: block; }
    .tp-code-text { display: block; font-family: 'DM Mono', monospace; font-size: 5.5pt; margin-top: .8mm; word-break: break-all; }

    @media print {
        @page { size: A4 portrait; margin: 8mm; }
        html, body { background: #fff !important; }
        body > *:not(#tech-print) { display: none !important; }
        #tech-print { display: block !important; }
        .tp-page, .tp-table, .tp-codes, .tp-dot, .tp-table th, .tp-codes th {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .tp-front { page-break-after: always; break-after: page; }
        .tp-back { page-break-before: always; break-before: page; }
    }
</style>

<script>
    (() => {
        const swatches = [...document.querySelectorAll('#tech-swatches [data-tech-color]')];
        const colorInput = document.getElementById('tech-color-input');
        const photo = document.getElementById('foto-ficha');
        const empty = document.getElementById('foto-ficha-vacia');
        const pill = document.getElementById('tech-pill');
        const pillDot = document.getElementById('tech-pill-dot');
        const pillLabel = document.getElementById('tech-pill-label');
        const fileInput = document.getElementById('foto-ficha-input');
        const fileLabel = document.querySelector('[data-tech-photo-label]');
        const generalPhoto = photo?.dataset.general || '';

        if (!photo || !swatches.length) return;

        let activeColor = colorInput?.value || '';

        const apply = (btn) => {
            const color = btn?.dataset.color || '';
            const hex = btn?.dataset.hex || '#d1d5db';
            const url = btn?.dataset.photo || generalPhoto;

            if (color !== activeColor && fileInput) fileInput.value = '';
            activeColor = color;

            swatches.forEach(s => s.setAttribute('aria-pressed', String(s === btn)));
            if (colorInput) colorInput.value = color;

            if (url) {
                photo.src = url;
                photo.dataset.photoSrc = url;
                photo.classList.remove('hidden');
                empty?.classList.add('hidden');
            } else {
                photo.removeAttribute('src');
                delete photo.dataset.photoSrc;
                photo.classList.add('hidden');
                empty?.classList.remove('hidden');
            }

            if (color) {
                pill?.classList.remove('hidden');
                if (pillDot) pillDot.style.background = hex;
                if (pillLabel) pillLabel.textContent = color;
            } else {
                pill?.classList.add('hidden');
            }

            document.querySelectorAll('[data-code-color]').forEach(row => {
                row.classList.toggle('bg-[#eff3ff]', !!color && row.dataset.codeColor === color);
            });
            document.querySelectorAll('input[name^="colores_material"][name$="[color]"]').forEach(inp => {
                const tr = inp.closest('tr');
                if (tr) tr.classList.toggle('bg-[#eff3ff]', !!color && inp.value.trim() === color);
            });

            if (fileInput) fileInput.name = color ? `fotos[ficha_colores][${color}]` : 'fotos[ficha][]';
            if (fileLabel) fileLabel.textContent = color ? `para ${color}` : 'general';
        };

        swatches.forEach(btn => btn.addEventListener('click', () => apply(btn)));

        document.querySelectorAll('[data-code]').forEach(btn => {
            btn.addEventListener('click', () => {
                const match = swatches.find(s => s.dataset.color === btn.dataset.color);
                if (match) apply(match);
            });
        });
        document.querySelectorAll('[data-fcode]').forEach(btn => {
            btn.addEventListener('click', () => {
                const match = swatches.find(s => s.dataset.color === btn.dataset.fcolor);
                if (match) apply(match);
            });
        });

        fileInput?.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (!file) return;
            photo.src = URL.createObjectURL(file);
            photo.classList.remove('hidden');
            empty?.classList.add('hidden');
        });

        document.addEventListener('input', (e) => {
            const match = e.target?.name?.match(/^colores_material\[(\d+)\]\[(hex|color)\]$/);
            if (!match) return;

            const index = Number(match[1]);
            const swatch = swatches.find(s => Number(s.dataset.rowIndex) === index);
            if (!swatch) return;

            const dot = swatch.querySelector('[data-swatch-dot]');
            const label = swatch.querySelector('[data-swatch-label]');
            const active = swatch.getAttribute('aria-pressed') === 'true';

            if (match[2] === 'hex') {
                const hex = e.target.value;
                swatch.dataset.hex = hex;
                if (dot) dot.style.background = hex;

                if (active) {
                    if (pillDot) pillDot.style.background = hex;
                    const acDot = document.getElementById('ac-dot');
                    if (acDot) acDot.style.background = hex;
                }

                const name = document.querySelector(`[name="colores_material[${index}][color]"]`)?.value?.trim();
                if (!name) return;
                document.querySelectorAll('[data-code-color]').forEach(row => {
                    if (row.dataset.codeColor === name) {
                        const codeDot = row.querySelector('[data-code-dot]');
                        if (codeDot) codeDot.style.background = hex;
                    }
                });
                document.querySelectorAll('[data-code]').forEach(btn => {
                    if (btn.dataset.color === name) btn.dataset.hex = hex;
                });
                document.querySelectorAll('[data-fcode]').forEach(btn => {
                    if (btn.dataset.fcolor === name) btn.dataset.fhex = hex;
                });
            } else {
                const name = e.target.value;
                swatch.dataset.color = name;
                if (label) label.textContent = name.toUpperCase();
                if (active) {
                    if (pillLabel) pillLabel.textContent = name;
                    if (colorInput) colorInput.value = name;
                }
            }
        });

        const current = swatches.find(s => s.dataset.color === (colorInput?.value || ''));
        if (current) apply(current);
    })();
</script>

<script>
    (() => {
        const printRoot = document.getElementById('tech-print');
        if (!printRoot) return;

        let placeholder = null;

        window.addEventListener('beforeprint', () => {
            const today = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
            document.querySelectorAll('[data-print-date]').forEach(el => { el.textContent = today; });

            if (placeholder) return;
            placeholder = document.createComment('tech-print');
            printRoot.parentNode.insertBefore(placeholder, printRoot);
            document.body.appendChild(printRoot);
        });

        window.addEventListener('afterprint', () => {
            if (!placeholder || !placeholder.parentNode) return;
            placeholder.parentNode.insertBefore(printRoot, placeholder);
            placeholder.remove();
            placeholder = null;
        });
    })();
</script>
