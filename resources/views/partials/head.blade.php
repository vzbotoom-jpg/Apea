<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'APIC')</title>

{{-- PWA Meta Tags --}}
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d9488">
<meta name="'mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="APIC">
<link rel="apple-touch-icon" href="/images/icons/icon-152x152.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

{{-- ===== Favicon & Branding ===== --}}
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
<meta name="theme-color" content="#0d9488">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('styles')