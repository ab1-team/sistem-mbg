<aside 
    x-show="sidebarOpen"
    x-transition:enter="transition-transform duration-500 ease-[cubic-bezier(0.16,1,0.3,1)]"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition-transform duration-300 ease-in"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-100 flex flex-col shadow-sm"
    x-cloak>
    
    <!-- Header: Compact Branding -->
    <div class="px-8 py-10 flex items-center gap-4 group cursor-pointer">
        <div class="w-12 h-12 rounded-2xl bg-primary-900 flex items-center justify-center p-0.5 shadow-lg group-hover:rotate-6 transition-transform">
            <div class="w-full h-full rounded-[0.9rem] bg-white flex items-center justify-center">
                 <span class="font-black text-xl text-primary-900 leading-none">M</span>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <h1 class="text-slate-900 font-black text-lg tracking-tight uppercase leading-none">Donezo <span class="text-primary-900">ERP</span></h1>
            <p class="text-[9px] text-slate-400 font-bold tracking-[0.2em] uppercase mt-2 leading-none">Enterprise Level</p>
        </div>
    </div>

    <!-- Navigation Hub (Ultra High Density) -->
    <div class="flex-1 overflow-y-auto py-6 space-y-10 custom-scrollbar">
        
        <!-- Group: Operational -->
        <div class="space-y-4">
            <div class="px-8 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em]">MENU</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="text-[13px] font-bold tracking-tight">Dashboard</span>
                </x-sidebar-link>
            </div>
        </div>

        <!-- Group: Master Data -->
        <div class="space-y-4">
            <div class="px-8">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em]">MANAGEMENT</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('dapurs.index')" :active="request()->routeIs('dapurs.*')">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-[13px] font-bold tracking-tight">Data Dapur</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[13px] font-bold tracking-tight">Mitra Supplier</span>
                </x-sidebar-link>
                <x-sidebar-link :href="route('investors.index')" :active="request()->routeIs('investors.*')">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[13px] font-bold tracking-tight">Investor System</span>
                </x-sidebar-link>
            </div>
        </div>

        <!-- Group: General -->
        <div class="space-y-4">
            <div class="px-8">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em]">GENERAL</span>
            </div>
            <div class="space-y-0.5">
                <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-[13px] font-bold tracking-tight">Settings Access</span>
                </x-sidebar-link>
            </div>
        </div>
    </div>

    <!-- Sidebar Bottom: Professional Identity (Donezo style) -->
    <div class="px-8 py-8 border-t border-slate-50 relative group cursor-pointer hover:bg-slate-50/50 transition-all">
        <div class="flex items-center gap-4">
             <div class="w-10 h-10 rounded-[12px] bg-slate-100 flex items-center justify-center p-0.5 shadow-sm overflow-hidden group-hover:scale-105 transition-transform">
                <div class="w-full h-full rounded-[10px] bg-white flex items-center justify-center font-black text-[10px] text-primary-900 border border-slate-200">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-slate-900 text-[11px] font-black truncate uppercase leading-none">{{ Auth::user()->name }}</p>
                <p class="text-[9px] text-slate-400 font-bold truncate lowercase mt-1.5 leading-none">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>
