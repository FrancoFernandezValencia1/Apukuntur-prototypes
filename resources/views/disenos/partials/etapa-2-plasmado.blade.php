<fieldset @disabled($readonly) class="m-0 min-w-0 border-0 p-0">
    <div class="flex flex-col gap-6">
        <div>
            <h3 class="text-base font-semibold leading-6">Etapa 2 · Idea plasmada en una prenda</h3>
            <p class="text-sm text-[#6b6b67]">Render, boceto desarrollado y especificaciones preliminares.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-field name="plasmado.material" label="Material / composición" :model="$diseno" />
            <x-field name="plasmado.hilado" label="Hilado" :model="$diseno" />
            <x-field name="plasmado.titulo" label="Título" :model="$diseno" />
            <x-field name="plasmado.galga" label="Galga" :model="$diseno" />
            <x-field name="plasmado.tallas" label="Tallas" placeholder="S, M, L, XL" :model="$diseno" />
            <x-field name="plasmado.peso_estimado" label="Peso estimado (g)" type="number" step="any" min="0" :model="$diseno" />
        </div>

        <x-field name="notas.diseno" label="Observaciones" as="textarea" :value="$diseno->nota('diseno')" />

        <x-archivos seccion="diseno" label="Imágenes del diseño" :diseno="$diseno" />
    </div>
</fieldset>
