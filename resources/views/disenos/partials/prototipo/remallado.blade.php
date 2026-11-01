<div class="flex flex-col gap-4">
    <div>
        <h4 class="text-sm font-semibold">Remallado</h4>
        <p class="text-xs text-[#6b6b67]">Unión y ensamble de piezas tejidas.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-field name="remallado.tipo_remallado" label="Tipo de remallado" :model="$diseno" />
        <x-field name="remallado.operario" label="Operario / responsable" :model="$diseno" />
        <x-field name="remallado.fecha_remallado" label="Fecha de remallado" type="date" :model="$diseno" />
    </div>

    <x-field name="notas.remallado" label="Observaciones" as="textarea" :value="$diseno->nota('remallado')" />
    <x-archivos seccion="remallado" label="Fotos del remallado" :diseno="$diseno" />
</div>
