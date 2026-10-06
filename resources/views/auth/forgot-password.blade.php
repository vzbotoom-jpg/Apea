<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - APIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4">
<div class="max-w-md w-full">
    <div class="text-center mb-8">
        @include('partials.auth-logo')
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <h1 class="text-xl font-bold text-slate-900 mb-2">Lupa Password</h1>
        <p class="text-sm text-slate-600 mb-6">Masukkan email terdaftar Anda. Kami akan mengirimkan tautan untuk mengatur ulang password.</p>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded-r-lg">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm">
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                Kirim Tautan Reset
            </button>
        </form>

        <a href="{{ route('login') }}" class="block text-center text-sm text-slate-600 hover:text-teal-600 mt-4">
            ← Kembali ke Login
        </a>
    </div>
</div>
</body>
</html>