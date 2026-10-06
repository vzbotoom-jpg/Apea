@include('partials.head')
@include('partials.navbar')

<div class="bg-slate-50 min-h-screen py-16 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-4 gap-12">
        
        <!-- Sidebar TOC -->
        <div class="lg:col-span-1">
            <div class="sticky top-24">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Daftar Isi</h3>
                <nav class="space-y-2 border-l border-slate-200 pl-4">
                    <a href="#intro" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Pendahuluan</a>
                    <a href="#register" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Registrasi</a>
                    <a href="#borrow" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Meminjam Alat</a>
                    <a href="#return" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Pengembalian</a>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="lg:col-span-3 bg-white p-8 md:p-12 rounded-lg shadow-sm border border-slate-200">
            <h1 class="text-3xl font-bold text-slate-900 mb-8 pb-8 border-b border-slate-100">Panduan Pengguna APIC</h1>

            <div id="intro" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">1. Pendahuluan</h2>
                <p class="text-slate-600 leading-relaxed mb-4">
                    Selamat datang di APIC. Sistem ini dirancang untuk mempermudah siswa dan guru dalam mengelola peminjaman alat laboratorium dan bengkel.
                </p>
                <div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-r-md">
                    <p class="text-sm text-teal-800"><strong>Catatan:</strong> Pastikan Anda menggunakan email sekolah yang aktif saat mendaftar.</p>
                </div>
            </div>

            <div id="register" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">2. Registrasi Akun</h2>
                <ol class="list-decimal list-inside space-y-3 text-slate-600 marker:text-teal-600 marker:font-bold">
                    <li>Klik tombol <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-sm">Daftar</span> di pojok kanan atas.</li>
                    <li>Isi formulir pendaftaran dengan data diri yang valid.</li>
                    <li>Password minimal 8 karakter kombinasi huruf dan angka.</li>
                    <li>Klik <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-sm">Buat Akun</span>.</li>
                    <li>Verifikasi email Anda melalui tautan yang dikirimkan.</li>
                    <li>Setelah verifikasi, Anda dapat masuk ke sistem menggunakan email dan password yang telah dibuat.</li>
                </ol>
            </div>

            <div id="borrow" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">3. Cara Meminjam Alat</h2>
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div class="border border-slate-200 p-6 rounded-md">
                        <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">1</div>
                        <h4 class="font-bold text-slate-900 mb-2">Cari Alat</h4>
                        <p class="text-sm text-slate-600">Gunakan fitur pencarian atau filter kategori untuk menemukan alat.</p>
                    </div>
                    <div class="border border-slate-200 p-6 rounded-md">
                        <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">2</div>
                        <h4 class="font-bold text-slate-900 mb-2">Ajukan Pinjam</h4>
                        <p class="text-sm text-slate-600">Pilih tanggal dan klik ajukan. Tunggu verifikasi petugas.</p>
                    </div>
                </div>
            </div>
            
             <div id="return" class="scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">4. Pengembalian</h2>
                <p class="text-slate-600 leading-relaxed">
                    Kembalikan alat ke petugas sesuai jadwal. Keterlambatan akan dikenakan denda sesuai ketentuan sekolah yang tertera di dashboard Anda.
                </p>
            </div>

            <div class="mt-16 pt-8 border-t border-slate-100">
                <a href="{{ route('home') }}" class="text-teal-600 font-semibold hover:text-teal-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
@include('partials.scripts')