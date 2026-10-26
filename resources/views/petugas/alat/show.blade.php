<!-- resources/views/petugas/alat/show.blade.php -->
@extends('layouts.petugas')
@section('title', 'Detail Alat')
@section('page-title', 'Detail Alat')
@section('content')
<div class="space-y-6">

    {{-- Header aksi --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('petugas.alat.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Alat
        </a>
        @if(Route::has('petugas.alat.edit'))
            <a href="{{ route('petugas.alat.edit', $alat) }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition shadow-sm">✏️ Edit Alat</a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ===== KOLOM KIRI (2/3) ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Kartu utama alat --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row gap-5">
                    @if($alat->gambar)
                        <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                             class="w-28 h-28 object-cover rounded-lg border border-slate-200 shrink-0">
                    @else
                        <div class="w-28 h-28 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="font-mono text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $alat->kode_alat }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $alat->status === 'tersedia' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $alat->status)) }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold
                                {{ match($alat->kondisi) { 'baik' => 'bg-emerald-100 text-emerald-700', 'rusak_ringan' => 'bg-amber-100 text-amber-700', default => 'bg-red-100 text-red-700' } }}">
                                {{ ucfirst(str_replace('_', ' ', $alat->kondisi)) }}
                            </span>
                        </div>
                        <h1 class="text-xl font-extrabold text-slate-900">{{ $alat->nama_alat }}</h1>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $alat->kategori->nama_kategori ?? 'Tanpa Kategori' }}</p>
                        @if($alat->deskripsi)
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $alat->deskripsi }}</p>
                        @endif
                        <p class="text-sm mt-3 text-slate-600">Harga sewa:
                            <strong class="text-teal-600">Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}/hari</strong>
                        </p>
                    </div>
                </div>

                {{-- Baris statistik --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 border-t border-slate-100 divide-x divide-slate-100">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-extrabold text-slate-900">{{ $alat->stok }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total Stok</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-extrabold text-emerald-600">{{ $alat->stok_tersedia }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Tersedia</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-extrabold text-teal-600">{{ max(0, $alat->stok - $alat->stok_tersedia) }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Sedang Dipinjam</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-extrabold text-slate-900">{{ $totalDipinjamkan }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Unit Dipinjamkan</p>
                    </div>
                </div>
            </div>

            {{-- Riwayat peminjaman alat --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900 text-sm">Riwayat Peminjaman Alat</h2>
                    <span class="text-[11px] text-slate-400">{{ $riwayatPeminjaman->count() }} transaksi terakhir</span>
                </div>
                @if($riwayatPeminjaman->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-slate-400">Alat ini belum pernah dipinjam.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Kode Transaksi</th>
                                    <th class="px-6 py-3">Peminjam</th>
                                    <th class="px-6 py-3">Tanggal Pinjam</th>
                                    <th class="px-6 py-3 text-center">Qty</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($riwayatPeminjaman as $detail)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-3.5 font-mono font-medium text-slate-900">{{ $detail->peminjaman->kode_peminjaman ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-slate-600">{{ $detail->peminjaman->user->name ?? 'User terhapus' }}</td>
                                        <td class="px-6 py-3.5 text-slate-600">{{ $detail->peminjaman->tanggal_pinjam?->format('d M Y') ?? '-' }}</td>
                                        <td class="px-6 py-3.5 text-center text-slate-600">{{ $detail->jumlah }}</td>
                                        <td class="px-6 py-3.5 text-center">
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold
                                                {{ match($detail->peminjaman->status ?? '') { 'menunggu' => 'bg-amber-100 text-amber-700', 'dipinjam' => 'bg-teal-100 text-teal-700', 'dikembalikan' => 'bg-emerald-100 text-emerald-700', 'terlambat' => 'bg-red-100 text-red-700', default => 'bg-slate-100 text-slate-600' } }}">
                                                {{ ucfirst($detail->peminjaman->status ?? '-') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Riwayat mutasi stok (opsional) --}}
            @if($mutasi->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h2 class="font-bold text-slate-900 text-sm">Riwayat Mutasi Stok</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Jenis</th>
                                    <th class="px-6 py-3 text-center">Jumlah</th>
                                    <th class="px-6 py-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($mutasi as $m)
                                    <tr>
                                        <td class="px-6 py-3 text-slate-600">{{ $m->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold
                                                {{ match($m->jenis_mutasi ?? $m->jenis ?? '') { 'masuk' => 'bg-emerald-100 text-emerald-700', 'keluar' => 'bg-amber-100 text-amber-700', default => 'bg-slate-100 text-slate-600' } }}">
                                                {{ ucfirst($m->jenis_mutasi ?? $m->jenis ?? 'penyesuaian') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center font-bold text-slate-900">{{ $m->jumlah }}</td>
                                        <td class="px-6 py-3 text-slate-500">{{ $m->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ===== KOLOM KANAN (1/3) ===== --}}
        <div class="space-y-6">

            {{-- Kartu QR alat --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 text-center">
                @if(Route::has('qr.alat'))
                    <img src="{{ route('qr.alat', $alat) }}" alt="QR {{ $alat->kode_alat }}"
                         class="w-40 h-40 mx-auto rounded-lg border border-slate-200 bg-white p-2">
                @else
                    <div class="w-40 h-40 mx-auto rounded-lg border border-dashed border-slate-300 flex items-center justify-center text-slate-400 text-xs">QR belum tersedia</div>
                @endif
                <p class="text-sm font-bold text-slate-900 mt-3">QR Code Alat</p>
                <p class="text-xs text-slate-500 mt-1">Scan untuk serah terima atau pengembalian cepat tanpa cari kode manual.</p>
                @if(Route::has('petugas.alat.scan'))
                    <a href="{{ route('petugas.alat.scan') }}" class="mt-4 w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2m0-8h18"/></svg>
                        Scan QR Sekarang
                    </a>
                @endif
            </div>

            {{-- Informasi sistem --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 space-y-3">
                <h3 class="text-sm font-bold text-slate-900">Informasi Sistem</h3>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Dibuat</span>
                    <span class="font-semibold text-slate-900">{{ $alat->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Diperbarui</span>
                    <span class="font-semibold text-slate-900">{{ $alat->updated_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">ID Alat</span>
                    <span class="font-mono font-semibold text-slate-900">#{{ $alat->id }}</span>
                </div>
            </div>

            {{-- Tips operasional --}}
            <div class="bg-teal-50/60 border border-teal-200 rounded-xl p-5">
                <p class="text-sm font-bold text-teal-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tips Petugas
                </p>
                <ul class="mt-2 space-y-1.5 text-xs text-teal-700 list-disc list-inside">
                    <li>Periksa kondisi alat sebelum serah terima.</li>
                    <li>Stok tersedia berkurang otomatis saat peminjaman diverifikasi.</li>
                    <li>Catat kerusakan pada proses pengembalian.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection