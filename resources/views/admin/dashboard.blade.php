<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    {{-- Welcome Banner --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Selamat Datang, {{ auth()->user()->name }}</h1>
            <p class="text-slate-500 mt-1">Kelola sistem peminjaman alat APIC dengan mudah.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.alat.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Alat Baru
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Alat</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalAlat }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total User</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalUser }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Peminjaman</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalPeminjaman }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peminjaman Aktif</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $peminjamanAktif }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Two Columns: Notifications & Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Notifications --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifikasi Sistem
                </h2>
                @if($unreadNotifications > 0)
                    <span class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded-full">{{ $unreadNotifications }} Baru</span>
                @endif
            </div>
            <div class="p-6 flex-1 overflow-y-auto max-h-[400px] space-y-4">
                @if($notifications->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 py-8">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        <p class="text-sm">Tidak ada notifikasi baru</p>
                    </div>
                @else
                    @foreach($notifications as $notification)
                        <div class="flex items-start gap-3 p-3 rounded-lg border {{ !$notification->is_read ? 'bg-teal-50/50 border-teal-100' : 'bg-white border-slate-100' }}">
                            <div class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ !$notification->is_read ? 'bg-teal-500' : 'bg-slate-300' }}"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $notification->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $notification->message }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Recent Peminjaman --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-slate-900">Peminjaman Terbaru</h2>
                <a href="{{ route('admin.peminjaman.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat semua &rarr;</a>
            </div>
            <div class="p-6 flex-1 overflow-y-auto max-h-[400px] space-y-3">
                @if($recentPeminjaman->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 py-8">
                        <p class="text-sm">Belum ada aktivitas peminjaman</p>
                    </div>
                @else
                    @foreach($recentPeminjaman as $peminjaman)
                        <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-lg hover:border-teal-200 hover:shadow-sm transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $peminjaman->user->name }}</p>
                                    <p class="text-xs text-slate-500 font-mono">{{ $peminjaman->kode_peminjaman }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide
                                @switch($peminjaman->status)
                                    @case('menunggu') bg-amber-100 text-amber-700 @break
                                    @case('dipinjam') bg-teal-100 text-teal-700 @break
                                    @case('dikembalikan') bg-emerald-100 text-emerald-700 @break
                                    @default bg-slate-100 text-slate-600
                                @endswitch">
                                {{ ucfirst($peminjaman->status) }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection