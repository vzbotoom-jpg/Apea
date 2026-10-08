@extends('layouts.user')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')
@php
    $steps = ['Menunggu', 'Diverifikasi', 'Dipinjam', 'Dikembalikan'];
    $stepIndex = ['menunggu' => 0, 'diverifikasi' => 1, 'dipinjam' => 2, 'terlambat' => 2, 'dikembalikan' => 3];
    $fmt = fn($d) => $d->locale('id')->translatedFormat('d M Y');
@endphp
<div class="space-y-6">

    {{-- 1. WELCOME BANNER --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Selamat datang, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola peminjaman alat dan pantau semua aktivitas Anda di sini.</p>
        </div>
        <a href="{{ route('user.peminjaman.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg transition shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- 2. BANNER KUOTA --}}
    <div class="bg-teal-50/60 border border-teal-200 rounded-xl px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </span>
            <p class="text-sm text-teal-700">
                <span class="font-bold">Kuota Harian: {{ $kuotaSisa ?? 2 }}/2 Alat</span>
                <span class="hidden md:inline text-teal-600/80 ml-2">Kuota pengajuan baru hari ini. Anda masih dapat mengajukan {{ $kuotaSisa ?? 2 }} alat.</span>
            </p>
        </div>
        <p class="text-[11px] text-teal-600/70">Reset pukul 00.00</p>
    </div>

    {{-- 3. STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Aktif', 'Sedang dipinjam', $dipinjam, 'text-teal-600', 'bg-teal-50 text-teal-600', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['Menunggu', 'Menunggu verifikasi', $menunggu, 'text-amber-600', 'bg-amber-50 text-amber-600', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Terlambat', $terlambat > 0 ? 'Perlu tindakan' : 'Tidak ada keterlambatan', $terlambat, 'text-red-600', 'bg-red-50 text-red-600', 'M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            ['Total', 'Sepanjang penggunaan', $totalPeminjaman, 'text-slate-900', 'bg-slate-100 text-slate-600', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ] as $s)
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $s[0] }}</p>
                        <p class="text-2xl font-bold {{ $s[3] }} mt-1">{{ $s[2] }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $s[1] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg {{ $s[4] }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $s[5] }}"/></svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 4. GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- KIRI: PEMINJAMAN AKTIF --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Peminjaman Aktif <span class="text-xs font-normal text-slate-400 ml-1">{{ $aktif->count() }} peminjaman</span></h2>
                <a href="{{ route('user.peminjaman.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat semua →</a>
            </div>

            @forelse($aktif as $p)
                @php
                    $cur   = $stepIndex[$p->status] ?? 0;
                    $items = $p->detailPeminjamans->map(fn($d) => (optional($d->alat)->nama_alat ?? 'Alat').' x'.$d->jumlah)->implode(' / ');
                    $total = $p->detailPeminjamans->sum('subtotal');
                    $lunas = ($p->status_pembayaran ?? optional($p->pembayarans->first())->status ?? 'belum_lunas') === 'lunas';
                @endphp
                <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-4 hover:shadow-sm transition">
                    {{-- header kartu --}}
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="font-mono text-sm font-bold text-slate-900">{{ $p->kode_peminjaman }}</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                {{ $p->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : ($p->status === 'dipinjam' ? 'bg-teal-100 text-teal-700' : ($p->status === 'terlambat' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ ucfirst($p->status) }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400">{{ $p->status === 'menunggu' ? 'Diajukan '.$fmt($p->created_at) : $p->detailPeminjamans->sum('jumlah').' unit alat' }}</span>
                    </div>

                    {{-- isi kartu --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $items ?: 'Belum ada item' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $fmt($p->tanggal_pinjam) }} → {{ $fmt($p->tanggal_jatuh_tempo) }}@if($p->status === 'menunggu') · Menunggu verifikasi petugas @endif</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-base font-bold text-slate-900">Rp {{ number_format($total, 0, ',', '.') }}</p>
                            @if($lunas)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">LUNAS</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700"><span class="w-1 h-1 rounded-full bg-amber-500"></span>BELUM LUNAS</span>
                            @endif
                        </div>
                    </div>

                    {{-- progress 4 langkah (selain menunggu) --}}
                    @if($p->status !== 'menunggu')
                        <div class="bg-slate-50 rounded-lg px-4 py-3 flex items-center">
                            @foreach($steps as $i => $label)
                                <div class="flex items-center {{ $i < 3 ? 'flex-1' : '' }}">
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center
                                            {{ $i < $cur ? 'bg-teal-600 text-white' : ($i === $cur ? 'bg-white border-2 border-teal-600' : 'bg-white border-2 border-slate-200') }}">
                                            @if($i < $cur)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($i === $cur)
                                                <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                                            @endif
                                        </span>
                                        <span class="text-[11px] font-medium {{ $i <= $cur ? 'text-slate-700' : 'text-slate-400' }}">{{ $label }}</span>
                                    </div>
                                    @if($i < 3)<div class="flex-1 h-px mx-2 {{ $i < $cur ? 'bg-teal-500' : 'bg-slate-200' }}"></div>@endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- aksi --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('user.peminjaman.show', $p) }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:border-slate-300 transition">Lihat Detail</a>
                            @if(in_array($p->status, ['dipinjam', 'terlambat']))
                                <a href="{{ route('user.peminjaman.show', $p) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:border-slate-300 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Perpanjang
                                </a>
                            @endif
                            @if($p->status === 'menunggu')
                                <button class="px-4 py-2 bg-white border border-red-200 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50 transition">Batalkan</button>
                            @endif
                        </div>
                        @if(in_array($p->status, ['dipinjam', 'terlambat']) && !$lunas)
                            <a href="{{ route('user.peminjaman.show', $p) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 rounded-lg text-xs font-bold text-white transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Bayar Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white border border-dashed border-slate-300 rounded-xl p-10 text-center">
                    <p class="text-sm font-semibold text-slate-900">Belum ada peminjaman aktif</p>
                    <p class="text-xs text-slate-500 mt-1 mb-4">Ajukan peminjaman pertama Anda sekarang.</p>
                    <a href="{{ route('user.peminjaman.create') }}" class="inline-block px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition">Ajukan Peminjaman</a>
                </div>
            @endforelse
        </div>

        {{-- KANAN: NOTIFIKASI + REMINDER --}}
        <div class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-sm">Notifikasi</h3>
                    <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-full">{{ $unread }} belum dibaca</span>
                </div>
                <div class="space-y-4">
                    @forelse($notifications as $n)
                        <div class="flex gap-3">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ !$n->is_read ? 'bg-teal-50 text-teal-600' : 'bg-slate-100 text-slate-400' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900">{{ $n->title }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $n->message }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$n->is_read)<span class="w-1.5 h-1.5 rounded-full bg-teal-500 mt-1 shrink-0"></span>@endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-2">Tidak ada notifikasi baru</p>
                    @endforelse
                </div>
                <a href="{{ route('user.peminjaman.index') }}" class="block text-center text-[11px] font-bold text-teal-600 hover:text-teal-700 mt-4 pt-3 border-t border-slate-100">Lihat semua notifikasi →</a>
            </div>

            @if($reminder)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <p class="text-sm font-bold text-amber-800 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Jatuh tempo {{ $daysLeft <= 0 ? 'hari ini' : 'dalam '.$daysLeft.' hari' }}
                    </p>
                    <p class="text-xs text-amber-700 mt-1.5 leading-relaxed">{{ $reminder->kode_peminjaman }} perlu dikembalikan pada {{ $fmt($reminder->tanggal_jatuh_tempo) }}. Ajukan perpanjangan jika masih dibutuhkan.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection