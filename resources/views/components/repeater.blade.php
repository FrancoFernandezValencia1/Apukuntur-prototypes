{{-- Tabla con filas dinámicas. columns: key, label, type (text|number|color), w. El slot va entre la cabecera y la tabla. --}}
@props(['name', 'title', 'columns', 'rows' => [], 'addLabel' => 'Agregar', 'hint' => null])
@php $rows = array_values(old($name, $rows) ?? []); @endphp

<div class="overflow-hidden rounded-xl border border-[#e2e2df] bg-white" data-repeater data-next="{{ count($rows) }}">
    <div class="flex items-center justify-between gap-3 border-b border-[#e2e2df] bg-[#f5f5f3] px-4 py-2.5">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-[0.6px] text-[#6b6b67]">{{ $title }}</p>
            @if ($hint)
                <p class="text-xs text-[#a0a09c]">{{ $hint }}</p>
            @endif
        </div>
        <button type="button" data-add
                class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[#1d4ed8] px-3 py-1.5 text-xs font-medium text-white transition hover:bg-[#1e40af] disabled:cursor-not-allowed disabled:opacity-50">
            <span class="text-sm leading-none">+</span> {{ $addLabel }}
        </button>
    </div>

    {{ $slot }}

    <div class="overflow-x-auto">
        <table class="w-full min-w-max text-xs">
            <thead class="bg-[#f5f5f3]">
                <tr>
                    @foreach ($columns as $col)
                        <th class="border-b border-[#e2e2df] px-3 py-2 text-left font-semibold uppercase tracking-[0.3px] text-[#6b6b67]">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="w-8 border-b border-[#e2e2df]"></th>
                </tr>
            </thead>
            <tbody data-rows>
                @foreach ($rows as $i => $row)
                    @include('disenos.partials.fila-repetidor', ['columns' => $columns, 'name' => $name, 'i' => $i, 'row' => $row])
                @endforeach

                <template data-template>
                    @include('disenos.partials.fila-repetidor', ['columns' => $columns, 'name' => $name, 'i' => '__I__', 'row' => []])
                </template>
            </tbody>
        </table>
    </div>
</div>
