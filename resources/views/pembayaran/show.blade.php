@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.petugas')
@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('content')
@php
    $statusBadge = match($pembayaran->status) {
        'menunggu'  => 'bg-amber-100 text-amber-700',
        'disetujui' => 'bg-emerald-100 text-emerald-700',
        'ditolak'   => 'bg-red-100 text-red-700',
        default     => 'bg-slate-100 text-slate-600',
    };
    $buktiUrl = $pembayaran->bukti_path
        ? (Route::has('pembayaran.bukti') ? route('pembayaran.bukti', $pembayaran) : asset('storage/' . $pembayaran->bukti_path))
        : null;
@endphp
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Kembali --}}
    <a href="{{ route('pembayaran.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-900 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar
    </a>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg text-sm font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-extrabold text-lg shrink-0">
                    {{ strtoupper(substr($pembayaran->user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $pembayaran->user->name }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <span class="font-mono font-bold">{{ $pembayaran->peminjaman->kode_peminjaman }}</span>
                        · Diajukan {{ $pembayaran->created_at->format('d M Y, H:i') }} WIB
                    </p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-2xl font-extrabold text-teal-600">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</p>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold mt-1 {{ $statusBadge }}">
                    {{ ucfirst($pembayaran->status) }}
                </span>
            </div>
        </div>

        <div class="p-6 space-y-6">

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Metode</p>
                    <p class="text-sm font-bold text-slate-900">{{ $pembayaran->metode_label }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nominal</p>
                    <p class="text-sm font-bold text-slate-900">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Tagihan</p>
                    <p class="text-sm font-bold text-slate-900">Rp {{ number_format($pembayaran->peminjaman->total_bayar, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Diverifikasi Oleh</p>
                    <p class="text-sm font-bold text-slate-900">{{ $pembayaran->verifier?->name ?? '—' }}</p>
                </div>
            </div>

            {{-- Bukti Pembayaran --}}
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Bukti Pembayaran</h3>
                @if($buktiUrl)
                    <a href="{{ $buktiUrl }}" target="_blank" class="block relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                        <img src="{{ $buktiUrl }}" alt="Bukti pembayaran" class="max-h-96 w-full object-contain bg-white">
                        <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition flex items-center justify-center">
                            <span class="opacity-0 group-hover:opacity-100 text-white text-sm font-bold flex items-center gap-2 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                Lihat Ukuran Penuh
                            </span>
                        </div>
                    </a>
                @else
                    <div class="p-8 border-2 border-dashed border-slate-200 rounded-xl text-center">
                        <p class="text-sm text-slate-500">Tidak ada bukti unggahan — pembayaran tunai di loket.</p>
                    </div>
                @endif
            </div>

            {{-- Catatan Penolakan --}}
            @if($pembayaran->catatan)
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-sm font-bold text-red-800">Catatan Penolakan</p>
                        <p class="text-xs text-red-600 mt-1">{{ $pembayaran->catatan }}</p>
                    </div>
                </div>
            @endif

            {{-- Info Verifikasi --}}
            @if($pembayaran->status === 'disetujui')
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">Pembayaran Terverifikasi — LUNAS</p>
                        <p class="text-xs text-emerald-600 mt-1">
                            Dikonfirmasi oleh {{ $pembayaran->verifier?->name }} pada {{ $pembayaran->verified_at?->format('d M Y, H:i') }} WIB.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Aksi Verifikasi --}}
        @if($pembayaran->status === 'menunggu')
            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Tindakan Verifikasi</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    {{-- Setujui --}}
                    <form action="{{ route('pembayaran.setujui', $pembayaran) }}" method="POST"
                          onsubmit="return confirm('Konfirmasi pembayaran ini sebagai LUNAS?')">
                        @csrf
                        <button type="submit"
                                class="w-full px-5 py-3 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Konfirmasi LUNAS
                        </button>
                    </form>
                    {{-- Tolak --}}
                    <form action="{{ route('pembayaran.tolak', $pembayaran) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="text" name="catatan" required placeholder="Alasan penolakan (wajib)…"
                               class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <button type="submit"
                                class="w-full px-5 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection