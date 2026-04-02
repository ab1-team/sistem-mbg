<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem MBG</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            letter-spacing: -0.0125em;
        }

        .container-hero {
            max-width: 1000px;
            margin: 0 auto;
        }

        .bg-dots {
            background-image: radial-gradient(#6ee7b7 1.4px, transparent 1.4px);
            background-size: 40px 40px;
        }

        .border-soft {
            border-color: #f1f5f9;
        }
    </style>
</head>

<body class="antialiased bg-dots">

    <!-- Nav -->
    <nav
        class="h-16 flex items-center justify-between px-8 border-b border-soft fixed top-0 w-full bg-white/80 backdrop-blur-sm z-50">
        <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            <div>
                <span class="text-[13px] font-bold text-slate-900 tracking-tight">
                    @if (tenant())
                        {{ tenant('name') }}
                    @else
                        Sistem MBG
                    @endif
                </span>
                @if (tenant())
                    <p class="text-[9px] font-medium text-slate-400 uppercase tracking-wider">Foundation Portal</p>
                @endif
            </div>
        </div>

        <div class="flex gap-2">
            @if (tenant())
                @auth
                    <x-btn href="{{ url('/dashboard') }}" size="sm">Dashboard</x-btn>
                @else
                    <x-btn href="{{ route('login') }}" variant="ghost" size="sm">Masuk Portal</x-btn>
                @endauth
            @else
                <x-btn href="{{ route('admin.yayasans.index') }}" variant="ghost" size="sm">SaaS Console</x-btn>
                <x-btn href="#features" size="sm">Pelajari Fitur</x-btn>
            @endif
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative pt-48 pb-32 px-8">
        <div class="container-hero text-center">
            <h1 class="text-5xl lg:text-7xl font-light text-slate-900 leading-[1.1] mb-12 tracking-tight">
                @if (tenant())
                    Manajemen <span class="font-bold">Yayasan Cerah</span>
                @else
                    Manajemen Terpercaya <br /> <span class="font-bold">Masa Depan Anak</span>
                @endif
            </h1>
            <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-xl mx-auto mb-16">
                Platform digital transparan untuk pengelolaan bantuan <span
                    class="underline decoration-emerald-100 decoration-2 underline-offset-4 font-semibold text-emerald-700">gizi</span>
                generasi emas Indonesia.
            </p>
            <div class="flex justify-center gap-4">
                @if (tenant())
                    @auth
                        <x-btn href="{{ url('/dashboard') }}" size="xl" class="shadow-lg shadow-emerald-700/20">Buka
                            Dashboard</x-btn>
                    @else
                        <x-btn href="{{ route('login') }}" size="xl" class="shadow-lg shadow-emerald-700/20">Masuk ke
                            Portal</x-btn>
                    @endauth
                @else
                    <x-btn href="#features" size="xl" class="shadow-lg shadow-emerald-700/20">Mulai Kelola
                        Sekarang</x-btn>
                @endif
            </div>
        </div>
    </section>

    <!-- Minimalist Features -->
    <section class="py-32 border-t border-soft">
        <div class="max-w-6xl mx-auto px-8">
            <div class="grid md:grid-cols-4 gap-6">
                <x-card class="hover:border-emerald-100 transition-colors">
                    <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-6">Produksi</div>
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Pengelolaan Menu</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">Sistem Bill of Materials (BOM) untuk
                        standarisasi gizi dan biaya setiap porsi makanan.</p>
                </x-card>

                <x-card class="hover:border-emerald-100 transition-colors">
                    <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-6">Logistik</div>
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Rantai Pasokan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-normal">Pemantauan stok bahan baku dan
                        verifikasi penerimaan barang yang akurat di setiap gudang.</p>
                </x-card>

                <x-card class="hover:border-emerald-100 transition-colors">
                    <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-6">Finansial</div>
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Transparansi Dana</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-normal">Pencatatan real-time arus kas yayasan
                        dan alokasi dana operasional dapur yang transparan.</p>
                </x-card>

                <x-card class="hover:border-emerald-100 transition-colors">
                    <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-6">Integrasi</div>
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Alur Kerja Terpadu</h3>
                    <p class="text-xs text-slate-500 leading-relaxed font-normal">Menghubungkan dapur, gudang, dan
                        manajemen dalam satu platform yang sinkron.</p>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-24 border-t border-soft">
        <div class="max-w-6xl mx-auto px-8 flex flex-col items-center gap-12">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                <span class="text-[14px] font-bold text-slate-900 tracking-tight">Sistem MBG</span>
            </div>

            <div class="flex gap-16 text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                <a href="#" class="hover:text-emerald-700 transition-colors">Visi Misi</a>
                <a href="#" class="hover:text-emerald-700 transition-colors">Laporan Dampak</a>
                <a href="#" class="hover:text-emerald-700 transition-colors">Privasi</a>
            </div>

            <div class="text-[10px] text-slate-300 font-bold tracking-widest uppercase">
                &copy; {{ date('Y') }} Yayasan Makan Bergizi Gratis &bull; v2.0
            </div>
        </div>
    </footer>

</body>

</html>
