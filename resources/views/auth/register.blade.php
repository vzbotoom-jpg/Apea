@extends('auth.layout')

@section('title', 'Register')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Buat Akun Baru</h1>
        <p class="text-slate-500">Daftar untuk mulai mengelola peminjaman alat.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('name') border-red-500 bg-red-50 @enderror"
                   placeholder="Masukkan nama lengkap Anda">
            @error('name')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('email') border-red-500 bg-red-50 @enderror"
                   placeholder="nama@sekolah.sch.id">
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="no_telepon" class="block text-sm font-medium text-slate-700 mb-1.5">No. Telepon</label>
                <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('no_telepon') border-red-500 bg-red-50 @enderror"
                       placeholder="0812...">
                @error('no_telepon')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="role_display" class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
                <input type="text" value="User / Peminjam" disabled
                       class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-sm text-slate-500 cursor-not-allowed">
            </div>
        </div>

        <div>
            <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
            <textarea id="alamat" name="alamat" rows="2" 
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('alamat') border-red-500 bg-red-50 @enderror"
                      placeholder="Alamat lengkap Anda">{{ old('alamat') }}</textarea>
            @error('alamat')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('password') border-red-500 bg-red-50 @enderror"
                       placeholder="Min. 8 karakter">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                       placeholder="Ulangi password">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" 
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-sm shadow-sm text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-200">
                Daftar Akun
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:text-teal-600 ml-1">Login di sini</a>
        </p>
    </div>
@endsection