<!-- resources/views/petugas/pengembalian/index.blade.php -->
@extends('layouts.petugas')

@section('title', 'Pengembalian Alat')
@section('page-title', 'Pengembalian Alat')

@section('content')
<div class="space-y-6">
    
    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Dipinjam</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 border-l-4 border-l-red-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlambat</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['terlambat'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 border-l-4 border-l-amber-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jatuh Tempo Hari Ini</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['today'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5 border-l-4 border-l-orange-500">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Overdue (>2 Hari)</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['overdue'] }}</p>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header & Actions --}}
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-900">Daftar Peminjaman Aktif</h2>
            <a href="{{ route('petugas.pengembalian.report') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Pengembalian
            </a>
        </div>

        {{-- Filters --}}
        <div class="p-5 border-b border-slate-100">
            <form method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama peminjam..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    <svg class="absolute left-3 top-3 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="flex flex-wrap gap-3">
                    <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none text-slate-700">
                        <option value="">Semua Status</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                    <select name="filter" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none text-slate-700">
                        <option value="">Semua Waktu</option>
                        <option value="overdue" {{ request('filter') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Jatuh Tempo Hari Ini</option>
                        <option value="tomorrow" {{ request('filter') == 'tomorrow' ? 'selected' : '' }}>Jatuh Tempo Besok</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'filter']))
                        <a href="{{ route('petugas.pengembalian.index') }}" class="px-4 py-2.5 text-slate-500 hover:text-slate-700 text-sm font-medium transition flex items-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if($peminjamans->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p>Tidak ada peminjaman aktif yang ditemukan.</p>
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
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($peminjamans as $peminjaman)
                            <tr class="hover:bg-slate-50/50 transition group {{ $peminjaman->status === 'terlambat' ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $peminjaman->kode_peminjaman }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">{{ $peminjaman->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $peminjaman->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="{{ now()->greaterThan($peminjaman->tanggal_jatuh_tempo) ? 'text-red-600 font-bold' : 'text-slate-600' }}">
                                            {{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}
                                        </span>
                                        @if(now()->greaterThan($peminjaman->tanggal_jatuh_tempo))
                                            <span class="text-[10px] text-red-500 font-bold uppercase tracking-wide mt-0.5">Terlambat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $peminjaman->status === 'terlambat' ? 'bg-red-100 text-red-700' : 'bg-teal-100 text-teal-700' }}">
                                        {{ ucfirst($peminjaman->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('petugas.pengembalian.proses', $peminjaman) }}" 
                                       class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold text-white transition shadow-sm
                                              {{ $peminjaman->status === 'terlambat' ? 'bg-red-600 hover:bg-red-700' : 'bg-teal-600 hover:bg-teal-700' }}">
                                        Proses Kembali
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