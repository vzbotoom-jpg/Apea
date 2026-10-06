@include('partials.head')
@include('partials.navbar')
@include('partials.alerts')

{{-- ================= HERO SECTION (UMICORE STYLE) ================= --}}
<section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden bg-slate-900">
    
    {{-- 1. BACKGROUND IMAGE --}}
    {{-- Ganti URL di bawah ini dengan path gambar lokal Anda jika ada, misal: {{ asset('images/hero-bg.jpg') }} --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=2070&auto=format&fit=crop" 
             alt="Background Laboratorium Teknologi" 
             class="w-full h-full object-cover object-center opacity-60">
        
        {{-- Gradient Overlay untuk keterbacaan teks --}}
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/80 to-slate-900/40"></div>
    </div>

    {{-- 2. CONTENT CONTAINER --}}
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            
            {{-- KOLOM KIRI: TEKS DALAM KOTAK GLASSMORPHISM --}}
            <div class="bg-white/5 backdrop-blur-md border border-white/10 p-8 md:p-12 rounded-2xl shadow-2xl max-w-2xl transform transition hover:bg-white/10 duration-500">
                
                <span class="inline-block text-teal-400 font-bold tracking-widest uppercase text-xs mb-4 border border-teal-500/30 px-3 py-1 rounded-full bg-teal-900/20">
                    Sistem Manajemen Aset Sekolah
                </span>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-[1.1] mb-6">
                    Solusi Peminjaman Alat yang <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-cyan-300">Efisien</span> & Transparan.
                </h1>
                
                <p class="text-lg text-slate-300 mb-8 leading-relaxed">
                    Digitalisasi inventaris laboratorium dan bengkel SMK Muhammadiyah 1 Bantul. Kelola stok, peminjaman, dan pengembalian dalam satu platform terintegrasi.
                </p>
                
                <div class="flex flex-wrap gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-3.5 bg-teal-600 hover:bg-teal-500 text-white text-sm font-bold rounded-full transition shadow-lg shadow-teal-900/50 flex items-center gap-2">
                            Mulai Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="{{ route('about') }}" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 text-white text-sm font-bold rounded-full transition">
                            Pelajari Lebih Lanjut
                        </a>
                    @else
                        <a href="{{ route('user.alat.index') }}" class="px-8 py-3.5 bg-teal-600 hover:bg-teal-500 text-white text-sm font-bold rounded-full transition shadow-lg shadow-teal-900/50">
                            Lihat Katalog Alat
                        </a>
                    @endguest
                </div>
            </div>

            {{-- KOLOM KANAN: KARTU STATISTIK (GLASSMORPHISM) --}}
            <div class="hidden lg:block relative">
                {{-- Efek Glow di belakang kartu --}}
                <div class="absolute -inset-4 bg-teal-500/20 blur-3xl rounded-full opacity-50"></div>
                
                <div class="relative bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-2xl shadow-2xl">
                    <div class="flex justify-between items-end mb-8 border-b border-white/10 pb-4">
                        <div>
                            <p class="text-sm text-slate-300 font-medium">Total Inventaris</p>
                            <p class="text-4xl font-bold text-white mt-1">{{ $totalAlat }}</p>
                        </div>
                        <span class="text-xs font-bold text-teal-400 bg-teal-900/30 border border-teal-500/30 px-3 py-1 rounded-full flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                            LIVE DATA
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-sm text-slate-300 mb-1">Tersedia</p>
                            <p class="text-3xl font-semibold text-white">{{ $totalTersedia }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-300 mb-1">Dipinjam</p>
                            <p class="text-3xl font-semibold text-white">{{ $totalPeminjaman }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ================= FEATURES: GRID CLEAN ALA CORPORATE ================= --}}
<section class="py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="mb-16 max-w-2xl">
            <p class="text-teal-600 font-bold tracking-wide uppercase text-sm mb-2">Mengapa Memilih APIC?</p>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Standar Profesional untuk Pengelolaan Alat</h2>
            <p class="text-slate-600 text-lg">Kami menghadirkan standar pengelolaan aset profesional ke lingkungan pendidikan.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-teal-500 hover:shadow-xl transition duration-300 group rounded-xl">
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Keamanan Data</h3>
                <p class="text-slate-600 leading-relaxed">Setiap transaksi tercatat dalam log sistem yang aman, memastikan transparansi antara peminjam dan petugas.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-teal-500 hover:shadow-xl transition duration-300 group rounded-xl">
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Proses Instan</h3>
                <p class="text-slate-600 leading-relaxed">Verifikasi peminjaman dilakukan secara digital. Tidak ada lagi antrean panjang atau paperwork yang berbelit.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white p-8 border border-slate-200 hover:border-teal-500 hover:shadow-xl transition duration-300 group rounded-xl">
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Laporan Real-time</h3>
                <p class="text-slate-600 leading-relaxed">Pantau ketersediaan alat dan statistik penggunaan secara langsung melalui dashboard analitik.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= CARA KERJA (CONNECTING LINE) ================= --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-teal-600 font-bold tracking-wide uppercase text-sm mb-2">Alur Kerja</p>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Empat Langkah Sederhana</h2>
        </div>

        <div class="relative grid md:grid-cols-4 gap-10">
            {{-- Garis Penghubung (Hanya Desktop) --}}
            <div class="hidden md:block absolute top-6 left-[12%] right-[12%] h-0.5 bg-gradient-to-r from-teal-200 via-teal-500 to-teal-200"></div>

            {{-- Step 1 --}}
            <div class="relative text-center group">
                <span class="relative z-10 mx-auto w-12 h-12 rounded-full bg-white border-2 border-teal-500 text-teal-600 grid place-items-center shadow-md group-hover:bg-teal-600 group-hover:text-white transition duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <h3 class="mt-6 font-bold text-slate-900 text-lg">Cari & Pilih Alat</h3>
                <p class="mt-2 text-sm text-slate-600">Telusuri katalog berdasarkan nama, kategori, atau ketersediaan.</p>
            </div>

            {{-- Step 2 --}}
            <div class="relative text-center group">
                <span class="relative z-10 mx-auto w-12 h-12 rounded-full bg-white border-2 border-teal-500 text-teal-600 grid place-items-center shadow-md group-hover:bg-teal-600 group-hover:text-white transition duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <h3 class="mt-6 font-bold text-slate-900 text-lg">Ajukan Peminjaman</h3>
                <p class="mt-2 text-sm text-slate-600">Tentukan tanggal pinjam dan kembali, lalu kirim pengajuan.</p>
            </div>

            {{-- Step 3 --}}
            <div class="relative text-center group">
                <span class="relative z-10 mx-auto w-12 h-12 rounded-full bg-white border-2 border-teal-500 text-teal-600 grid place-items-center shadow-md group-hover:bg-teal-600 group-hover:text-white transition duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </span>
                <h3 class="mt-6 font-bold text-slate-900 text-lg">Verifikasi Petugas</h3>
                <p class="mt-2 text-sm text-slate-600">Petugas menyetujui dan melakukan serah terima alat secara tercatat.</p>
            </div>

            {{-- Step 4 --}}
            <div class="relative text-center group">
                <span class="relative z-10 mx-auto w-12 h-12 rounded-full bg-white border-2 border-teal-500 text-teal-600 grid place-items-center shadow-md group-hover:bg-teal-600 group-hover:text-white transition duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </span>
                <h3 class="mt-6 font-bold text-slate-900 text-lg">Pengembalian</h3>
                <p class="mt-2 text-sm text-slate-600">Alat diperiksa, kondisi dicatat, dan transaksi ditutup dengan laporan.</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= KATEGORI ALAT ================= --}}
<section class="py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <div>
                <p class="text-teal-600 font-bold tracking-wide uppercase text-sm mb-2">Kategori</p>
                <h2 class="text-3xl font-bold text-slate-900">Temukan Berdasarkan Kategori</h2>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($kategoris as $kategori)
                <div class="group p-6 rounded-xl bg-white border border-slate-200 hover:border-teal-500 hover:shadow-lg transition cursor-pointer">
                    <div class="w-12 h-12 rounded-lg bg-teal-50 text-teal-600 grid place-items-center mb-4 group-hover:bg-teal-600 group-hover:text-white transition">
                        {{-- Icon SVG Default untuk Kategori --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 group-hover:text-teal-600 transition">{{ $kategori->nama_kategori }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $kategori->alats_count }} alat terdaftar</p>
                </div>
            @empty
                <div class="col-span-full p-10 rounded-xl border border-dashed border-slate-300 text-center text-slate-500 text-sm bg-white">
                    Belum ada kategori yang terdaftar.
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ================= ALAT TERBARU (CATALOG PREVIEW) ================= --}}
<section id="alat" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <div>
                <p class="text-teal-600 font-bold tracking-wide uppercase text-sm mb-2">Katalog</p>
                <h2 class="text-3xl font-bold text-slate-900">Alat Terbaru</h2>
                <p class="text-slate-500 mt-2">Peralatan yang baru saja ditambahkan ke inventaris.</p>
            </div>
            <a href="{{ route('user.alat.index') }}" class="hidden md:flex items-center text-teal-600 font-semibold text-sm hover:text-teal-700 group">
                Lihat Semua Alat 
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        @if($alatTerbaru->isEmpty())
            <div class="text-center py-20 bg-slate-50 border border-dashed border-slate-300 rounded-xl">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-slate-500 text-lg">Belum ada data alat.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($alatTerbaru as $alat)
                    <div class="group cursor-pointer">
                        <div class="aspect-[4/3] bg-slate-100 rounded-xl overflow-hidden mb-4 relative shadow-sm group-hover:shadow-xl transition duration-300">
                            @if($alat->gambar)
                                <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2.5 py-1 text-xs font-bold text-slate-900 rounded-md shadow-sm">
                                {{ $alat->stok_tersedia > 0 ? 'Tersedia' : 'Habis' }}
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-teal-600 font-bold uppercase tracking-wider mb-1">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</p>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-teal-600 transition line-clamp-1">{{ $alat->nama_alat }}</h3>
                            <p class="text-sm text-slate-500 mt-1 font-mono">{{ $alat->kode_alat }}</p>
                            
                            <div class="mt-3 flex items-center justify-between">
                                @if($alat->harga_sewa_per_hari > 0)
                                    <span class="text-sm font-bold text-slate-900">Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}<span class="text-xs font-normal text-slate-400">/hari</span></span>
                                @else
                                    <span class="text-sm font-bold text-emerald-600">Gratis</span>
                                @endif
                                <a href="{{ route('user.alat.show', $alat) }}" class="text-teal-600 text-sm font-semibold hover:underline">Detail &rarr;</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <div class="mt-12 text-center md:hidden">
            <a href="{{ route('user.alat.index') }}" class="inline-block px-6 py-3 border border-slate-300 text-slate-900 font-semibold rounded-lg hover:bg-slate-50 transition">Lihat Semua</a>
        </div>
    </div>
</section>

{{-- ================= ALAT TERPOPULER (DARK SECTION) ================= --}}
@if($alatTerpopuler->isNotEmpty())
<section class="py-24 bg-slate-900 relative overflow-hidden">
    {{-- ✅ BACKGROUND IMAGE + OVERLAY --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80&w=2070&auto=format&fit=crop"
             alt="Background Teknologi Sirkuit"
             class="w-full h-full object-cover object-center opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-slate-900/85 to-slate-900"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="flex justify-between items-end mb-12">
            <div>
                <p class="text-teal-400 font-bold tracking-wide uppercase text-sm mb-2">Performa Tertinggi</p>
                <h2 class="text-3xl font-bold text-white">Alat Terpopuler</h2>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach($alatTerpopuler as $alat)
                <div class="p-6 rounded-xl bg-slate-800/50 border border-slate-700 hover:border-teal-500 hover:bg-slate-800 transition group backdrop-blur-sm">
                    <div class="flex items-center justify-between mb-4">
                        <span class="w-10 h-10 rounded-lg bg-teal-500/10 text-teal-400 grid place-items-center group-hover:bg-teal-500 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                        <span class="flex items-center gap-1 px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-400 text-xs font-bold border border-teal-500/20">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            {{ $alat->detail_peminjamans_count }}x
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-white group-hover:text-teal-400 transition">{{ $alat->nama_alat }}</h3>
                    <p class="text-xs text-slate-400 mt-1 font-mono">{{ $alat->kode_alat }}</p>
                    <a href="{{ route('user.alat.show', $alat) }}" class="inline-block mt-4 text-sm font-semibold text-teal-400 hover:text-teal-300 group-hover:translate-x-1 transition-transform">Lihat Detail &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= TESTIMONI ================= --}}
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-teal-600 font-bold tracking-wide uppercase text-sm mb-2">Testimoni</p>
            <h2 class="text-3xl font-bold text-slate-900">Apa Kata Pengguna Kami</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="p-8 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col h-full">
                    <div class="flex gap-1 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= ($testimonial['rating'] ?? 5) ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                        @endfor
                    </div>
                    <p class="text-slate-600 leading-relaxed flex-1 italic">"{{ $testimonial['message'] }}"</p>
                    <div class="flex items-center gap-4 mt-8 pt-6 border-t border-slate-100">
                        <span class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 text-white grid place-items-center text-lg font-bold shadow-md">
                            {{ strtoupper(substr($testimonial['name'], 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $testimonial['name'] }}</p>
                            <p class="text-xs text-slate-500">{{ $testimonial['role'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= CTA STRIP ================= --}}
<section class="bg-slate-900 py-20 relative overflow-hidden">
    {{-- ✅ BACKGROUND IMAGE + OVERLAY --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=2070&auto=format&fit=crop"
             alt="Background Laboratorium"
             class="w-full h-full object-cover object-center opacity-30">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/85 to-slate-900/60"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Siap mendigitalisasi laboratorium Anda?</h2>
        <p class="text-slate-400 mb-8 max-w-2xl mx-auto text-lg">Bergabunglah dengan ratusan siswa dan guru yang sudah menggunakan APIC untuk manajemen alat yang lebih baik.</p>
        
        @guest
            <a href="{{ route('register') }}" class="inline-block px-8 py-4 bg-teal-600 text-white font-bold hover:bg-teal-500 transition rounded-lg shadow-lg shadow-teal-900/50">
                Buat Akun Gratis
            </a>
        @else
            <a href="{{ route('user.dashboard') }}" class="inline-block px-8 py-4 bg-white text-slate-900 font-bold hover:bg-slate-200 transition rounded-lg shadow-lg">
                Masuk ke Dashboard
            </a>
        @endguest
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')