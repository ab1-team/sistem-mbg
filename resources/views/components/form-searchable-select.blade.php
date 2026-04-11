@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'selected' => null,
    'id' => null,
    'placeholder' => 'Pilih...',
    'required' => false,
    'onSelected' => null,
])

@php
    $id = $id ?? $name;
    $optionsJson = json_encode($options);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }} data-options="{{ $optionsJson }}" x-data="{
    open: false,
    search: '',
    value: @js($selected),
    options: [],
    position: { top: 0, left: 0, width: 0 },

    init() {
        try {
            this.options = JSON.parse(this.$el.dataset.options || '[]');
        } catch (e) {
            this.options = [];
        }
        this.$watch('$el.dataset.options', value => {
            try { this.options = JSON.parse(value || '[]'); } catch (e) {}
        });
    },

    get filteredOptions() {
        if (!this.search || this.search === '') return this.options;
        let s = this.search.toLowerCase();
        return this.options.filter(o => (o.label || '').toLowerCase().includes(s));
    },

    get selectedLabel() {
        let option = this.options.find(opt => opt.value == this.value);
        return option ? option.label : '';
    },

    updatePosition() {
        if (!this.$refs.trigger) return;
        let rect = this.$refs.trigger.getBoundingClientRect();
        this.position.top = rect.bottom + window.scrollY;
        this.position.left = rect.left + window.scrollX;
        this.position.width = rect.width;
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.updatePosition();
            setTimeout(() => { if (this.$refs.searchInput) this.$refs.searchInput.focus(); }, 50);
        }
    },

    select(opt) {
        this.value = opt.value;
        this.search = '';
        this.open = false;

        // Execute Callback (Jalur Langsung)
        @if($onSelected)
            (function(opt) {
                {!! $onSelected !!}
            })(opt);
        @endif

        this.$el.dispatchEvent(new CustomEvent('selected', {
            detail: opt,
            bubbles: true,
            composed: true
        }));

        this.$dispatch('input', opt.value);
    }
}"
    x-modelable="value" @click.away="open = false" @scroll.window="if(open) updatePosition()"
    @resize.window="if(open) updatePosition()" class="relative">

    @if ($label)
        <label for="{{ $id }}" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
            {{ $label }}@if ($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative" x-ref="trigger">
        <input type="hidden" name="{{ $name }}" x-model="value" {{ $required ? 'required' : '' }}>

        <div class="relative">
            <input type="text" x-model="search" x-ref="searchInput" @click="if(!open) toggle()"
                @focus="if(!open) toggle()" @keydown.escape="open = false"
                @keydown.enter.prevent="if(filteredOptions.length > 0) select(filteredOptions[0])"
                :placeholder="value ? selectedLabel : '{{ $placeholder }}'"
                class="block w-full bg-slate-50 border border-slate-200 text-slate-900 text-[13px] rounded-xl px-4 py-2.5 
                          focus:bg-white focus:border-green-900 focus:ring-4 focus:ring-green-900/5 
                          transition-all outline-none placeholder:text-slate-900 placeholder:font-bold"
                :class="open ? 'ring-4 ring-green-900/5 border-green-900 bg-white' : ''">

            <div @click="toggle()"
                class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 cursor-pointer text-slate-400 hover:text-green-600 transition-colors">
                <svg x-show="!open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
                <svg x-show="open" class="w-4 h-4 animate-in fade-in zoom-in duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="open"
            class="searchable-select-menu fixed z-9999 mt-2 bg-white border border-slate-200 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] overflow-hidden"
            :style="`top: ${position.top}px; left: ${position.left}px; width: ${position.width}px;`"
            style="display: none;">
            <div class="max-h-60 overflow-y-auto p-1.5 custom-scrollbar">
                <template x-for="option in filteredOptions" :key="option.value">
                    <button type="button" @click="select(option)"
                        class="flex items-center w-full px-4 py-2.5 text-left text-[13px] rounded-xl transition-all hover:bg-green-50 group"
                        :class="value == option.value ? 'bg-green-50 text-green-700 font-extrabold' :
                            'text-slate-600 hover:text-slate-900'">
                        <span x-text="option.label" class="flex-1"></span>
                        <template x-if="value == option.value">
                            <svg class="w-4 h-4 text-green-700 font-bold" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </button>
                </template>

                <div x-show="filteredOptions.length === 0" class="p-8 text-center bg-slate-50/50">
                    <p class="text-[12px] text-slate-400 font-bold italic">Bahan tidak ditemukan...</p>
                </div>
            </div>
        </div>
    </template>
</div>
