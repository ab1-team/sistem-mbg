<x-app-layout title="Dashboard">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-[28px] font-extrabold text-slate-900 tracking-tight leading-none">Ringkasan Operasional</h1>
            <p class="text-[13px] text-slate-400 mt-2">Selamat datang kembali! Berikut adalah status sistem hari ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-btn href="{{ route('menu-items.create') }}" variant="secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4"/>
                </svg>
                Buat Menu Baru
            </x-btn>
            <x-btn href="{{ route('materials.create') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                Tambah Bahan
            </x-btn>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Card: Dapur --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Dapur</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['total_dapurs'] }}</span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase">Unit Aktif</span>
                </div>
            </div>
        </div>

        {{-- Card: Bahan Baku --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Bahan Baku</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['total_materials'] }}</span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase">Item Terdaftar</span>
                </div>
            </div>
        </div>

        {{-- Card: Menu --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Daftar Menu</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['total_menu_items'] }}</span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase">Resep Tersedia</span>
                </div>
            </div>
        </div>

        {{-- Card: Periode --}}
        <div class="bg-white rounded-[24px] p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">Periode Aktif</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['total_periods'] }}</span>
                    <span class="text-[11px] font-bold text-slate-400 uppercase">Input Periode</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Recent Menus --}}
        <div class="lg:col-span-2">
            <x-card title="Masakan Terbaru" subtitle="Daftar menu yang baru saja ditambahkan ke sistem.">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Nama Masakan</th>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tipe</th>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Kalori</th>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentMenus as $menu)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-slate-900 tracking-tight leading-none">{{ $menu->name }}</p>
                                        <p class="text-[11px] text-slate-400 mt-1 truncate max-w-xs">{{ $menu->description }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">{{ $menu->meal_type }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right font-mono text-[13px] font-bold text-slate-600">
                                        {{ number_format($menu->calories, 0) }} <span class="text-[10px] text-slate-400">kcal</span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('menu-items.show', $menu) }}" class="text-[11px] font-bold text-green-700 hover:text-green-900 underline transition-colors">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-slate-400 text-[13px]">Belum ada data menu tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-50">
                    <a href="{{ route('menu-items.index') }}" class="text-[12px] font-bold text-slate-500 hover:text-green-800 flex items-center justify-center gap-2 transition-colors">
                        Lihat Semua Menu
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </x-card>
        </div>

        {{-- Right Column: Side Info --}}
        <div class="space-y-6">
            <x-card title="Stok Bahan Rendah" subtitle="Segera lakukan pemesanan (PO).">
                <div class="space-y-4">
                    @php 
                        // Temporary placeholder for low stock items
                        $lowStockItems = []; 
                    @endphp
                    @forelse($lowStockItems as $item)
                        {{-- Future item loop --}}
                    @empty
                        <div class="flex flex-col items-center justify-center py-6 text-center border-2 border-dashed border-slate-50 rounded-2xl bg-slate-50/30">
                            <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-green-500 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-[12px] font-bold text-slate-700">Semua Stok Aman</p>
                            <p class="text-[11px] text-slate-400">Tidak ada bahan yang kritis.</p>
                        </div>
                    @endforelse
                </div>
            </x-card>

            {{-- Quick Links --}}
            <div class="bg-linear-to-br from-green-900 to-green-800 rounded-[24px] p-6 text-white overflow-hidden relative group">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full group-hover:scale-125 transition-transform"></div>
                <div class="relative z-10">
                    <h4 class="text-[16px] font-bold mb-1">Butuh Bantuan?</h4>
                    <p class="text-[12px] text-green-100 opacity-80 mb-4 leading-relaxed">Cek dokumentasi sistem atau hubungi Administrator Yayasan.</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 bg-white text-green-900 text-[12px] font-extrabold px-4 py-2.5 rounded-xl hover:bg-green-50 transition-colors shadow-lg">
                        Support Center
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
