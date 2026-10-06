@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.petugas')
@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div>
        <h2 class="text-xl font-extrabold text-slate-900">Verifikasi Pembayaran</h2>
        <p class="text-sm text-slate-500 mt-0.5">Periksa bukti transfer / e-wallet dari peminjam, lalu konfirmasi status pembayaran.</p>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Verifikasi</p>
                <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $menunggu }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Uang Terkumpul</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $pembayarans->total() }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Daftar Pembayaran</h3>
        </div>

        @if($pembayarans->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <p class="text-sm font-bold text-slate-900">Belum ada pembayaran</p>
                <p class="text-xs text-slate-500 mt-1">Pembayaran dari peminjam akan muncul di sini untuk diverifikasi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3">Peminjam</th>
                            <th class="px-5 py-3">Transaksi</th>
                            <th class="px-5 py-3">Metode</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pembayarans as $p)
                            @php
                                $metodeBadge = match($p->metode) {
                                    'transfer_bank' => 'bg-blue-100 text-blue-700',
                                    'dana'          => 'bg-sky-100 text-sky-700',
                                    'shopeepay'     => 'bg-orange-100 text-orange-700',
                                    'gopay'         => 'bg-teal-100 text-teal-700',
                                    'ovo'           => 'bg-purple-100 text-purple-700',
                                    default         => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-slate-900">{{ $p->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-500">{{ $p->peminjaman->kode_peminjaman }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $metodeBadge }}">{{ $p->metode_label }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-slate-900">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold
                                        {{ $p->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : ($p->status === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('pembayaran.show', $p) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition">
                                        Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-slate-100">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection