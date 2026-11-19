<!-- resources/views/user/profile/edit.blade.php -->
@extends('layouts.user')

@section('title', 'Edit Profil')
@section('page-title', 'Pengaturan Profil')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        {{-- Header Form --}}
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-bold text-slate-900">Informasi Akun</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui data diri dan keamanan akun Anda.</p>
        </div>

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 md:p-8 space-y-7 sm:space-y-8">
            @csrf
            @method('PUT')

            <section class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
                <h3 class="text-sm font-bold text-slate-900">Foto Profil</h3>
                <p class="mt-1 text-xs text-slate-500">Gunakan gambar JPG, PNG, atau WEBP dengan ukuran maksimal 2 MB.</p>

                <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <img id="foto_profil_preview"
                         @if($user->foto_profil) src="{{ asset('storage/'.$user->foto_profil) }}" @endif
                         alt="Foto profil {{ $user->name }}"
                         class="h-20 w-20 shrink-0 rounded-full border border-slate-200 bg-white object-cover {{ $user->foto_profil ? '' : 'hidden' }}">
                    <div id="foto_profil_placeholder" class="{{ $user->foto_profil ? 'hidden' : 'grid' }} h-20 w-20 shrink-0 place-items-center rounded-full bg-teal-100 text-2xl font-bold text-teal-700" aria-hidden="true">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <label for="foto_profil" class="mb-1.5 block text-sm font-medium text-slate-700">Unggah foto baru</label>
                        <input type="file" name="foto_profil" id="foto_profil" accept="image/jpeg,image/png,image/webp"
                               class="block w-full min-w-0 cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-3 file:cursor-pointer file:border-0 file:bg-teal-600 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 @error('foto_profil') border-red-500 @enderror">
                        @error('foto_profil') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- Section: Data Diri --}}
            <div class="space-y-5">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Data Diri</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('name') border-red-500 bg-red-50 @enderror">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('email') border-red-500 bg-red-50 @enderror">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-sm font-medium text-slate-700 mb-1.5">No. Telepon</label>
                        <input type="tel" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" autocomplete="tel" inputmode="tel"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('no_telepon') border-red-500 bg-red-50 @enderror">
                        @error('no_telepon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3"
                                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('alamat') border-red-500 bg-red-50 @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Section: Keamanan --}}
            <div class="space-y-5 pt-4 border-t border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Keamanan</h3>
                    <p class="text-xs text-slate-500 mt-1">Kosongkan kolom di bawah jika tidak ingin mengubah password.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                        <input type="password" name="password" id="password" autocomplete="new-password"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm @error('password') border-red-500 bg-red-50 @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition text-sm"
                               placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                <a href="{{ route('user.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 text-center text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const profilePhotoInput = document.getElementById('foto_profil');
    const profilePhotoPreview = document.getElementById('foto_profil_preview');
    const profilePhotoPlaceholder = document.getElementById('foto_profil_placeholder');
    let profilePhotoPreviewUrl;

    profilePhotoInput?.addEventListener('change', () => {
        const file = profilePhotoInput.files?.[0];
        if (!file) return;

        if (profilePhotoPreviewUrl) URL.revokeObjectURL(profilePhotoPreviewUrl);
        profilePhotoPreviewUrl = URL.createObjectURL(file);
        profilePhotoPreview.src = profilePhotoPreviewUrl;
        profilePhotoPreview.classList.remove('hidden');
        profilePhotoPlaceholder.classList.add('hidden');
    });
</script>
@endpush