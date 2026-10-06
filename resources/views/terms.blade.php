<!-- resources/views/terms.blade.php -->
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
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-white">Syarat & Ketentuan</h1>
        <p class="mt-4 text-slate-400">Terakhir diperbarui: {{ date('d F Y') }}</p>
    </div>
</section>

{{-- ================= CONTENT ================= --}}
<section class="py-16 bg-slate-50">
    <div class="max-w-4xl mx-auto px-6 space-y-6">

        {{-- Intro --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <p class="text-slate-600 leading-relaxed">
                Selamat datang di <strong class="text-slate-900">APIC (Alat Pinjam Mudah Cepat)</strong>, sistem manajemen
                peminjaman alat SMK Muhammadiyah 1 Bantul. Dengan mendaftar, mengakses, atau menggunakan layanan ini,
                Anda dianggap telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan di bawah ini,
                termasuk <a href="{{ route('privacy') }}" class="text-teal-600 font-semibold hover:underline">Kebijakan Privasi</a> kami.
            </p>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-teal-600 rounded-2xl p-8 text-white shadow-card">
            <h2 class="font-bold text-lg mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Ringkasan Syarat & Ketentuan
            </h2>
            <ul class="space-y-2 text-sm text-teal-50">
                <li class="flex gap-2"><span>✅</span> Akun wajib menggunakan data asli dan dijaga kerahasiaannya.</li>
                <li class="flex gap-2"><span>✅</span> Maksimal peminjaman <strong>2 alat per hari</strong> per pengguna.</li>
                <li class="flex gap-2"><span>✅</span> Setiap peminjaman wajib melalui verifikasi petugas.</li>
                <li class="flex gap-2"><span>✅</span> Keterlambatan pengembalian dikenakan denda setelah masa tenggang 2 hari.</li>
                <li class="flex gap-2"><span>✅</span> Kerusakan/kehilangan alat menjadi tanggung jawab peminjam.</li>
            </ul>
        </div>

        {{-- 1. Definisi --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">1</span>
                <h2 class="text-lg font-bold text-slate-900">Definisi</h2>
            </div>
            <ul class="text-sm text-slate-600 leading-relaxed space-y-2 list-disc list-inside">
                <li><strong class="text-slate-900">APIC</strong> — platform digital peminjaman alat SMK Muhammadiyah 1 Bantul.</li>
                <li><strong class="text-slate-900">Pengguna</strong> — siswa/guru/staf yang memiliki akun terdaftar.</li>
                <li><strong class="text-slate-900">Petugas</strong> — personel yang memverifikasi peminjaman & pengembalian.</li>
                <li><strong class="text-slate-900">Admin</strong> — pengelola sistem dan data inventaris.</li>
                <li><strong class="text-slate-900">Alat</strong> — barang inventaris yang dapat dipinjam melalui sistem.</li>
                <li><strong class="text-slate-900">Denda</strong> — biaya akibat keterlambatan pengembalian.</li>
            </ul>
        </div>

        {{-- 2. Akun --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">2</span>
                <h2 class="text-lg font-bold text-slate-900">Akun & Pendaftaran</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>2.1. Pengguna wajib memberikan data yang <strong class="text-slate-900">benar, lengkap, dan terkini</strong> (nama, email, nomor telepon, alamat).</p>
                <p>2.2. Pengguna bertanggung jawab menjaga <strong class="text-slate-900">kerahasiaan password</strong> dan seluruh aktivitas pada akunnya.</p>
                <p>2.3. Pengguna dilarang meminjamkan/memindahtangankan akun kepada pihak lain.</p>
                <p>2.4. Admin berhak menangguhkan atau menonaktifkan akun yang melanggar ketentuan.</p>
            </div>
        </div>

        {{-- 3. Peminjaman --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">3</span>
                <h2 class="text-lg font-bold text-slate-900">Peminjaman Alat</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>3.1. Maksimal peminjaman adalah <strong class="text-slate-900">2 (dua) alat per hari</strong> untuk setiap pengguna.</p>
                <p>3.2. Pengguna wajib menentukan tanggal pinjam dan tanggal jatuh tempo saat mengajukan peminjaman.</p>
                <p>3.3. Setiap pengajuan bersifat <strong class="text-slate-900">menunggu</strong> hingga diverifikasi oleh petugas.</p>
                <p>3.4. Peminjaman dianggap sah setelah petugas melakukan verifikasi dan serah terima alat.</p>
                <p>3.5. Ketersediaan alat mengikuti stok real-time pada sistem.</p>
            </div>
        </div>

        {{-- 4. Pengembalian & Denda --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">4</span>
                <h2 class="text-lg font-bold text-slate-900">Pengembalian & Denda</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>4.1. Alat wajib dikembalikan paling lambat pada <strong class="text-slate-900">tanggal jatuh tempo</strong>.</p>
                <p>4.2. Tersedia <strong class="text-slate-900">masa tenggang 2 hari</strong>; denda berlaku mulai hari ke-3 keterlambatan.</p>
                <p>4.3. Denda dihitung <strong class="text-slate-900">per alat per hari</strong> sesuai tarif yang tercantum pada sistem dan bertambah otomatis setiap hari.</p>
                <p>4.4. Kondisi alat saat pengembalian diperiksa dan dicatat oleh petugas.</p>
                <p>4.5. Denda yang belum diselesaikan dapat menahan proses peminjaman berikutnya.</p>
            </div>
        </div>

        {{-- 5. Kerusakan & Kehilangan --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">5</span>
                <h2 class="text-lg font-bold text-slate-900">Kerusakan, Kehilangan & Ganti Rugi</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>5.1. Pengguna wajib menjaga alat dalam kondisi baik selama masa peminjaman.</p>
                <p>5.2. Kerusakan akibat kelalaian pengguna dikenakan <strong class="text-slate-900">biaya perbaikan</strong>.</p>
                <p>5.3. Alat yang hilang atau rusak berat wajib diganti dengan <strong class="text-slate-900">barang setara atau nilai pengganti</strong> yang ditetapkan sekolah.</p>
                <p>5.4. Kerusakan wajar (aus) akibat pemakaian normal bukan tanggung jawab pengguna.</p>
            </div>
        </div>

        {{-- 6. Larangan --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-red-600 text-white grid place-items-center font-bold shrink-0">6</span>
                <h2 class="text-lg font-bold text-slate-900">Larangan</h2>
            </div>
            <ul class="text-sm text-slate-600 leading-relaxed space-y-2 list-disc list-inside">
                <li>Meminjamkan ulang alat kepada pihak lain tanpa izin petugas.</li>
                <li>Menggunakan alat di luar kepentingan pendidikan/sekolah.</li>
                <li>Membongkar, memodifikasi, atau menghilangkan bagian alat.</li>
                <li>Memberikan data palsu saat pendaftaran atau peminjaman.</li>
                <li>Melakukan tindakan yang mengganggu keamanan/kestabilan sistem.</li>
            </ul>
        </div>

        {{-- 7. Sanksi --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-red-600 text-white grid place-items-center font-bold shrink-0">7</span>
                <h2 class="text-lg font-bold text-slate-900">Sanksi</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-2">
                <p>7.1. Pelanggaran terhadap ketentuan ini dapat mengakibatkan:</p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li>Peringatan tertulis melalui sistem;</li>
                    <li>Pembekuan hak peminjaman sementara;</li>
                    <li>Penonaktifan akun permanen;</li>
                    <li>Pelaporan kepada pihak sekolah untuk pembinaan.</li>
                </ul>
            </div>
        </div>

        {{-- 8. Perubahan & Kontak --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <div class="flex items-center gap-4 mb-4">
                <span class="w-10 h-10 rounded-lg bg-teal-600 text-white grid place-items-center font-bold shrink-0">8</span>
                <h2 class="text-lg font-bold text-slate-900">Perubahan Ketentuan & Kontak</h2>
            </div>
            <div class="text-sm text-slate-600 leading-relaxed space-y-3">
                <p>8.1. Syarat & Ketentuan ini dapat diperbarui sewaktu-waktu; perubahan diumumkan melalui sistem atau email.</p>
                <p>8.2. Dengan tetap menggunakan layanan setelah perubahan, pengguna dianggap menyetujui ketentuan baru.</p>
                <p>8.3. Pertanyaan mengenai dokumen ini dapat ditujukan ke:</p>
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
            <a href="{{ route('privacy') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 transition">
                Baca Kebijakan Privasi
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')