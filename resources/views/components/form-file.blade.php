@props([
    'label' => '',
    'name' => '',
    'id' => null,
    'hint' => null,
    'required' => false,
    'accept' => '*',
])

@php $id = $id ?? $name; @endphp

<div class="space-y-1.5" x-data="{ fileName: '' }">
    @if ($label)
        <label class="block text-[12px] font-semibold text-slate-500">
            {{ $label }}@if ($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative group">
        {{-- Hidden Input --}}
        <input type="file" id="{{ $id }}" name="{{ $name }}" accept="{{ $accept }}"
            {{ $required ? 'required' : '' }} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
            @change="fileName = $event.target.files[0].name" {{ $attributes }}>

        {{-- Styled Mask --}}
        <div
            class="flex items-center gap-3 w-full bg-slate-50 border border-slate-100 text-slate-900 text-[13px] rounded-xl px-4 py-2.5
                    group-hover:bg-slate-100/50 group-focus-within:bg-white group-focus-within:border-brand-primary group-focus-within:ring-4 group-focus-within:ring-brand-primary/5
                    transition-all outline-none">

            <div class="shrink-0 w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>

            <div class="flex-1 truncate">
                <span x-show="!fileName" class="text-slate-400">Pilih berkas atau tarik ke sini...</span>
                <span x-show="fileName" x-text="fileName" class="font-bold text-emerald-700"></span>
            </div>

            <span
                class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-hover:text-emerald-600 transition-colors">Telusuri</span>
        </div>
    </div>

    @if ($hint)
        <p class="text-[10px] text-slate-400">{{ $hint }}</p>
    @endif

    @if ($name)
        <x-input-error :messages="$errors->get($name)" />
    @endif
</div>
