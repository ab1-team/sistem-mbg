<x-admin-layout>
    <div class="max-w-4xl mx-auto py-12">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">SaaS Command Center</h1>
                <p class="text-sm text-slate-400 mt-2">Kelola pendaftaran Yayasan dan infrastruktur database.</p>
            </div>
            <x-btn @click="$dispatch('open-modal', 'create-foundation')">Daftarkan Yayasan Baru</x-btn>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-2xl mb-8 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6">
            @forelse($yayasans as $yayasan)
                <div
                    class="bg-white rounded-[24px] border border-slate-100 p-6 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 leading-tight">{{ $yayasan->name }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Tenant ID: <span
                                    class="font-mono">{{ $yayasan->id }}</span></p>
                            <div class="flex gap-4 mt-2">
                                @foreach ($yayasan->domains as $domain)
                                    <a href="http://{{ $domain->domain }}" target="_blank"
                                        class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 underline">
                                        {{ $domain->domain }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="px-3 py-1 rounded-full text-[10px] font-bold bg-slate-50 text-slate-400 uppercase">Active</span>
                        <x-btn variant="ghost" size="sm">Manage</x-btn>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 border-2 border-dashed border-slate-50 rounded-[32px]">
                    <p class="text-slate-400 text-sm">Belum ada Yayasan yang terdaftar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Dialog -->
    <x-dialog name="create-foundation" title="Daftarkan Yayasan Baru" maxWidth="lg" :show="$errors->isNotEmpty()">
        <form method="post" action="{{ route('admin.yayasans.store') }}" class="space-y-6" x-data="{ loading: false }"
            @submit="loading = true">
            @csrf

            <x-form-input label="Nama Yayasan" name="name" required autofocus />

            <x-form-input label="Domain / Hostname" name="domain" placeholder="misal: mbg.test"
                hint="* Pastikan domain ini diarahkan ke server aplikasi." required />

            <x-form-input label="Email Admin Pertama" name="email" type="email"
                hint="* Akun SuperAdmin akan dibuat otomatis dengan password default: 'password'." required />

            <div class="mt-8 flex justify-end gap-3" x-cloak>
                <button type="button" @click="open = false" x-show="!loading"
                    class="px-6 py-2.5 rounded-2xl bg-slate-50 text-slate-500 font-bold text-[12px] hover:bg-slate-100 transition-all">
                    Batal
                </button>
                <button type="submit" :disabled="loading"
                    class="px-8 py-2.5 rounded-2xl bg-emerald-700 text-white font-bold text-[12px] hover:bg-emerald-800 transition-all disabled:opacity-50">
                    <span x-show="!loading">Proses Provisioning</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </x-dialog>
</x-admin-layout>
