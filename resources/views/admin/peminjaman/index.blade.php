<!-- resources/views/admin/peminjaman/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manajemen Peminjaman')
@section('page-title', 'Manajemen Peminjaman')

@section('content')
<div class="space-y-6">
    
    {{-- Status Counts Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-4 border-t-4 border-t-amber-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $statusCount['menunggu'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 border-t-4 border-t-blue-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Diverifikasi</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $statusCount['diverifikasi'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 border-t-4 border-t-teal-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dipinjam</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $statusCount['dipinjam'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 border-t-4 border-t-red-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlambat</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $statusCount['terlambat'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 border-t-4 border-t-emerald-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $statusCount['dikembalikan'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 border-t-4 border-t-slate-400">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dibatalkan</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $statusCount['dibatalkan'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header --}}
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-900">Daftar Peminjaman</h2>
        </div>

        {{-- Filter & Search --}}
        <div class="p-5 border-b border-slate-100">
            <form method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kode atau nama peminjam..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none text-slate-700">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">
                        Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.peminjaman.index') }}" class="px-4 py-2.5 text-slate-500 hover:text-slate-700 text-sm font-medium transition flex items-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            @if($peminjamans->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p>Tidak ada data peminjaman</p>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Peminjam</th>
                            <th class="px-6 py-4">Tanggal Pinjam</th>
                            <th class="px-6 py-4">Jatuh Tempo</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($peminjamans as $peminjaman)
                            <tr class="hover:bg-slate-50/50 transition group {{ $peminjaman->status === 'terlambat' ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $peminjaman->kode_peminjaman }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-slate-900">{{ $peminjaman->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="{{ $peminjaman->status == 'terlambat' ? 'text-red-600 font-bold' : 'text-slate-600' }}">
                                            {{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}
                                        </span>
                                        @if($peminjaman->status == 'terlambat')
                                            <span class="text-[10px] text-red-500 font-bold uppercase tracking-wide mt-0.5">Terlambat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                        @switch($peminjaman->status)
                                            @case('menunggu') bg-amber-100 text-amber-700 @break
                                            @case('diverifikasi') bg-blue-100 text-blue-700 @break
                                            @case('dipinjam') bg-teal-100 text-teal-700 @break
                                            @case('terlambat') bg-red-100 text-red-700 @break
                                            @case('dikembalikan') bg-emerald-100 text-emerald-700 @break
                                            @default bg-slate-100 text-slate-600
                                        @endswitch">
                                        {{ ucfirst($peminjaman->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.peminjaman.show', $peminjaman) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        
        @if($peminjamans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection