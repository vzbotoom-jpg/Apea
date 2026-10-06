@include('partials.head')
@include('partials.navbar')
@include('partials.alerts')

{{-- ================= HERO: DOWNLOAD CENTER ================= --}}
<section class="relative bg-slate-900 py-20 lg:py-24 overflow-hidden">
    {{-- ✅ BACKGROUND IMAGE + OVERLAY (sama seperti Beranda) --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?q=80&w=2070&auto=format&fit=crop"
             alt="Background Laboratorium Teknologi"
             class="w-full h-full object-cover object-center opacity-30">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900/85 to-slate-900/60"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
        <span class="inline-block px-4 py-1.5 rounded-full border border-teal-500 bg-teal-900 text-teal-300 text-[11px] font-bold tracking-widest uppercase mb-6">
            Download Center
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-6 leading-tight">
            Unduh Aplikasi <span class="text-teal-400">APIC</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Dapatkan pengalaman peminjaman alat yang lebih cepat dengan menginstall APIC
            di perangkat Anda. Tersedia untuk semua platform populer.
        </p>
    </div>
</section>

{{-- ================= PLATFORM DOWNLOADS ================= --}}
<section class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Pilih Platform Anda</h2>
            <p class="mt-3 text-slate-600">Klik tombol download sesuai sistem operasi perangkat Anda.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Windows --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-lg hover:border-teal-300 transition text-center">
                <div class="w-14 h-14 mx-auto bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.9-1.801"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Windows</h3>
                <p class="text-xs text-slate-500 mt-1 mb-5">Windows 10/11 · .exe · 45 MB</p>
                <a href="{{ asset('downloads/APIC-Setup.exe') }}" download
                   class="block w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                    Download
                </a>
            </div>

            {{-- Android --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-lg hover:border-teal-300 transition text-center">
                <div class="w-14 h-14 mx-auto bg-green-50 text-green-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993.9993.4482.9993.9993-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993.9993.4482.9993.9993-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0223 3.503C15.5902 8.2441 13.8533 7.8508 12 7.8508s-3.5902.3933-5.1367 1.0989L4.841 5.4467a.4161.4161 0 00-.5677-.1521.4157.4161 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3483 14.6474 0 18.761h24c-.3483-4.1136-2.6889-7.5743-6.1185-9.4396"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Android</h3>
                <p class="text-xs text-slate-500 mt-1 mb-5">Android 8.0+ · .apk · 28 MB</p>
                <a href="{{ asset('downloads/APIC.apk') }}" download
                   class="block w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                    Download
                </a>
            </div>

            {{-- Linux --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-lg hover:border-teal-300 transition text-center">
                <div class="w-14 h-14 mx-auto bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Linux</h3>
                <p class="text-xs text-slate-500 mt-1 mb-5">Ubuntu/Debian · .AppImage · 50 MB</p>
                <a href="{{ asset('downloads/APIC.AppImage') }}" download
                   class="block w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                    Download
                </a>
            </div>

            {{-- macOS --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-lg hover:border-teal-300 transition text-center">
                <div class="w-14 h-14 mx-auto bg-slate-100 text-slate-700 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">macOS</h3>
                <p class="text-xs text-slate-500 mt-1 mb-5">macOS 11+ · .dmg · 52 MB</p>
                <a href="{{ asset('downloads/APIC.dmg') }}" download
                   class="block w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                    Download
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-10">
            File installer disimpan di folder
            <code class="px-1.5 py-0.5 bg-slate-200 text-slate-600 rounded">public/downloads/</code>
            — upload file build Anda agar tombol download aktif.
        </p>
    </div>
</section>

{{-- ================= PWA QUICK INSTALL ================= --}}
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-teal-600 rounded-2xl p-8 md:p-10 text-center shadow-lg">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Install Cepat (PWA)</h2>
            <p class="text-teal-50 mb-6">Cara termudah! Install langsung dari browser — bekerja di semua platform.</p>
            <button id="installBtn"
                    class="px-8 py-3 bg-white text-teal-700 font-bold rounded-lg hover:bg-teal-50 transition">
                Install APIC Sekarang
            </button>
            <p class="text-xs text-teal-100 mt-4">
                Tips: jika tombol tidak merespons, buka menu browser (⋮) → pilih "Install App" / "Add to Home Screen"
            </p>
        </div>
    </div>
</section>

{{-- ================= SYSTEM REQUIREMENTS ================= --}}
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-6">
        <div class="bg-slate-900 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-8 py-5 border-b border-slate-800">
                <h3 class="font-bold text-white text-lg">System Requirements</h3>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-4 divide-y divide-slate-800 md:divide-y-0 md:divide-x">
                <div class="p-6">
                    <p class="text-xs font-bold tracking-widest uppercase text-blue-400 mb-3">Windows</p>
                    <ul class="text-sm text-slate-300 space-y-2">
                        <li>• Windows 10+</li><li>• RAM 4 GB</li><li>• Storage 200 MB</li>
                    </ul>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold tracking-widest uppercase text-green-400 mb-3">Android</p>
                    <ul class="text-sm text-slate-300 space-y-2">
                        <li>• Android 8.0+</li><li>• RAM 2 GB</li><li>• Storage 100 MB</li>
                    </ul>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold tracking-widest uppercase text-orange-400 mb-3">Linux</p>
                    <ul class="text-sm text-slate-300 space-y-2">
                        <li>• Ubuntu 20.04+</li><li>• RAM 4 GB</li><li>• Storage 200 MB</li>
                    </ul>
                </div>
                <div class="p-6">
                    <p class="text-xs font-bold tracking-widest uppercase text-slate-300 mb-3">macOS</p>
                    <ul class="text-sm text-slate-300 space-y-2">
                        <li>• macOS 11+</li><li>• RAM 4 GB</li><li>• Storage 200 MB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= VERSION HISTORY ================= --}}
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-3xl mx-auto px-6">
        <div class="text-center mb-10">
            <span class="text-xs font-bold tracking-widest text-teal-600 uppercase">Changelog</span>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Version History</h2>
        </div>
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6">
            <div class="flex items-start justify-between mb-3 flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-teal-600 text-white text-xs font-bold rounded-full">v1.0.0</span>
                    <h3 class="font-bold text-slate-900">Initial Release</h3>
                </div>
                <span class="text-xs text-slate-500">{{ date('d F Y') }}</span>
            </div>
            <ul class="text-sm text-slate-600 space-y-1.5 ml-1 list-disc list-inside">
                <li>Peminjaman dan pengembalian alat</li>
                <li>Manajemen inventaris lengkap</li>
                <li>Notifikasi real-time</li>
                <li>Laporan dan analitik dashboard</li>
                <li>Multi-platform support (PWA + Desktop + Mobile)</li>
            </ul>
        </div>
    </div>
</section>

{{-- ================= CTA ================= --}}
<section class="py-16 bg-slate-900">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-6">Butuh bantuan instalasi?</h2>
        <a href="{{ route('contact') }}"
           class="inline-block px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
            Hubungi Tim Support
        </a>
    </div>
</section>

@include('partials.footer')

{{-- ================= PWA INSTALL SCRIPT ================= --}}
<script>
    let deferredPrompt = null;
    const installBtn = document.getElementById('installBtn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) {
                alert('Untuk install:\n\n• Chrome/Edge: klik ikon ⊕ di address bar\n• Safari iOS: tap Share → "Add to Home Screen"\n• Android Chrome: menu ⋮ → "Install app"');
                return;
            }
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') console.log('[APIC] User menerima install');
            deferredPrompt = null;
        });
    }
</script>

@include('partials.scripts')