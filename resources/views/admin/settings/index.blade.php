<!-- resources/views/admin/settings/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
@php
    $settings = $settings ?? [
        'max_alat_per_hari'      => 2,
        'masa_tenggang_hari'     => 2,
        'denda_per_hari'         => 5000,
        'maks_perpanjangan_hari' => 7,
        'metode_pembayaran_aktif' => ['tunai', 'transfer'],
        'info_transfer'          => '',
    ];
    $contohDenda = max(0, 5 - $settings['masa_tenggang_hari']) * $settings['denda_per_hari'];
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- ===== ATURAN PEMINJAMAN ===== --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Aturan Peminjaman</h2>
                    <p class="text-sm text-gray-500">Batas peminjaman yang berlaku untuk seluruh user.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="max_alat_per_hari" class="block text-sm font-medium text-gray-700 mb-2">
                        Maks. Alat / Hari / User <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_alat_per_hari" id="max_alat_per_hari" min="1" max="10"
                           value="{{ old('max_alat_per_hari', $settings['max_alat_per_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Jumlah alat maksimal yang dapat dipinjam satu user dalam satu hari.</p>
                    @error('max_alat_per_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="maks_perpanjangan_hari" class="block text-sm font-medium text-gray-700 mb-2">
                        Maks. Perpanjangan (Hari) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="maks_perpanjangan_hari" id="maks_perpanjangan_hari" min="1" max="30"
                           value="{{ old('maks_perpanjangan_hari', $settings['maks_perpanjangan_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Batas tambahan hari saat user mengajukan perpanjangan.</p>
                    @error('maks_perpanjangan_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===== ATURAN DENDA ===== --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Aturan Denda</h2>
                    <p class="text-sm text-gray-500">Masa tenggang dan tarif denda keterlambatan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="masa_tenggang_hari" class="block text-sm font-medium text-gray-700 mb-2">
                        Masa Tenggang (Hari) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="masa_tenggang_hari" id="masa_tenggang_hari" min="0" max="14"
                           value="{{ old('masa_tenggang_hari', $settings['masa_tenggang_hari']) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Hari bebas denda setelah jatuh tempo.</p>
                    @error('masa_tenggang_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="denda_per_hari" class="block text-sm font-medium text-gray-700 mb-2">
                        Tarif Denda / Hari <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500">Rp</span>
                        <input type="number" name="denda_per_hari" id="denda_per_hari" min="0" step="500"
                               value="{{ old('denda_per_hari', $settings['denda_per_hari']) }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Denda per alat per hari setelah masa tenggang habis.</p>
                    @error('denda_per_hari') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Preview perhitungan --}}
            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600">
                <strong>Contoh:</strong> terlambat 5 hari → (5 − {{ $settings['masa_tenggang_hari'] }}) ×
                Rp {{ number_format($settings['denda_per_hari'], 0, ',', '.') }} =
                <strong class="text-gray-800">Rp {{ number_format($contohDenda, 0, ',', '.') }}</strong> per alat.
            </div>
        </div>

        {{-- ===== ✅ BARU: METODE PEMBAYARAN (Kendali Admin) ===== --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Metode Pembayaran</h2>
                    <p class="text-sm text-gray-500">Metode yang boleh dipakai petugas saat konfirmasi pembayaran.</p>
                </div>
            </div>

            <div class="space-y-3">
                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition {{ in_array('tunai', $settings['metode_pembayaran_aktif']) ? 'border-teal-300 bg-teal-50/50' : 'border-gray-200 hover:bg-gray-50' }}">
                    <input type="checkbox" name="metode_pembayaran_aktif[]" value="tunai"
                           {{ in_array('tunai', $settings['metode_pembayaran_aktif']) ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <span>
                        <span class="block text-sm font-medium text-gray-800">Tunai</span>
                        <span class="block text-xs text-gray-500">Pembayaran langsung di loket petugas.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition {{ in_array('transfer', $settings['metode_pembayaran_aktif']) ? 'border-teal-300 bg-teal-50/50' : 'border-gray-200 hover:bg-gray-50' }}">
                    <input type="checkbox" name="metode_pembayaran_aktif[]" value="transfer"
                           {{ in_array('transfer', $settings['metode_pembayaran_aktif']) ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <span>
                        <span class="block text-sm font-medium text-gray-800">Transfer Bank</span>
                        <span class="block text-xs text-gray-500">User transfer ke rekening, petugas/admin verifikasi.</span>
                    </span>
                </label>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Info Rekening Tujuan (jika transfer aktif)</label>
                    <input type="text" name="info_transfer" value="{{ old('info_transfer', $settings['info_transfer']) }}"
                           placeholder="Contoh: BRI 1234-5678-9012 a.n."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @error('info_transfer') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200">
                Simpan Pengaturan
            </button>
            <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                Batal
            </a>
        </div>
    </form>

    {{-- Catatan --}}
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
        <strong>Catatan:</strong> Nilai di atas otomatis dipakai oleh seluruh modul (validasi peminjaman,
        perhitungan denda, dan perpanjangan) tanpa perlu mengubah kode.
    </div>
</div>
@endsection