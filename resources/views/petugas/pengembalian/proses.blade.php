<!-- resources/views/petugas/pengembalian/proses.blade.php -->
@extends('layouts.petugas')

@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ===== Kartu Info Peminjaman ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kode Transaksi</p>
                <h2 class="text-2xl font-extrabold text-slate-900 font-mono">{{ $peminjaman->kode_peminjaman }}</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Peminjam: <span class="font-bold text-slate-900">{{ $peminjaman->user->name }}</span>
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold {{ $peminjaman->status === 'terlambat' ? 'bg-red-100 text-red-700' : 'bg-teal-100 text-teal-700' }}">
                {{ ucfirst($peminjaman->status) }}
            </span>
        </div>

        <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pinjam</p>
                <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jatuh Tempo</p>
                <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Terlambat</p>
                <p class="text-sm font-bold {{ $hariTerlambat > 0 ? 'text-red-600' : 'text-slate-900' }}">{{ $hariTerlambat }} hari</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Denda</p>
                <p class="text-sm font-bold {{ $denda > 0 ? 'text-red-600' : 'text-slate-900' }}">Rp {{ number_format($denda, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- ===== Alat yang Dikembalikan ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Alat yang Dikembalikan</h3>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                <tr>
                    <th class="px-6 py-3">Kode</th>
                    <th class="px-6 py-3">Nama Alat</th>
                    <th class="px-6 py-3 text-center">Qty</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($peminjaman->detailPeminjamans as $detail)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-3 font-mono text-slate-500">{{ $detail->alat->kode_alat }}</td>
                        <td class="px-6 py-3 font-medium text-slate-900">{{ $detail->alat->nama_alat }}</td>
                        <td class="px-6 py-3 text-center text-slate-600">{{ $detail->jumlah }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ===== Form Pemeriksaan Pengembalian ===== --}}
    <form method="POST" action="{{ route('petugas.pengembalian.save', $peminjaman) }}"
          class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pemeriksaan Pengembalian</h3>

        <div>
            <label for="kondisi_alat" class="block text-sm font-medium text-slate-700 mb-2">
                Kondisi Alat <span class="text-red-500">*</span>
            </label>
            <select name="kondisi_alat" id="kondisi_alat" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm">
                <option value="baik" {{ old('kondisi_alat') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak_ringan" {{ old('kondisi_alat') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak_berat" {{ old('kondisi_alat') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
            @error('kondisi_alat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="catatan" class="block text-sm font-medium text-slate-700 mb-2">Catatan Pemeriksaan (Opsional)</label>
            <textarea name="catatan" id="catatan" rows="3"
                      class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm"
                      placeholder="Contoh: casing retak, kelengkapan lengkap...">{{ old('catatan') }}</textarea>
            @error('catatan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @if($denda > 0)
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                ⚠️ Peminjam dikenakan denda <strong>Rp {{ number_format($denda, 0, ',', '.') }}</strong>
                (terlambat {{ $hariTerlambat }} hari di luar masa tenggang).
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengembalian
            </button>
            <a href="{{ route('petugas.pengembalian.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection