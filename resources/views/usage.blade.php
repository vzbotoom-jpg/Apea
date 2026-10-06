<!-- resources/views/usage.blade.php -->
@include('partials.head')
@include('partials.navbar')

{{-- ================= HEADER ================= --}}
<section class="relative bg-slate-900 py-20 overflow-hidden">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:36px_36px]"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl"></div>

    <div class="relative max-w-4xl mx-auto px-6 text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-400 text-[11px] font-bold tracking-widest uppercase mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
            Dokumen Legal
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-white">Kebijakan Penggunaan</h1>
        <p class="mt-4 text-slate-400">Terakhir diperbarui: {{ date('d F Y') }}</p>
    </div>
</section>

{{-- ================= CONTENT ================= --}}
<section class="py-16 bg-slate-50">
    <div class="max-w-4xl mx-auto px-6 space-y-6">

        {{-- Intro --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <p class="text-slate-600 leading-relaxed">
                Kebijakan Penggunaan ini mengatur cara Anda boleh menggunakan
                <strong class="text-slate-900">APIC (Alat Pinjam Mudah Cepat)</strong>. Dengan menggunakan layanan ini,
                Anda setuju untuk mematuhi seluruh ketentuan di bawah ini, serta
                <a href="{{ route('terms') }}" class="text-teal-600 font-semibold hover:underline">Syarat & Ketentuan</a> dan
                <a href="{{ route('privacy') }}" class="text-teal-600 font-semibold hover:underline">Kebijakan Privasi</a> kami.
            </p>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-teal-600 rounded-2xl p-8 text-white shadow-card">
            <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Ringkasan Aturan Utama
            </h2>
            <ul class="space-y-2 text-sm text-teal-50">
                <li class="flex gap-2"><span>✅</span> Layanan hanya untuk kepentingan pendidikan SMK Muhammadiyah 1 Bantul.</li>
                <li class="flex gap-2"><span>✅</span> Maksimal peminjaman <strong>2 alat per hari</strong> per pengguna.</li>
                <li class="flex gap-2"><span>✅</span> Alat wajib dikembalikan tepat waktu; denda berlaku setelah masa tenggang 2 hari.</li>
                <li class="flex gap-2"><span>✅</span> Dilarang menyalahgunakan akun, alat, maupun sistem.</li>
            </ul>
        </div>

        {{-- 1 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">1</span>
                <h2 class="text-lg font-bold text-slate-900">Tujuan & Ruang Lingkup</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>1.1. APIC disediakan untuk mendukung kegiatan pembelajaran dan praktikum di lingkungan SMK Muhammadiyah 1 Bantul.</p>
                <p>1.2. Kebijakan ini berlaku bagi seluruh pengguna (siswa, guru, staf), petugas, dan administrator.</p>
            </div>
        </div>

        {{-- 2 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">2</span>
                <h2 class="text-lg font-bold text-slate-900">Akun & Tanggung Jawab Pengguna</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>2.1. Gunakan data asli saat pendaftaran dan jaga kerahasiaan kata sandi Anda.</p>
                <p>2.2. Seluruh aktivitas yang terjadi melalui akun Anda menjadi tanggung jawab Anda.</p>
                <p>2.3. Segera laporkan kepada petugas/admin jika akun diduga disalahgunakan.</p>
            </div>
        </div>

        {{-- 3 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">3</span>
                <h2 class="text-lg font-bold text-slate-900">Aturan Peminjaman & Pengembalian</h2>
            </div>
            <ul class="text-sm text-slate-600 leading-relaxed space-y-2 list-disc list-inside">
                <li>Maksimal <strong class="text-slate-900">2 alat per hari</strong> per pengguna.</li>
                <li>Peminjaman sah setelah diverifikasi petugas dan dilakukan serah terima.</li>
                <li>Alat wajib dikembalikan paling lambat tanggal jatuh tempo.</li>
                <li>Masa tenggang <strong class="text-slate-900">2 hari</strong>; denda per alat per hari berlaku mulai hari ke-3.</li>
                <li>Kerusakan/kehilangan akibat kelalaian menjadi tanggung jawab peminjam (perbaikan atau ganti rugi).</li>
                <li>Perpanjangan hanya memungkinkan jika alat tidak sedang diantri pengguna lain dan disetujui petugas.</li>
            </ul>
        </div>

        {{-- 4 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-red-600 text-white grid place-items-center font-bold shrink-0">4</span>
                <h2 class="text-lg font-bold text-slate-900">Penggunaan yang Dilarang</h2>
            </div>
            <ul class="text-sm text-slate-600 leading-relaxed space-y-2 list-disc list-inside">
                <li>Menggunakan layanan untuk kepentingan komersial tanpa izin tertulis sekolah.</li>
                <li>Meminjamkan ulang alat kepada pihak lain di luar sistem.</li>
                <li>Membongkar, memodifikasi, atau menghilangkan bagian alat.</li>
                <li>Mencoba mengakses, mengganggu, atau merusak sistem (termasuk akun orang lain).</li>
                <li>Memberikan data palsu atau menyesatkan dalam transaksi apa pun.</li>
                <li>Mengunggah konten berbahaya/terlarang melalui fitur sistem.</li>
            </ul>
        </div>

        {{-- 5 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-red-600 text-white grid place-items-center font-bold shrink-0">5</span>
                <h2 class="text-lg font-bold text-slate-900">Sanksi & Penangguhan</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>5.1. Pelanggaran dapat mengakibatkan: peringatan, pembekuan hak pinjam sementara, penonaktifan akun, hingga pelaporan kepada pihak sekolah.</p>
                <p>5.2. Denda atau ganti rugi yang belum diselesaikan dapat menahan seluruh layanan peminjaman akun terkait.</p>
            </div>
        </div>

        {{-- 6 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">6</span>
                <h2 class="text-lg font-bold text-slate-900">Ketersediaan Layanan & Batas Tanggung Jawab</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>6.1. Kami berupaya menjaga layanan tetap tersedia, namun tidak menjamin bebas gangguan (pemeliharaan, force majeure, dll).</p>
                <p>6.2. Sekolah tidak bertanggung jawab atas kerugian tidak langsung akibat penggunaan atau ketidakmampuan penggunaan layanan.</p>
            </div>
        </div>

        {{-- 7 --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">7</span>
                <h2 class="text-lg font-bold text-slate-900">Perubahan Kebijakan & Kontak</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-3">
                <p>7.1. Kebijakan ini dapat berubah sewaktu-waktu; perubahan diumumkan melalui sistem atau email.</p>
                <p>7.2. Pertanyaan? Hubungi kami:</p>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-1">
                    <p>📧 Email: <a href="mailto:info@apic.sch.id" class="text-teal-600 font-semibold">info@apic.sch.id</a></p>
                    <p>📞 Telepon: (0274) 1234567</p>
                    <p>📍 Alamat: Jl. Pendidikan No. 123, Bantul</p>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
            <a href="{{ route('privacy-choices') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition">
                Kelola Pilihan Privasi
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')