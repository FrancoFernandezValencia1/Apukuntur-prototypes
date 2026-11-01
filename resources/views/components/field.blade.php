@props([
    'name',
    'label',
    'model' => null,
    'value' => null,
    'as' => 'input',        // input | textarea | select | checkbox | switch
    'type' => 'text',
    'options' => [],
    'span' => '',
])
@php
    $segments  = explode('.', $name);
    $inputName = array_shift($segments) . collect($segments)->map(fn ($s) => "[{$s}]")->implode('');
    $id        = 'f_' . str_replace('.', '_', $name);

    $current = old($name, $value ?? ($model ? data_get($model, $name) : null));
    if ($current instanceof \DateTimeInterface) {
        $current = $current->format('Y-m-d');
    }
    if (is_string($current) && preg_match('/^-?\d+\.\d+$/', $current)) {
        $current = rtrim(rtrim($current, '0'), '.');
    }

    $box = 'w-full rounded-md border bg-white px-3 py-2 text-sm text-[#1a1a18] outline-none transition focus:border-[#1d4ed8] focus:ring-2 focus:ring-[#1d4ed8]/20 disabled:cursor-not-allowed disabled:bg-[#fafaf9] disabled:text-[#6b6b67] '
         . ($errors->has($name) ? 'border-[#dc2626]' : 'border-[#e2e2df]');
@endphp

@if ($as === 'switch')
    <label class="inline-flex cursor-pointer items-center gap-3 self-end py-2 text-sm text-[#1a1a18] {{ $span }}">
        <input type="hidden" name="{{ $inputName }}" value="0">
        <input type="checkbox" name="{{ $inputName }}" value="1" @checked((bool) $current) {{ $attributes }} class="peer sr-only">
        <span class="relative h-6 w-11 shrink-0 rounded-full bg-[#d4d4d0] transition peer-checked:bg-[#1d4ed8] peer-focus-visible:ring-2 peer-focus-visible:ring-[#1d4ed8]/40 peer-disabled:opacity-60 after:absolute after:left-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
        {{ $label }}
    </label>
@elseif ($as === 'checkbox')
    <label class="inline-flex items-center gap-2 text-sm text-[#1a1a18] {{ $span }}">
        <input type="hidden" name="{{ $inputName }}" value="0">
        <input type="checkbox" name="{{ $inputName }}" value="1" @checked((bool) $current) {{ $attributes }}
               class="size-4 rounded border-[#e2e2df] text-[#1d4ed8] focus:ring-[#1d4ed8]/30">
        {{ $label }}
    </label>
@else
    <div class="flex flex-col gap-1 {{ $span }}">
        <label for="{{ $id }}" class="text-xs font-medium uppercase tracking-[0.3px] text-[#6b6b67]">{{ $label }}</label>

        @if ($as === 'textarea')
            <textarea id="{{ $id }}" name="{{ $inputName }}"
                      {{ $attributes->merge(['rows' => 3, 'class' => $box . ' min-h-24 resize-y']) }}>{{ $current }}</textarea>
        @elseif ($as === 'select')
            <select id="{{ $id }}" name="{{ $inputName }}" {{ $attributes->merge(['class' => $box]) }}>
                @foreach ($options as $optValue => $optLabel)
                    <option value="{{ $optValue }}" @selected((string) $current === (string) $optValue)>{{ $optLabel }}</option>
                @endforeach
            </select>
        @else
            <input id="{{ $id }}" type="{{ $type }}" name="{{ $inputName }}" value="{{ $current }}"
                   {{ $attributes->merge(['class' => $box]) }}>
        @endif

        @error($name)
            <p class="text-xs text-[#dc2626]">{{ $message }}</p>
        @enderror
    </div>
@endif
