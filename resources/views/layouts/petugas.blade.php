<!DOCTYPE html>
<html lang="id" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Petugas Panel') - APIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 font-sans text-slate-900" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden">

    {{-- Overlay Mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden" x-transition.opacity></div>

    {{-- SIDEBAR GELAP (petugas) --}}
    @include('components.sidebar.petugas')

    {{-- MAIN WRAPPER --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- ===== TOPBAR (konsisten dengan panel user) ===== --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 z-30 shrink-0">
            {{-- Kiri: hamburger + breadcrumb --}}
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 min-w-0">
                    <span class="hidden sm:block">Workspace</span>
                    <svg class="w-3 h-3 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-slate-900 text-sm truncate">@yield('page-title', 'Dashboard Petugas')</span>
                </nav>
            </div>

            {{-- Kanan: tanggal + notifikasi + profil --}}
            <div class="flex items-center gap-3 md:gap-5">
                <span class="hidden md:block text-xs text-slate-500">{{ now()->locale('id')->translatedFormat('l, d M Y') }}</span>

                {{-- Notifikasi Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(($unreadNotifications ?? 0) > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-teal-500 ring-2 ring-white"></span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak x-transition
                         class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                            <p class="text-sm font-bold text-slate-900">Notifikasi</p>
                            @if(($unreadNotifications ?? 0) > 0)
                                <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full">{{ $unreadNotifications }} Baru</span>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            @forelse(($notifications ?? []) as $n)
                                <a href="{{ $n->link ?? '#' }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition">
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                        {{ match($n->type ?? 'info') {
                                            'success' => 'bg-emerald-100 text-emerald-600',
                                            'warning' => 'bg-amber-100 text-amber-600',
                                            'danger'  => 'bg-red-100 text-red-600',
                                            'payment' => 'bg-teal-100 text-teal-600',
                                            default   => 'bg-slate-100 text-slate-500',
                                        } }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-900">{{ $n->title }}</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $n->message }}</p>
                                        <p class="text-[10px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$n->is_read)<span class="w-1.5 h-1.5 rounded-full bg-teal-500 mt-1.5 shrink-0"></span>@endif
                                </a>
                            @empty
                                <p class="px-4 py-6 text-xs text-slate-400 text-center">Tidak ada notifikasi</p>
                            @endforelse
                        </div>
                        @if(count($notifications ?? []) > 0)
                            <a href="{{ route('user.notifikasi.index') }}" class="block text-center text-[11px] font-bold text-teal-600 hover:text-teal-700 px-4 py-2.5 border-t border-slate-100 bg-slate-50/50">Lihat semua notifikasi →</a>
                        @endif
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                        <span class="hidden md:block text-left leading-tight">
                            <span class="block text-sm font-bold text-slate-900">{{ auth()->user()->name }}</span>
                            <span class="block text-[10px] text-slate-500">Petugas</span>
                        </span>
                        <svg class="w-4 h-4 text-slate-400 hidden md:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak x-transition
                         class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ route('user.profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- ===== PAGE CONTENT ===== --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Alpine.js — hapus jika sudah dibundle di app.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>