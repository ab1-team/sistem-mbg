<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SaaS Console | {{ config('app.name', 'Laravel') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Scripts & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-800 bg-white min-h-screen">
    <div class="min-h-screen bg-slate-50/30 relative overflow-hidden flex flex-col">

        <!-- Navbar -->
        <nav class="bg-white border-b border-slate-100 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center justify-center">
                    <span class="font-bold text-sm text-emerald-700">MBG</span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight leading-none">SaaS CONSOLE</h2>
                    <p class="text-[9px] font-medium text-slate-400 mt-1 uppercase tracking-wider">Foundation Manager
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <x-btn href="/" variant="ghost" size="sm">Landing Page</x-btn>
                <div class="w-8 h-8 rounded-full bg-slate-200 border border-white shadow-sm"></div>
            </div>
        </nav>

        <main class="grow container mx-auto">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="py-12 text-center">
            <p class="text-[9px] font-medium text-slate-400 uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} YAYASAN MBG &bull; SaaS Control Plane
            </p>
        </footer>

        <!-- Decorative Elements -->
        <div class="absolute -top-40 -left-40 w-120 h-120 bg-emerald-900/5 rounded-full blur-[80px] z-[-1]"></div>
        <div class="absolute -bottom-40 -right-40 w-100 h-100 bg-slate-200/40 rounded-full blur-[60px] z-[-1]"></div>
    </div>
</body>

</html>
