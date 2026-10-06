<!-- resources/views/auth/layout.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi') - APIC</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        {{-- ✅ LOGO APIC --}}
        <div class="flex justify-center mb-8">
            @include('partials.auth-logo')
        </div>

        {{-- Kartu Konten --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
            @yield('content')
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; {{ date('Y') }} <span class="font-bold text-slate-500">APIC</span>. All Right Reserved
        </p>
    </div>
</body>
</html>