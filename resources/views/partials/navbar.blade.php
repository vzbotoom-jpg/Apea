<!-- resources/views/partials/navbar.blade.php -->
<nav class="sticky top-0 z-50 bg-white border-b border-slate-200" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
    <img src="{{ asset('images/logo.png') }}" alt="Logo APIC"
         class="w-10 h-10 rounded-lg object-contain shadow-sm">
    <span class="leading-tight">
        <span class="block font-extrabold text-lg tracking-[0.18em] text-slate-900">APIC</span>
        <span class="hidden sm:block text-[10px] text-slate-500 -mt-0.5 tracking-wide">Alat Pinjam Mudah Cepat</span>
    </span>
</a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                @guest
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition {{ request()->routeIs('home') ? 'text-teal-600' : '' }}">Beranda</a>
                     <a href="{{ route('alat') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition {{ request()->routeIs('alat') ? 'text-teal-600' : '' }}">Alat</a>
                    <a href="{{ route('about') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition {{ request()->routeIs('about') ? 'text-teal-600' : '' }}">Tentang Kami</a>
                    <a href="{{ route('guide') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition">Panduan</a>
                    <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600 transition">Kontak</a>
                    <div class="h-4 w-px bg-slate-300"></div>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-teal-600">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700 transition rounded-sm">Daftar</a>
                @else
                    {{-- Menu User (Sederhana seperti Umicore) --}}
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isPetugas() ? route('petugas.dashboard') : route('user.dashboard')) }}" class="text-sm font-medium text-slate-600 hover:text-teal-600">Dashboard</a>
                    
                    @if(auth()->user()->role === 'user')
                        <a href="{{ route('user.alat.index') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600">Katalog</a>
                        <a href="{{ route('user.peminjaman.index') }}" class="text-sm font-medium text-slate-600 hover:text-teal-600">Riwayat</a>
                    @endif

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold border border-slate-200">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-slate-100 py-1 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs text-slate-500">Masuk sebagai</p>
                                <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Keluar</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile Button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen" x-cloak class="md:hidden bg-white border-t border-slate-100 p-4 space-y-3">
        <a href="{{ route('home') }}" class="block text-sm font-medium text-slate-600">Beranda</a>
        <a href="{{ route('alat') }}" class="block text-sm font-medium text-slate-600">Alat</a>
        <a href="{{ route('about') }}" class="block text-sm font-medium text-slate-600">Tentang Kami</a>
        @guest
            <a href="{{ route('login') }}" class="block text-sm font-medium text-slate-900">Login</a>
            <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-sm">Daftar</a>
        @endguest
    </div>
</nav>