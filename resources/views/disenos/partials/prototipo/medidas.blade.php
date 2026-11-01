<div class="flex flex-col gap-4">
    <div>
        <h4 class="text-sm font-semibold">Medidas del prototipo</h4>
        <p class="text-xs text-[#6b6b67]">Medidas tomadas sobre la muestra física (cm).</p>
    </div>

    <x-repeater name="medidas_prototipo" title="Medidas (cm)" add-label="Agregar fila"
                :rows="$diseno->medidasPrototipo->toArray()"
                :columns="[
                    ['key' => 'nombre', 'label' => 'Medida', 'w' => 'min-w-56'],
                    ['key' => 's',  'label' => 'S',  'type' => 'number', 'w' => 'w-20'],
                    ['key' => 'm',  'label' => 'M',  'type' => 'number', 'w' => 'w-20'],
                    ['key' => 'l',  'label' => 'L',  'type' => 'number', 'w' => 'w-20'],
                    ['key' => 'xl', 'label' => 'XL', 'type' => 'number', 'w' => 'w-20'],
                ]" />

    <x-archivos seccion="medidas" label="Fotos de medidas" :diseno="$diseno" />
</div>
