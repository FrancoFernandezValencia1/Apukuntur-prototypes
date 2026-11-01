<div class="flex flex-col gap-4">
    <div>
        <h4 class="text-sm font-semibold">Acabado</h4>
        <p class="text-xs text-[#6b6b67]">Proceso de terminación y presentación final de la prenda.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-field name="acabado.tipo_acabado" label="Tipo de acabado" :model="$diseno" />
        <x-field name="acabado.temperatura_vapor" label="Temperatura de vaporizado (°C)" type="number" min="0" max="300" :model="$diseno" />
        <x-field name="acabado.minutos_proceso" label="Tiempo de proceso (min)" type="number" min="0" :model="$diseno" />
        <x-field name="acabado.con_vapor" label="Con vaporizado" as="switch" :model="$diseno" />
    </div>

    <x-field name="notas.acabado" label="Observaciones" as="textarea" :value="$diseno->nota('acabado')" />
    <x-archivos seccion="acabado" label="Fotos del acabado" :diseno="$diseno" />
</div>
