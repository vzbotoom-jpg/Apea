@props(['id' => 'logout-modal'])

<div x-data="{ open: false }"
     @open-logout-modal.window="open = true"
     @keydown.escape.window="open = false"
     class="relative z-50">
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         @click="open = false"
         style="display: none;"></div>

    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4"
         style="display: none;">
        <div class="relative bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl border border-slate-100">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Konfirmasi Keluar</h3>
            <p class="text-xs text-slate-500 mb-6">Apakah Anda yakin ingin keluar dari sistem? Anda harus masuk kembali untuk mengakses akun Anda.</p>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="open = false"
                        class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Batal
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-xs">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
