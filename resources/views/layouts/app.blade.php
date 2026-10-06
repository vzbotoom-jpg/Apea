<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - APIC</title>
    
    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

    <div class="flex h-screen overflow-hidden">
        
        {{-- SIDEBAR --}}
        @include('components.sidebar') {{-- Kita akan buat file sidebar universal atau spesifik di bawah --}}

        {{-- MAIN CONTENT WRAPPER --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            
            {{-- TOPBAR / HEADER --}}
            @include('components.header') {{-- Kita akan buat file header di bawah --}}

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 bg-slate-50">
                <div class="max-w-7xl mx-auto">
                    
                    {{-- Page Title & Breadcrumb Area --}}
                    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                            @if(isset($breadcrumb))
                                <nav class="text-sm text-slate-500 mt-1">{{ $breadcrumb }}</nav>
                            @endif
                        </div>
                        <div>
                            @yield('page-actions')
                        </div>
                    </div>

                    {{-- Alerts --}}
                    @include('components.alerts')

                    {{-- Content Yield --}}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>