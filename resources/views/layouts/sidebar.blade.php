<aside
    class="fixed inset-y-0 left-0 z-50 lg:relative lg:z-30 flex flex-col bg-white border-r border-slate-100 transition-all duration-300 ease-in-out overflow-y-auto overflow-x-hidden"
    :class="sidebarOpen ? 'w-64 opacity-100' : 'w-0 lg:w-0 opacity-0 pointer-events-none'" x-cloak>

    {{-- Internal Wrapper --}}
    <div class="w-64 flex flex-col h-full min-h-screen">
        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-7">
            <div class="w-9 h-9 rounded-xl bg-green-900 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" />
                </svg>
            </div>
            <span
                class="text-[17px] font-extrabold text-slate-900 tracking-tight">{{ tenant('name') ?? config('app.name', 'Yayasan MBG') }}</span>
        </div>

        {{-- MENU GROUPS --}}
        @php
            $navGroups = [
                [
                    'label' => 'Operasional',
                    'items' => [
                        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                    ],
                ],
                [
                    'label' => 'SCM & Perencanaan',
                    'items' => [
                        [
                            'route' => 'materials.index',
                            'match' => 'materials.*',
                            'label' => 'Bahan Baku',
                            'icon' => 'material',
                        ],
                        [
                            'route' => 'menu-items.index',
                            'match' => 'menu-items.*',
                            'label' => 'Daftar Menu',
                            'icon' => 'menu',
                        ],
                        [
                            'route' => 'menu-periods.index',
                            'match' => 'menu-periods.*',
                            'label' => 'Perencanaan Menu',
                            'icon' => 'planning',
                        ],
                        [
                            'route' => 'purchase-orders.index',
                            'match' => 'purchase-orders.*',
                            'label' => 'Purchase Orders',
                            'icon' => 'material',
                        ],
                    ],
                ],
                [
                    'label' => 'Logistik & SCM',
                    'role' => ['admin_yayasan', 'logistik', 'superadmin'],
                    'items' => [
                        ['route' => 'gr.index', 'match' => 'gr.*', 'label' => 'Penerimaan (GR)', 'icon' => 'gr'],
                    ],
                ],
                [
                    'label' => 'Dapur Unit',
                    'role' => ['kepala_dapur', 'koki', 'superadmin'],
                    'items' => [
                        [
                            'route' => 'kitchen.index',
                            'match' => 'kitchen.index',
                            'label' => 'Dashboard Masak',
                            'icon' => 'kitchen',
                        ],
                        [
                            'route' => 'kitchen.inventory',
                            'match' => 'kitchen.inventory',
                            'label' => 'Inventaris Unit',
                            'icon' => 'planning',
                        ],
                    ],
                ],
                [
                    'label' => 'Finance & Keuangan',
                    'role' => ['finance_yayasan', 'superadmin'],
                    'items' => [
                        [
                            'route' => 'finance.journal.index',
                            'match' => 'finance.journal.*',
                            'label' => 'Jurnal Umum',
                            'icon' => 'revenue',
                        ],
                        [
                            'route' => 'finance.reports.index',
                            'match' => 'finance.reports.*',
                            'label' => 'Pelaporan',
                            'icon' => 'planning',
                        ],
                        [
                            'route' => 'finance.invoices.index',
                            'match' => 'invoices.*',
                            'label' => 'Invoice Supplier',
                            'icon' => 'invoice',
                        ],
                        [
                            'route' => 'finance.kitchen-invoices.index',
                            'match' => 'finance.kitchen-invoices.*',
                            'label' => 'Invoice Dapur (Rekap)',
                            'icon' => 'gr',
                        ],
                        [
                            'route' => 'finance.profit-sharing.index',
                            'match' => 'finance.profit-sharing.*',
                            'label' => 'Bagi Hasil',
                            'icon' => 'revenue',
                        ],
                        [
                            'route' => 'finance.withdrawals.index',
                            'match' => 'finance.withdrawals.*',
                            'label' => 'Penarikan Dana',
                            'icon' => 'supplier',
                        ],
                    ],
                ],
                [
                    'label' => 'Portal Supplier',
                    'role' => 'supplier',
                    'items' => [
                        [
                            'route' => 'supplier.purchase-orders.index',
                            'match' => 'supplier.*',
                            'label' => 'Pesanan Saya',
                            'icon' => 'material',
                        ],
                    ],
                ],
                [
                    'label' => 'Portal Investor',
                    'role' => 'investor',
                    'items' => [
                        [
                            'route' => 'investor.dashboard',
                            'match' => 'investor.dashboard',
                            'label' => 'Dashboard Investasi',
                            'icon' => 'dashboard',
                        ],
                        [
                            'route' => 'investor.withdrawals.create',
                            'match' => 'investor.withdrawals.*',
                            'label' => 'Tarik Dana',
                            'icon' => 'revenue',
                        ],
                    ],
                ],
                [
                    'label' => 'Manajemen Data',
                    'items' => [
                        ['route' => 'dapurs.index', 'match' => 'dapurs.*', 'label' => 'Data Dapur', 'icon' => 'dapur'],
                        [
                            'route' => 'suppliers.index',
                            'match' => 'suppliers.*',
                            'label' => 'Data Supplier',
                            'icon' => 'supplier',
                        ],
                        [
                            'route' => 'investors.index',
                            'match' => 'investors.*',
                            'label' => 'Data Investor',
                            'icon' => 'investor',
                        ],
                        [
                            'route' => 'finance.periods.index',
                            'match' => 'finance.periods.*',
                            'label' => 'Periode Keuangan',
                            'icon' => 'period',
                        ],
                    ],
                ],
                [
                    'label' => 'Sistem',
                    'items' => [
                        ['route' => 'users.index', 'match' => 'users.*', 'label' => 'Data Pengguna', 'icon' => 'team'],
                        [
                            'route' => 'profile.edit',
                            'match' => 'profile.edit',
                            'label' => 'Profil Saya',
                            'icon' => 'team',
                        ],
                        [
                            'route' => 'settings.index',
                            'match' => 'settings.*',
                            'label' => 'Pengaturan',
                            'icon' => 'settings',
                        ],
                    ],
                ],
            ];

            $icons = [
                'dashboard' => '<path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>',
                'material' =>
                    '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/>',
                'menu' =>
                    '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
                'planning' =>
                    '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
                'dapur' =>
                    '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                'supplier' =>
                    '<path d="M21 8V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h10M17 13l3 3m0 0l-3 3m3-3H8" />',
                'investor' =>
                    '<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'period' =>
                    '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                'team' =>
                    '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                'settings' =>
                    '<path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><circle cx="12" cy="12" r="3" />',
                'gr' =>
                    '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
                'kitchen' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />',
                'invoice' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'revenue' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'expense' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ];
        @endphp

        <nav class="flex flex-col gap-1 pb-10">
            @foreach ($navGroups as $group)
                @php
                    $visible =
                        !isset($group['role']) ||
                        (is_array($group['role'])
                            ? auth()->user()->hasAnyRole($group['role'])
                            : auth()->user()->hasRole($group['role']));
                @endphp
                @if ($visible)
                    <div class="px-5 pt-5 pb-2">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">
                            {{ $group['label'] }}</p>
                    </div>
                    @foreach ($group['items'] as $item)
                        @php $isActive = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 px-5 py-2.5 text-[13px] font-medium border-l-[3px] transition-all
                      {{ $isActive
                          ? 'border-green-900 text-green-900 font-bold bg-green-50'
                          : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                            <svg class="w-4.5 h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                {!! $icons[$item['icon']] !!}
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endif
            @endforeach

            {{-- Logout Button --}}
            <div class="mt-4 pt-4 border-t border-slate-50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-5 py-2.5 text-[13px] font-bold text-red-400 border-l-[3px] border-transparent hover:text-red-700 hover:bg-red-50 transition-all text-left group">
                        <svg class="w-4.5 h-4.5 shrink-0 transition-transform group-hover:-translate-x-0.5"
                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="flex-1"></div>
    </div> {{-- End of Internal Wrapper --}}
</aside>
