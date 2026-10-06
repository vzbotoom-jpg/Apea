<!-- resources/views/user/dashboard.blade.php -->
@extends('layouts.user')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    {{-- Welcome Banner --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Selamat Datang Kembali, {{ auth()->user()->name }}</h1>
            <p class="text-slate-500 mt-1">Kelola peminjaman alat Anda dengan mudah dan transparan.</p>
        </div>
        <a href="{{ route('user.peminjaman.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1 -->
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Peminjaman</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalPeminjaman }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>
        <!-- Stat 2 -->
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Verifikasi</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $menunggu }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <!-- Stat 3 -->
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-teal-600 mt-1">{{ $dipinjam }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <!-- Stat 4 -->
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $selesai }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Activity (Main Content) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Peminjaman Terbaru</h2>
                    <a href="{{ route('user.peminjaman.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    @if($recentPeminjaman->isEmpty())
                        <div class="p-8 text-center text-slate-500 text-sm">Belum ada riwayat peminjaman.</div>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Kode</th>
                                    <th class="px-6 py-3">Tanggal Pinjam</th>
                                    <th class="px-6 py-3">Jatuh Tempo</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($recentPeminjaman as $peminjaman)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-medium text-slate-900">{{ $peminjaman->kode_peminjaman }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $peminjaman->status === 'dikembalikan' ? 'bg-emerald-100 text-emerald-800' : 
                                                  ($peminjaman->status === 'dipinjam' ? 'bg-teal-100 text-teal-800' : 
                                                  ($peminjaman->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800')) }}">
                                                {{ ucfirst($peminjaman->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar / Quick Actions --}}
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <h3 class="font-bold text-slate-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('user.alat.index') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50 transition group">
                        <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 group-hover:bg-teal-100 group-hover:text-teal-600 flex items-center justify-center mr-3 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-teal-700">Cari Alat</span>
                    </a>
                    <a href="{{ route('user.peminjaman.index') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50 transition group">
                        <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 group-hover:bg-teal-100 group-hover:text-teal-600 flex items-center justify-center mr-3 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-teal-700">Riwayat Peminjaman</span>
                    </a>
                    <a href="{{ route('user.profile.edit') }}" class="flex items-center p-3 rounded-lg border border-slate-200 hover:border-teal-500 hover:bg-teal-50 transition group">
                        <div class="w-8 h-8 rounded bg-slate-100 text-slate-500 group-hover:bg-teal-100 group-hover:text-teal-600 flex items-center justify-center mr-3 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-teal-700">Edit Profil</span>
                    </a>
                </div>
            </div>

            {{-- Notifications Widget --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifikasi
                </h3>
                <div class="space-y-4">
                    @forelse($notifications as $notification)
                        <div class="flex gap-3">
                            <div class="mt-1.5 w-2 h-2 rounded-full {{ !$notification->is_read ? 'bg-teal-500' : 'bg-slate-300' }}"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-900 {{ !$notification->is_read ? '' : 'text-slate-500' }}">{{ $notification->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $notification->message }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-2">Tidak ada notifikasi baru</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection