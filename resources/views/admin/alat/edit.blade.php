<!-- resources/views/admin/alat/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Alat')
@section('page-title', 'Edit Alat')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-4">
            <a href="{{ route('admin.alat.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-white rounded-lg transition border border-transparent hover:border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-slate-900">Edit Data Alat</h2>
                <p class="text-sm text-slate-500">{{ $alat->nama_alat }} ({{ $alat->kode_alat }})</p>
            </div>
        </div>

        <form action="{{ route('admin.alat.update', $alat) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="nama_alat" class="block text-sm font-bold text-slate-700 mb-2">Nama Alat <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_alat" id="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('nama_alat') border-red-500 bg-red-50 @enderror">
                    @error('nama_alat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kategori_id" class="block text-sm font-bold text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori_id" id="kategori_id" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('kategori_id') border-red-500 bg-red-50 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $alat->kategori_id) == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stok" class="block text-sm font-bold text-slate-700 mb-2">Stok Total <span class="text-red-500">*</span></label>
                    <input type="number" name="stok" id="stok" value="{{ old('stok', $alat->stok) }}" min="0"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('stok') border-red-500 bg-red-50 @enderror">
                    <p class="mt-1 text-xs text-slate-500">Stok tersedia saat ini: {{ $alat->stok_tersedia }}</p>
                    @error('stok') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="harga_sewa_per_hari" class="block text-sm font-bold text-slate-700 mb-2">Harga Sewa / Hari <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="harga_sewa_per_hari" id="harga_sewa_per_hari" value="{{ old('harga_sewa_per_hari', $alat->harga_sewa_per_hari) }}" min="0"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('harga_sewa_per_hari') border-red-500 bg-red-50 @enderror">
                    </div>
                    @error('harga_sewa_per_hari') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="denda_per_hari" class="block text-sm font-bold text-slate-700 mb-2">Denda / Hari <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="denda_per_hari" id="denda_per_hari" value="{{ old('denda_per_hari', $alat->denda_per_hari) }}" min="0"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('denda_per_hari') border-red-500 bg-red-50 @enderror">
                    </div>
                    @error('denda_per_hari') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kondisi" class="block text-sm font-bold text-slate-700 mb-2">Kondisi <span class="text-red-500">*</span></label>
                    <select name="kondisi" id="kondisi" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('kondisi') border-red-500 bg-red-50 @enderror">
                        <option value="baik" {{ old('kondisi', $alat->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak_ringan" {{ old('kondisi', $alat->kondisi) == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="rusak_berat" {{ old('kondisi', $alat->kondisi) == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                        <option value="perbaikan" {{ old('kondisi', $alat->kondisi) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                    @error('kondisi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-bold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" 
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('status') border-red-500 bg-red-50 @enderror">
                        <option value="tersedia" {{ old('status', $alat->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="dipinjam" {{ old('status', $alat->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="perbaikan" {{ old('status', $alat->status) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="tidak_tersedia" {{ old('status', $alat->status) == 'tidak_tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('deskripsi') border-red-500 bg-red-50 @enderror">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
                    @error('deskripsi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="gambar" class="block text-sm font-bold text-slate-700 mb-2">Gambar Alat</label>
                    @if($alat->gambar)
                        <div class="mb-3 flex items-center gap-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                            <div>
                                <p class="text-sm font-medium text-slate-900">Gambar saat ini</p>
                                <p class="text-xs text-slate-500">Upload gambar baru untuk mengganti</p>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="gambar" id="gambar" accept="image/*"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 @error('gambar') border-red-500 bg-red-50 @enderror">
                    <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                    @error('gambar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.alat.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm">
                    Update Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection