<!-- resources/views/admin/pengembalian/proses.blade.php -->
@extends('layouts.admin')

@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-4">
            <a href="{{ route('admin.pengembalian.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-bold text-slate-900">Proses Pengembalian</h2>
        </div>

        <div class="p-6 space-y-6">
            
            {{-- Info Banner --}}
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong class="font-bold">Kode Peminjaman:</strong> <span class="font-mono">{{ $peminjaman->kode_peminjaman }}</span><br>
                    <strong class="font-bold">Peminjam:</strong> {{ $peminjaman->user->name }}
                </p>
            </div>

            {{-- Late Warning --}}
            @if($hariTerlambat > 0)
                <div class="p-4 {{ $hariTerlambat > 2 ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200' }} border rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 {{ $hariTerlambat > 2 ? 'text-red-600' : 'text-amber-600' }} mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-bold {{ $hariTerlambat > 2 ? 'text-red-800' : 'text-amber-800' }}">
                            Terlambat {{ $hariTerlambat }} hari
                        </p>
                        @if($denda > 0)
                            <p class="text-sm {{ $hariTerlambat > 2 ? 'text-red-700' : 'text-amber-700' }} mt-1">
                                Denda: <strong>Rp {{ number_format($denda, 0, ',', '.') }}</strong>
                            </p>
                        @else
                            <p class="text-sm {{ $hariTerlambat > 2 ? 'text-red-700' : 'text-amber-700' }} mt-1">
                                Belum terkena denda (masa tenggang 2 hari)
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('admin.pengembalian.save', $peminjaman) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <div>
                    <label for="kondisi_alat" class="block text-sm font-bold text-slate-700 mb-2">
                        Kondisi Alat <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi_alat" id="kondisi_alat" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('kondisi_alat') border-red-500 bg-red-50 @enderror">
                        <option value="baik">Baik</option>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                        <option value="hilang">Hilang</option>
                    </select>
                    @error('kondisi_alat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="denda" class="block text-sm font-bold text-slate-700 mb-2">
                        Denda
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="denda" id="denda" 
                               value="{{ old('denda', $denda) }}"
                               min="0"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('denda') border-red-500 bg-red-50 @enderror">
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Isi dengan jumlah denda jika ada. Kosongkan untuk menggunakan perhitungan otomatis.</p>
                    @error('denda') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="bukti_foto" class="block text-sm font-bold text-slate-700 mb-2">
                        Bukti Foto (Opsional)
                    </label>
                    <input type="file" name="bukti_foto" id="bukti_foto" 
                           accept="image/*"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 @error('bukti_foto') border-red-500 bg-red-50 @enderror">
                    <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG. Maksimal 2MB</p>
                    @error('bukti_foto') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="catatan" class="block text-sm font-bold text-slate-700 mb-2">
                        Catatan
                    </label>
                    <textarea name="catatan" id="catatan" rows="3"
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('catatan') border-red-500 bg-red-50 @enderror"
                              placeholder="Tambahkan catatan jika diperlukan...">{{ old('catatan') }}</textarea>
                    @error('catatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="pt-6 border-t border-slate-100 flex items-center gap-3 justify-end">
                    <button type="submit" onclick="return confirm('Konfirmasi pengembalian ini?')"
                            class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Proses Pengembalian
                    </button>
                    <a href="{{ route('admin.pengembalian.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection