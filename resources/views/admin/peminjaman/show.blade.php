<!-- resources/views/admin/peminjaman/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- Main Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $peminjaman->kode_peminjaman }}</h2>
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
                </div>
                <p class="text-sm text-slate-500 flex items-center gap-2">
                    Diajukan oleh: 
                    <span class="font-bold text-slate-900">{{ $peminjaman->user->name }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span>{{ $peminjaman->created_at->format('d M Y, H:i') }} WIB</span>
                </p>
            </div>
            <a href="{{ route('admin.peminjaman.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200" title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        <div class="p-6 space-y-8">
            
            {{-- Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pinjam</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jatuh Tempo</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Item</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->detailPeminjamans->sum('jumlah') }} Alat</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Sewa</p>
                    <p class="text-sm font-bold text-slate-900">Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}</p>
                </div>
                
                @if($peminjaman->petugas)
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Diverifikasi oleh</p>
                        <p class="text-sm font-bold text-slate-900">{{ $peminjaman->petugas->name }}</p>
                    </div>
                @endif
                
                @if($peminjaman->tanggal_verifikasi)
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Verifikasi</p>
                        <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_verifikasi->format('d M Y H:i') }}</p>
                    </div>
                @endif
                
                @if($peminjaman->catatan)
                    <div class="col-span-2 p-4 bg-amber-50 rounded-lg border border-amber-100 border-l-4 border-l-amber-400">
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-sm text-amber-900 italic">"{{ $peminjaman->catatan }}"</p>
                    </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Daftar Alat</h3>
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama Alat</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                                <th class="px-4 py-3 text-right">Harga Sewa</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($peminjaman->detailPeminjamans as $detail)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-mono text-slate-500">{{ $detail->alat->kode_alat }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $detail->alat->nama_alat }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-center">{{ $detail->jumlah }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-right">Rp {{ number_format($detail->harga_sewa_saat_pinjam, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 font-bold text-slate-900 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex flex-wrap gap-3 justify-end">
                @if($peminjaman->status === 'menunggu')
                    <form action="{{ route('admin.peminjaman.verifikasi', $peminjaman) }}" method="POST" class="inline-block" onsubmit="return confirm('Verifikasi peminjaman ini?')">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Verifikasi
                        </button>
                    </form>
                    <form action="{{ route('admin.peminjaman.batalkan', $peminjaman) }}" method="POST" class="inline-block" onsubmit="return confirm('Batalkan peminjaman ini?')">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Batalkan
                        </button>
                    </form>
                @endif
                
                @if(in_array($peminjaman->status, ['dipinjam', 'terlambat']))
                    <a href="{{ route('admin.pengembalian.proses', $peminjaman) }}" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Proses Pengembalian
                    </a>
                @endif
                
                <a href="{{ route('admin.peminjaman.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection