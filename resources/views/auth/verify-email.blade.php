<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - APIC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4">
<div class="max-w-md w-full">
    <div class="text-center mb-8">
        @include('partials.auth-logo')
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
        <div class="w-16 h-16 mx-auto bg-teal-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-slate-900 mb-2">Verifikasi Email Anda</h1>
        <p class="text-sm text-slate-600 mb-6">
            Kami telah mengirimkan tautan verifikasi ke
            <strong class="text-slate-900">{{ auth()->user()->email }}</strong>.
            Klik tautan tersebut untuk mengaktifkan akun Anda.
        </p>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded-r-lg text-left">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('verification.resend') }}" method="POST" class="space-y-3">
            @csrf
            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form action="{{ route('logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="w-full py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                Keluar
            </button>
        </form>

        <p class="text-xs text-slate-400 mt-4">
            💡 Mode demo: buka <code class="bg-slate-100 px-1 rounded">storage/logs/laravel.log</code> untuk melihat tautan verifikasi.
        </p>
    </div>
</div>
</body>
</html>