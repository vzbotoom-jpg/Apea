<!-- resources/views/petugas/peminjaman/show.blade.php -->
@extends('layouts.petugas')
@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Main Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1 flex-wrap">
                    <h2 class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $peminjaman->kode_peminjaman }}</h2>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                        @switch($peminjaman->status)
                            @case('menunggu') bg-amber-100 text-amber-700 @break
                            @case('diverifikasi') bg-blue-100 text-blue-700 @break
                            @case('dipinjam') bg-teal-100 text-teal-700 @break
                            @case('terlambat') bg-red-100 text-red-700 @break
                            @case('dikembalikan') bg-emerald-100 text-emerald-700 @break
                            @default bg-slate-100 text-slate-600
                        @endswitch">
                        {{ ucfirst($peminjaman->status) }}
                    </span>
                    {{-- ✅ BARU: badge pengajuan perpanjangan --}}
                    @if($peminjaman->perpanjanganMenunggu())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 animate-pulse">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Perpanjangan Diajukan
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 flex items-center gap-2">
                    Diajukan oleh:
                    <span class="font-bold text-slate-900">{{ $peminjaman->user->name }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                    <span>{{ $peminjaman->created_at->format('d M Y, H:i') }} WIB</span>
                </p>
            </div>
            <a href="{{ route('petugas.peminjaman.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition" title="Kembali">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        <div class="p-6 space-y-8">
            {{-- Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Pinjam</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->tanggal_pinjam->format('d M Y') }}</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jatuh Tempo</p>
                    <p class="text-sm font-bold {{ $peminjaman->status === 'terlambat' ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}
                        @if($peminjaman->status === 'terlambat')
                            <span class="text-xs font-normal text-red-500 ml-1">(Terlambat {{ $hariTerlambat }} hari)</span>
                        @endif
                    </p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Item</p>
                    <p class="text-sm font-bold text-slate-900">{{ $peminjaman->detailPeminjamans->sum('jumlah') }} Alat</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estimasi Biaya</p>
                    <p class="text-sm font-bold text-slate-900">Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}</p>
                </div>
                @if($peminjaman->catatan)
                    <div class="col-span-2 p-4 bg-amber-50 rounded-lg border border-amber-100 border-l-4 border-l-amber-400">
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Catatan Peminjam</p>
                        <p class="text-sm text-amber-900 italic">"{{ $peminjaman->catatan }}"</p>
                    </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Rincian Alat</h3>
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama Alat</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($peminjaman->detailPeminjamans as $detail)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 font-mono text-slate-500">{{ $detail->alat->kode_alat }}</td>
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

            {{-- Denda Information --}}
            @if($peminjaman->status === 'terlambat' || $peminjaman->status === 'dipinjam')
                <div class="p-4 rounded-lg border flex items-start gap-3
                            {{ $peminjaman->status === 'terlambat' ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200' }}">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5 {{ $peminjaman->status === 'terlambat' ? 'text-red-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold {{ $peminjaman->status === 'terlambat' ? 'text-red-800' : 'text-amber-800' }}">Informasi Denda</h4>
                        <p class="text-xs mt-1 {{ $peminjaman->status === 'terlambat' ? 'text-red-600' : 'text-amber-600' }}">
                            @if($peminjaman->status === 'terlambat')
                                Keterlambatan {{ $hariTerlambat }} hari.
                                @if($hariTerlambat > 2)
                                    Total denda: <strong>Rp {{ number_format($peminjaman->calculateDenda(), 0, ',', '.') }}</strong>. Harap segera proses pengembalian.
                                @else
                                    Masih dalam masa tenggang (2 hari).
                                @endif
                            @else
                                Masih dalam masa peminjaman. Denda akan dikenakan jika terlambat lebih dari 2 hari.
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- ===== ✅ BARU: TRANSAKSI PEMBAYARAN ===== --}}
            @php
                $metodeAktif = json_decode(\App\Models\Setting::get('metode_pembayaran_aktif', json_encode(['tunai','transfer'])), true);
                $infoTransfer = \App\Models\Setting::get('info_transfer', '');
            @endphp
            <div class="p-4 rounded-lg border {{ $peminjaman->status_pembayaran === 'lunas' ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold {{ $peminjaman->status_pembayaran === 'lunas' ? 'text-emerald-800' : 'text-slate-800' }}">
                            Transaksi Pembayaran
                            <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold {{ $peminjaman->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $peminjaman->status_pembayaran === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                            </span>
                        </h4>
                        <p class="text-xs {{ $peminjaman->status_pembayaran === 'lunas' ? 'text-emerald-600' : 'text-slate-600' }} mt-1">
                            Sewa Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}
                            + Denda Rp {{ number_format($peminjaman->calculateDenda(), 0, ',', '.') }}
                            = <strong>Rp {{ number_format($peminjaman->total_bayar, 0, ',', '.') }}</strong>
                        </p>
                        @if($peminjaman->status_pembayaran === 'lunas')
                            <p class="text-xs text-emerald-600 mt-1">
                                Dibayar {{ ucfirst($peminjaman->metode_pembayaran) }} · {{ $peminjaman->tanggal_bayar?->format('d M Y, H:i') }} · oleh {{ $peminjaman->pembayar?->name }}
                            </p>
                        @elseif(in_array('transfer', $metodeAktif) && $infoTransfer)
                            <p class="text-xs text-slate-500 mt-1">Rekening tujuan: <strong class="text-slate-700">{{ $infoTransfer }}</strong></p>
                        @endif
                    </div>

                    @if($peminjaman->status_pembayaran !== 'lunas')
                        <form action="{{ route('petugas.peminjaman.konfirmasi-bayar', $peminjaman) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="metode" required class="px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                                @foreach($metodeAktif as $m)
                                    <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm">
                                Konfirmasi Bayar
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- ===== ✅ BARU: PENGAJUAN PERPANJANGAN ===== --}}
            @if($peminjaman->perpanjangan_requested)
                @php
                    $ext = match($peminjaman->status_perpanjangan) {
                        'disetujui' => ['bg-emerald-50', 'border-emerald-200', 'text-emerald-800', 'text-emerald-600'],
                        'ditolak'   => ['bg-red-50', 'border-red-200', 'text-red-800', 'text-red-600'],
                        default     => ['bg-amber-50', 'border-amber-200', 'text-amber-800', 'text-amber-600'],
                    };
                @endphp
                <div class="p-4 rounded-lg border {{ $ext[0] }} {{ $ext[1] }}">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 {{ $ext[3] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold {{ $ext[2] }}">
                                Pengajuan Perpanjangan: {{ ucfirst($peminjaman->status_perpanjangan) }}
                            </h4>
                            <p class="text-xs {{ $ext[3] }} mt-1">
                                Diajukan hingga <strong>{{ $peminjaman->tanggal_perpanjangan?->format('d M Y') }}</strong>
                                · Alasan: {{ $peminjaman->alasan_perpanjangan }}
                            </p>
                            @if($peminjaman->status_perpanjangan === 'disetujui' && $peminjaman->approvedBy)
                                <p class="text-xs {{ $ext[3] }} mt-1">
                                    ✓ Disetujui oleh {{ $peminjaman->approvedBy->name }} — jatuh tempo telah diperbarui.
                                </p>
                            @elseif($peminjaman->status_perpanjangan === 'ditolak')
                                <p class="text-xs {{ $ext[3] }} mt-1">
                                    ✗ Ditolak — peminjam wajib mengembalikan sesuai jatuh tempo semula.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol aksi hanya saat menunggu --}}
                    @if($peminjaman->status_perpanjangan === 'menunggu')
                        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t {{ $ext[1] }}">
                            <form action="{{ route('petugas.peminjaman.perpanjang.setujui', $peminjaman) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Setujui perpanjangan ini? Jatuh tempo akan diperbarui menjadi {{ $peminjaman->tanggal_perpanjangan?->format('d M Y') }}.')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Setujui Perpanjangan
                                </button>
                            </form>
                            <form action="{{ route('petugas.peminjaman.perpanjang.tolak', $peminjaman) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Tolak pengajuan perpanjangan ini?')">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex flex-wrap gap-3 justify-end">
                @if($peminjaman->status === 'menunggu')
                    <form action="{{ route('petugas.peminjaman.verifikasi', $peminjaman) }}" method="POST" class="inline-block" onsubmit="return confirm('Verifikasi peminjaman ini?')">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Verifikasi
                        </button>
                    </form>
                    <button onclick="showCancelModal('{{ $peminjaman->id }}')" class="px-5 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Batalkan
                    </button>
                @endif
                @if($peminjaman->status === 'diverifikasi')
                    <form action="{{ route('petugas.peminjaman.ambil', $peminjaman) }}" method="POST" class="inline-block" onsubmit="return confirm('Konfirmasi penyerahan alat?')">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-teal-600 text-white text-sm font-bold rounded-lg hover:bg-teal-700 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Serahkan Alat
                        </button>
                    </form>
                    <button onclick="showCancelModal('{{ $peminjaman->id }}')" class="px-5 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Batalkan
                    </button>
                @endif
                @if(in_array($peminjaman->status, ['dipinjam', 'terlambat']))
                    <a href="{{ route('petugas.pengembalian.proses', $peminjaman) }}" class="px-5 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Proses Pengembalian
                    </a>
                @endif
                <a href="{{ route('petugas.peminjaman.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl transform transition-all">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Batalkan Peminjaman</h3>
        </div>
        <form id="cancelForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="alasan" class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Pembatalan <span class="text-red-500">*</span></label>
                <textarea name="alasan" id="alasan" rows="3" required
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition text-sm"
                          placeholder="Berikan alasan pembatalan..."></textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white text-sm font-bold rounded-lg hover:bg-red-700 transition shadow-sm">
                    Ya, Batalkan
                </button>
                <button type="button" onclick="hideCancelModal()" class="flex-1 px-4 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // ✅ Generate URL sekali dengan placeholder, lalu ganti ID-nya via JS
    const cancelUrlTemplate = @json(route('petugas.peminjaman.batalkan', ['peminjaman' => '__ID__']));

    function showCancelModal(id) {
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('cancelForm').action = cancelUrlTemplate.replace('__ID__', id);
    }

    function hideCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Close modal on click outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideCancelModal();
        }
    });
</script>
@endpush
@endsection