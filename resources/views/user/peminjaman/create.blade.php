<!-- resources/views/user/peminjaman/create.blade.php -->
@extends('layouts.user')

@section('title', 'Ajukan Peminjaman')
@section('page-title', 'Ajukan Peminjaman')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- Info Banner: Limit Check --}}
    <div class="mb-6 p-4 rounded-lg flex items-start gap-3 {{ $totalDipinjamHariIni >= 2 ? 'bg-red-50 border border-red-200' : 'bg-teal-50 border border-teal-200' }}">
        <div class="mt-0.5">
            @if($totalDipinjamHariIni >= 2)
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            @else
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        <div>
            <p class="text-sm font-bold {{ $totalDipinjamHariIni >= 2 ? 'text-red-800' : 'text-teal-800' }}">
                Kuota Harian: {{ $totalDipinjamHariIni }} / 2 Alat
            </p>
            <p class="text-xs {{ $totalDipinjamHariIni >= 2 ? 'text-red-600' : 'text-teal-600' }} mt-0.5">
                @if($totalDipinjamHariIni >= 2)
                    Anda telah mencapai batas maksimal peminjaman untuk hari ini.
                @else
                    Anda masih dapat meminjam {{ 2 - $totalDipinjamHariIni }} alat lagi hari ini.
                @endif
            </p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 md:p-8">
            <form action="{{ route('user.peminjaman.store') }}" method="POST" class="space-y-8">
                @csrf

                {{-- Step 1: Pilih Alat --}}
                <div>
                    <label class="block text-sm font-bold text-slate-900 mb-4">1. Pilih Alat</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($alats as $alat)
                            <label class="relative flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-all duration-200
                                {{ $totalDipinjamHariIni >= 2 ? 'opacity-50 cursor-not-allowed bg-slate-50 border-slate-200' : 'border-slate-200 hover:border-teal-500 hover:bg-teal-50/30 has-[:checked]:border-teal-600 has-[:checked]:bg-teal-50 has-[:checked]:ring-1 has-[:checked]:ring-teal-600' }}">
                                
                                <input type="checkbox" name="alat_ids[]" value="{{ $alat->id }}" 
                                       class="mt-1 w-4 h-4 text-teal-600 border-slate-300 rounded focus:ring-teal-500"
                                       {{ $totalDipinjamHariIni >= 2 ? 'disabled' : '' }}>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm font-bold text-slate-900">{{ $alat->nama_alat }}</p>
                                        <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">{{ $alat->kode_alat }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1">Stok tersedia: <span class="font-semibold {{ $alat->stok_tersedia > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $alat->stok_tersedia }}</span></p>
                                </div>
                            </label>
                        @empty
                            <div class="col-span-2 p-8 text-center border border-dashed border-slate-300 rounded-lg">
                                <p class="text-slate-500 text-sm">Tidak ada alat yang tersedia saat ini.</p>
                            </div>
                        @endforelse
                    </div>
                    @error('alat_ids') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    @error('alat_ids.*') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Step 2: Jadwal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                    <div>
                        <label for="tanggal_pinjam" class="block text-sm font-bold text-slate-900 mb-2">2. Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" 
                               value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm text-slate-700">
                        @error('tanggal_pinjam') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tanggal_jatuh_tempo" class="block text-sm font-bold text-slate-900 mb-2">3. Tanggal Kembali</label>
                        <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" 
                               value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+3 days'))) }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm text-slate-700">
                        @error('tanggal_jatuh_tempo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Step 3: Catatan --}}
                <div class="pt-6 border-t border-slate-100">
                    <label for="catatan" class="block text-sm font-bold text-slate-900 mb-2">4. Catatan Tambahan <span class="font-normal text-slate-400">(Opsional)</span></label>
                    <textarea name="catatan" id="catatan" rows="3" 
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm text-slate-700"
                              placeholder="Contoh: Untuk praktikum lab fisika...">{{ old('catatan') }}</textarea>
                    @error('catatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="pt-6 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('user.peminjaman.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $totalDipinjamHariIni >= 2 ? 'disabled' : '' }}>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Rules Info --}}
    <div class="mt-6 p-5 bg-slate-50 border border-slate-200 rounded-lg">
        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ketentuan Peminjaman
        </h4>
        <ul class="space-y-2 text-xs text-slate-600">
            <li class="flex items-start gap-2">
                <span class="text-teal-500 mt-0.5">&#10003;</span>
                <span>Maksimal peminjaman adalah <strong>2 alat per hari</strong> untuk setiap pengguna.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-teal-500 mt-0.5">&#10003;</span>
                <span>Denda keterlambatan berlaku jika pengembalian melewati <strong>2 hari</strong> dari jatuh tempo.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-teal-500 mt-0.5">&#10003;</span>
                <span>Pastikan alat dikembalikan dalam kondisi baik untuk menghindari biaya kerusakan.</span>
            </li>
        </ul>
    </div>
</div>
@endsection