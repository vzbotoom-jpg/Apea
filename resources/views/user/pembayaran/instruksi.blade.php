@extends('layouts.user')
@section('title', 'Instruksi Pembayaran')
@section('page-title', 'Instruksi Pembayaran')
@section('content')
@php
    $statusBadge = match($pembayaran->status) {
        'menunggu' => 'bg-amber-100 text-amber-700',
        'disetujui' => 'bg-emerald-100 text-emerald-700',
        'ditolak' => 'bg-red-100 text-red-700',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Pembayaran {{ $pembayaran->metode_label }}</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Transaksi <span class="font-mono font-semibold">{{ $pembayaran->peminjaman->kode_peminjaman }}</span>
                </p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $statusBadge }}">
                {{ ucfirst($pembayaran->status) }}
            </span>
        </div>

        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
            <span class="text-sm font-semibold text-slate-700">Nominal tagihan</span>
            <span class="text-xl font-extrabold text-teal-600">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 text-center">
        @if($pembayaran->status === 'menunggu')
            @if($pembayaran->metode === 'tunai')
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-teal-50 text-3xl">💵</div>
                <h3 class="mt-4 text-base font-bold text-slate-900">Bayar di loket petugas</h3>
                <p class="mt-2 text-sm text-slate-600">Tunjukkan kode transaksi dan lakukan pembayaran tunai kepada petugas. Pembayaran akan dikonfirmasi setelah diterima.</p>
            @elseif($pembayaran->qr_path)
                <h3 class="text-base font-bold text-slate-900">Scan QR {{ $pembayaran->metode_label }}</h3>
                <img src="{{ asset('storage/'.$pembayaran->qr_path) }}" alt="QR tujuan pembayaran {{ $pembayaran->metode_label }}"
                     class="mx-auto mt-4 h-64 w-64 rounded-lg border border-slate-200 bg-white object-contain p-2">
                <p class="mt-4 text-sm text-slate-600">
                    Bayar tepat sebesar <strong>Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</strong>.
                    Pembayaran akan diverifikasi admin setelah dana diterima.
                </p>
            @else
                <p class="text-sm text-amber-800">QR untuk metode ini tidak tersedia. Silakan hubungi admin sebelum melakukan pembayaran.</p>
            @endif
            <p class="mt-5 rounded-lg bg-amber-50 p-3 text-left text-xs text-amber-800">
                Status ini belum berarti pembayaran sudah diterima. Jangan melakukan pembayaran berulang; admin/petugas akan memverifikasi transaksi secara manual.
            </p>
            <form action="{{ route('user.pembayaran.cancel', $pembayaran) }}" method="POST" class="mt-5 border-t border-slate-100 pt-4 text-left">
                @csrf
                <label class="flex items-start gap-2 text-xs text-slate-600">
                    <input type="checkbox" name="belum_dibayar" value="1" required class="mt-0.5 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span>Saya belum mengirim pembayaran untuk instruksi ini.</span>
                </label>
                @error('belum_dibayar') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                <button type="submit" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batalkan dan pilih metode lain
                </button>
            </form>
        @elseif($pembayaran->status === 'disetujui')
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-3xl">✓</div>
            <h3 class="mt-4 text-base font-bold text-emerald-800">Pembayaran sudah diverifikasi</h3>
            <p class="mt-2 text-sm text-emerald-700">Pembayaran ini telah diterima dan disetujui oleh petugas.</p>
        @else
            <h3 class="text-base font-bold text-red-800">{{ $pembayaran->status === 'dibatalkan' ? 'Instruksi pembayaran dibatalkan' : 'Pembayaran ditolak' }}</h3>
            <p class="mt-2 text-sm text-red-700">{{ $pembayaran->catatan ?: 'Silakan hubungi petugas untuk informasi lebih lanjut.' }}</p>
            <a href="{{ route('user.peminjaman.bayar', $pembayaran->peminjaman) }}"
               class="mt-4 inline-flex rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-teal-700">
                Pilih Metode Lain
            </a>
        @endif
    </div>

    <a href="{{ route('user.peminjaman.show', $pembayaran->peminjaman) }}"
       class="inline-flex w-full justify-center rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">
        Kembali ke Detail Peminjaman
    </a>
</div>
@endsection
