@include('partials.head')
@include('partials.navbar')

{{-- Header --}}
<section class="relative bg-slate-900 py-24 overflow-hidden">
    {{-- ✅ BACKGROUND IMAGE + OVERLAY (sama seperti Beranda) --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=2070&auto=format&fit=crop"
             alt="Background Laboratorium Teknologi"
             class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/85 to-slate-900/50"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-400 text-[11px] font-bold tracking-widest uppercase mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
            Tentang Kami
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">Tentang APIC</h1>
        <p class="text-xl text-slate-300 max-w-3xl leading-relaxed">
            Inovasi manajemen aset pendidikan yang dikembangkan oleh siswa, untuk siswa.
            Kami berkomitmen menghadirkan efisiensi teknologi ke lingkungan sekolah.
        </p>
    </div>
</section>

{{-- Mission & Vision Split --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16">
        <div>
            <span class="text-teal-600 font-bold text-sm uppercase tracking-wider mb-4 block">Misi Kami</span>
            <h2 class="text-3xl font-bold text-slate-900 mb-6">Mendigitalisasi Proses Manual</h2>
            <p class="text-slate-600 leading-relaxed mb-6">
                APIC hadir untuk menggantikan buku catatan fisik yang rawan hilang dan sulit dilacak. Sistem kami memastikan setiap bor, mikroskop, atau kamera memiliki riwayat penggunaan yang jelas.
            </p>
            <ul class="space-y-4">
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-teal-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-slate-700">Transparansi data inventaris real-time.</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-teal-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-slate-700">Akuntabilitas peminjam dan petugas.</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-teal-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-slate-700">Efisiensi waktu administrasi sekolah.</span>
                </li>
            </ul>
        </div>
        <div class="bg-slate-900 p-10 rounded-lg text-white flex flex-col justify-center">
            <span class="text-teal-400 font-bold text-sm uppercase tracking-wider mb-4 block">Visi</span>
            <h3 class="text-2xl font-light leading-relaxed">
                "Menjadi standar emas pengelolaan fasilitas pendidikan di Indonesia, di mana teknologi mempermudah akses belajar bagi setiap siswa."
            </h3>
        </div>
    </div>
</section>

{{-- Team / Institution Info --}}
<section class="py-24 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-slate-900">Di Balik Layar</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 border border-slate-200">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-6 text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Tim Developer</h3>
                <p class="text-sm text-slate-500 uppercase tracking-wide mb-4">Rekayasa Perangkat Lunak</p>
                <p class="text-slate-600 text-sm">Siswa kelas XII RPL SMK Muhammadiyah 1 Bantul yang merancang arsitektur sistem.</p>
            </div>
            <div class="bg-white p-8 border border-slate-200">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-6 text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Pembimbing</h3>
                <p class="text-sm text-slate-500 uppercase tracking-wide mb-4">Guru Produktif</p>
                <p class="text-slate-600 text-sm">Pengawasan teknis dan validasi kebutuhan bisnis sekolah.</p>
            </div>
            <div class="bg-white p-8 border border-slate-200">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-6 text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Institusi</h3>
                <p class="text-sm text-slate-500 uppercase tracking-wide mb-4">SMK Muhammadiyah 1 Bantul</p>
                <p class="text-slate-600 text-sm">Pemilik aset dan pengguna utama sistem manajemen ini.</p>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')