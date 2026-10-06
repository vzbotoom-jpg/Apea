<!-- resources/views/privacy.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('partials.head')

<body class="font-sans antialiased bg-slate-50">
    @include('partials.navbar')

    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-slate-800">🔒 Kebijakan Privasi</h1>
                <div class="w-20 h-1 bg-teal-600 mx-auto mt-4 rounded-full"></div>
                <p class="mt-4 text-slate-500">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>

            <div class="prose prose-slate max-w-none space-y-6 text-slate-600 leading-relaxed">
                <p>
                    Kami di <strong>APIC (Alat Pinjam Mudah Cepat)</strong> menghargai privasi Anda. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan aplikasi kami.
                </p>

                <div class="bg-teal-50 border border-teal-200 rounded-xl p-6">
                    <h3 class="font-bold text-teal-800">Ringkasan Kebijakan Privasi</h3>
                    <ul class="mt-2 space-y-1 text-sm text-teal-700">
                        <li>✅ Kami hanya mengumpulkan data yang diperlukan</li>
                        <li>✅ Data Anda aman dan tidak dibagikan ke pihak ketiga</li>
                        <li>✅ Anda dapat mengakses dan mengubah data Anda kapan saja</li>
                        <li>✅ Kami menggunakan enkripsi untuk melindungi data sensitif</li>
                    </ul>
                </div>

                <!-- 1. Informasi yang Kami Kumpulkan -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">1. Informasi yang Kami Kumpulkan</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Data Registrasi:</strong> Nama, email, nomor telepon, dan alamat</li>
                        <li><strong>Data Aktivitas:</strong> Riwayat peminjaman, pengembalian, dan denda</li>
                        <li><strong>Data Teknis:</strong> IP address, browser, dan perangkat yang digunakan</li>
                        <li><strong>Kata Sandi:</strong> Disimpan dalam bentuk terenkripsi (hash)</li>
                    </ul>
                </div>

                <!-- 2. Cara Kami Menggunakan Informasi -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">2. Cara Kami Menggunakan Informasi</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Memproses transaksi peminjaman alat</li>
                        <li>Memberikan notifikasi terkait status peminjaman</li>
                        <li>Mengelola akun dan preferensi pengguna</li>
                        <li>Analisis dan peningkatan layanan</li>
                        <li>Keamanan dan pencegahan penipuan</li>
                    </ul>
                </div>

                <!-- 3. Perlindungan Data -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">3. Perlindungan Data</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Enkripsi SSL/TLS untuk semua transmisi data</li>
                        <li>Kata sandi disimpan dengan hashing (bcrypt)</li>
                        <li>Akses terbatas hanya untuk pengguna yang berwenang</li>
                        <li>Backup data secara berkala</li>
                    </ul>
                </div>

                <!-- 4. Berbagi Data -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">4. Berbagi Data dengan Pihak Ketiga</h3>
                    <p>
                        Kami <strong>TIDAK</strong> menjual, menyewakan, atau membagikan data pribadi Anda dengan pihak ketiga untuk tujuan pemasaran. Data hanya akan dibagikan jika:
                    </p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Diperlukan untuk memproses transaksi (misalnya, notifikasi email)</li>
                        <li>Diperlukan oleh hukum atau peraturan yang berlaku</li>
                        <li>Ada persetujuan tertulis dari Anda</li>
                    </ul>
                </div>

                <!-- 5. Hak Anda -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">5. Hak Anda sebagai Pengguna</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Akses:</strong> Anda dapat melihat data Anda di halaman Profil</li>
                        <li><strong>Perbaiki:</strong> Anda dapat mengubah data Anda di halaman Profil</li>
                        <li><strong>Hapus:</strong> Anda dapat meminta penghapusan akun kepada Admin</li>
                        <li><strong>Batalkan:</strong> Anda dapat membatalkan peminjaman yang masih menunggu</li>
                    </ul>
                </div>

                <!-- 6. Cookie -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">6. Penggunaan Cookie</h3>
                    <p>
                        Kami menggunakan cookie untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Menjaga sesi login Anda tetap aktif</li>
                        <li>Mengingat preferensi Anda</li>
                        <li>Analisis penggunaan aplikasi</li>
                    </ul>
                    <p class="mt-2">Anda dapat menonaktifkan cookie di pengaturan browser Anda.</p>
                </div>

                <!-- 7. Perubahan Kebijakan -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">7. Perubahan Kebijakan Privasi</h3>
                    <p>
                        Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan diumumkan melalui notifikasi di aplikasi atau email.
                    </p>
                </div>

                <!-- 8. Kontak -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800">8. Hubungi Kami</h3>
                    <p>
                        Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami:
                    </p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Email: <a href="mailto:info@apic.sch.id" class="text-teal-600 hover:underline">info@apic.sch.id</a></li>
                        <li>Telepon: (0274) 1234567</li>
                        <li>Alamat: Jl. Pendidikan No. 123, Bantul</li>
                    </ul>
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