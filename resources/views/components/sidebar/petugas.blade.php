<!-- resources/views/components/sidebar/petugas.blade.php -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto flex flex-col">

    {{-- Logo Header --}}
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100 shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="Logo APIC"
                    class="w-9 h-9 rounded-lg object-contain shadow-sm shrink-0">
            <span class="leading-tight hidden sm:block">
                <span class="block font-extrabold text-base tracking-[0.18em] text-slate-900">APIC</span>
                <span class="block text-[10px] text-slate-500 -mt-0.5">Petugas Panel</span>
            </span>
        </a>
        {{-- Tombol Close (Mobile Only) --}}
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Navigation --}}
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6 sidebar-scroll">

        {{-- ✅ PERBAIKAN: semua variabel badge didefinisikan di sini --}}
        @php
            $menunggu = \App\Models\Peminjaman::where('status', 'menunggu')->count();
            $perpanjanganMenunggu = \App\Models\Peminjaman::where('status_perpanjangan', 'menunggu')->count();
            $overdue = \App\Models\Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])
                ->whereDate('tanggal_jatuh_tempo', '<', now())->count();
            $bayarMenunggu = \App\Models\Pembayaran::where('status', 'menunggu')->count();
        @endphp

        {{-- Menu Operasional --}}
        <div>
            <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Operasional</p>
            <nav class="space-y-1">
                <a href="{{ route('petugas.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.dashboard') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                    @if($menunggu > 0)
                        <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-bold">{{ $menunggu }}</span>
                    @endif
                </a>
                <a href="{{ route('petugas.alat.index') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.alat.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Daftar Alat
                </a>

                <a href="{{ route('petugas.alat.scan') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.alat.scan') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2m0-8h18"/>
                    </svg>
                    Scan QR Alat
                </a>
            </nav>
        </div>

        {{-- Menu Transaksi --}}
        <div>
            <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Transaksi</p>
            <nav class="space-y-1">
                <a href="{{ route('petugas.peminjaman.index') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.peminjaman.*') && !request('perpanjangan') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Verifikasi Pinjam
                    @if($menunggu > 0)
                        <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-bold">{{ $menunggu }}</span>
                    @endif
                </a>

                {{-- ✅ Menu Perpanjangan (variabel sudah aman) --}}
                <a href="{{ route('petugas.peminjaman.index', ['perpanjangan' => 1]) }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.peminjaman.*') && request('perpanjangan') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pengajuan Perpanjangan
                    @if($perpanjanganMenunggu > 0)
                        <span class="ml-auto bg-amber-100 text-amber-600 text-xs px-2 py-0.5 rounded-full font-bold">{{ $perpanjanganMenunggu }}</span>
                    @endif
                </a>

                <a href="{{ route('petugas.pengembalian.index') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.pengembalian.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Pengembalian
                    @if($overdue > 0)
                        <span class="ml-auto bg-amber-100 text-amber-600 text-xs px-2 py-0.5 rounded-full font-bold">{{ $overdue }}</span>
                    @endif
                </a>
                <a href="{{ route('pembayaran.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pembayaran.*') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Verifikasi Pembayaran
                    @if($bayarMenunggu > 0)
                        <span class="ml-auto bg-amber-100 text-amber-600 text-xs px-2 py-0.5 rounded-full font-bold">{{ $bayarMenunggu }}</span>
                    @endif
                </a>
                <a href="{{ route('petugas.pengembalian.report') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('petugas.pengembalian.report') ? 'bg-teal-50 text-teal-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
            </nav>
        </div>
    </div>

    {{-- Footer Sidebar --}}
    <div class="p-4 border-t border-slate-100 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-md transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>