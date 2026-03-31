<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Google Fonts (Inter prioritized for Donezo aesthetic) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">


        <!-- Scripts & Built Components -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-white min-h-screen selection:bg-green-900/10 selection:text-green-900">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 sm:p-12 relative overflow-hidden bg-slate-50/30">
            
            <!-- Branding Header (Above Card) -->
            <div class="mb-8 text-center animate-reveal" style="animation-delay: 0.1s">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white border border-slate-100 shadow-sm mb-6 group hover:scale-105 transition-transform duration-500">
                     <span class="font-bold text-xl text-slate-900 leading-none tracking-tight">MBG</span>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight leading-none uppercase">
                    YAYASAN <span class="text-green-800">MBG</span>
                </h2>
                <p class="mt-3 text-[10px] font-medium text-slate-400 uppercase tracking-[0.3em]">
                    Enterprise System v2
                </p>
            </div>

            <!-- Authentic Minimalist Card -->
            <div class="w-full max-w-[440px] bg-white rounded-2xl shadow-sm border border-slate-100 p-8 sm:p-12 animate-reveal" style="animation-delay: 0.2s">
                {{ $slot }}
            </div>

            <!-- Footer Meta -->
            <div class="mt-12 text-center animate-reveal" style="animation-delay: 0.3s">
                <p class="text-[9px] font-medium text-slate-400 uppercase tracking-[0.2em]">
                    &copy; {{ date('Y') }} YAYASAN MBG &bull; PREMIER EDITION
                </p>
            </div>

            <!-- Decorative Elements (Ultra Soft) -->
            <div class="absolute -top-40 -left-40 w-120 h-120 bg-green-900/5 rounded-full blur-[80px] z-[-1]"></div>
            <div class="absolute -bottom-40 -right-40 w-100 h-100 bg-slate-200/40 rounded-full blur-[60px] z-[-1]"></div>
        </div>
    </body>
</html>
