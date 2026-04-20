@props([
    'label'    => '',
    'name'     => '',
    'id'       => null,
    'value'    => '',
    'required' => false,
    'placeholder' => 'Pilih tanggal...',
    'enableTime'  => false,
    'dateFormat'  => 'Y-m-d',
])

@php 
    $id = $id ?? $name; 
    $finalDateFormat = $enableTime ? ($dateFormat === 'Y-m-d' ? 'Y-m-d H:i' : $dateFormat) : $dateFormat;
    $finalAltFormat = $enableTime ? 'j F Y, H:i' : 'j F Y';
@endphp

<div class="space-y-1.5"
     wire:ignore
     x-data="{ 
        value: '{{ $value }}',
        instance: null,
        init() {
            this.instance = flatpickr(this.$refs.input, {
                enableTime: {{ $enableTime ? 'true' : 'false' }},
                dateFormat: '{{ $finalDateFormat }}',
                altInput: true,
                altFormat: '{{ $finalAltFormat }}',
                defaultDate: this.value,
                onChange: (selectedDates, dateStr) => {
                    this.value = dateStr;
                },
                monthSelectorType: 'static',
                static: false
            });
            
            this.$watch('value', val => {
                if (this.instance && val !== this.instance.currentDateStr) {
                    this.instance.setDate(val, false);
                }
            });
        }
     }"
     x-modelable="value">
    @if($label)
        <label for="{{ $id }}" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
            {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
        </label>
    @endif

    <div class="relative group">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-900 transition-colors z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>

        <input
            x-ref="input"
            id="{{ $id }}"
            name="{{ $name }}"
            type="text"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->whereDoesntStartWith('wire:model')->merge([
                'class' => 'block w-full bg-slate-50 border border-slate-100 text-slate-900 text-[13px] font-bold rounded-xl pl-10 pr-10 py-2.5
                            placeholder:text-slate-300 placeholder:font-normal focus:bg-white focus:border-emerald-900 focus:ring-4 focus:ring-emerald-900/10
                            transition-all outline-none cursor-pointer appearance-none'
            ]) }}
        >
        
        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-300 group-hover:text-slate-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="m19 9-7 7-7-7" />
            </svg>
        </div>
    </div>

    @if($name)
        <x-input-error :messages="$errors->get($name)" />
    @endif
</div>
