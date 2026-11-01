@php
    $usuario   = auth()->user();
    $isAdmin   = $usuario->isAdmin();
    $isFactory = $usuario->isFactory() || $isAdmin;
    $isEu      = $usuario->isEu() || $isAdmin;
    $readonly  = ! $isFactory;           // EU ve todo, pero no edita los datos del diseño
    $tabs      = ['idea' => '1 · Idea', 'diseno' => '2 · Diseño plasmado', 'prototipo' => '3 · Prototipo', 'ficha' => 'Ficha técnica'];
    $tab       = old('_tab', request('tab', 'idea'));
    $tab       = $tab === 'tech' ? 'ficha' : $tab;
    $tab       = array_key_exists($tab, $tabs) ? $tab : 'idea';
    $action    = $diseno->exists ? route('disenos.update', $diseno) : route('disenos.store');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Base de diseños y desarrollo de producto</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f5f3] font-['DM_Sans'] text-[#1a1a18] antialiased">

    @include('disenos.partials.encabezado')

    <div class="flex min-h-[calc(100vh-73px)] flex-col lg:flex-row">
        @include('disenos.partials.barra-lateral')

        <main class="flex min-w-0 flex-1 flex-col">

            @if (session('status'))
                <div class="border-b border-green-200 bg-green-50 px-6 py-2 text-sm text-green-800" role="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="border-b border-red-200 bg-red-50 px-6 py-3 text-sm text-[#dc2626]" role="alert">
                    <p class="font-medium">Revisa los siguientes campos:</p>
                    <ul class="mt-1 list-inside list-disc text-xs">
                        @foreach (array_unique($errors->all()) as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Decisión de EU: estado del diseño y prenda apta para producción --}}
            @if ($isEu && $diseno->exists)
                <form id="approval-form" method="POST" action="{{ route('disenos.aprobacion.update', $diseno) }}"
                      class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-[#1d4ed8]/20 bg-[#eff3ff] px-6 py-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="_tab" value="{{ $tab }}" data-follow-tab>

                    <span class="text-xs font-semibold uppercase tracking-[0.6px] text-[#1d4ed8]">Decisión de EU</span>

                    <select name="estado_aprobacion" aria-label="Estado del diseño según EU" title="Estado del diseño"
                            class="rounded-md border border-[#e2e2df] bg-white px-3 py-1.5 text-sm outline-none focus:border-[#1d4ed8]">
                        @foreach ($estados as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado_aprobacion', $diseno->estado_aprobacion) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <label class="flex items-center gap-2 text-sm" title="Marca si la prenda ya puede fabricarse">
                        <input type="hidden" name="aprobado_para_produccion" value="0">
                        <input type="checkbox" name="aprobado_para_produccion" value="1" @checked((bool) old('aprobado_para_produccion', $diseno->aprobado_para_produccion))
                               class="size-4 rounded border-[#e2e2df] text-[#1d4ed8] focus:ring-[#1d4ed8]/30">
                        Aprobada para producción
                    </label>

                    <button type="submit" class="rounded-md bg-[#1d4ed8] px-4 py-1.5 text-sm font-medium text-white transition hover:bg-[#1e40af]">Guardar decisión</button>
                </form>
            @endif

            <form id="design-form" method="POST" action="{{ $action }}" enctype="multipart/form-data" class="flex flex-1 flex-col"
                  @if (! $isFactory) onsubmit="return false" @endif>
                @csrf
                @if ($diseno->exists) @method('PUT') @endif
                <input type="hidden" name="_tab" id="_tab" value="{{ $tab }}">
                <input type="hidden" name="_sub" id="_sub" value="{{ old('_sub', request('sub', 'remallado')) }}">

                {{-- Cabecera del diseño + pestañas --}}
                <div class="flex flex-col gap-4 border-b border-[#e2e2df] bg-[#f5f5f3] px-6 py-4">
                    @if ($isFactory)
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <label data-dropzone="single" @class([
                                'relative flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-[#e2e2df] bg-white text-xs text-[#a0a09c]',
                                'cursor-pointer' => $isFactory,
                            ]) title="{{ $isFactory ? 'Cambiar miniatura (o arrastra una imagen)' : '' }}">
                                @if ($diseno->urlMiniatura)
                                    <img src="{{ $diseno->urlMiniatura }}" alt="" class="size-full object-cover" data-thumb-preview>
                                @else
                                    <span data-thumb-placeholder>Foto</span>
                                    <img src="" alt="" class="absolute inset-0 hidden size-full object-cover" data-thumb-preview>
                                @endif
                                <input type="file" name="miniatura" accept="image/*" class="sr-only" data-thumb-input @disabled($readonly)>
                            </label>
                            <div>
                                <h2 class="text-xl font-semibold leading-7">{{ old('nombre', $diseno->nombre) ?: 'Nuevo diseño' }}</h2>
                                <p class="text-sm text-[#a0a09c]">{{ old('codigo', $diseno->codigo) ?: 'Sin código' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="rounded-full border border-[#e2e2df] px-3 py-1 text-xs font-medium text-[#6b6b67]">Versión {{ $diseno->version }}</span>
                            <span class="rounded-full border border-[#e2e2df] bg-white px-4 py-1 text-xs font-medium text-[#6b6b67]">{{ $diseno->etiquetaEstado }}</span>
                            @if ($diseno->aprobado_para_produccion)
                                <span class="rounded-full border border-green-200 bg-green-50 px-3 py-1 text-xs font-medium text-green-700">✓ Aprobada para producción</span>
                            @endif
                        </div>
                    </div>

                    <fieldset @disabled($readonly) class="m-0 min-w-0 border-0 p-0">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <x-field name="codigo" label="Código" :model="$diseno" />
                            <x-field name="nombre" label="Nombre del diseño" :model="$diseno" />
                            <x-field name="responsable" label="Responsable" :model="$diseno" />
                            <x-field name="fecha_inicio" label="Fecha de inicio" type="date" :model="$diseno" />
                            <x-field name="version" label="Versión" type="number" min="1" :model="$diseno" />
                            {{-- Solo informativo: el estado lo cambia EU desde «Decisión de EU» --}}
                            <x-field name="estado_aprobacion" label="Estado del diseño" as="select" :options="$estados" :model="$diseno" disabled />
                        </div>
                    </fieldset>
                    @endif

                    <div class="flex gap-1 overflow-x-auto" role="tablist" data-tabs="main">
                        @foreach ($tabs as $key => $label)
                            <button type="button" role="tab" data-tab="{{ $key }}" aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                                    class="whitespace-nowrap rounded-md border border-transparent px-4 py-2 text-sm font-medium text-[#6b6b67] transition hover:bg-white aria-selected:border-[#1d4ed8]/30 aria-selected:bg-[#eff3ff] aria-selected:text-[#1d4ed8]">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Contenido por etapa --}}
                <div class="flex-1 overflow-auto bg-[#f5f5f3] px-6 py-5">
                    <section data-panel-group="main" data-panel="idea" @class(['hidden' => $tab !== 'idea'])>
                        @include('disenos.partials.etapa-1-idea')
                    </section>
                    <section data-panel-group="main" data-panel="diseno" @class(['hidden' => $tab !== 'diseno'])>
                        @include('disenos.partials.etapa-2-plasmado')
                    </section>
                    <section data-panel-group="main" data-panel="prototipo" @class(['hidden' => $tab !== 'prototipo'])>
                        @include('disenos.partials.etapa-3-prototipo')
                    </section>
                    <section data-panel-group="main" data-panel="ficha" @class(['hidden' => $tab !== 'ficha'])>
                        @include('disenos.partials.etapa-4-ficha-tecnica')
                    </section>
                </div>

                {{-- Barra de acciones --}}
                <div class="flex items-center justify-between gap-3 border-t border-[#e2e2df] bg-[#f5f5f3] px-6 py-3">
                    <span id="save-state" class="text-xs text-[#a0a09c]">
                        {{ $isFactory ? ($diseno->exists ? 'Guardado' : 'Sin guardar') : 'Solo lectura' }}
                    </span>
                    @if ($isFactory)
                        <div class="flex gap-2">
                            @if ($diseno->exists)
                                <button type="submit" form="delete-form"
                                        class="rounded-md border border-[#e2e2df] bg-white px-4 py-2 text-sm text-[#dc2626] transition hover:bg-red-50">Eliminar</button>
                            @endif
                            <button type="submit"
                                    class="rounded-md bg-[#1d4ed8] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#1e40af]">Guardar cambios</button>
                        </div>
                    @endif
                </div>
            </form>

            {{-- Formularios independientes (los campos se enlazan con form="...") --}}
            @if ($diseno->exists)
                @if ($isFactory)
                    <form id="delete-form" method="POST" action="{{ route('disenos.destroy', $diseno) }}"
                          onsubmit="return confirm('¿Eliminar este diseño y todos sus datos?')">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

                <form id="obs-form" method="POST" action="{{ route('disenos.observaciones.store', $diseno) }}">@csrf</form>

                @if ($isEu)
                    <form id="review-form" method="POST" action="{{ route('disenos.revisiones.store', $diseno) }}">@csrf</form>

                    <form id="barcode-store-form" method="POST" action="{{ route('disenos.codigos.store', $diseno) }}">
                        @csrf
                        <input type="hidden" name="codigo_color" class="js-bc-color">
                        <input type="hidden" name="talla" class="js-bc-size">
                    </form>

                    <form id="barcode-delete-form" method="POST" action="{{ route('disenos.codigos.destroy', $diseno) }}"
                          onsubmit="return confirm('¿Quitar el código de barras asignado?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="codigo_color" class="js-bc-color">
                        <input type="hidden" name="talla" class="js-bc-size">
                    </form>
                @endif

                @if ($isFactory)
                    <form id="factory-barcode-store-form" method="POST" action="{{ route('disenos.codigos-fabrica.store', $diseno) }}">
                        @csrf
                        <input type="hidden" name="codigo_color" class="js-fbc-color">
                        <input type="hidden" name="talla" class="js-fbc-size">
                    </form>

                    <form id="factory-barcode-delete-form" method="POST" action="{{ route('disenos.codigos-fabrica.destroy', $diseno) }}"
                          onsubmit="return confirm('¿Quitar el código de barras de Fábrica?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="codigo_color" class="js-fbc-color">
                        <input type="hidden" name="talla" class="js-fbc-size">
                    </form>
                @endif
            @endif
        </main>
    </div>

    {{-- Visor de fotos (álbum) --}}
    <div id="photo-lightbox" role="dialog" aria-modal="true" aria-label="Visor de fotos"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/85 p-4">
        <button type="button" data-lb-close aria-label="Cerrar"
                class="absolute right-4 top-4 flex size-9 items-center justify-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20">×</button>
        <button type="button" data-lb-prev aria-label="Anterior"
                class="absolute left-3 flex size-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20 sm:left-6">‹</button>
        <figure class="flex max-h-full max-w-5xl flex-col items-center gap-2">
            <img data-lb-img src="" alt="" class="max-h-[82vh] max-w-full rounded-lg bg-white object-contain shadow-2xl">
            <figcaption class="text-xs text-white/80">
                <span data-lb-caption></span>
                <span data-lb-counter class="ml-1"></span>
            </figcaption>
        </figure>
        <button type="button" data-lb-next aria-label="Siguiente"
                class="absolute right-3 flex size-10 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20 sm:right-6">›</button>
    </div>

    <script>
        (() => {
            const lb = document.getElementById('photo-lightbox');
            if (!lb) return;

            const img = lb.querySelector('[data-lb-img]');
            const caption = lb.querySelector('[data-lb-caption]');
            const counter = lb.querySelector('[data-lb-counter]');
            let items = [];
            let index = 0;

            const render = () => {
                if (!items.length) return;
                index = (index + items.length) % items.length;
                img.src = items[index];
                if (counter) counter.textContent = `${index + 1} / ${items.length}`;
                const many = items.length > 1;
                lb.querySelector('[data-lb-prev]').classList.toggle('hidden', !many);
                lb.querySelector('[data-lb-next]').classList.toggle('hidden', !many);
            };

            const open = (list, start) => {
                items = list.filter(Boolean);
                if (!items.length) return;
                index = Math.max(0, start);
                if (caption) caption.textContent = '';
                render();
                lb.classList.remove('hidden');
                lb.classList.add('flex');
                document.body.style.overflow = 'hidden';
            };

            const close = () => {
                lb.classList.add('hidden');
                lb.classList.remove('flex');
                document.body.style.overflow = '';
                img.removeAttribute('src');
            };

            document.addEventListener('click', (e) => {
                const trigger = e.target.closest('[data-photo-src]');
                if (trigger) {
                    e.preventDefault();
                    let list = [];
                    const album = trigger.dataset.album;
                    if (album) {
                        try { list = JSON.parse(album); } catch { list = []; }
                    }
                    if (!list.length) {
                        const wrap = trigger.closest('[data-photo-album]');
                        list = wrap ? [...wrap.querySelectorAll('[data-photo-src]')].map(x => x.dataset.photoSrc) : [];
                    }
                    if (!list.length) list = [trigger.dataset.photoSrc];
                    open(list, list.indexOf(trigger.dataset.photoSrc));
                    return;
                }

                if (e.target.closest('[data-lb-close]')) return close();
                if (e.target.closest('[data-lb-prev]')) { index--; return render(); }
                if (e.target.closest('[data-lb-next]')) { index++; return render(); }
                if (e.target === lb) close();
            });

            document.addEventListener('keydown', (e) => {
                if (e.target instanceof Element && e.target.matches('[data-photo-src]') && (e.key === 'Enter' || e.key === ' ')) {
                    e.preventDefault();
                    e.target.click();
                    return;
                }
                if (lb.classList.contains('hidden')) return;
                if (e.key === 'Escape') close();
                if (e.key === 'ArrowLeft') { index--; render(); }
                if (e.key === 'ArrowRight') { index++; render(); }
            });
        })();
    </script>

    <script>
        (() => {
            const form = document.getElementById('design-form');
            const state = document.getElementById('save-state');
            const editable = {{ $isFactory ? 'true' : 'false' }};

            const onEdit = (e) => {
                if (!editable) return;
                if (e.target.form && e.target.form !== form) return;
                if (e.target.closest('[data-nodirty]')) return;
                state.textContent = 'Cambios sin guardar';
            };
            form.addEventListener('input', onEdit);
            form.addEventListener('change', onEdit);
            const markDirty = () => { if (editable) state.textContent = 'Cambios sin guardar'; };

            // Pestañas (principal y sub-pestañas de prototipo)
            const initTabs = (group, hiddenInput) => {
                const buttons = document.querySelectorAll(`[data-tabs="${group}"] [data-tab]`);
                const panels = document.querySelectorAll(`[data-panel-group="${group}"]`);
                const show = (key) => {
                    buttons.forEach(b => b.setAttribute('aria-selected', String(b.dataset.tab === key)));
                    panels.forEach(p => p.classList.toggle('hidden', p.dataset.panel !== key));
                    if (hiddenInput) document.getElementById(hiddenInput).value = key;
                    if (group === 'main') document.querySelectorAll('[data-follow-tab]').forEach(i => i.value = key);
                };
                buttons.forEach(b => b.addEventListener('click', () => show(b.dataset.tab)));
            };
            initTabs('main', '_tab');
            initTabs('proto', '_sub');

            // Filas dinámicas
            document.querySelectorAll('[data-repeater]').forEach(rep => {
                const body = rep.querySelector('[data-rows]');
                const tpl = rep.querySelector('template[data-template]');
                rep.querySelector('[data-add]').addEventListener('click', () => {
                    const i = Number(rep.dataset.next);
                    rep.dataset.next = i + 1;
                    body.insertAdjacentHTML('beforeend', tpl.innerHTML.replaceAll('__I__', i));
                    body.lastElementChild.querySelector('input')?.focus();
                    markDirty();
                });
                rep.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-remove]');
                    if (btn) { btn.closest('tr').remove(); markDirty(); }
                });
            });

            // Etiqueta de archivos elegidos
            document.querySelectorAll('[data-file-input]').forEach(input => {
                input.addEventListener('change', () => {
                    const label = input.closest('label')?.querySelector('[data-file-label]');
                    if (!label) return;
                    const n = input.files.length;
                    label.textContent = n === 0 ? 'Sin archivos seleccionados' : (n === 1 ? input.files[0].name : `${n} archivos`);
                });
            });

            // Arrastrar y soltar fotos en las zonas de carga
            const imageFiles = (files) => [...files].filter(file =>
                file.type.startsWith('image/') || /\.(jpe?g|png|gif|webp|bmp|avif|heic|heif)$/i.test(file.name)
            );

            document.querySelectorAll('[data-dropzone]').forEach(zone => {
                const input = zone.querySelector('input[type="file"]')
                    || (zone.dataset.dropzoneFor ? document.getElementById(zone.dataset.dropzoneFor) : null);
                if (!input) return;

                const active = ['ring-2', 'ring-[#1d4ed8]', 'border-[#1d4ed8]', 'bg-[#eff3ff]'];
                const highlight = (on) => active.forEach(cls => zone.classList.toggle(cls, on));
                const canUpload = () => !input.disabled && !input.matches(':disabled');
                const draggingFiles = (e) => !!e.dataTransfer && Array.from(e.dataTransfer.types || []).includes('Files');

                zone.addEventListener('dragenter', (e) => {
                    if (!draggingFiles(e)) return;
                    e.preventDefault();
                    highlight(canUpload());
                });

                zone.addEventListener('dragover', (e) => {
                    if (!draggingFiles(e)) return;
                    e.preventDefault();
                    e.dataTransfer.dropEffect = canUpload() ? 'copy' : 'none';
                    highlight(canUpload());
                });

                zone.addEventListener('dragleave', (e) => {
                    if (!zone.contains(e.relatedTarget)) highlight(false);
                });

                zone.addEventListener('drop', (e) => {
                    if (!draggingFiles(e)) return;
                    e.preventDefault();
                    highlight(false);
                    if (!canUpload() || typeof DataTransfer === 'undefined') return;

                    const files = imageFiles(e.dataTransfer.files);
                    if (!files.length) return;

                    const dt = new DataTransfer();
                    (input.multiple ? files : files.slice(0, 1)).forEach(file => dt.items.add(file));
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });

            // Vista previa de la miniatura
            const thumbInput = document.querySelector('[data-thumb-input]');
            thumbInput?.addEventListener('change', () => {
                const file = thumbInput.files[0];
                if (!file) return;
                const img = document.querySelector('[data-thumb-preview]');
                img.src = URL.createObjectURL(file);
                img.classList.remove('hidden');
                document.querySelector('[data-thumb-placeholder]')?.classList.add('hidden');
            });

            // Mostrar / ocultar cajas (agregar observación, nueva revisión EU)
            document.querySelectorAll('[data-toggle]').forEach(btn => {
                btn.addEventListener('click', () => document.querySelector(btn.dataset.toggle)?.classList.toggle('hidden'));
            });

            // Historial: expandir y filtrar
            document.querySelectorAll('[data-expand]').forEach(btn => {
                btn.addEventListener('click', () => {
                    btn.querySelector('[data-comment]').classList.toggle('line-clamp-1');
                    btn.querySelector('[data-arrow]').classList.toggle('rotate-90');
                });
            });
            document.querySelectorAll('[data-history]').forEach(hist => {
                const who = hist.querySelector('[data-filter-who]');
                const from = hist.querySelector('[data-filter-from]');
                const to = hist.querySelector('[data-filter-to]');
                if (!who || !from || !to) return;

                const apply = () => hist.querySelectorAll('[data-history-list] > li').forEach(li => {
                    const ok = (!who.value || li.dataset.author === who.value)
                        && (!from.value || li.dataset.date >= from.value)
                        && (!to.value || li.dataset.date <= to.value);
                    li.classList.toggle('hidden', !ok);
                });
                [who, from, to].forEach(el => el.addEventListener('input', apply));
            });

            // Observaciones de la ficha: usar una del historial y reflejarla en impresión
            const techNoteEditor = document.querySelector('textarea[name="ficha[observaciones]"]');
            const printTechNote = document.querySelector('[data-print-tech-note]');
            const refreshPrintTechNote = () => {
                if (!printTechNote) return;
                printTechNote.textContent = (techNoteEditor?.value || '').trim() || 'Sin observaciones.';
            };
            const clearTechNoteSelection = () => {
                document.querySelectorAll('[data-tech-note-select]').forEach(b => {
                    b.setAttribute('aria-pressed', 'false');
                    b.textContent = 'Seleccionar';
                });
                document.querySelectorAll('[data-tech-note-item]').forEach(li => {
                    li.classList.remove('ring-2', 'ring-[#1d4ed8]');
                });
            };
            techNoteEditor?.addEventListener('input', (e) => {
                refreshPrintTechNote();
                if (e.isTrusted) clearTechNoteSelection();
            });
            document.querySelectorAll('[data-tech-note-select]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const text = btn.closest('[data-tech-note-item]')?.querySelector('[data-comment]')?.textContent || '';

                    if (techNoteEditor) {
                        techNoteEditor.value = text;
                        techNoteEditor.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    refreshPrintTechNote();

                    clearTechNoteSelection();
                    btn.setAttribute('aria-pressed', 'true');
                    btn.textContent = 'Seleccionada';
                    btn.closest('[data-tech-note-item]')?.classList.add('ring-2', 'ring-[#1d4ed8]');

                    techNoteEditor?.focus();
                });
            });

            // Códigos por talla y color + código de barras activo
            if (document.getElementById('code-panel')) {
                const $ = (id) => document.getElementById(id);
                const panel = $('code-panel');
                const previewUrl = panel?.dataset.previewUrl || '';
                const codeInput = $('ac-barcode-input');
                const preview = (code) => {
                    const clean = (code || '').replace(/[^\x20-\x7E]/g, '');
                    const box = $('ac-barcode-box');
                    const none = $('ac-none');
                    if (!clean) {
                        box?.classList.add('hidden');
                        none?.classList.remove('hidden');
                        return;
                    }
                    box?.classList.remove('hidden');
                    none?.classList.add('hidden');
                    if ($('ac-barcode')) $('ac-barcode').src = previewUrl + '?codigo=' + encodeURIComponent(clean);
                    if ($('ac-barcode-text')) $('ac-barcode-text').textContent = clean;
                };
                const select = (btn) => {
                    document.querySelectorAll('[data-code]').forEach(b => b.setAttribute('aria-pressed', String(b === btn)));
                    $('ac-dot').style.background = btn.dataset.hex;
                    $('ac-color').textContent = btn.dataset.color;
                    $('ac-size').textContent = 'Talla ' + btn.dataset.sizeLabel;
                    $('ac-code').textContent = btn.dataset.code;

                    const has = !!btn.dataset.barcode;
                    const code = btn.dataset.barcodeCode || btn.dataset.code;
                    if (codeInput) {
                        codeInput.value = code;
                        preview(code);
                    } else {
                        const box = $('ac-barcode-box');
                        const none = $('ac-none');
                        box?.classList.toggle('hidden', !has);
                        none?.classList.toggle('hidden', has);
                        if (!has && none) none.textContent = 'Sin código de barras asignado.';
                        if (has) {
                            if ($('ac-barcode')) $('ac-barcode').src = btn.dataset.barcode;
                            if ($('ac-barcode-text')) $('ac-barcode-text').textContent = btn.dataset.barcodeCode;
                        }
                    }

                    document.querySelectorAll('.js-bc-color').forEach(i => i.value = btn.dataset.colorCode);
                    document.querySelectorAll('.js-bc-size').forEach(i => i.value = btn.dataset.sizeKey);
                    $('ac-assign')?.classList.remove('hidden');
                    $('ac-remove')?.classList.toggle('hidden', !has);
                };

                let previewTimer = null;
                codeInput?.addEventListener('input', () => {
                    clearTimeout(previewTimer);
                    previewTimer = setTimeout(() => preview(codeInput.value.trim()), 250);
                });

                const buttons = [...document.querySelectorAll('[data-code]')];
                buttons.forEach(b => b.addEventListener('click', () => select(b)));

                const wanted = new URLSearchParams(location.search).get('active');
                const first = buttons.find(b => b.dataset.key === wanted)
                    || buttons.find(b => b.dataset.sizeKey === 'l')
                    || buttons[0];
                if (first) select(first);
            }

            // Códigos de barras de Fábrica (independientes de EU)
            if (document.getElementById('fc-code-panel')) {
                const $ = (id) => document.getElementById(id);
                const panel = $('fc-code-panel');
                const previewUrl = panel?.dataset.previewUrl || '';
                const codeInput = $('fc-barcode-input');
                const preview = (code) => {
                    const clean = (code || '').replace(/[^\x20-\x7E]/g, '');
                    const box = $('fc-barcode-box');
                    const none = $('fc-none');
                    if (!clean) {
                        box?.classList.add('hidden');
                        none?.classList.remove('hidden');
                        return;
                    }
                    box?.classList.remove('hidden');
                    none?.classList.add('hidden');
                    if ($('fc-barcode')) $('fc-barcode').src = previewUrl + '?codigo=' + encodeURIComponent(clean);
                    if ($('fc-barcode-text')) $('fc-barcode-text').textContent = clean;
                };
                const select = (btn) => {
                    document.querySelectorAll('[data-fcode]').forEach(b => b.setAttribute('aria-pressed', String(b === btn)));
                    $('fc-dot').style.background = btn.dataset.fhex;
                    $('fc-color').textContent = btn.dataset.fcolor;
                    $('fc-size').textContent = 'Talla ' + btn.dataset.fsizeLabel;
                    $('fc-code').textContent = btn.dataset.fcode;

                    const has = !!btn.dataset.fbarcode;
                    const code = btn.dataset.fbarcodeCode || btn.dataset.fcode;
                    if (codeInput) {
                        codeInput.value = code;
                        preview(code);
                    }

                    document.querySelectorAll('.js-fbc-color').forEach(i => i.value = btn.dataset.fcolorCode);
                    document.querySelectorAll('.js-fbc-size').forEach(i => i.value = btn.dataset.fsizeKey);
                    $('fc-assign')?.classList.remove('hidden');
                    $('fc-remove')?.classList.toggle('hidden', !has);
                };

                let previewTimer = null;
                codeInput?.addEventListener('input', () => {
                    clearTimeout(previewTimer);
                    previewTimer = setTimeout(() => preview(codeInput.value.trim()), 250);
                });

                const buttons = [...document.querySelectorAll('[data-fcode]')];
                buttons.forEach(b => b.addEventListener('click', () => select(b)));

                const wanted = new URLSearchParams(location.search).get('active');
                const first = buttons.find(b => b.dataset.fkey === wanted)
                    || buttons.find(b => b.dataset.fsizeKey === 'l')
                    || buttons[0];
                if (first) select(first);
            }
        })();
    </script>
</body>
</html>
