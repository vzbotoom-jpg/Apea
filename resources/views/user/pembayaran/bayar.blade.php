@extends('layouts.user')
@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Ringkasan Tagihan --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Ringkasan Tagihan</h2>
        <div class="flex justify-between text-sm text-slate-600 mb-2">
            <span>Kode Transaksi</span>
            <strong class="font-mono text-slate-900">{{ $peminjaman->kode_peminjaman }}</strong>
        </div>
        <div class="flex justify-between items-center pt-3 border-t border-slate-100">
            <span class="text-sm font-bold text-slate-900">Total Tagihan (sewa + denda)</span>
            <span class="text-xl font-extrabold text-teal-600">Rp {{ number_format($peminjaman->total_bayar, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Form Pembayaran --}}
    <form action="{{ route('user.peminjaman.bayar.store', $peminjaman) }}" method="POST"
          enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-slate-900 mb-3">1. Pilih Metode Pembayaran</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($metodeAktif as $m)
                    <label class="relative p-3 border border-slate-200 rounded-lg cursor-pointer text-center transition
                                  has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50 has-[:checked]:ring-1 has-[:checked]:ring-teal-600">
                        <input type="radio" name="metode" value="{{ $m }}" class="sr-only" {{ $loop->first ? 'checked' : '' }} required>
                        <span class="block text-sm font-bold text-slate-900">{{ \App\Models\Pembayaran::METODE_LIST[$m] ?? $m }}</span>
                        @if(!empty($tujuan[$m]))
                            <span class="block text-[10px] text-slate-500 mt-1">{{ $tujuan[$m] }}</span>
                        @endif
                    </label>
                @endforeach
            </div>
            @error('metode') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">2. Nominal yang Ditransfer</label>
            <input type="number" name="nominal" value="{{ old('nominal', $peminjaman->total_bayar) }}" min="1" required
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 outline-none">
            @error('nominal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-900 mb-2">3. Unggah Bukti Transfer / Pembayaran</label>
            <input type="file" name="bukti" accept="image/*" required
                   class="w-full text-sm border border-slate-200 rounded-lg file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:bg-teal-600 file:text-white file:text-sm file:font-bold">
            <p class="text-xs text-slate-500 mt-1">Screenshot bukti transfer/e-wallet (JPG/PNG, maks 2MB).</p>
            @error('bukti') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
            📤 Kirim untuk Verifikasi
        </button>
    </form>
</div>
@endsection