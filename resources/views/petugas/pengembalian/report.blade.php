<!-- resources/views/petugas/pengembalian/report.blade.php -->
@extends('layouts.petugas')

@section('title', 'Laporan Pengembalian')
@section('page-title', 'Laporan Pengembalian')

@section('content')
<div class="space-y-6">
    
    {{-- Stats Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Denda</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kondisi Baik</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['baik'] }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rusak / Hilang</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rusak'] + $stats['hilang'] }}</p>
        </div>
    </div>

    {{-- Report Table Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Filters --}}
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" class="grid gap-4 md:grid-cols-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1.5">Kondisi Alat</label>
                    <select name="kondisi_alat" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        <option value="">Semua</option>
                        <option value="baik" {{ request('kondisi_alat') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ request('kondisi_alat') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ request('kondisi_alat') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                        <option value="hilang" {{ request('kondisi_alat') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition">Terapkan</button>
                    <a href="{{ route('petugas.pengembalian.report') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">Reset</a>
                </div>
            </form>
        </div>

        {{-- Table Header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Riwayat Pengembalian</h2>
            <span class="text-sm text-slate-500">Total: {{ $pengembalians->total() }} data</span>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            @if($pengembalians->isEmpty())
                <div class="p-12 text-center text-slate-500">
                    <p>Tidak ada data pengembalian pada periode ini.</p>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Kode Transaksi</th>
                            <th class="px-6 py-3">Peminjam</th>
                            <th class="px-6 py-3">Tgl Kembali</th>
                            <th class="px-6 py-3">Kondisi</th>
                            <th class="px-6 py-3">Denda</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($pengembalians as $pengembalian)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-3 font-mono font-medium text-slate-900">{{ $pengembalian->peminjaman->kode_peminjaman }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $pengembalian->peminjaman->user->name }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ optional($pengembalian->tanggal_kembali)->format('d M Y') }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $pengembalian->kondisi_alat === 'baik' ? 'bg-emerald-50 text-emerald-700' : 
                                           ($pengembalian->kondisi_alat === 'hilang' ? 'bg-red-100 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $pengembalian->kondisi_alat)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 font-medium text-slate-900">Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('petugas.pengembalian.receipt', $pengembalian) }}" class="text-teal-600 hover:text-teal-700 font-semibold text-xs hover:underline">
                                        Cetak Struk
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        
        @if($pengembalians->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $pengembalians->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection