<tr data-row>
    @foreach ($columns as $col)
        @php
            $type = $col['type'] ?? 'text';
            $v    = $row[$col['key']] ?? '';
            if (is_string($v) && preg_match('/^-?\d+\.\d+$/', $v)) {
                $v = rtrim(rtrim($v, '0'), '.');
            }
            if ($type === 'color' && blank($v)) {
                $v = '#d1d5db';
            }
        @endphp
        <td class="border-b border-[#e2e2df] px-1 py-0.5 {{ $col['w'] ?? '' }}">
            @if ($type === 'color')
                <input type="color" name="{{ $name }}[{{ $i }}][{{ $col['key'] }}]" value="{{ $v }}"
                       class="mx-2 block size-6 cursor-pointer rounded-full border border-[#e2e2df] bg-transparent p-0 disabled:cursor-not-allowed">
            @else
                <input type="{{ $type }}" @if ($type === 'number') step="any" min="0" @endif
                       name="{{ $name }}[{{ $i }}][{{ $col['key'] }}]" value="{{ $v }}"
                       class="w-full rounded px-2 py-2 text-xs text-[#1a1a18] outline-none focus:bg-[#eff3ff] focus:ring-1 focus:ring-[#1d4ed8]/40 disabled:bg-transparent disabled:text-[#6b6b67] {{ $type === 'number' ? 'text-right' : '' }}">
            @endif
        </td>
    @endforeach
    <td class="border-b border-[#e2e2df] pr-3 text-right">
        <button type="button" data-remove class="text-sm text-[#a0a09c] transition hover:text-[#dc2626] disabled:hidden" aria-label="Quitar fila">×</button>
    </td>
</tr>
