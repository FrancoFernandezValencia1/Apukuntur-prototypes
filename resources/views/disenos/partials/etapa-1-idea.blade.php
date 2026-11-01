<fieldset @disabled($readonly) class="m-0 min-w-0 border-0 p-0">
    <div class="flex flex-col gap-6">
        <div>
            <h3 class="text-base font-semibold leading-6">Etapa 1 · Idea del diseño</h3>
            <p class="text-sm text-[#6b6b67]">Inspiración, concepto y datos iniciales.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-field name="idea.tipo_prenda" label="Tipo de prenda" :model="$diseno" />
            <x-field name="idea.temporada" label="Colección / temporada" :model="$diseno" />
            <x-field name="idea.paleta" label="Color / paleta" :model="$diseno" />
            <x-field name="idea.tipo_tejido" label="Tejido / punto" :model="$diseno" />
        </div>

        <x-field name="idea.descripcion" label="Descripción de la idea" as="textarea" rows="4" :model="$diseno" />

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-field name="idea.codigo_hilado" label="Código de hilado" :model="$diseno" />
            <x-field name="idea.titulo_hilado" label="Título de hilado" :model="$diseno" />
            <x-field name="idea.composicion" label="Composición" :model="$diseno" />
            <x-field name="idea.nombre_modelo" label="Nombre" :model="$diseno" />
        </div>

        <x-repeater name="colores_idea" title="Colores, Tejidos y Composiciones"
                    :rows="$diseno->coloresIdea->toArray()"
                    :columns="[
                        ['key' => 'color', 'label' => 'Color', 'w' => 'min-w-40'],
                        ['key' => 'tipo_tejido', 'label' => 'Tipo de tejido', 'w' => 'min-w-40'],
                        ['key' => 'composicion', 'label' => 'Composición', 'w' => 'min-w-48'],
                    ]" />

        <x-archivos seccion="idea" label="Imágenes de la idea" :diseno="$diseno" />
    </div>
</fieldset>
