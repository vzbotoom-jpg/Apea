<!-- resources/views/partials/auth-logo.blade.php -->
<a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group">
    @if(file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png') }}" alt="Logo APIC"
             class="w-11 h-11 rounded-lg object-contain shadow-sm shrink-0 group-hover:scale-105 transition">
    @else
        {{-- Fallback jika file logo belum ada --}}
        <span class="w-11 h-11 bg-teal-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">A</span>
    @endif
    <span class="leading-tight text-left">
        <span class="block text-xl font-extrabold tracking-tight text-slate-900">APIC</span>
        <span class="block text-[10px] text-slate-500 -mt-0.5 tracking-wide">Alat Pinjam Mudah Cepat</span>
    </span>
</a>