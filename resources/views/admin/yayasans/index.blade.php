<x-admin-layout>
    <div class="max-w-4xl mx-auto py-12" x-data="{ deleteUrl: '', deleteName: '' }">
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
                        <form action="{{ route('admin.yayasans.toggle', $yayasan) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-all {{ $yayasan->is_active ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}">
                                {{ $yayasan->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>

                        <x-btn href="http://{{ $yayasan->domains->first()->domain }}" target="_blank" variant="secondary"
                            size="sm">Manage</x-btn>
                        <x-btn variant="ghost" size="sm" class="text-red-500 hover:text-red-700 hover:bg-red-50"
                            @click="deleteUrl = '{{ route('admin.yayasans.destroy', $yayasan) }}'; deleteName = '{{ $yayasan->name }}'; $dispatch('open-modal', 'confirm-deletion')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </x-btn>
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

    <!-- Delete Confirmation -->
    <x-dialog name="confirm-deletion" title="Konfirmasi Penghapusan" maxWidth="md">
        <form method="post" :action="deleteUrl" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Push the Button?</h2>
                <p class="text-sm text-slate-500 mt-2">
                    Apakah Anda yakin ingin menghapus <span class="font-bold text-slate-900" x-text="deleteName"></span>?
                    Tindakan ini akan menghapus seluruh data dan database yayasan tersebut secara permanen.
                </p>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="open = false"
                    class="px-6 py-2.5 rounded-2xl bg-slate-50 text-slate-500 font-bold text-[12px] hover:bg-slate-100 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-8 py-2.5 rounded-2xl bg-red-600 text-white font-bold text-[12px] hover:bg-red-700 transition-all">
                    Ya, Hapus Permanen
                </button>
            </div>
        </form>
    </x-dialog>
</x-admin-layout>
