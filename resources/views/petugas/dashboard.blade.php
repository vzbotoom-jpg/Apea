{{-- resources/views/petugas/dashboard.blade.php --}}
@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-4 pb-6">

    {{-- ================= BARIS 1: HEADER & AKSI CEPAT ================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 px-5 py-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-[26px] font-extrabold italic text-[#1e40af] tracking-tight">
                Dashboard Operasional Petugas APIC
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Halo, <span class="font-semibold text-slate-700">{{ auth()->user()->name }}</span> &bull; Pantau verifikasi peminjaman dan pengembalian alat hari ini ({{ now()->translatedFormat('d F Y') }}).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('petugas.pengembalian.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded transition">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Data Pengembalian
            </a>

            <a href="{{ route('petugas.peminjaman.index', ['status' => 'menunggu']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1d4ed8] hover:bg-blue-800 text-white text-xs font-semibold rounded transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Verifikasi Antrian ({{ $peminjamanMenunggu }})
            </a>
        </div>
    </div>

    {{-- ================= BARIS 2: INFO BAR HARIAN (SETARA BAR FILTER ADMIN) ================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 px-5 py-3 flex flex-wrap items-center justify-between gap-4 text-xs">
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
            <span class="font-bold text-slate-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Ringkasan Harian:
            </span>
            <span class="text-slate-600">
                Peminjaman Masuk Hari Ini: <strong class="text-blue-700">{{ $peminjamanHariIni }} transaksi</strong>
            </span>
            <span class="text-slate-300 hidden sm:inline">|</span>
            <span class="text-slate-600">
                Jatuh Tempo Hari Ini: <strong class="text-orange-600">{{ $pengembalianHariIni }} transaksi</strong>
            </span>
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
            Status Layanan: <span class="text-emerald-600 font-bold">Aktif</span>
        </div>
    </div>

    {{-- ================= BARIS 3: 3 KARTU KPI GRADASI ================= --}}
    @php
        $totalSirkulasi = max(1, $peminjamanMenunggu + $peminjamanAktif);
        $pctMenunggu    = round(($peminjamanMenunggu / $totalSirkulasi) * 100, 1);
        $pctTerlambat   = $peminjamanAktif > 0 ? round(($peminjamanTerlambat / $peminjamanAktif) * 100, 1) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Kartu 1 — Biru (Kesiapan Alat & Peminjaman Hari Ini) --}}
        <div class="relative overflow-hidden rounded-lg shadow-sm px-6 py-5 text-white bg-gradient-to-r from-[#1d4ed8] via-[#2563eb] to-[#3b82f6]">
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none" viewBox="0 0 400 140" fill="none" preserveAspectRatio="none">
                <circle cx="340" cy="20" r="110" stroke="white" stroke-width="18"/>
                <circle cx="340" cy="20" r="75" stroke="white" stroke-width="12"/>
                <path d="M-20 130 Q 120 40 280 140" stroke="white" stroke-width="20" fill="none"/>
            </svg>
            <p class="text-xs font-bold tracking-wide text-white/95 mb-4 relative">Kesiapan Inventaris</p>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-3xl lg:text-[34px] font-bold tracking-tight leading-none">{{ $alatTersedia }}</p>
                    <p class="text-[11px] text-blue-100 mt-1.5">Unit alat siap dipinjam</p>
                </div>
                <div class="text-xs space-y-1.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-blue-100">Pinjam Hari Ini:</span>
                        <span class="font-bold text-white min-w-[28px]">{{ $peminjamanHariIni }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-blue-100">Sedang Dipinjam:</span>
                        <span class="font-bold text-white min-w-[28px]">{{ $peminjamanAktif }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu 2 — Oranye (Antrian Verifikasi & Sirkulasi Aktif) --}}
        <div class="relative overflow-hidden rounded-lg shadow-sm px-6 py-5 text-white bg-gradient-to-r from-[#c2410c] via-[#dd6b20] to-[#ed8936]">
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none" viewBox="0 0 400 140" fill="none" preserveAspectRatio="none">
                <path d="M0 20 L160 150 M40 0 L200 150 M80 0 L240 150 M120 0 L280 150" stroke="white" stroke-width="6"/>
                <circle cx="360" cy="110" r="95" stroke="white" stroke-width="16"/>
            </svg>
            <p class="text-xs font-bold tracking-wide text-white/95 mb-4 relative">Antrian Verifikasi</p>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-3xl lg:text-[34px] font-bold tracking-tight leading-none">{{ $peminjamanMenunggu }}</p>
                    <p class="text-[11px] text-orange-100 mt-1.5">Permintaan menunggu ({{ $pctMenunggu }}%)</p>
                </div>
                <div class="text-xs space-y-1.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-orange-100">Menunggu:</span>
                        <span class="font-bold text-white min-w-[28px]">{{ $peminjamanMenunggu }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-orange-100">Sirkulasi Aktif:</span>
                        <span class="font-bold text-white min-w-[28px]">{{ $peminjamanAktif }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu 3 — Kuning Emas (Pengawasan Keterlambatan & Jatuh Tempo) --}}
        <div class="relative overflow-hidden rounded-lg shadow-sm px-6 py-5 text-white bg-gradient-to-r from-[#d97706] via-[#eab308] to-[#facc15]">
            <svg class="absolute inset-0 w-full h-full opacity-20 pointer-events-none" viewBox="0 0 400 140" fill="none" preserveAspectRatio="none">
                <circle cx="60" cy="130" r="100" stroke="white" stroke-width="18"/>
                <circle cx="350" cy="20" r="85" stroke="white" stroke-width="14"/>
            </svg>
            <p class="text-xs font-bold tracking-wide text-white/95 mb-4 relative">Pengawasan Pengembalian</p>
            <div class="flex items-center justify-between relative">
                <div>
                    <p class="text-3xl lg:text-[34px] font-bold tracking-tight leading-none">{{ $peminjamanTerlambat }}</p>
                    <p class="text-[11px] text-yellow-50 mt-1.5">Transaksi terlambat ({{ $pctTerlambat }}%)</p>
                </div>
                <div class="text-xs space-y-1.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-yellow-50">Terlambat:</span>
                        <span class="font-bold text-white min-w-[28px]">{{ $peminjamanTerlambat }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <span class="text-yellow-50">Jatuh Tempo Hari Ini:</span>
                        <span class="font-bold text-white min-w-[28px]">{{ $pengembalianHariIni }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= BARIS 4: GRID UTAMA (8 Kolom Kiri : 4 Kolom Kanan) ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">

        {{-- ===== KOLOM KIRI (8/12): DUA ANTRIAN KERJA UTAMA ===== --}}
        <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Widget 1: Menunggu Verifikasi --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 overflow-hidden flex flex-col">
                <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-[13px] font-bold text-slate-800">
                        <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                        <span>Menunggu Verifikasi</span>
                        @if($peminjamanMenunggu > 0)
                            <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded-full">{{ $peminjamanMenunggu }}</span>
                        @endif
                    </h2>
                    <a href="{{ route('petugas.peminjaman.index', ['status' => 'menunggu']) }}" class="text-xs font-semibold text-[#1d4ed8] hover:underline">
                        Lihat semua &rarr;
                    </a>
                </div>

                <div class="p-4 flex-1 overflow-y-auto max-h-[460px] space-y-2.5">
                    @forelse($needVerification as $peminjaman)
                        <div class="flex items-center justify-between p-3 bg-slate-50/60 border border-slate-200/70 rounded-md hover:border-blue-300 hover:bg-blue-50/30 transition">
                            <div class="flex-1 min-w-0 mr-3">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $peminjaman->user->name }}</p>
                                </div>
                                <p class="text-[11px] text-blue-700 font-mono font-semibold mt-0.5">{{ $peminjaman->kode_peminjaman }}</p>
                                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.2 bg-white border border-slate-200 rounded text-[10px] font-medium text-slate-700">
                                        {{ $peminjaman->detailPeminjamans->count() }} alat
                                    </span>
                                    <span>&bull;</span>
                                    <span>{{ $peminjaman->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                            <a href="{{ route('petugas.peminjaman.show', $peminjaman) }}"
                               class="shrink-0 px-3 py-1.5 bg-[#1d4ed8] hover:bg-blue-800 text-white text-[11px] font-bold rounded transition shadow-xs">
                                Proses
                            </a>
                        </div>
                    @empty
                        <div class="h-full min-h-[260px] flex flex-col items-center justify-center text-slate-400 py-8">
                            <svg class="w-10 h-10 mb-2 opacity-40 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-medium">Semua beres! Tidak ada antrian verifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Widget 2: Perlu Dikembalikan --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 overflow-hidden flex flex-col">
                <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="flex items-center gap-2 text-[13px] font-bold text-slate-800">
                        <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                        <span>Perlu Dikembalikan</span>
                        @if($peminjamanTerlambat > 0)
                            <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">{{ $peminjamanTerlambat }} Telat</span>
                        @endif
                    </h2>
                    <a href="{{ route('petugas.pengembalian.index') }}" class="text-xs font-semibold text-[#1d4ed8] hover:underline">
                        Lihat semua &rarr;
                    </a>
                </div>

                <div class="p-4 flex-1 overflow-y-auto max-h-[460px] space-y-2.5">
                    @forelse($needReturn as $peminjaman)
                        <div class="flex items-center justify-between p-3 border rounded-md transition
                                    {{ $peminjaman->status === 'terlambat' ? 'border-red-200 bg-red-50/40' : 'border-slate-200/70 bg-slate-50/60 hover:border-blue-300 hover:bg-blue-50/30' }}">
                            <div class="flex-1 min-w-0 mr-3">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ $peminjaman->user->name }}</p>
                                <p class="text-[11px] text-slate-600 font-mono font-semibold mt-0.5">{{ $peminjaman->kode_peminjaman }}</p>
                                <p class="text-[11px] mt-1 flex flex-wrap items-center gap-1.5 {{ $peminjaman->status === 'terlambat' ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                                    <span>Tempo: {{ $peminjaman->tanggal_jatuh_tempo->format('d M Y') }}</span>
                                    @if($peminjaman->status === 'terlambat')
                                        <span class="px-1.5 py-0.2 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase">Terlambat</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('petugas.pengembalian.proses', $peminjaman) }}"
                               class="shrink-0 px-3 py-1.5 text-white text-[11px] font-bold rounded transition shadow-xs
                                      {{ $peminjaman->status === 'terlambat' ? 'bg-red-600 hover:bg-red-700' : 'bg-[#1d4ed8] hover:bg-blue-800' }}">
                                Proses
                            </a>
                        </div>
                    @empty
                        <div class="h-full min-h-[260px] flex flex-col items-center justify-center text-slate-400 py-8">
                            <svg class="w-10 h-10 mb-2 opacity-40 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                            <p class="text-xs font-medium">Tidak ada pengembalian yang tertunda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ===== KOLOM KANAN (4/12): VISUALISASI & RINGKASAN STATUS ===== --}}
        <div class="lg:col-span-4 flex flex-col gap-4">

            {{-- Donut Chart Proporsi Operasional (Menggunakan Data Asli) --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 p-4">
                <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800 mb-2">
                    <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                    <span>Proporsi Status Sirkulasi</span>
                </h3>
                <div class="relative h-56">
                    <canvas id="chartPetugasStatus"></canvas>
                </div>
            </div>

            {{-- Tabel Ringkasan Metrik Operasional --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 overflow-hidden flex-1">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800">
                        <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                        <span>Rekap Operasional Saat Ini</span>
                    </h3>
                </div>
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#e2e8f0]/80 text-slate-700 font-bold">
                        <tr>
                            <th class="px-4 py-2.5">Indikator</th>
                            <th class="px-4 py-2.5 text-right">Jumlah</th>
                            <th class="px-4 py-2.5 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 font-medium text-slate-700">Menunggu Verifikasi</td>
                            <td class="px-4 py-2.5 text-right font-bold text-slate-900">{{ $peminjamanMenunggu }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 text-[10px] font-bold">Antrian</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 font-medium text-slate-700">Sedang Dipinjam</td>
                            <td class="px-4 py-2.5 text-right font-bold text-slate-900">{{ $peminjamanAktif }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-[10px] font-bold">Aktif</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 font-medium text-slate-700">Terlambat Kembali</td>
                            <td class="px-4 py-2.5 text-right font-bold text-red-600">{{ $peminjamanTerlambat }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold">Prioritas</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 font-medium text-slate-700">Jatuh Tempo Hari Ini</td>
                            <td class="px-4 py-2.5 text-right font-bold text-slate-900">{{ $pengembalianHariIni }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-[10px] font-bold">Hari Ini</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 font-medium text-slate-700">Alat Tersedia</td>
                            <td class="px-4 py-2.5 text-right font-bold text-emerald-600">{{ $alatTersedia }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold">Siap</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataValues = [
        {{ (int) $peminjamanMenunggu }},
        {{ (int) $peminjamanAktif }},
        {{ (int) $peminjamanTerlambat }},
        {{ (int) $alatTersedia }}
    ];

    const total = dataValues.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('chartPetugasStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Menunggu', 'Dipinjam', 'Terlambat', 'Alat Tersedia'],
            datasets: [{
                data: total > 0 ? dataValues : [1],
                backgroundColor: total > 0
                    ? ['#eab308', '#2563eb', '#dc2626', '#60a5fa']
                    : ['#e2e8f0'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 10 }, padding: 10, color: '#475569' }
                },
                tooltip: {
                    enabled: total > 0,
                    callbacks: {
                        label: (c) => ` ${c.label}: ${c.parsed} unit/transaksi`
                    }
                }
            }
        }
    });
});
</script>
@endsection