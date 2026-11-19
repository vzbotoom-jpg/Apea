@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.petugas')

@section('title', 'Pengelolaan Denda')
@section('page-title', 'Pengelolaan Denda')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Denda Keterlambatan</h2>
            <p class="mt-1 text-sm text-slate-500">Nominal dihitung otomatis dari aturan denda. Verifikasi pembayaran melalui menu pembayaran.</p>
        </div>
        <a href="{{ route('pembayaran.index') }}"
           class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-teal-700">
            Verifikasi Pembayaran
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Denda Belum Lunas</p>
            <p class="mt-2 text-2xl font-extrabold text-amber-800">Rp {{ number_format($summary['belum_bayar'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-amber-700">{{ $summary['jumlah_belum_bayar'] }} tagihan</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Denda Lunas</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-800">Rp {{ number_format($summary['lunas'], 0, ',', '.') }}</p>
        </div>
        <form method="GET" action="{{ route('denda.index') }}" class="rounded-xl border border-slate-200 bg-white p-5">
            <label for="status" class="block text-xs font-bold uppercase tracking-wide text-slate-500">Filter Status</label>
            <div class="mt-2 flex gap-2">
                <select id="status" name="status" class="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="belum_bayar" @selected($status === 'belum_bayar')>Belum bayar</option>
                    <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                </select>
                <button class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Filter</button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        @if($dendas->isEmpty())
            <div class="p-12 text-center">
                <p class="font-bold text-slate-800">Belum ada catatan denda</p>
                <p class="mt-1 text-sm text-slate-500">Denda akan tercatat otomatis setelah melewati masa tenggang.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Peminjam / Transaksi</th>
                            <th class="px-5 py-3">Terlambat</th>
                            <th class="px-5 py-3">Tanggal Denda</th>
                            <th class="px-5 py-3 text-right">Nominal</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($dendas as $denda)
                            @php
                                $loan = $denda->peminjaman;
                                $loanRoute = auth()->user()->role === 'admin' ? 'admin.peminjaman.show' : 'petugas.peminjaman.show';
                            @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $loan->user->name }}</p>
                                    <p class="mt-1 font-mono text-xs text-slate-500">{{ $loan->kode_peminjaman }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-700">{{ $denda->hari_terlambat }} hari</td>
                                <td class="px-5 py-4 text-slate-700">{{ $denda->tanggal_denda->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-right font-bold text-slate-900">Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $denda->status === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $denda->status === 'lunas' ? 'Lunas' : 'Belum bayar' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route($loanRoute, $loan) }}" class="font-semibold text-teal-700 hover:text-teal-900">Lihat transaksi</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 p-4">{{ $dendas->links() }}</div>
        @endif
    </div>
</div>
@endsection
