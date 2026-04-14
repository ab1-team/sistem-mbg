@props([
    'label'       => '',
    'name'        => '',
    'id'          => null,
    'hint'        => null,
    'required'    => false,
    'prefix'      => 'Rp',
    'placeholder' => '0',
])

@php
    $id        = $id ?? $name;
    $wireModel = $attributes->wire('model')->value();
@endphp

<div
    class="space-y-1.5"
    x-data="{
        @if($wireModel)
        value: @entangle($wireModel),
        @else
        value: null,
        @endif
        fmt(v) {
            if (v === null || v === undefined || v === '') return '';
            let parsed = parseFloat(v);
            if (isNaN(parsed)) return '';
            return Math.floor(parsed).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    }"
    x-init="
        $watch('value', val => {
            if (document.activeElement !== $refs.inp) {
                $refs.inp.value = (val !== null && val !== undefined && val !== '') ? fmt(val) : '';
            }
        });
        $nextTick(() => {
            if (value !== null && value !== undefined && value !== '') {
                $refs.inp.value = fmt(value);
            }
        });
    "
>
    @if($label)
        <label for="{{ $id }}_display" class="block text-[12px] font-medium text-slate-500">
            {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
        </label>
    @endif

    <div class="relative group">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span class="text-[13px] font-bold text-slate-400 group-focus-within:text-green-900 transition-colors uppercase tracking-tight">{{ $prefix }}</span>
            </div>
        @endif

        <input
            type="text"
            id="{{ $id }}_display"
            x-ref="inp"
            placeholder="{{ $placeholder }}"
            @input="
                let el = $event.target;
                let cur = el.selectionStart;
                let old = el.value.length;
                let raw = el.value.replace(/\D/g, '');
                let formatted = fmt(raw);
                el.value = formatted;
                let pos = Math.max(0, cur + (formatted.length - old));
                el.setSelectionRange(pos, pos);
                
                let parsed = raw ? parseInt(raw) : null;
                if (value !== parsed) value = parsed;
            "
            {{ $required ? 'required' : '' }}
            {{ $attributes->whereDoesntStartWith('wire:model')->merge([
                'class' => 'block w-full bg-slate-50 border border-slate-100 text-slate-900 text-[13px] rounded-xl ' .
                           ($prefix ? 'pl-10 pr-4' : 'px-4') . ' py-2.5
                            placeholder:text-slate-300 focus:bg-white focus:border-green-900 focus:ring-2 focus:ring-green-900/10
                            transition-all outline-none font-bold'
            ]) }}
        >
    </div>

    @if($hint)
        <p class="text-[10px] text-slate-400">{{ $hint }}</p>
    @endif

    @if($name)
        <x-input-error :messages="$errors->get($name)" />
    @endif
</div>
