<div x-data="{ 
        show: false, 
        permission: 'default',
        checking: true
    }" 
    x-init="
        permission = Notification.permission;
        const storageKey = 'fcm_token_registered_user_{{ Auth::id() }}';
        const isRegistered = localStorage.getItem(storageKey);
        
        console.log('FCM Setup - Permission:', permission, 'Registered on this device:', !!isRegistered);
        
        // Show modal if not registered on THIS device
        if (!isRegistered) {
            show = true;
            
            // Auto-trigger token generation if permission is already granted
            if (permission === 'granted') {
                window.requestPushPermission();
            }
        }
        
        // Listen for token saved event
        window.addEventListener('fcm-token-saved', () => {
            console.log('FCM Token Saved - Setting localStorage and hiding modal');
            localStorage.setItem(storageKey, 'true');
            show = false;
        });

        // Update state periodically or on interaction
        setInterval(() => {
            permission = Notification.permission;
            // If user manually allowed, trigger permission request
            if (show && permission === 'granted' && !localStorage.getItem(storageKey)) {
                window.requestPushPermission();
            }
        }, 1000);
    "
    x-show="show" 
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center overflow-hidden bg-slate-900/95 backdrop-blur-md"
    style="display: none;">
    
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 border border-slate-200 m-4">
        {{-- State 1: Request Permission --}}
        <template x-if="permission === 'default' || permission === 'prompt'">
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Aktifkan Notifikasi</h2>
                <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                    Demi keamanan dan kelancaran operasional, semua pengguna wajib mengaktifkan notifikasi push untuk menerima update pesanan dan stok secara real-time.
                </p>
                <div class="space-y-3">
                    <button @click="window.requestPushPermission()" 
                        class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        Izinkan Sekarang
                    </button>
                    <p class="text-[11px] text-slate-400">
                        Klik tombol di atas, lalu pilih <b>"Allow"</b> atau <b>"Izinkan"</b> pada prompt yang muncul.
                    </p>
                </div>
            </div>
        </template>

        {{-- State 2: Permission Granted but Token Pending --}}
        <template x-if="permission === 'granted'">
            <div class="text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Sinkronisasi...</h2>
                <p class="text-slate-500 text-sm mb-4">Sedang mendaftarkan perangkat Anda ke server.</p>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mb-4">
                    <div class="bg-green-600 h-1.5 rounded-full animate-[progress_2s_ease-in-out_infinite]" style="width: 45%"></div>
                </div>
            </div>
        </template>

        {{-- State 3: Permission Denied (The Lockdown) --}}
        <template x-if="permission === 'denied'">
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Akses Terkunci</h2>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                    Anda telah menolak izin notifikasi. Aplikasi tidak dapat diakses sebelum Anda memberikan izin secara manual melalui pengaturan browser.
                </p>
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-left mb-6">
                    <h4 class="text-xs font-bold text-blue-700 uppercase mb-2">Cara Membuka:</h4>
                    <ol class="text-xs text-blue-600 space-y-2 list-decimal ml-4">
                        <li>Klik ikon <b>Gembok</b> atau <b>Slider</b> di sebelah kiri alamat website (address bar).</li>
                        <li>Cari menu <b>Notification</b> atau <b>Notifikasi</b>.</li>
                        <li>Ubah status dari <b>Block</b> menjadi <b>Allow</b>.</li>
                        <li>Klik <b>Reload</b> / Muat Ulang halaman ini.</li>
                    </ol>
                </div>
                <button @click="window.location.reload()" 
                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-slate-200 text-sm font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition-all duration-200">
                    Selesai & Muat Ulang Halaman
                </button>
            </div>
        </template>
    </div>
</div>

<style>
@keyframes progress {
    0% { width: 0%; margin-left: 0%; }
    50% { width: 40%; margin-left: 30%; }
    100% { width: 0%; margin-left: 100%; }
}
</style>
