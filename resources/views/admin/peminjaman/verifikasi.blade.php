<!-- resources/views/admin/peminjaman/verifikasi.blade.php -->
@extends('layouts.admin')

@section('title', 'Verifikasi Peminjaman')
@section('page-title', 'Verifikasi Peminjaman')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-bold text-slate-900">Verifikasi Peminjaman</h2>
        </div>

        <div class="p-6 space-y-6">
            
            {{-- Warning Banner --}}
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-bold text-amber-800">Konfirmasi Verifikasi</p>
                    <p class="text-sm text-amber-700 mt-1">
                        Anda akan memverifikasi peminjaman <strong class="font-mono">{{ $peminjaman->kode_peminjaman }}</strong> 
                        oleh <strong>{{ $peminjaman->user->name }}</strong>.
                    </p>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pinjam</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jatuh Tempo</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</p>
                </div>
            </div>

            {{-- Items Table --}}
            <div>
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Daftar Alat</h4>
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3">Nama Alat</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
                                <th class="px-4 py-3 text-center">Stok Tersedia</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($peminjaman->detailPeminjamans as $detail)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $detail->alat->nama_alat }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-center">{{ $detail->jumlah }}</td>
                                    <td class="px-4 py-3 text-center font-bold {{ $detail->alat->stok_tersedia >= $detail->jumlah ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $detail->alat->stok_tersedia }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-slate-100 flex items-center gap-3 justify-end">
                <form action="{{ route('admin.peminjaman.verifikasi', $peminjaman) }}" method="POST" class="inline-block" onsubmit="return confirm('Konfirmasi verifikasi?')">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi Verifikasi
                    </button>
                </form>
                <a href="{{ route('admin.peminjaman.show', $peminjaman) }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Batal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection