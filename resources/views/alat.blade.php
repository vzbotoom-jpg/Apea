<!-- resources/views/alat.blade.php -->
@include('partials.head')
@include('partials.navbar')

{{-- ================= HERO ================= --}}
<section class="relative bg-slate-900 py-20 overflow-hidden">
    {{-- ✅ BACKGROUND IMAGE + OVERLAY (sama seperti Beranda) --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80&w=2070&auto=format&fit=crop"
             alt="Background Laboratorium Teknologi"
             class="w-full h-full object-cover object-center opacity-40">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/85 to-slate-900/50"></div>
    </div>
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6">
        {{-- ... isi existing (badge, judul, search form) TETAP sama ... --}}
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-400 text-[11px] font-bold tracking-widest uppercase mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
            Katalog Publik
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-white mb-3">Katalog Alat</h1>
        <p class="text-lg text-slate-300 max-w-2xl">Cek ketersediaan stok alat secara real-time. Login untuk mengajukan peminjaman.</p>

        {{-- Search --}}
        <form method="GET" action="{{ route('alat') }}" class="mt-8 flex flex-col sm:flex-row gap-3 max-w-2xl">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode alat..."
                   class="flex-1 px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500">
            <button class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">Cari</button>
        </form>
    </div>
</section>

{{-- ================= KATALOG ================= --}}
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Filter Kategori --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="{{ route('alat') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition {{ !request('kategori') ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-teal-400' }}">
                Semua
            </a>
            @foreach($kategoris as $k)
                <a href="{{ route('alat', ['kategori' => $k->id, 'search' => request('search')]) }}"
                   class="px-4 py-2 rounded-full text-sm font-semibold transition {{ request('kategori') == $k->id ? 'bg-teal-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-teal-400' }}">
                    {{ $k->nama_kategori }}
                </a>
            @endforeach
        </div>

        <p class="text-sm text-slate-500 mb-6"><strong>{{ $alats->total() }}</strong> alat ditemukan</p>

        {{-- Grid Alat --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @forelse($alats as $alat)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-teal-300 transition group overflow-hidden">

            {{-- ✅ GAMBAR ALAT + BADGE STOK OVERLAY --}}
            <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                @if($alat->gambar)
                    <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                    {{-- Placeholder jika alat belum punya foto --}}
                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif

                @if($alat->stok_tersedia > 0)
                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100/95 text-emerald-700 shadow-sm backdrop-blur">
                        Tersedia: {{ $alat->stok_tersedia }}
                    </span>
                @else
                    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100/95 text-red-700 shadow-sm backdrop-blur">
                        Stok Habis
                    </span>
                @endif
            </div>

            {{-- Isi kartu --}}
            <div class="p-5">
                <h3 class="font-bold text-slate-900 group-hover:text-teal-600 transition line-clamp-1">{{ $alat->nama_alat }}</h3>
                <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $alat->kode_alat }}</p>
                <p class="text-xs text-slate-500 mt-2">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</p>

                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <span class="text-sm font-bold text-teal-600">
                        Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}<span class="text-xs font-normal text-slate-400">/hari</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $alat->kondisi_badge }}">
                        {{ ucfirst(str_replace('_', ' ', $alat->kondisi)) }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full p-12 text-center bg-white rounded-2xl border border-dashed border-slate-300">
            <p class="text-sm font-bold text-slate-900">Alat tidak ditemukan</p>
            <p class="text-xs text-slate-500 mt-1">Coba kata kunci lain atau reset filter kategori.</p>
        </div>
    @endforelse
</div>

        <div class="mt-10">{{ $alats->links() }}</div>

        {{-- CTA Guest --}}
        @guest
            <div class="mt-12 bg-teal-600 rounded-2xl p-8 text-center">
                <h3 class="text-xl font-bold text-white mb-2">Ingin meminjam alat?</h3>
                <p class="text-teal-50 text-sm mb-5">Buat akun gratis untuk mengajukan peminjaman secara online.</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-white text-teal-700 text-sm font-bold rounded-lg hover:bg-teal-50 transition">Login</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 bg-teal-700 text-white text-sm font-bold rounded-lg hover:bg-teal-800 transition border border-teal-500">Daftar Sekarang</a>
                </div>
            </div>
        @endguest
    </div>
</section>

@include('partials.footer')
@include('partials.scripts')