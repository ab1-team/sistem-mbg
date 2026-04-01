@props(['sort' => null, 'active' => false, 'asc' => true])

<th
    {{ $attributes->merge(['class' => 'px-6 py-4 text-[10px] font-semibold text-slate-400 tracking-widest leading-none']) }}>
    @if ($sort)
        <button wire:click="sortBy('{{ $sort }}')"
            class="flex items-center gap-1.5 hover:text-slate-900 transition-colors group">
            <span>{{ $slot }}</span>
            <div
                class="flex flex-col text-[8px] {{ $active ? 'opacity-100 text-green-700 font-black' : 'opacity-30 group-hover:opacity-60 text-slate-400 font-bold' }}">
                <svg class="w-1.5 h-1.5 {{ $active && $asc ? 'hidden' : '' }}" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z" />
                </svg>
                <svg class="w-1.5 h-1.5 {{ $active && !$asc ? 'hidden' : '' }}" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.41 8.58L12 13.17l4.59-4.59L18 10l-6 6-6-6z" />
                </svg>
            </div>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
