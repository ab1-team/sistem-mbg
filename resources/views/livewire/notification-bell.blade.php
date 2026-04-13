<div class="relative" x-data="{ open: false }" wire:poll.15s>
    {{-- Bell Button --}}
    <button @click="open = !open" 
        class="relative p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all duration-200 group">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if($this->unreadCount > 0)
            <span class="absolute top-2 right-2 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500 border-2 border-white text-[9px] font-bold text-white items-center justify-center">
                    {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
                </span>
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-3 w-80 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden"
        x-cloak>
        
        <div class="p-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-800">Notifikasi</h3>
            @if($this->unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[11px] font-semibold text-green-600 hover:text-green-700 transition-colors">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="max-h-[350px] overflow-y-auto overflow-x-hidden">
            @forelse($this->notifications as $notification)
                <div class="p-4 border-b border-slate-50 last:border-0 hover:bg-slate-50 transition-colors {{ $notification->read_at ? 'opacity-60' : 'bg-green-50/30' }}">
                    <div class="flex gap-3">
                        <div class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notification->read_at ? 'bg-slate-300' : 'bg-green-500' }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-slate-800 leading-snug truncate">
                                {{ $notification->data['title'] ?? 'Notifikasi Baru' }}
                            </p>
                            <p class="text-[12px] text-slate-500 mt-1 leading-normal break-words">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-[10px] text-slate-400 font-medium">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="text-[11px] font-bold text-green-600 hover:underline">
                                        Lihat detail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-[13px] text-slate-400 font-medium">Belum ada notifikasi.</p>
                </div>
            @endforelse
        </div>

        @if($this->notifications->count() > 0)
            <a href="#" class="block p-3 text-center text-[12px] font-bold text-slate-500 hover:bg-slate-50 border-t border-slate-50 transition-colors">
                Lihat Semua Notifikasi
            </a>
        @endif
    </div>
</div>
