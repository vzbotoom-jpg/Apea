<!-- resources/views/admin/laporan/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
<div class="space-y-6">
    
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Peminjaman</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalPeminjaman) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total User</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalUser) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Alat</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalAlat) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Denda</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalDenda) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">TOTAL PEMASUKAN</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Top Alat --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart Peminjaman --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-bold text-slate-900 mb-6">Peminjaman per Bulan ({{ date('Y') }})</h3>
            <div class="h-64 flex items-end gap-2">
                @foreach($chartData as $index => $value)
                    @php
                        $height = $value > 0 ? ($value / max($chartData) * 100) : 0;
                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    @endphp
                    <div class="flex-1 flex flex-col items-center group">
                        <div class="w-full bg-teal-500 rounded-t transition-all duration-500 group-hover:bg-teal-600 relative" 
                             style="height: {{ $height }}%; min-height: {{ $value > 0 ? '4px' : '0' }};">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold text-slate-700 opacity-0 group-hover:opacity-100 transition-opacity">{{ $value }}</span>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-2 font-medium">{{ $months[$index] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Alat --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-bold text-slate-900 mb-6">Top 5 Alat Terpopuler</h3>
            @if($topAlat->isEmpty())
                <div class="h-64 flex items-center justify-center text-slate-400">
                    <p>Belum ada data</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($topAlat as $index => $alat)
                        <div class="flex items-center gap-4">
                            <span class="w-6 text-sm font-bold text-slate-400">#{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <div class="flex justify-between mb-1">
                                    <p class="text-sm font-medium text-slate-900 truncate pr-2">{{ $alat->nama_alat }}</p>
                                    <span class="text-xs font-bold text-slate-500">{{ $alat->detail_peminjamans_count }}x</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5">
                                    @php
    $maxCount = $topAlat->first()->detail_peminjamans_count ?? 0;
    $percentage = $maxCount > 0 ? ($alat->detail_peminjamans_count / $maxCount) * 100 : 0;
@endphp
<div class="bg-teal-500 h-1.5 rounded-full transition-all duration-500" 
     style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <h3 class="font-bold text-slate-900 mb-4">Export Laporan</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.laporan.export-pdf') }}" 
               class="flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
            <a href="{{ route('admin.laporan.export-excel') }}" 
               class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
            <a href="{{ route('admin.laporan.peminjaman') }}" 
               class="flex items-center gap-2 px-4 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Lihat Detail Laporan
            </a>
        </div>
    </div>
</div>
@endsection