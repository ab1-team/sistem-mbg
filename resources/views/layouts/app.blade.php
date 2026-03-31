@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' | ' . config('app.name', 'Yayasan MBG') : config('app.name', 'Yayasan MBG') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 99px;
        }
    </style>
</head>

<body class="bg-slate-100 antialiased" x-data="{ sidebarOpen: true }">

    <div class="flex h-screen overflow-hidden">

        {{-- ============================================================ --}}
        {{-- SIDEBAR                                                        --}}
        {{-- ============================================================ --}}
        <aside
            class="flex flex-col w-[220px] shrink-0 bg-white border-r border-slate-100 overflow-y-auto overflow-x-hidden"
            x-show="sidebarOpen" x-transition:enter="transition-transform duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-cloak>
            {{-- Brand --}}
            <div class="flex items-center gap-3 px-5 py-7">
                <div class="w-9 h-9 rounded-xl bg-green-900 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83" />
                    </svg>
                </div>
                <span class="text-[17px] font-extrabold text-slate-900 tracking-tight">Donezo</span>
            </div>

            {{-- MENU section --}}
            <p class="px-5 pb-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Menu</p>
            <nav class="flex flex-col">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
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
                        ['route' => 'periods.index', 'match' => 'periods.*', 'label' => 'Periode', 'icon' => 'period'],
                    ];
                    $icons = [
                        'dashboard' => '<path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>',
                        'dapur' =>
                            '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                        'supplier' =>
                            '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/>',
                        'investor' =>
                            '<path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'period' =>
                            '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                        'team' =>
                            '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $isActive = request()->routeIs($item['match']); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-5 py-2.5 text-[13px] font-medium border-l-[3px] transition-colors
                          {{ $isActive
                              ? 'border-green-900 text-green-900 font-semibold bg-green-50'
                              : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            {!! $icons[$item['icon']] !!}
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- GENERAL section --}}
            <p class="px-5 pt-5 pb-2 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">General</p>
            <nav class="flex flex-col">
                @php $isUserActive = request()->routeIs('users.*'); @endphp
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-5 py-2.5 text-[13px] font-medium border-l-[3px] transition-colors
                      {{ $isUserActive
                          ? 'border-green-900 text-green-900 font-semibold bg-green-50'
                          : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    {!! $icons['team'] !!}
                </svg>
                Data Pengguna
            </a>

            @php $isProfileActive = request()->routeIs('profile.edit'); @endphp
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-5 py-2.5 text-[13px] font-medium border-l-[3px] transition-colors
                      {{ $isProfileActive
                          ? 'border-green-900 text-green-900 font-semibold bg-green-50'
                          : 'border-transparent text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
                Pengaturan
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-5 py-2.5 text-[13px] font-medium text-red-400 border-l-[3px] border-transparent hover:text-red-600 hover:bg-red-50 transition-colors text-left group">
                    <svg class="w-4 h-4 shrink-0 transition-transform group-hover:-translate-x-0.5" fill="none"
                        stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
            </nav>

            <div class="flex-1"></div>
        </aside>

        {{-- ============================================================ --}}
        {{-- MAIN AREA                                                       --}}
        {{-- ============================================================ --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            {{-- TOP NAV --}}
            <header class="flex items-center h-16 bg-white border-b border-slate-100 px-6 gap-4 shrink-0">
                {{-- Hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                    class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M4 8h16M4 16h16" />
                    </svg>
                </button>

                {{-- Search --}}
                <div
                    class="flex items-center gap-2.5 bg-slate-50/50 border border-slate-100 rounded-xl px-3 py-2 flex-1 max-w-xs ml-2">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" placeholder="Cari data..."
                        class="bg-transparent border-none outline-none text-[13px] text-slate-600 placeholder:text-slate-300 w-full p-0 focus:ring-0">
                    <div
                        class="flex items-center gap-1 overflow-hidden px-1.5 py-0.5 rounded-lg border border-slate-200 bg-white shadow-sm shrink-0">
                        <span class="text-[9px] font-bold text-slate-400">⌘</span>
                        <span class="text-[9px] font-bold text-slate-400">F</span>
                    </div>
                </div>

                {{-- Right actions --}}
                <div class="ml-auto flex items-center gap-4">
                    <button class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button class="text-slate-400 hover:text-slate-600 transition-colors relative">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span
                            class="absolute top-0.5 right-0.5 w-2 h-2 bg-green-500 rounded-full border-2 border-white"></span>
                    </button>

                    {{-- Profile Dropdown --}}
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                            <div
                                class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center border border-green-200 shrink-0">
                                @php
                                    $initials = collect(explode(' ', Auth::user()->name))
                                        ->map(fn($n) => strtoupper(substr($n, 0, 1)))
                                        ->take(2)
                                        ->join('');
                                @endphp
                                <span
                                    class="text-[12px] font-bold text-green-700 tracking-tighter">{{ $initials }}</span>
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-[13px] font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}
                                </p>
                                <p class="text-[11px] text-slate-400 leading-tight mt-0.5">{{ Auth::user()->email }}
                                </p>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor"
                                stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute right-0 top-12 z-50 bg-white border border-slate-100 rounded-2xl shadow-xl w-52 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-50">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Akun</p>
                                <p class="text-[12px] font-bold text-slate-900 truncate mt-0.5">
                                    {{ Auth::user()->email }}</p>
                            </div>
                            <div class="p-1.5">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-2 px-3 py-2 rounded-xl text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    Profil Saya
                                </a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-[13px] font-medium text-red-500 hover:bg-red-50 transition-colors text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" y1="12" x2="9" y2="12" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto p-7">
                {{ $slot }}
            </main>
        </div>

    </div>
    @livewireScripts
</body>

</html>
