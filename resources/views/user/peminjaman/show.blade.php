<!-- resources/views/user/peminjaman/show.blade.php -->
@extends('layouts.user')
@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Transaksi')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Main Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        {{-- Header Invoice --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Kode Transaksi</p>
                <h2 class="text-2xl font-extrabold text-slate-900 font-mono">{{ $peminjaman->kode_peminjaman }}</h2>
                <p class="text-xs text-slate-500 mt-1">Diajukan pada {{ $peminjaman->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="text-left sm:text-right flex flex-col sm:items-end gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold shadow-sm
                    @switch($peminjaman->status)
                        @case('menunggu') bg-amber-100 text-amber-700 @break
                        @case('dipinjam') bg-teal-100 text-teal-700 @break
                        @case('dikembalikan') bg-emerald-100 text-emerald-700 @break
                        @case('terlambat') bg-red-100 text-red-700 @break
                        @default bg-slate-100 text-slate-600
                    @endswitch">
                    {{ ucfirst($peminjaman->status) }}
                </span>
                {{-- Badge perpanjangan menunggu --}}
                @if($peminjaman->perpanjanganMenunggu())
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-100 text-amber-700 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Perpanjangan Diajukan
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-8">
            {{-- Grid Info --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pinjam</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jatuh Tempo</p>
                    <p class="text-sm font-bold {{ $peminjaman->status === 'terlambat' ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}
                    </p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Item</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->total_item }} Alat</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Estimasi Biaya</p>
                    <p class="text-sm font-bold text-slate-900">
                        Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            @if($peminjaman->petugas)
                <div class="flex items-center gap-3 p-3 bg-teal-50/50 rounded-lg border border-teal-100">
                    <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($peminjaman->petugas->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs text-teal-600 font-medium">Diverifikasi oleh</p>
                        <p class="text-sm font-bold text-slate-900">{{ $peminjaman->petugas->name }}</p>
                    </div>
                </div>
            @endif

            {{-- Items Table --}}
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Rincian Alat</h3>
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3">Nama Alat</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($peminjaman->detailPeminjamans as $detail)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $detail->alat->nama_alat }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $detail->jumlah }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">Rp {{ number_format($detail->harga_sewa_saat_pinjam, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Denda Alert --}}
            @if($peminjaman->status === 'terlambat' || ($peminjaman->status === 'dipinjam' && now()->greaterThan($peminjaman->tanggal_jatuh_tempo)))
                <div class="p-4 rounded-lg border {{ $peminjaman->status === 'terlambat' ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200' }}">
                    <div class="flex gap-3">
                        <div class="mt-0.5">
                            <svg class="w-5 h-5 {{ $peminjaman->status === 'terlambat' ? 'text-red-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold {{ $peminjaman->status === 'terlambat' ? 'text-red-800' : 'text-amber-800' }}">Informasi Denda</h4>
                            <p class="text-xs {{ $peminjaman->status === 'terlambat' ? 'text-red-600' : 'text-amber-600' }} mt-1">
                                @if($peminjaman->status === 'terlambat')
                                    Keterlambatan {{ $peminjaman->getLateDays() }} hari.
                                    {{-- ✅ FIX: aman jika $denda tidak dikirim controller --}}
                                    @if(($denda ?? 0) > 0)
                                        Total denda: <strong>Rp {{ number_format($denda, 0, ',', '.') }}</strong>. Harap segera lakukan pengembalian.
                                    @else
                                        Masih dalam masa tenggang (2 hari).
                                    @endif
                                @else
                                    Melewati jatuh tempo. Denda akan dikenakan jika terlambat lebih dari 2 hari.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===== PERPANJANGAN PINJAMAN ===== --}}
            @if($peminjaman->bisaPerpanjangan())
                <div class="p-5 bg-teal-50/60 border border-teal-200 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h4 class="text-sm font-bold text-teal-800">Butuh waktu lebih lama?</h4>
                    </div>
                    <p class="text-xs text-teal-700 mb-4">Ajukan perpanjangan maksimal 7 hari. Pengajuan harus disetujui petugas.</p>
                    <form action="{{ route('user.peminjaman.perpanjang', $peminjaman) }}" method="POST" class="grid sm:grid-cols-2 gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Tambahan Hari (1–7)</label>
                            <input type="number" name="tambahan_hari" min="1" max="7" value="1" required
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Alasan</label>
                            <input type="text" name="alasan" required maxlength="500" placeholder="Contoh: praktikum belum selesai"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                        </div>
                        <button type="submit" class="sm:col-span-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                            Ajukan Perpanjangan
                        </button>
                    </form>
                </div>
            @elseif($peminjaman->perpanjangan_requested)
                @php
                    $ext = match($peminjaman->status_perpanjangan) {
                        'disetujui' => ['bg-emerald-50 border-emerald-200', 'text-emerald-800', 'text-emerald-600'],
                        'ditolak'   => ['bg-red-50 border-red-200', 'text-red-800', 'text-red-600'],
                        default     => ['bg-amber-50 border-amber-200', 'text-amber-800', 'text-amber-600'],
                    };
                @endphp
                <div class="p-4 rounded-lg border {{ $ext[0] }}">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 {{ $ext[2] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h4 class="text-sm font-bold {{ $ext[1] }}">Status Perpanjangan: {{ ucfirst($peminjaman->status_perpanjangan) }}</h4>
                            <p class="text-xs {{ $ext[2] }} mt-1">
                                Diajukan hingga <strong>{{ $peminjaman->tanggal_perpanjangan?->format('d M Y') }}</strong>
                                · Alasan: {{ $peminjaman->alasan_perpanjangan }}
                            </p>
                            <p class="text-xs {{ $ext[2] }} mt-1">
                                @if($peminjaman->status_perpanjangan === 'disetujui')
                                    Jatuh tempo Anda telah diperbarui.
                                @elseif($peminjaman->status_perpanjangan === 'ditolak')
                                    Harap kembalikan alat sesuai jatuh tempo semula.
                                @else
                                    Menunggu persetujuan petugas.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $metodeAktif = json_decode(\App\Models\Setting::get('metode_pembayaran_aktif', json_encode(['tunai','transfer'])), true);
                $infoTransfer = \App\Models\Setting::get('info_transfer', '');
            @endphp

            {{-- ===== STATUS PEMBAYARAN ===== --}}
            <div class="p-4 rounded-lg border {{ $peminjaman->status_pembayaran === 'lunas' ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-bold {{ $peminjaman->status_pembayaran === 'lunas' ? 'text-emerald-800' : 'text-slate-800' }}">Status Pembayaran</h4>
                        <p class="text-xs {{ $peminjaman->status_pembayaran === 'lunas' ? 'text-emerald-600' : 'text-slate-600' }} mt-1">
                            Total tagihan: <strong>Rp {{ number_format($peminjaman->total_bayar, 0, ',', '.') }}</strong>
                            @if($peminjaman->status_pembayaran === 'lunas')
                                — LUNAS ({{ ucfirst($peminjaman->metode_pembayaran) }}).
                            @else
                                — dibayar di loket petugas saat pengambilan/pengembalian alat.
                            @endif
                        </p>
                        @if($peminjaman->status_pembayaran !== 'lunas' && in_array('transfer', $metodeAktif) && $infoTransfer)
                            <p class="text-xs text-slate-500 mt-2">💳 Transfer ke: <strong class="text-slate-700">{{ $infoTransfer }}</strong> — tunjukkan bukti transfer ke petugas.</p>
                        @endif
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 {{ $peminjaman->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $peminjaman->status_pembayaran === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                    </span>
                </div>

                {{-- ✅ FIX: tombol bayar KONDISIONAL (tidak duplikat, tidak muncul saat menunggu/lunas) --}}
                @if($peminjaman->status_pembayaran !== 'lunas' && in_array($peminjaman->status, ['diverifikasi', 'dipinjam', 'terlambat']))
                    <a href="{{ route('user.peminjaman.bayar', $peminjaman) }}"
                       class="mt-4 w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg transition">
                        💳 Bayar Sekarang
                    </a>
                @elseif($peminjaman->status === 'menunggu' && $peminjaman->status_pembayaran !== 'lunas')
                    <p class="mt-3 text-[11px] text-slate-500">ℹ️ Pembayaran dapat dilakukan setelah pengajuan diverifikasi petugas.</p>
                @endif
            </div>

            {{-- ===== QR TRANSAKSI ===== --}}
            <div class="p-5 bg-white border border-slate-200 rounded-lg text-center">
                <img src="{{ route('qr.peminjaman', $peminjaman) }}" alt="QR Transaksi"
                     class="w-40 h-40 mx-auto rounded-lg border border-slate-200 bg-white p-2">
                <p class="text-xs font-bold text-slate-700 mt-3">Tunjukkan QR ini ke petugas</p>
                <p class="text-xs text-slate-500 mt-1">Petugas cukup scan untuk verifikasi, serah terima, atau pengembalian — tanpa cari kode manual.</p>
            </div>

            @if($peminjaman->catatan)
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 border-l-4 border-l-slate-400">
                    <p class="text-xs font-bold text-slate-500 uppercase mb-1">Catatan Peminjam</p>
                    <p class="text-sm text-slate-700 italic">"{{ $peminjaman->catatan }}"</p>
                </div>
            @endif

            {{-- ✅ FIX: tombol "Bayar Sekarang" standalone DUPLIKAT dihapus --}}

            {{-- Actions --}}
            <div class="pt-6 border-t border-slate-100 flex flex-wrap gap-3 justify-between items-center">
                <a href="{{ route('user.peminjaman.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Riwayat
                </a>
                @if($peminjaman->status === 'menunggu')
                    <form action="{{ route('user.peminjaman.batalkan', $peminjaman) }}" method="POST" onsubmit="return confirm('Batalkan peminjaman ini?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition">
                            Batalkan Pengajuan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection