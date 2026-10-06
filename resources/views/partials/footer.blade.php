<!-- resources/views/partials/footer.blade.php -->
<footer class="bg-slate-900 text-slate-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-10">
        <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-4">

            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo APIC" class="w-10 h-10 rounded-lg object-contain shadow-sm">
                    <span class="leading-tight">
                        <span class="block font-extrabold text-lg tracking-[0.18em] text-white">APIC</span>
                        <span class="block text-[10px] text-slate-500 -mt-0.5">Alat Pinjam Mudah Cepat</span>
                    </span>
                </a>
                <p class="mt-5 text-sm leading-relaxed">
                    Sistem manajemen peminjaman alat yang profesional, efisien, dan transparan
                    untuk lingkungan sekolah.
                </p>
                <div class="flex gap-3 mt-6">
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 grid place-items-center hover:bg-teal-600 hover:text-white transition" aria-label="Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 grid place-items-center hover:bg-teal-600 hover:text-white transition" aria-label="Telegram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.99h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 grid place-items-center hover:bg-teal-600 hover:text-white transition" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Perusahaan --}}
            <div>
                <h4 class="text-xs font-bold tracking-[0.2em] uppercase text-white">Produk</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-teal-400 transition">Beranda</a></li>
                    <li><a href="{{ route('features') }}" class="hover:text-teal-400 transition">Fitur</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-teal-400 transition">Tentang Kami</a></li>
                    <li><a href="{{ route('model') }}" class="hover:text-teal-400 transition">Download</a></li>
                </ul>
            </div>

            {{-- Layanan --}}
            <div>
                <h4 class="text-xs font-bold tracking-[0.2em] uppercase text-white">Layanan</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('faq') }}" class="hover:text-teal-400 transition">FAQ</a></li>
                    <li><a href="{{ route('alat') }}" class="hover:text-teal-400 transition">Katalog Alat</a></li>
                    <li><a href="{{ route('guide') }}" class="hover:text-teal-400 transition">Panduan Pengguna</a></li>
                    <li><a href="{{ route('overview') }}" class="hover:text-teal-400 transition">Overview</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-teal-400 transition">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold tracking-[0.2em] uppercase text-white">Syarat dan Kebijakan</h4>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('privacy-choices') }}" class="hover:text-teal-400 transition">Privacy Choices</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-teal-400 transition">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-teal-400 transition">Terms of Service</a></li>
                    <li><a href="{{ route('usage') }}" class="hover:text-teal-400 transition">Usage Policy</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-slate-800 mt-14 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} <span class="font-bold tracking-widest text-slate-300">APIC</span>. All Right Reserved.</p>
        </div>
    </div>
</footer>