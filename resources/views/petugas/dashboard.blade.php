<!-- resources/views/petugas/dashboard.blade.php -->
@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    {{-- Welcome Banner --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Selamat Datang, {{ auth()->user()->name }}</h1>
            <p class="text-slate-500 mt-1">Kelola verifikasi peminjaman dan pengembalian alat hari ini.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('petugas.peminjaman.index', ['status' => 'menunggu']) }}" class="inline-flex items-center px-4 py-2 bg-amber-50 text-amber-700 text-sm font-semibold rounded-lg hover:bg-amber-100 transition border border-amber-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Verifikasi ({{ $peminjamanMenunggu }})
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Verifikasi</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $peminjamanMenunggu }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-teal-600 mt-1">{{ $peminjamanAktif }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlambat</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $peminjamanTerlambat }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alat Tersedia</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $alatTersedia }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Info Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Peminjaman Hari Ini</p>
                <p class="text-xl font-bold text-slate-900">{{ $peminjamanHariIni }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Jatuh Tempo Hari Ini</p>
                <p class="text-xl font-bold text-slate-900">{{ $pengembalianHariIni }}</p>
            </div>
        </div>
    </div>

    {{-- Two Columns: Verification & Returns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Need Verification --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                    Menunggu Verifikasi
                </h2>
                <a href="{{ route('petugas.peminjaman.index', ['status' => 'menunggu']) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat semua &rarr;</a>
            </div>
            <div class="p-6 flex-1 overflow-y-auto max-h-[400px] space-y-4">
                @if($needVerification->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 py-8">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm">Semua beres! Tidak ada antrian.</p>
                    </div>
                @else
                    @foreach($needVerification as $peminjaman)
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-lg hover:border-teal-200 hover:shadow-sm transition group">
                            <div class="flex-1 min-w-0 mr-4">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $peminjaman->user->name }}</p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $peminjaman->kode_peminjaman }}</p>
                                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                    <span>{{ $peminjaman->detailPeminjamans->count() }} alat</span>
                                    <span>&bull;</span>
                                    <span>{{ $peminjaman->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                            <a href="{{ route('petugas.peminjaman.show', $peminjaman) }}" class="shrink-0 px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition shadow-sm">
                                Proses
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Need Return --}}
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    Perlu Dikembalikan
                </h2>
                <a href="{{ route('petugas.pengembalian.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat semua &rarr;</a>
            </div>
            <div class="p-6 flex-1 overflow-y-auto max-h-[400px] space-y-4">
                @if($needReturn->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 py-8">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                        <p class="text-sm">Tidak ada pengembalian pending.</p>
                    </div>
                @else
                    @foreach($needReturn as $peminjaman)
                        <div class="flex items-center justify-between p-4 bg-white border rounded-lg transition group
                                    {{ $peminjaman->status === 'terlambat' ? 'border-red-200 bg-red-50/30' : 'border-slate-100 hover:border-teal-200 hover:shadow-sm' }}">
                            <div class="flex-1 min-w-0 mr-4">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $peminjaman->user->name }}</p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $peminjaman->kode_peminjaman }}</p>
                                <p class="text-xs mt-1 flex items-center gap-2 {{ $peminjaman->status === 'terlambat' ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                                    <span>Jatuh tempo: {{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</span>
                                    @if($peminjaman->status === 'terlambat')
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[10px] uppercase">Terlambat</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('petugas.pengembalian.proses', $peminjaman) }}" 
                               class="shrink-0 px-4 py-2 text-white text-xs font-bold rounded-lg transition shadow-sm
                                      {{ $peminjaman->status === 'terlambat' ? 'bg-red-600 hover:bg-red-700' : 'bg-teal-600 hover:bg-teal-700' }}">
                                Proses
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection