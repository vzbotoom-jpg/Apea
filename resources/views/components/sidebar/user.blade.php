<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto flex flex-col">

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

    <div class="flex-1 overflow-y-auto py-5 px-3">
        <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Ruang Pengguna</p>
        <nav class="space-y-1">
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('user.dashboard') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('user.alat.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('user.alat.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Katalog Alat
            </a>
            <a href="{{ route('user.peminjaman.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('user.peminjaman.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Peminjaman Saya
            </a>
            <a href="{{ route('user.notifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifikasi
                @if(($unreadNotifications ?? 0) > 0)
                    <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-500 text-white">{{ $unreadNotifications }}</span>
                @endif
            </a>
            <a href="{{ route('user.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition {{ request()->routeIs('user.profile.*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profil
            </a>
        </nav>
    </div>

    {{-- Kartu bantuan --}}
    <div class="px-3 pb-3 shrink-0">
        <div class="bg-slate-800/70 border border-slate-700/60 rounded-xl p-4">
            <span class="w-7 h-7 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01"/></svg>
            </span>
            <p class="text-sm font-semibold text-white">Butuh bantuan?</p>
            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Pelajari panduan peminjaman atau hubungi petugas.</p>
            <a href="{{ route('guide') }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-400 hover:text-teal-300 mt-2">Buka panduan →</a>
        </div>
    </div>

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