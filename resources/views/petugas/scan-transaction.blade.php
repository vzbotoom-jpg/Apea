@extends('layouts.petugas')

@section('title', 'Hasil Scan Transaksi')
@section('page-title', 'Hasil Scan Transaksi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Transaksi ditemukan</p>
                <h2 class="mt-1 text-xl font-bold font-mono text-slate-900">{{ $peminjaman->kode_peminjaman }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $peminjaman->user->name }}</p>
            </div>
            <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-bold {{ $peminjaman->status_badge }}">
                {{ ucfirst($peminjaman->status) }}
            </span>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Tanggal pinjam</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
            </div>
            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Jatuh tempo</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</p>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-sm font-bold text-slate-900 mb-2">Alat dalam transaksi</h3>
            <ul class="divide-y divide-slate-100 rounded-lg border border-slate-200">
                @foreach($peminjaman->detailPeminjamans as $detail)
                    <li class="flex justify-between gap-4 p-3 text-sm">
                        <span class="text-slate-700">{{ $detail->alat->nama_alat }}</span>
                        <span class="shrink-0 font-semibold text-slate-900">Qty {{ $detail->jumlah }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6 border-t border-slate-100 pt-5">
            @if($peminjaman->status === 'menunggu')
                <p class="mb-3 text-sm text-slate-600">Periksa transaksi lalu verifikasi pengajuan ini.</p>
                <form action="{{ route('petugas.peminjaman.verifikasi', $peminjaman) }}" method="POST">
                    @csrf
                    <button class="w-full rounded-lg bg-teal-600 px-4 py-3 text-sm font-bold text-white hover:bg-teal-700">
                        Verifikasi Peminjaman
                    </button>
                </form>
            @elseif($peminjaman->status === 'diverifikasi')
                <p class="mb-3 text-sm text-slate-600">Konfirmasikan serah terima alat kepada peminjam.</p>
                <form action="{{ route('petugas.peminjaman.ambil', $peminjaman) }}" method="POST">
                    @csrf
                    <button class="w-full rounded-lg bg-teal-600 px-4 py-3 text-sm font-bold text-white hover:bg-teal-700">
                        Konfirmasi Serah Terima
                    </button>
                </form>
            @elseif(in_array($peminjaman->status, ['dipinjam', 'terlambat'], true))
                <p class="mb-3 text-sm text-slate-600">Transaksi aktif. Lanjutkan ke formulir untuk mencatat pengembalian dan kondisi alat.</p>
                <a href="{{ route('petugas.pengembalian.proses', $peminjaman) }}"
                   class="block w-full rounded-lg bg-teal-600 px-4 py-3 text-center text-sm font-bold text-white hover:bg-teal-700">
                    Proses Pengembalian
                </a>
            @else
                <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                    Tidak ada tindakan lanjutan untuk transaksi berstatus {{ $peminjaman->status }}.
                </p>
            @endif
        </div>

        <a href="{{ route('petugas.alat.scan') }}" class="mt-5 inline-flex text-sm font-semibold text-teal-700 hover:text-teal-800">
            Scan transaksi lain
        </a>
    </div>
</div>
@endsection
