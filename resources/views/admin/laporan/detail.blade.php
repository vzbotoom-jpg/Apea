<!-- resources/views/admin/laporan/detail.blade.php -->

@extends('layouts.admin')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan Peminjaman')

@section('content')
<div class="space-y-6">
    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('admin.laporan.detail') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['start_date', 'end_date', 'status']))
                <a href="{{ route('admin.laporan.detail') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Total Peminjaman</p>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Total Item</p>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total_item'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Total Sewa</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($summary['total_sewa'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Total Denda</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($summary['total_denda'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3">
        <a href="{{ route('admin.laporan.export-pdf', request()->all()) }}" 
           class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('admin.laporan.export-excel', request()->all()) }}" 
           class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export Excel
        </a>
        <button onclick="window.print()" 
                class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @if(empty($peminjamans) || $peminjamans->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>Tidak ada data peminjaman</p>
                </div>
            @else
                <table class="w-full" id="laporanTable">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Kembali</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sewa</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $no = ($peminjamans->currentPage() - 1) * $peminjamans->perPage() + 1; @endphp
                        @foreach($peminjamans as $peminjaman)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $no++ }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $peminjaman->kode_peminjaman }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $peminjaman->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $peminjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $peminjaman->pengembalian?->tanggal_kembali?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $peminjaman->status_badge }}">
                                        {{ ucfirst($peminjaman->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $peminjaman->total_item }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-800">
                                    Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm {{ $peminjaman->pengembalian?->denda > 0 ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                    {{ $peminjaman->pengembalian?->denda > 0 ? 'Rp ' . number_format($peminjaman->pengembalian->denda, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-bold">
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-700">TOTAL</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $summary['total_item'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">Rp {{ number_format($summary['total_sewa'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">Rp {{ number_format($summary['total_denda'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        @if(isset($peminjamans) && method_exists($peminjamans, 'links'))
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .bg-white { background: white !important; }
        .shadow-sm { box-shadow: none !important; }
        .border { border-color: #ddd !important; }
        body { background: white !important; }
    }
</style>
@endpush
@endsection