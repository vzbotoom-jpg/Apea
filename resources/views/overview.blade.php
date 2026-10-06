<!-- resources/views/overview.blade.php -->
@include('partials.head')
@include('partials.navbar')

{{-- ================= HERO (EDITORIAL, ALA CLAUDE) ================= --}}
<section class="bg-[#F7F4EC] border-b border-[#E7E2D6]">
    <div class="max-w-7xl mx-auto px-6 py-24 lg:py-28 grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <span class="inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase text-teal-700 border border-teal-700/30 bg-teal-700/5 px-3 py-1 rounded-full mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                APIC Platform
            </span>
            <h1 class="font-serif text-4xl md:text-5xl lg:text-[3.4rem] leading-[1.08] text-slate-900 mb-6">
                Satu platform untuk seluruh operasional alat sekolah.
            </h1>
            <p class="text-lg text-slate-600 leading-relaxed mb-10 max-w-xl">
                Dari katalog inventaris hingga verifikasi pembayaran — APIC menyediakan
                seluruh bangunan sistem yang dibutuhkan laboratorium & bengkel
                SMK Muhammadiyah 1 Bantul, siap dipakai dalam hitungan menit.
            </p>
            <div class="flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="px-7 py-3.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-lg transition">
                        Mulai Gunakan
                    </a>
                    <a href="{{ route('guide') }}" class="px-7 py-3.5 bg-white border border-slate-300 hover:border-slate-400 text-slate-800 text-sm font-bold rounded-lg transition">
                        Baca Panduan
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="px-7 py-3.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-lg transition">
                        Buka Dashboard
                    </a>
                @endguest
            </div>
        </div>

        {{-- Code Window Quickstart --}}
        <div class="relative">
            <div class="absolute -inset-6 bg-teal-600/10 rounded-3xl blur-2xl"></div>
            <div class="relative bg-slate-900 rounded-xl shadow-2xl border border-slate-800 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800 bg-slate-950/60">
                    <div class="flex gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    </div>
                    <span class="text-xs font-mono text-slate-500">apic — quickstart</span>
                </div>
<pre class="p-6 text-[13px] leading-relaxed font-mono text-slate-300 overflow-x-auto"><span class="text-slate-500"># 1 · User mengajukan peminjaman</span>
<span class="text-teal-400">POST</span> /user/peminjaman
{
  "alat_ids": [<span class="text-amber-300">2</span>, <span class="text-amber-300">1</span>],
  "tanggal_pinjam": <span class="text-emerald-300">"2026-08-15"</span>,
  "tanggal_jatuh_tempo": <span class="text-emerald-300">"2026-08-18"</span>
}

<span class="text-slate-500"># 2 · Sistem merespons & memberi notifikasi</span>
<span class="text-emerald-400">201</span> · menunggu verifikasi petugas
{
  "kode": <span class="text-emerald-300">"PMJ-009"</span>,
  "status": <span class="text-emerald-300">"menunggu"</span>,
  "notifikasi_petugas": <span class="text-emerald-300">true</span>
}</pre>
            </div>
        </div>
    </div>
</section>

{{-- ================= STATS STRIP ================= --}}
<section class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-2 md:grid-cols-4 gap-8">
        @foreach([
            ['label' => 'Alat Terdaftar', 'value' => $stats['alat']],
            ['label' => 'Kategori Aktif', 'value' => $stats['kategori']],
            ['label' => 'Transaksi Dibuat', 'value' => $stats['peminjaman']],
            ['label' => 'Pengguna Terdaftar', 'value' => $stats['user']],
        ] as $s)
            <div class="text-center md:text-left">
                <p class="font-serif text-3xl md:text-4xl text-slate-900">{{ $s['value'] }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ================= KAPABILITAS INTI ================= --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="max-w-2xl mb-16">
            <p class="text-teal-700 font-bold tracking-widest uppercase text-xs mb-3">Kemampuan Platform</p>
            <h2 class="font-serif text-3xl md:text-4xl text-slate-900">Semua yang dibutuhkan operasional, dalam satu sistem.</h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-12">
            @foreach([
                ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 't' => 'Inventaris Real-time', 'd' => 'Stok alat berkurang & pulih otomatis mengikuti verifikasi, pembatalan, dan pengembalian.'],
                ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 't' => 'Transaksi End-to-End', 'd' => 'Pengajuan → verifikasi → serah terima → pengembalian → struk, seluruhnya tercatat rapi.'],
                ['icon' => 'M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2m0-8h18', 't' => 'QR Code Serah Terima', 'd' => 'Setiap alat & transaksi memiliki QR. Petugas scan untuk verifikasi instan tanpa input manual.'],
                ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 't' => 'Pembayaran Digital', 'd' => 'Transfer bank & e-wallet (DANA, ShopeePay, GoPay) dengan unggah bukti + verifikasi petugas. Denda dihitung otomatis.'],
                ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 't' => 'Notifikasi & Pengingat', 'd' => 'Persetujuan, pengingat jatuh tempo H-1, status perpanjangan, dan pembayaran — real-time.'],
                ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 't' => 'Laporan & Analitik', 'd' => 'Dashboard statistik, alat terpopuler, total pemasukan, serta export PDF/Excel sekali klik.'],
            ] as $f)
                <div class="border-t border-slate-200 pt-6 group">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center mb-5 group-hover:bg-teal-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">{{ $f['t'] }}</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $f['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= TIGA ROLE ================= --}}
<section class="py-24 bg-[#F7F4EC] border-y border-[#E7E2D6]">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-teal-700 font-bold tracking-widest uppercase text-xs mb-3">Dirancang per Peran</p>
            <h2 class="font-serif text-3xl md:text-4xl text-slate-900">Satu platform, tiga pengalaman berbeda.</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach([
                ['r' => 'User · Siswa', 'c' => 'bg-white', 'items' => ['Katalog publik & stok real-time', 'Pengajuan + perpanjangan online', 'QR transaksi untuk serah terima', 'Pembayaran transfer / e-wallet', 'Riwayat & status transparan']],
                ['r' => 'Petugas', 'c' => 'bg-white', 'items' => ['Verifikasi & serah terima sekali klik', 'Scan QR alat & transaksi', 'Proses pengembalian + struk', 'Verifikasi bukti pembayaran', 'Laporan pengembalian harian']],
                ['r' => 'Admin', 'c' => 'bg-slate-900', 'items' => ['Manajemen alat, kategori & user', 'Pengaturan denda, kuota & metode bayar', 'Log aktivitas seluruh pengguna', 'Total pemasukan & analitik', 'Export laporan PDF/Excel']],
            ] as $role)
                <div class="{{ $role['c'] }} rounded-2xl border {{ $role['c'] === 'bg-slate-900' ? 'border-slate-800' : 'border-slate-200' }} p-8">
                    <h3 class="font-serif text-xl {{ $role['c'] === 'bg-slate-900' ? 'text-white' : 'text-slate-900' }} mb-6">{{ $role['r'] }}</h3>
                    <ul class="space-y-3">
                        @foreach($role['items'] as $item)
                            <li class="flex items-start gap-2.5 text-sm {{ $role['c'] === 'bg-slate-900' ? 'text-slate-300' : 'text-slate-600' }}">
                                <svg class="w-4 h-4 text-teal-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= KEAMANAN ================= --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <p class="text-teal-400 font-bold tracking-widest uppercase text-xs mb-3">Keamanan & Kepercayaan</p>
            <h2 class="font-serif text-3xl md:text-4xl text-white mb-6">Dibangun dengan keamanan sebagai fondasi.</h2>
            <p class="text-slate-400 leading-relaxed mb-10">
                Setiap transaksi keuangan dan perubahan data di APIC terlacak,
                terverifikasi, dan terlindungi — sesuai standar aplikasi produksi.
            </p>
            <ul class="space-y-4">
                @foreach([
                    'Password ter-hash bcrypt + verifikasi email akun baru',
                    'Proteksi CSRF & rate-limit login anti brute-force',
                    'Akses berbasis role: user, petugas, admin',
                    'Bukti pembayaran tersimpan privat, hanya bisa dibuka pihak berwenang',
                    'Log aktivitas (activity log) untuk audit penuh',
                ] as $sec)
                    <li class="flex items-start gap-3 text-sm text-slate-300">
                        <span class="w-5 h-5 rounded-full bg-teal-500/20 border border-teal-500/40 text-teal-400 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        {{ $sec }}
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-slate-800/60 border border-slate-700 rounded-2xl p-8 font-mono text-[13px] leading-relaxed text-slate-300">
            <p class="text-slate-500 mb-4"># cuplikan log aktivitas</p>
            <p><span class="text-teal-400">[petugas]</span> memverifikasi peminjaman <span class="text-amber-300">PMJ-009</span></p>
            <p><span class="text-teal-400">[sistem]</span> mengirim pengingat jatuh tempo H-1</p>
            <p><span class="text-teal-400">[user]</span> mengunggah bukti pembayaran <span class="text-amber-300">Rp 6.000</span> via DANA</p>
            <p><span class="text-teal-400">[petugas]</span> konfirmasi LUNAS <span class="text-amber-300">PMJ-009</span></p>
            <p><span class="text-teal-400">[admin]</span> export laporan bulanan → <span class="text-emerald-300">PDF ✓</span></p>
        </div>
    </div>
</section>

{{-- ================= CTA ================= --}}
<section class="py-24 bg-white">
    <div class="max-w-3xl mx-auto px-6 text-center">
        <h2 class="font-serif text-3xl md:text-4xl text-slate-900 mb-6">Siap mendigitalisasi laboratorium Anda?</h2>
        <p class="text-slate-600 mb-10">Gratis untuk lingkungan sekolah. Mulai dalam hitungan menit.</p>
        <div class="flex justify-center gap-3">
            @guest
                <a href="{{ route('register') }}" class="px-8 py-3.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">Buat Akun Gratis</a>
            @else
                <a href="{{ route('user.alat.index') }}" class="px-8 py-3.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">Buka Katalog</a>
            @endguest
            <a href="{{ route('features') }}" class="px-8 py-3.5 bg-white border border-slate-300 hover:border-slate-400 text-slate-800 text-sm font-bold rounded-lg transition">Lihat Semua Fitur</a>
        </div>
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')