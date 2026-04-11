<nav x-data="{ open: false }"
    class="sticky top-0 z-40 bg-white border-b border-slate-50 px-10 h-16 flex items-center justify-between transition-all duration-300">

    <!-- Left: Donezo Search Architecture -->
    <div class="flex items-center gap-6 flex-1 max-w-xl">
        <!-- Sidebar Toggle (Minimalist) -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-primary-900 transition-colors">
            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Integrated Search Bar (Donezo v1:1) -->
        <div class="flex-1 relative group">
            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-300 group-focus-within:text-primary-900 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" placeholder="Search task"
                class="w-full bg-transparent border-none text-slate-900 text-[13px] font-medium pl-8 pr-12 py-2 focus:ring-0 placeholder:text-slate-300" />
            <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none">
                <div class="flex items-center gap-1 border border-slate-100 rounded-md px-1.5 py-0.5 bg-slate-50/50">
                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">⌘</span>
                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">F</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Contextual Controls & Integrated Profile -->
    <div class="flex items-center gap-6">
        <!-- Messenger & Notification Icons -->
        <div class="hidden sm:flex items-center gap-4">
            <button class="text-slate-400 hover:text-primary-900 transition-all relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </button>
            <button class="text-slate-400 hover:text-primary-900 transition-all relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
        </div>

        <!-- Integrated User Profile (Pure Donezo Style) -->
        <x-dropdown align="right" width="60">
            <x-slot name="trigger">
                <button class="group flex items-center gap-3 py-1 transition-all active:scale-95">
                    <div
                        class="w-10 h-10 rounded-full border-2 border-slate-100 bg-slate-50 flex items-center justify-center p-0.5 overflow-hidden group-hover:border-primary-900/10 transition-all">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=14532d&background=f0fdf4&bold=true"
                            class="w-full h-full rounded-full" alt="Avatar">
                    </div>
                    <div class="hidden lg:block text-left">
                        <p class="text-[12px] font-black text-slate-900 tracking-tight leading-none uppercase">
                            {{ Auth::user()->name }}</p>
                        <p class="text-[10px] font-bold text-slate-400 truncate lowercase mt-1.5 leading-none">
                            {{ Auth::user()->email }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-primary-900 transition-colors ml-1"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-5 py-4 border-b border-slate-50 mb-1">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">
                        CONNECTED AS</p>
                    <p class="text-[10px] font-black text-slate-900 truncate uppercase">{{ Auth::user()->email }}</p>
                </div>

                <x-dropdown-link :href="route('profile.edit')" class="mx-1.5 rounded-xl flex items-center gap-2.5 py-3 group">
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-primary-900 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[11px] font-bold text-slate-600 transition-colors">Profile Account</span>
                </x-dropdown-link>

                <div class="h-px bg-slate-50 my-1 mx-1.5"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="mx-1.5 rounded-xl flex items-center gap-2.5 py-3 text-rose-500 hover:bg-rose-50 group">
                        <svg class="w-4 h-4 text-rose-300 group-hover:text-rose-500 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="text-[11px] font-bold">Terminate Session</span>
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>
