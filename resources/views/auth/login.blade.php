@extends('auth.layout')

@section('title', 'Login')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Selamat Datang Kembali</h1>
        <p class="text-slate-500">Silakan masuk ke akun Anda untuk melanjutkan.</p>
    </div>

    @if(session('status'))
        <div class="mb-6 p-4 bg-teal-50 border border-teal-200 text-teal-700 text-sm rounded-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 @error('email') border-red-500 bg-red-50 @enderror"
                   placeholder="nama@sekolah.sch.id">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-teal-600 hover:underline">Lupa password?</a>
            </div>
            <input id="password" type="password" name="password" required 
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 @error('password') border-red-500 bg-red-50 @enderror"
                   placeholder="••••••••">
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" 
                   class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-slate-300 rounded">
            <label for="remember_me" class="ml-2 block text-sm text-slate-600">
                Ingat saya selama 30 hari
            </label>
        </div>

        <div>
            <button type="submit" 
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-sm shadow-sm text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition duration-200">
                Masuk ke Dashboard
            </button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <p class="text-sm text-slate-500">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-semibold text-teal-600 hover:text-teal-500 ml-1">Daftar sekarang</a>
        </p>
    </div>
@endsection