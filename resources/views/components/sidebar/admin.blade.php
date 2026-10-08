<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto flex flex-col">

    {{-- Logo Header --}}
    <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('images/logo.png') }}" alt="Logo APIC"
                 class="w-9 h-9 rounded-lg object-contain shadow-sm shrink-0 bg-white">
            <span class="leading-tight">
                <span class="block font-extrabold text-base tracking-[0.18em] text-white">APIC</span>
                <span class="block text-[10px] text-slate-400 -mt-0.5">Alat Pinjam Mudah Cepat</span>
            </span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Navigation --}}
    <div class="flex-1 overflow-y-auto py-5 px-3">
        @php
            $alatCount            = \App\Models\Alat::count();
            $pjmMenunggu          = \App\Models\Peminjaman::where('status', 'menunggu')->count();
            $perpanjanganMenunggu = \App\Models\Peminjaman::where('status_perpanjangan', 'menunggu')->count();
            $bayarMenunggu        = \App\Models\Pembayaran::where('status', 'menunggu')->count();
        @endphp

        {{-- MENU UTAMA --}}
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Menu Utama</p>
        <nav class="space-y-1 mb-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.kategori.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori
            </a>
            <a href="{{ route('admin.alat.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.alat.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Data Alat
                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-200">{{ $alatCount }}</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Manajemen User
            </a>
        </nav>

        {{-- TRANSAKSI --}}
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Transaksi</p>
        <nav class="space-y-1 mb-5">
            <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Peminjaman
                @if($pjmMenunggu > 0)
                    <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white">{{ $pjmMenunggu }}</span>
                @endif
                @if($perpanjanganMenunggu > 0)
                    <span class="{{ $pjmMenunggu > 0 ? '' : 'ml-auto' }} px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white" title="Perpanjangan menunggu">⏳ {{ $perpanjanganMenunggu }}</span>
                @endif
            </a>
            <a href="{{ route('admin.pengembalian.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.pengembalian.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Pengembalian
            </a>
            <a href="{{ route('pembayaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('pembayaran.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Verifikasi Pembayaran
                @if($bayarMenunggu > 0)
                    <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white">{{ $bayarMenunggu }}</span>
                @endif
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.laporan.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
        </nav>

        {{-- SISTEM --}}
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Sistem</p>
        <nav class="space-y-1 mb-5">
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.settings*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-2.572-1.065c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Sistem
            </a>
            <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.logs.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Log Aktivitas
            </a>
        </nav>

        {{-- Kartu bantuan --}}
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4">
            <span class="w-7 h-7 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01"/></svg>
            </span>
            <p class="text-sm font-semibold text-white">Panduan Admin</p>
            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Kelola data alat, user, kebijakan denda, dan laporan sistem.</p>
            <a href="{{ route('guide') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-400 hover:text-teal-300 mt-2">Buka panduan →</a>
        </div>
    </div>

    {{-- Footer: user info + logout --}}
    <div class="p-3 border-t border-slate-800 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>