<!-- resources/views/user/peminjaman/index.blade.php -->
@extends('layouts.user')
@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')
@section('content')
<div class="space-y-6">
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-slate-500 text-sm">Total {{ $peminjamans->total() }} transaksi ditemukan.</p>
        </div>
        <a href="{{ route('user.peminjaman.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Peminjaman Baru
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            @if($peminjamans->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Belum ada riwayat</h3>
                    <p class="text-slate-500 mt-1 mb-6">Anda belum pernah meminjam alat sebelumnya.</p>
                    <a href="{{ route('user.peminjaman.create') }}" class="text-teal-600 font-semibold hover:text-teal-700">
                        Mulai pinjam sekarang &rarr;
                    </a>
                </div>
            @else
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Kode Transaksi</th>
                            <th class="px-6 py-4">Tanggal Pinjam</th>
                            <th class="px-6 py-4">Jatuh Tempo</th>
                            <th class="px-6 py-4">Item</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            {{-- ✅ BARU: kolom pembayaran --}}
                            <th class="px-6 py-4 text-center">Pembayaran</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($peminjamans as $peminjaman)
                            <tr class="hover:bg-slate-50/50 transition group">
                                <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $peminjaman->kode_peminjaman }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-slate-600">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</span>
                                        @if(now()->greaterThan($peminjaman->tanggal_jatuh_tempo) && !in_array($peminjaman->status, ['dikembalikan', 'dibatalkan']))
                                            <span class="text-[10px] font-bold text-red-500 uppercase tracking-wide mt-0.5">Terlambat</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $peminjaman->total_item }} Alat</td>
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
                                    {{-- ✅ BARU: badge perpanjangan --}}
                                    @if($peminjaman->perpanjanganMenunggu())
                                        <span class="block mt-1 text-[10px] font-bold text-amber-600">+ Perpanjangan diajukan</span>
                                    @endif
                                </td>
                                {{-- ✅ BARU: status pembayaran --}}
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($peminjaman->status, ['diverifikasi', 'dipinjam', 'terlambat', 'dikembalikan']))
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold
                                            {{ $peminjaman->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $peminjaman->status_pembayaran === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('user.peminjaman.show', $peminjaman) }}"
                                           class="text-slate-500 hover:text-teal-600 font-medium transition">
                                            Detail
                                        </a>
                                        @if($peminjaman->status === 'menunggu')
                                            <form action="{{ route('user.peminjaman.batalkan', $peminjaman) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                                @csrf
                                                <button type="submit" class="text-red-400 hover:text-red-600 font-medium transition text-xs">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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