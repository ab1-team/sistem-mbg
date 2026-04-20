@props([
    'label' => '',
    'name' => '',
    'id' => null,
    'type' => 'text',
    'hint' => null,
    'required' => false,
    'prefix' => null,
])

@php $id = $id ?? $name; @endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $id }}" class="block text-[11px] font-semibold text-slate-500 tracking-wider">
            {{ $label }}@if ($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        @if (isset($icon))
            <div
                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-green-900 transition-colors">
                {{ $icon }}
            </div>
        @endif

        @if ($prefix)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <span
                    class="text-[13px] font-bold text-slate-400 group-focus-within:text-green-900 transition-colors">{{ $prefix }}</span>
            </div>
        @endif

        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' =>
                    'block w-full bg-slate-50 border border-slate-100 text-slate-900 text-[13px] font-bold rounded-xl ' .
                    (isset($icon) ? 'pl-10 pr-4' : ($prefix ? 'pl-10 pr-4' : 'px-4')) .
                    ' py-2.5
                                                                            placeholder:text-slate-300 placeholder:font-normal focus:bg-white focus:border-green-900 focus:ring-2 focus:ring-green-900/10
                                                                            transition-all outline-none',
            ]) }}>
    </div>

    @if ($hint)
        <p class="text-[10px] text-slate-400">{{ $hint }}</p>
    @endif

    @if ($name)
        <x-input-error :messages="$errors->get($name)" />
    @endif
</div>
