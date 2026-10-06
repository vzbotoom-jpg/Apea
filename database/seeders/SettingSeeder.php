@extends('layouts.admin')
@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Pengaturan Sistem</h2>
        <p class="text-sm text-gray-600 mb-6">
            Aturan bisnis di bawah ini dipakai <strong>otomatis</strong> oleh seluruh modul
            (peminjaman, denda, perpanjangan) — tidak ada lagi nilai hardcoded.
        </p>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maks. Alat / Hari / User</label>
                    <input type="number" name="max_alat_per_hari" min="1" max="10"
                           value="{{ old('max_alat_per_hari', $settings['max_alat_per_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-gray-500">Batas jumlah alat yang bisa dipinjam per user per hari.</p>
                    @error('max_alat_per_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Masa Tenggang (hari)</label>
                    <input type="number" name="masa_tenggang_hari" min="0" max="14"
                           value="{{ old('masa_tenggang_hari', $settings['masa_tenggang_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-gray-500">Hari bebas denda setelah jatuh tempo.</p>
                    @error('masa_tenggang_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarif Denda / Hari (Rp)</label>
                    <input type="number" name="denda_per_hari" min="0" step="500"
                           value="{{ old('denda_per_hari', $settings['denda_per_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-gray-500">Denda per alat per hari setelah masa tenggang.</p>
                    @error('denda_per_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maks. Perpanjangan (hari)</label>
                    <input type="number" name="maks_perpanjangan_hari" min="1" max="30"
                           value="{{ old('maks_perpanjangan_hari', $settings['maks_perpanjangan_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <p class="mt-1 text-xs text-gray-500">Batas tambahan hari saat user mengajukan perpanjangan.</p>
                    @error('maks_perpanjangan_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200">
                    Simpan Pengaturan
                </button>
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection