@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' | ' . config('app.name', 'Yayasan MBG') : config('app.name', 'Yayasan MBG') }}
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
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

<body class="bg-slate-50 font-sans antialiased text-slate-900">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }" x-init="sidebarOpen = window.innerWidth > 1024">
    {{-- Backdrop for Mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"
        x-cloak>
    </div>

    {{-- ============================================================ --}}
    {{-- SIDEBAR                                                        --}}
    {{-- ============================================================ --}}
    @include('layouts.sidebar')
    {{-- ============================================================ --}}
    {{-- MAIN AREA                                                       --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden transition-all duration-300 ease-in-out">
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
                <livewire:notification-bell />

                {{-- Profile Dropdown --}}
                <div class="relative ml-2" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
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
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
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
            @if (session('success'))
                <div class="mb-6">
                    <x-alert variant="success" title="Berhasil">
                        {{ session('success') }}
                    </x-alert>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6">
                    <x-alert variant="danger" title="Kesalahan">
                        {{ session('error') }}
                    </x-alert>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

</div>
    @livewireScriptConfig
    @vite(['resources/js/app.js'])
    @livewire('notifications')
    <x-push-mandatory-modal />
</body>

</html>
