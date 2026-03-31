@props([
    'label'    => '',
    'name'     => '',
    'id'       => null,
    'hint'     => null,
    'required' => false,
])

@php $id = $id ?? $name; @endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
            {{ $label }}@if($required)<span class="text-red-500 ml-0.5">*</span>@endif
        </label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'block w-full bg-slate-50 border border-slate-200 text-slate-900 text-[13px] rounded-xl px-4 py-2.5
                        focus:bg-white focus:border-green-900 focus:ring-4 focus:ring-green-900/5
                        transition-all outline-none'
        ]) }}
    >
        {{ $slot }}
    </select>

    @if($hint)
        <p class="text-[10px] text-slate-400">{{ $hint }}</p>
    @endif

    @if($name)
        <x-input-error :messages="$errors->get($name)" />
    @endif
</div>
