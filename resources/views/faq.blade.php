<!-- resources/views/faq.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('partials.head')

<body class="font-sans antialiased bg-slate-50">
    @include('partials.navbar')

    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-800">❓ FAQ</h1>
                <div class="w-20 h-1 bg-teal-600 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-slate-500">Pertanyaan yang sering diajukan tentang Aplikasi Peminjaman Alat</p>
            </div>

            <div class="space-y-4" x-data="{ active: null }">
                <!-- FAQ 1 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 1 ? null : 1" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Apa itu APIC?</span>
                        <span class="text-teal-600" x-text="active === 1 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 1" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p><strong>APIC</strong> (Alat Pinjam Mudah Cepat) adalah sistem manajemen peminjaman alat berbasis web yang dirancang untuk memudahkan proses peminjaman dan pengembalian alat di lingkungan sekolah.</p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 2 ? null : 2" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Siapa yang dapat menggunakan aplikasi ini?</span>
                        <span class="text-teal-600" x-text="active === 2 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 2" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Aplikasi ini memiliki 3 jenis pengguna:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Admin</strong> - Mengelola semua data dan pengaturan</li>
                            <li><strong>Petugas</strong> - Memverifikasi dan memproses peminjaman/pengembalian</li>
                            <li><strong>User</strong> - Mengajukan peminjaman dan melihat riwayat</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 3 ? null : 3" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Bagaimana cara mendaftar?</span>
                        <span class="text-teal-600" x-text="active === 3 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 3" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Klik <a href="{{ route('register') }}" class="text-teal-600 hover:underline">Daftar</a> di halaman utama, isi form pendaftaran, lalu klik tombol <strong>Daftar</strong>. Akun Anda akan langsung aktif.</p>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 4 ? null : 4" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Berapa maksimal alat yang bisa dipinjam?</span>
                        <span class="text-teal-600" x-text="active === 4 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 4" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Maksimal <strong>2 alat</strong> per hari untuk setiap pengguna.</p>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 5 ? null : 5" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Apakah ada denda jika terlambat?</span>
                        <span class="text-teal-600" x-text="active === 5 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 5" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Ya. Denda akan dikenakan jika pengembalian terlambat <strong>lebih dari 2 hari</strong> dari tanggal jatuh tempo. Denda akan otomatis bertambah setiap hari.</p>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 6 ? null : 6" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Bagaimana cara melihat status peminjaman?</span>
                        <span class="text-teal-600" x-text="active === 6 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 6" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Masuk ke <strong>Riwayat Peminjaman</strong> di dashboard Anda. Semua status peminjaman akan ditampilkan dengan warna yang berbeda.</p>
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 7 ? null : 7" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Bagaimana cara mengembalikan alat?</span>
                        <span class="text-teal-600" x-text="active === 7 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 7" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Kembalikan alat langsung ke <strong>petugas/lab</strong>. Petugas akan memproses pengembalian di sistem dan memberitahu jika ada denda.</p>
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 8 ? null : 8" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Lupa password, apa yang harus dilakukan?</span>
                        <span class="text-teal-600" x-text="active === 8 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 8" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Hubungi <strong>Admin</strong> untuk mereset password Anda. Fitur reset password otomatis akan ditambahkan pada versi berikutnya.</p>
                    </div>
                </div>

                <!-- FAQ 9 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 9 ? null : 9" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Apakah ada biaya untuk menggunakan APIC?</span>
                        <span class="text-teal-600" x-text="active === 9 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 9" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Tidak ada biaya untuk menggunakan APIC. Ini adalah sistem internal yang dikembangkan untuk kepentingan sekolah.</p>
                    </div>
                </div>

                <!-- FAQ 10 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button @click="active = active === 10 ? null : 10" 
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition">
                        <span class="font-semibold text-slate-800">Bagaimana cara menghubungi tim pengembang?</span>
                        <span class="text-teal-600" x-text="active === 10 ? '−' : '+'"></span>
                    </button>
                    <div x-show="active === 10" x-transition class="px-5 pb-5 text-slate-600 leading-relaxed">
                        <p>Anda dapat menghubungi tim pengembang melalui:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Email: <a href="mailto:info@apic.sch.id" class="text-teal-600 hover:underline">info@apic.sch.id</a></li>
                            <li>Telepon: (0274) 1234567</li>
                            <li>Atau melalui halaman <a href="{{ route('contact') }}" class="text-teal-600 hover:underline">Kontak</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tombol Kembali -->
            <div class="mt-12 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </section>

    @include('partials.footer')
    @include('partials.scripts')
</body>
</html>