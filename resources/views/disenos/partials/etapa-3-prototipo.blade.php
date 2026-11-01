@php
    $sub = old('_sub', request('sub', 'remallado'));
    $subTabs = ['remallado' => 'Remallado', 'tejido' => 'Tejido', 'acabado' => 'Acabado', 'medidas' => 'Medidas'];
@endphp

<fieldset @disabled($readonly) class="m-0 min-w-0 border-0 p-0">
    <div class="flex flex-col gap-6">
        <div>
            <h3 class="text-base font-semibold leading-6">Etapa 3 · Prototipo físico</h3>
            <p class="text-sm text-[#6b6b67]">Resultado de muestra, correcciones y aprobación.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-field name="prototipo.peso_real" label="Peso real (g)" type="number" step="any" min="0" :model="$diseno" />
            <x-field name="prototipo.fecha_prototipo" label="Fecha del prototipo" type="date" :model="$diseno" />
        </div>

        <x-field name="notas.general" label="Observaciones generales" as="textarea" :value="$diseno->nota('general')" />
        <x-field name="notas.correcciones" label="Correcciones finales" as="textarea" :value="$diseno->nota('correcciones')" />

        <x-archivos seccion="final" label="Fotos de la prenda final" :diseno="$diseno" />
    </div>
</fieldset>

{{-- Los botones de sub-pestañas quedan fuera del fieldset para que EU también pueda navegar --}}
<div class="mt-6 flex flex-col gap-4 rounded-xl border border-[#e2e2df] bg-[#f5f5f3] p-4">
    <div class="flex gap-1 overflow-x-auto" role="tablist" data-tabs="proto">
        @foreach ($subTabs as $key => $label)
            <button type="button" role="tab" data-tab="{{ $key }}" aria-selected="{{ $sub === $key ? 'true' : 'false' }}"
                    class="whitespace-nowrap rounded-md border border-transparent px-4 py-2 text-sm font-medium text-[#6b6b67] transition hover:bg-white/60 aria-selected:bg-white aria-selected:text-[#1a1a18] aria-selected:shadow-sm">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <fieldset @disabled($readonly) class="m-0 min-w-0 border-0 p-0">
        @foreach ($subTabs as $key => $label)
            <div data-panel-group="proto" data-panel="{{ $key }}" @class(['hidden' => $sub !== $key])>
                @include("disenos.partials.prototipo.{$key}")
            </div>
        @endforeach
    </fieldset>
</div>
