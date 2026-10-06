<!-- resources/views/petugas/pengembalian/receipt.blade.php -->
@extends('layouts.petugas')

@section('title', 'Struk Pengembalian')
@section('page-title', 'Struk Pengembalian')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden print:shadow-none print:border-none">
        
        {{-- Header Struk --}}
        <div class="p-8 border-b border-slate-100 flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-teal-600 rounded flex items-center justify-center text-white font-bold text-sm">A</div>
                    <span class="font-bold text-xl text-slate-900 tracking-tight">APIC</span>
                </div>
                <h2 class="text-lg font-bold text-slate-900 mt-4">Bukti Pengembalian Alat</h2>
                <p class="text-sm text-slate-500">SMK Muhammadiyah 1 Bantul</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-slate-900">{{ optional($pengembalian->tanggal_kembali)->format('d M Y') }}</p>
                <p class="text-sm text-slate-500">{{ optional($pengembalian->tanggal_kembali)->format('H:i') }} WIB</p>
                <p class="text-xs text-slate-400 mt-2 font-mono">NO: {{ $pengembalian->peminjaman->kode_peminjaman }}</p>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="p-8 grid grid-cols-2 gap-8 border-b border-slate-100">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data Peminjam</p>
                <p class="font-bold text-slate-900">{{ $pengembalian->peminjaman->user->name }}</p>
                <p class="text-sm text-slate-500">{{ $pengembalian->peminjaman->user->email }}</p>
                <p class="text-sm text-slate-500">{{ $pengembalian->peminjaman->user->no_telepon }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data Petugas</p>
                <p class="font-bold text-slate-900">{{ optional($pengembalian->petugas)->name ?? '-' }}</p>
                <p class="text-sm text-slate-500">{{ optional($pengembalian->petugas)->email ?? '-' }}</p>
            </div>
        </div>

        {{-- Details Table --}}
        <div class="p-8">
            <table class="w-full text-sm mb-8">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3 text-slate-500 w-1/3">Kode Peminjaman</td>
                        <td class="py-3 font-medium text-slate-900 text-right">{{ $pengembalian->peminjaman->kode_peminjaman }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-500">Tanggal Pengembalian</td>
                        <td class="py-3 font-medium text-slate-900 text-right">{{ optional($pengembalian->tanggal_kembali)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-500">Kondisi Alat</td>
                        <td class="py-3 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                {{ $pengembalian->kondisi_alat === 'baik' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $pengembalian->kondisi_alat)) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-500">Status Denda</td>
                        <td class="py-3 text-right font-bold {{ $pengembalian->denda > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            @if($pengembalian->denda > 0)
                                Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                            @else
                                LUNAS / TIDAK ADA
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            @if($pengembalian->denda > 0)
                <div class="bg-red-50 border border-red-100 rounded-lg p-4 mb-8">
                    <p class="text-sm text-red-800 font-medium">Harap segera lakukan pembayaran denda sebesar <strong>Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</strong> ke petugas administrasi.</p>
                </div>
            @endif

            <div class="text-center pt-8 border-t border-slate-100">
                <p class="text-slate-400 text-xs">Terima kasih telah menggunakan layanan APIC.</p>
                <p class="text-slate-400 text-xs mt-1">Struk ini dicetak secara otomatis oleh sistem.</p>
            </div>
        </div>

        {{-- Action Buttons (Hidden on Print) --}}
        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 print:hidden">
            <a href="{{ route('petugas.pengembalian.report') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition">
                Kembali
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Struk
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:border-none { border: none !important; }
        main, main * { visibility: visible; }
        main { position: absolute; left: 0; top: 0; width: 100%; padding: 0; margin: 0; }
        .print\:hidden { display: none !important; }
    }
</style>
@endsection