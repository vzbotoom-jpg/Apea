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
                    <a href="#register" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Akun & verifikasi</a>
                    <a href="#borrow" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Peminjaman</a>
                    <a href="#payment" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Pembayaran</a>
                    <a href="#return" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Pengembalian & denda</a>
                    <a href="#staff" class="block text-sm text-slate-600 hover:text-teal-600 border-l-2 border-transparent hover:border-teal-600 -ml-[17px] pl-4 py-1">Petugas & admin</a>
                </nav>
            </div>
        </div>

        <!-- Content -->
        <div class="lg:col-span-3 bg-white p-8 md:p-12 rounded-lg shadow-sm border border-slate-200">
            <h1 class="text-3xl font-bold text-slate-900 mb-8 pb-8 border-b border-slate-100">Panduan Pengguna APIC</h1>

            <div id="intro" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">1. Pendahuluan</h2>
                <p class="text-slate-600 leading-relaxed mb-4">
                    APIC membantu peminjam mengajukan dan memantau peminjaman alat laboratorium atau bengkel. Petugas memproses pengajuan, serah terima, pengembalian, serta verifikasi pembayaran; admin mengelola data dan pengaturan sistem.
                </p>
                <div class="bg-teal-50 border-l-4 border-teal-500 p-4 rounded-r-md">
                    <p class="text-sm text-teal-800"><strong>Catatan:</strong> Menu dan tindakan yang tersedia bergantung pada peran akun Anda. Gunakan email aktif agar dapat menerima tautan verifikasi dan notifikasi.</p>
                </div>
            </div>

            <div id="register" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">2. Akun dan Verifikasi Email</h2>
                <ol class="list-decimal list-inside space-y-3 text-slate-600 marker:text-teal-600 marker:font-bold">
                    <li>Klik tombol <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-sm">Daftar</span> di pojok kanan atas.</li>
                    <li>Isi formulir pendaftaran dengan data diri yang valid.</li>
                    <li>Buat password minimal 8 karakter dan konfirmasi password.</li>
                    <li>Klik <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-sm">Buat Akun</span>.</li>
                    <li>Buka tautan verifikasi yang dikirim ke email. Akses panel dan API memerlukan email yang sudah diverifikasi.</li>
                    <li>Setelah verifikasi, masuk menggunakan email dan password yang telah dibuat. Jika alamat email diubah dari profil, verifikasi ulang alamat baru sebelum melanjutkan.</li>
                </ol>
            </div>

            <div id="borrow" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">3. Mengajukan dan Mengelola Peminjaman</h2>
                <ol class="list-decimal list-inside space-y-3 text-slate-600 marker:text-teal-600 marker:font-bold">
                    <li>Cari alat dari katalog atau menu alat. Periksa stok yang tersedia.</li>
                    <li>Pada menu pengajuan, pilih alat, tanggal pinjam, dan tanggal kembali. Tanggal kembali harus setelah tanggal pinjam. Tambahkan catatan bila diperlukan.</li>
                    <li>Perhatikan kuota alat per tanggal yang ditetapkan sistem, lalu kirim pengajuan. Stok akan dipesan dan status pengajuan menjadi <strong>menunggu</strong>.</li>
                    <li>Pantau status dari riwayat atau detail transaksi. Setelah petugas memverifikasi pengajuan, bawa atau tampilkan QR transaksi pada halaman detail saat mengambil alat. Petugas memproses serah terima.</li>
                    <li>Pengajuan yang masih berstatus <strong>menunggu</strong> dapat dibatalkan dari detail transaksi. Untuk memperpanjang peminjaman yang sedang berlangsung, ajukan perpanjangan dan tunggu persetujuan petugas.</li>
                </ol>
                <p class="mt-4 text-sm text-slate-600">Biaya sewa dihitung berdasarkan harga alat dan lama peminjaman. Batas kuota dan maksimal perpanjangan mengikuti pengaturan admin.</p>
            </div>

            <div id="payment" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">4. Pembayaran</h2>
                <ol class="list-decimal list-inside space-y-3 text-slate-600 marker:text-teal-600 marker:font-bold">
                    <li>Pembayaran tersedia setelah pengajuan diverifikasi. Buka detail transaksi dan pilih <strong>Bayar Sekarang</strong> jika tagihan belum lunas.</li>
                    <li>Pilih salah satu metode yang tersedia. Metode dapat berupa tunai atau transfer/QRIS/dompet digital sesuai pilihan yang diaktifkan admin.</li>
                    <li>Untuk pembayaran digital, ikuti instruksi dan pindai QR tujuan yang ditampilkan. Untuk tunai, lakukan pembayaran di loket petugas.</li>
                    <li>Status pembayaran tetap menunggu sampai admin atau petugas memeriksa dan menyetujuinya. Jangan membayar berulang saat verifikasi masih berlangsung.</li>
                    <li>Jika belum mengirim pembayaran, instruksi berstatus menunggu dapat dibatalkan dengan mencentang konfirmasi belum membayar, lalu pilih metode kembali. Pembayaran yang sudah dikirim tidak boleh dibatalkan dengan cara ini.</li>
                </ol>
                <p class="mt-4 text-sm text-slate-600">Nominal tagihan dan sisa pembayaran ditampilkan sebelum memilih metode. Unggah bukti transfer tidak diperlukan pada alur pembayaran saat ini.</p>
            </div>

            <div id="return" class="mb-12 scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">5. Pengembalian dan Denda</h2>
                <p class="text-slate-600 leading-relaxed mb-4">
                    Kembalikan alat kepada petugas paling lambat pada tanggal jatuh tempo. Petugas mencatat kondisi alat dan menyelesaikan proses pengembalian; kondisi rusak atau hilang perlu disampaikan saat serah terima.
                </p>
                <p class="text-slate-600 leading-relaxed">
                    Sistem menghitung denda keterlambatan berdasarkan masa tenggang dan tarif yang dikonfigurasi admin, termasuk tarif per alat yang berlaku saat peminjaman. Pantau status, denda, dan sisa tagihan pada detail transaksi. Tagihan yang masih ada dapat dibayar melalui metode yang tersedia dan baru berstatus lunas setelah diverifikasi.
                </p>
            </div>

            <div id="staff" class="scroll-mt-24">
                <h2 class="text-xl font-bold text-slate-900 mb-4">6. Panduan Petugas dan Admin</h2>
                <ul class="list-disc list-inside space-y-3 text-slate-600 marker:text-teal-600">
                    <li><strong>Petugas:</strong> verifikasi pengajuan, proses serah terima dan pengembalian, tinjau kondisi alat, serta verifikasi pembayaran yang telah diterima.</li>
                    <li><strong>Scan QR:</strong> buka menu scan dan izinkan akses kamera. QR transaksi dari detail peminjaman membantu membuka transaksi untuk diproses tanpa mencari kode secara manual. Jika kamera tidak tersedia, cari alat menggunakan kode atau nama pada kolom pencarian.</li>
                    <li><strong>Admin:</strong> kelola alat, kategori, pengguna, dan laporan. Pada Pengaturan Sistem, admin dapat menetapkan kuota, masa tenggang, tarif denda, batas perpanjangan, metode pembayaran aktif, serta QR tujuan untuk metode non-tunai.</li>
                </ul>
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