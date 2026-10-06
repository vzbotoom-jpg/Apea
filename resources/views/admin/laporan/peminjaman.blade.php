<!-- resources/views/admin/laporan/peminjaman.blade.php -->
@extends('layouts.admin')

@section('title', 'Laporan Peminjaman')
@section('page-title', 'Laporan Peminjaman')

@section('content')
<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    
    {{-- Header --}}
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Detail Laporan Peminjaman</h2>
            <p class="text-sm text-slate-500 mt-0.5">Total: {{ $peminjamans->count() }} peminjaman</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="p-5 border-b border-slate-100">
        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                        <option value="">Semua</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                        <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">Filter</button>
                @if(request()->hasAny(['start_date', 'end_date', 'status']))
                    <a href="{{ route('admin.laporan.peminjaman') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Peminjam</th>
                    <th class="px-6 py-4">Tgl Pinjam</th>
                    <th class="px-6 py-4">Jatuh Tempo</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Item</th>
                    <th class="px-6 py-4 text-right">Sewa</th>
                    <th class="px-6 py-4 text-right">Denda</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($peminjamans as $peminjaman)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $peminjaman->kode_peminjaman }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $peminjaman->user->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                @switch($peminjaman->status)
                                    @case('menunggu') bg-amber-100 text-amber-700 @break
                                    @case('dipinjam') bg-teal-100 text-teal-700 @break
                                    @case('dikembalikan') bg-emerald-100 text-emerald-700 @break
                                    @case('terlambat') bg-red-100 text-red-700 @break
                                    @default bg-slate-100 text-slate-600
                                @endswitch">
                                {{ ucfirst($peminjaman->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-slate-600">{{ $peminjaman->total_item }}</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-900">
                            Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right {{ $peminjaman->pengembalian?->denda > 0 ? 'text-red-600 font-medium' : 'text-slate-400' }}">
                            {{ $peminjaman->pengembalian?->denda > 0 ? 'Rp ' . number_format($peminjaman->pengembalian->denda, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p>Tidak ada data peminjaman</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection