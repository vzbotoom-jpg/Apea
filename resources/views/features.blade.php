<!-- resources/views/features.blade.php -->
@include('partials.head')
@include('partials.navbar')

{{-- ================= HERO ================= --}}
<section class="relative bg-slate-900 py-20 overflow-hidden">
    {{-- ✅ BACKGROUND IMAGE + OVERLAY (sama seperti Beranda) --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=2070&auto=format&fit=crop"
             alt="Background Teknologi Sirkuit"
             class="w-full h-full object-cover object-center opacity-30">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900/80 to-slate-900"></div>
    </div>
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl"></div>

    <div class="relative z-10 max-w-5xl mx-auto px-6 text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-400 text-[11px] font-bold tracking-widest uppercase mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
            Produk APIC
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-6 leading-tight">
            Fitur yang <span class="bg-gradient-to-r from-teal-400 to-cyan-400 bg-clip-text text-transparent">Siap Membantu</span> Anda
        </h1>
        <p class="text-xl text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Semua yang Anda butuhkan untuk meminjam, mengelola, dan memantau alat
            laboratorium & bengkel — dalam satu platform terintegrasi.
        </p>
    </div>
</section>

{{-- ================= FITUR UNTUK USER ================= --}}
<section class="py-20 bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold tracking-widest text-teal-600 uppercase">Untuk Peminjam</span>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Fitur yang Bisa Diakses User</h2>
            <p class="mt-3 text-slate-600">Dirancang agar proses peminjaman alat semudah dan setransparan mungkin.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- 1. Katalog --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Katalog Alat</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Telusuri alat berdasarkan nama, kategori, dan ketersediaan stok secara real-time.</p>
            </div>
            {{-- 2. Pengajuan --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Pengajuan Peminjaman</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Ajukan hingga 2 alat per hari dengan memilih tanggal pinjam & jatuh tempo sendiri.</p>
            </div>
            {{-- 3. Tracking --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Tracking Status</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Pantau status peminjaman: menunggu → diverifikasi → dipinjam → dikembalikan.</p>
            </div>
            {{-- 4. Perpanjangan --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Perpanjangan Online</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Butuh waktu lebih? Ajukan perpanjangan hingga 7 hari langsung dari halaman peminjaman.</p>
            </div>
            {{-- 5. Pembatalan --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Pembatalan Mandiri</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Batalkan pengajuan selama status masih "menunggu" tanpa perlu menghubungi petugas.</p>
            </div>
            {{-- 6. Denda transparan --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Info Denda Transparan</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Masa tenggang 2 hari & perhitungan denda otomatis yang terlihat jelas di detail peminjaman.</p>
            </div>
            {{-- 7. Notifikasi --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Notifikasi Real-time</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Dapatkan pemberitahuan verifikasi, pengingat jatuh tempo H-1, dan status perpanjangan.</p>
            </div>
            {{-- 8. Profil --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-teal-300 transition group">
                <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Manajemen Profil</h3>
                <p class="text-sm text-slate-600 leading-relaxed">Kelola data pribadi, email, dan password Anda kapan saja dari halaman profil.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= FITUR PETUGAS & ADMIN ================= --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold tracking-widest text-teal-600 uppercase">Operasional</span>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Fitur untuk Petugas & Admin</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            {{-- Petugas --}}
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-teal-600 text-white rounded-lg flex items-center justify-center font-bold">P</div>
                    <h3 class="text-lg font-bold text-slate-900">Panel Petugas</h3>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Verifikasi & persetujuan pengajuan peminjaman',
                        'Serah terima alat sekali klik',
                        'Proses pengembalian + cetak struk',
                        'Scan QR Code alat untuk akses detail instan',
                        'Persetujuan pengajuan perpanjangan',
                        'Laporan pengembalian harian',
                    ] as $fitur)
                        <li class="flex items-start gap-2.5 text-sm text-slate-600">
                            <svg class="w-4 h-4 text-teal-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $fitur }}
                        </li>
                    @endforeach
                </ul>
            </div>
            {{-- Admin --}}
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-teal-600 text-white rounded-lg flex items-center justify-center font-bold">A</div>
                    <h3 class="text-lg font-bold text-white">Panel Admin</h3>
                </div>
                <ul class="space-y-3">
                    @foreach([
                        'Manajemen kategori, alat & stok',
                        'Manajemen user & role (aktif/nonaktif)',
                        'Pengaturan sistem: batas pinjam, tenggang & tarif denda',
                        'Log aktivitas seluruh pengguna',
                        'Laporan analitik + export PDF/Excel',
                        'Monitoring peminjaman terlambat',
                    ] as $fitur)
                        <li class="flex items-start gap-2.5 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-teal-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $fitur }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ================= FITUR PLATFORM ================= --}}
<section class="py-20 bg-slate-900">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold tracking-widest text-teal-400 uppercase">Platform</span>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-white">Andal di Semua Perangkat</h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-6 hover:border-teal-500/50 transition">
                <div class="w-10 h-10 bg-teal-500/20 border border-teal-500/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-bold text-white text-sm mb-1">PWA Installable</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Install seperti aplikasi native di Windows, Android, Linux, dan iOS langsung dari browser.</p>
            </div>
            <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-6 hover:border-teal-500/50 transition">
                <div class="w-10 h-10 bg-teal-500/20 border border-teal-500/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="font-bold text-white text-sm mb-1">Keamanan Data</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Password ter-hash (bcrypt), koneksi HTTPS, dan verifikasi email untuk akun baru.</p>
            </div>
            <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-6 hover:border-teal-500/50 transition">
                <div class="w-10 h-10 bg-teal-500/20 border border-teal-500/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-2.572-1.065c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-bold text-white text-sm mb-1">Kontrol Privasi</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Halaman Privacy Choices memberi Anda kendali penuh atas data & preferensi Anda.</p>
            </div>
            <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-6 hover:border-teal-500/50 transition">
                <div class="w-10 h-10 bg-teal-500/20 border border-teal-500/30 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-bold text-white text-sm mb-1">Cepat & Responsif</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Tampilan optimal di HP, tablet, maupun desktop dengan performa ringan.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= CTA ================= --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-6">Siap mencoba semua fitur ini?</h2>
        <div class="flex flex-wrap justify-center gap-3">
            @guest
                <a href="{{ route('register') }}" class="px-8 py-3 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition">Buat Akun Gratis</a>
            @else
                <a href="{{ route('user.alat.index') }}" class="px-8 py-3 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition">Buka Katalog Alat</a>
            @endguest
            <a href="{{ route('guide') }}" class="px-8 py-3 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition">Baca Panduan</a>
        </div>
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')