<!-- resources/views/privacy-choices.blade.php -->
@include('partials.head')
@include('partials.navbar')

{{-- ================= HEADER ================= --}}
<section class="relative bg-slate-900 py-20 overflow-hidden">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:36px_36px]"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl"></div>

    <div class="relative max-w-4xl mx-auto px-6 text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-400 text-[11px] font-bold tracking-widest uppercase mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
            Pusat Kontrol Privasi
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-white">Pilihan Privasi Anda</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">
            Anda memegang kendali. Atur bagaimana APIC mengumpulkan dan menggunakan data Anda.
            Selengkapnya lihat <a href="{{ route('privacy') }}" class="text-teal-400 font-semibold hover:underline">Kebijakan Privasi</a>.
        </p>
    </div>
</section>

{{-- ================= CONTENT ================= --}}
<section class="py-16 bg-slate-50" x-data="privacyChoices()" x-init="load()">
    <div class="max-w-3xl mx-auto px-6 space-y-6">

        {{-- Notifikasi tersimpan --}}
        <div x-show="saved" x-cloak x-transition
             class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">Preferensi privasi Anda berhasil disimpan.</span>
        </div>

        {{-- Aksi cepat --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-card flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-600">Atur sekaligus:</p>
            <div class="flex flex-wrap gap-2">
                <button @click="allowAll()" class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition">Izinkan Semua</button>
                <button @click="rejectAll()" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-300 transition">Tolak yang Opsional</button>
                <button @click="save()" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">💾 Simpan Preferensi</button>
            </div>
        </div>

        {{-- Cookie & Penyimpanan --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Cookie & Penyimpanan</h2>
            <p class="text-sm text-slate-500 mb-6">Kontrol cookie yang digunakan browser Anda saat mengakses APIC.</p>
            <div class="space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Cookie Wajib</p>
                        <p class="text-xs text-slate-500 mt-0.5">Diperlukan untuk login, keamanan sesi, dan fungsi inti. Tidak dapat dinonaktifkan.</p>
                    </div>
                    <x-toggle bound="prefs.necessary" :disabled="true" />
                </div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Cookie Analitik</p>
                        <p class="text-xs text-slate-500 mt-0.5">Membantu kami memahami penggunaan layanan untuk peningkatan kualitas.</p>
                    </div>
                    <x-toggle bound="prefs.analytics" />
                </div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Cookie Pemasaran</p>
                        <p class="text-xs text-slate-500 mt-0.5">Untuk personalisasi informasi dan pengumuman. Nonaktif secara bawaan.</p>
                    </div>
                    <x-toggle bound="prefs.marketing" />
                </div>
            </div>
        </div>

        {{-- Penggunaan Data --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Penggunaan Data</h2>
            <p class="text-sm text-slate-500 mb-6">Tentukan bagaimana data aktivitas Anda boleh dimanfaatkan.</p>
            <div class="space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Data Penggunaan untuk Peningkatan Layanan</p>
                        <p class="text-xs text-slate-500 mt-0.5">Izinkan analisis anonim riwayat penggunaan demi pengembangan fitur.</p>
                    </div>
                    <x-toggle bound="prefs.usage" />
                </div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Berbagi Data dengan Pihak Ketiga</p>
                        <p class="text-xs text-slate-500 mt-0.5">Kami tidak pernah menjual data. Opsi ini hanya untuk integrasi yang Anda izinkan secara eksplisit.</p>
                    </div>
                    <x-toggle bound="prefs.thirdParty" />
                </div>
            </div>
        </div>

        {{-- Notifikasi --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Notifikasi</h2>
            <p class="text-sm text-slate-500 mb-6">Pilih pemberitahuan yang ingin Anda terima.</p>
            <div class="space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Notifikasi Status Peminjaman</p>
                        <p class="text-xs text-slate-500 mt-0.5">Verifikasi, jatuh tempo, dan pengembalian. Disarankan tetap aktif.</p>
                    </div>
                    <x-toggle bound="prefs.statusNotif" />
                </div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Email Informasi Umum</p>
                        <p class="text-xs text-slate-500 mt-0.5">Pengumuman dan informasi non-transaksional lainnya.</p>
                    </div>
                    <x-toggle bound="prefs.emailNotif" />
                </div>
            </div>
        </div>

        {{-- Hak Anda --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-card">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Hak Anda atas Data</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <a href="{{ route('user.profile.edit') }}" class="p-4 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/40 transition group">
                    <p class="text-sm font-bold text-slate-900 group-hover:text-teal-700">Lihat & Perbaiki Data Saya</p>
                    <p class="text-xs text-slate-500 mt-1">Akses dan perbarui informasi profil Anda kapan saja.</p>
                </a>
                <a href="{{ route('contact') }}" class="p-4 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/40 transition group">
                    <p class="text-sm font-bold text-slate-900 group-hover:text-teal-700">Ajukan Ekspor / Penghapusan Data</p>
                    <p class="text-xs text-slate-500 mt-1">Hubungi admin untuk meminta salinan atau penghapusan akun.</p>
                </a>
            </div>
        </div>

        {{-- Simpan --}}
        <div class="flex justify-end">
            <button @click="save()" class="px-8 py-3 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition shadow-card">
                Simpan Preferensi
            </button>
        </div>
    </div>

    {{-- Logic Alpine --}}
    <script>
        function privacyChoices() {
            return {
                saved: false,
                prefs: {
                    necessary: true,
                    analytics: true,
                    marketing: false,
                    usage: true,
                    thirdParty: false,
                    statusNotif: true,
                    emailNotif: false,
                },
                load() {
                    try {
                        const stored = localStorage.getItem('apic_privacy_choices');
                        if (stored) this.prefs = { ...this.prefs, ...JSON.parse(stored) };
                    } catch (e) { /* abaikan */ }
                },
                save() {
                    localStorage.setItem('apic_privacy_choices', JSON.stringify(this.prefs));
                    this.saved = true;
                    setTimeout(() => this.saved = false, 3000);
                },
                allowAll() {
                    Object.keys(this.prefs).forEach(k => this.prefs[k] = true);
                },
                rejectAll() {
                    Object.keys(this.prefs).forEach(k => {
                        this.prefs[k] = (k === 'necessary' || k === 'statusNotif');
                    });
                },
            };
        }
    </script>
</section>

@include('partials.footer')
@include('partials.scripts')