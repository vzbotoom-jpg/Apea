<!-- resources/views/admin/alat/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Alat')
@section('page-title', 'Detail Alat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- Main Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.alat.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200" title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $alat->nama_alat }}</h2>
                    <p class="text-sm text-slate-500 font-mono mt-0.5">{{ $alat->kode_alat }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide
                    {{ $alat->status === 'tersedia' ? 'bg-teal-100 text-teal-700' : 
                       ($alat->status === 'dipinjam' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ ucfirst(str_replace('_', ' ', $alat->status)) }}
                </span>
                <a href="{{ route('admin.alat.edit', $alat) }}" class="px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Alat
                </a>
                <a href="{{ route('qr.alat.print', $alat) }}" target="_blank"
                   class="px-3 py-1 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200 text-sm">
                    🖨️ Cetak QR
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Image Column --}}
                <div class="md:col-span-1">
                    <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden border border-slate-200">
                        @if($alat->gambar)
                            <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info Column --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kategori</p>
                            <p class="text-sm font-bold text-slate-900">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                {{ $alat->kondisi === 'baik' ? 'bg-emerald-100 text-emerald-700' : 
                                   ($alat->kondisi === 'rusak_ringan' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst(str_replace('_', ' ', $alat->kondisi)) }}
                            </span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Stok</p>
                            <p class="text-sm font-bold text-slate-900">{{ $alat->stok_tersedia }} <span class="text-slate-400 font-normal">/ {{ $alat->stok }}</span></p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Harga Sewa / Hari</p>
                            <p class="text-sm font-bold text-teal-600">Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Denda / Hari</p>
                            <p class="text-sm font-bold text-red-600">Rp {{ number_format($alat->denda_per_hari, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Dipinjam</p>
                            <p class="text-sm font-bold text-slate-900">{{ $alat->detailPeminjamans->count() }} kali</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg flex items-center gap-3">
                            <img src="{{ route('qr.alat', $alat) }}" alt="QR" class="w-16 h-16 rounded border border-gray-200 bg-white">
                            <div>
                                <p class="text-xs text-gray-500">QR Serah Terima</p>
                                <p class="text-xs text-gray-400">Scan untuk buka detail alat</p>
                            </div>
                        </div>
                    </div>

                    @if($alat->deskripsi)
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi</p>
                            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $alat->deskripsi }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Riwayat Peminjaman --}}
            @if($alat->detailPeminjamans->isNotEmpty())
                <div class="mt-8 pt-8 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Riwayat Peminjaman Terakhir</h3>
                    <div class="border border-slate-200 rounded-lg overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-3">Kode</th>
                                    <th class="px-4 py-3">Peminjam</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($alat->detailPeminjamans->take(5) as $detail)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-3 font-mono text-slate-900">{{ $detail->peminjaman->kode_peminjaman }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $detail->peminjaman->user->name }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $detail->peminjaman->tanggal_pinjam->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                {{ $detail->peminjaman->status === 'dikembalikan' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                {{ ucfirst($detail->peminjaman->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Riwayat Mutasi Stok --}}
            @if($alat->mutasis->isNotEmpty())
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Riwayat Mutasi Stok</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Waktu</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Jenis</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Jumlah</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Keterangan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($alat->mutasis as $mutasi)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">{{ $mutasi->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $mutasi->jenis_badge }}">{{ ucfirst($mutasi->jenis) }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-center font-medium">{{ $mutasi->jumlah }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $mutasi->keterangan }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $mutasi->user?->name ?? 'Sistem' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection