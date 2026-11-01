<div class="flex flex-col gap-4">
    <div>
        <h4 class="text-sm font-semibold">Tejido</h4>
        <p class="text-xs text-[#6b6b67]">Detalles del proceso de tejido del prototipo.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-field name="tejido.tipo_tejido" label="Tipo de tejido" :model="$diseno" />
        <x-field name="tejido.galga" label="Galga" type="number" step="any" min="0" :model="$diseno" />
        <x-field name="tejido.tension" label="Tensión" type="number" step="any" min="0" :model="$diseno" />
        <x-field name="tejido.peso_por_metro" label="Peso por metro (g)" type="number" step="any" min="0" :model="$diseno" />
    </div>

    <x-field name="notas.tejido" label="Observaciones" as="textarea" :value="$diseno->nota('tejido')" />
    <x-archivos seccion="tejido" label="Fotos del tejido" :diseno="$diseno" />
</div>
