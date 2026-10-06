<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - APIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4">
<div class="max-w-md w-full">
    <div class="text-center mb-8">
        @include('partials.auth-logo')
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <h1 class="text-xl font-bold text-slate-900 mb-2">Atur Ulang Password</h1>
        <p class="text-sm text-slate-600 mb-6">Buat password baru untuk akun <strong>{{ $email }}</strong>.</p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru</label>
                <input type="password" name="password" required autofocus
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none text-sm">
            </div>
            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>
</body>
</html>