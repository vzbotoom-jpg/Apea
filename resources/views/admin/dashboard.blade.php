{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-4 pb-6" id="apic-dashboard">

    {{-- ================= BARIS 1: HEADER & FILTER ================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 px-5 py-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="text-xl sm:text-2xl lg:text-[26px] font-extrabold italic text-[#1e40af] tracking-tight">
                Dashboard Manajemen Peminjaman Alat APIC
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            {{-- Filter Periode --}}
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <span class="hidden sm:inline">Pilih Periode</span>
                <select id="filterBulan" onchange="updateDashboardFilter()"
                        class="px-3 py-1.5 border border-slate-200 rounded text-xs font-medium text-slate-700 bg-white hover:border-blue-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                    <option value="all" {{ $filters['bulan'] === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $b)
                        <option value="{{ $i + 1 }}" {{ (int) $filters['bulan'] === $i + 1 ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
                <select id="filterTahun" onchange="updateDashboardFilter()"
                        class="px-3 py-1.5 border border-slate-200 rounded text-xs font-medium text-slate-700 bg-white hover:border-blue-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                    @for($y = max(now()->year, $filters['tahun']); $y >= min(now()->year - 3, $filters['tahun']); $y--)
                        <option value="{{ $y }}" {{ $filters['tahun'] === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            {{-- Filter Perbandingan --}}
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <span class="hidden md:inline">Perbandingan</span>
                <select id="filterCompare" onchange="updateDashboardFilter()"
                        class="px-3 py-1.5 border border-slate-200 rounded text-xs font-medium text-slate-700 bg-white hover:border-blue-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition">
                    <option value="yoy" {{ $filters['compare'] === 'yoy' ? 'selected' : '' }}>Year-on-year</option>
                    <option value="mom" {{ $filters['compare'] === 'mom' ? 'selected' : '' }}>Month-on-month</option>
                </select>
            </div>

            {{-- Tombol Tambah Alat --}}
            <a href="{{ route('admin.alat.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#1d4ed8] hover:bg-blue-800 text-white text-xs font-semibold rounded transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Alat Baru
            </a>
        </div>
    </div>

    {{-- ================= BARIS 2: FILTER KATEGORI (RADIO BAR) ================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 px-5 py-3 flex flex-wrap items-center gap-x-7 gap-y-2 text-xs">
        <label class="inline-flex items-center gap-2 cursor-pointer select-none group">
            <input type="radio" name="filter_kategori" value="all" {{ $filters['kategori'] === 'all' ? 'checked' : '' }}
                   onchange="selectKategori(this.value)"
                   class="w-3.5 h-3.5 text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
            <span class="kategori-label font-medium transition {{ $filters['kategori'] === 'all' ? 'text-blue-700 font-semibold' : 'text-slate-500 group-hover:text-blue-600' }}">
                Semua Kategori
            </span>
        </label>
        @foreach($categories as $kategori)
            @php($isSelected = $filters['kategori'] === (string) $kategori->id)
            <label class="inline-flex items-center gap-2 cursor-pointer select-none group">
                <input type="radio" name="filter_kategori" value="{{ $kategori->id }}" {{ $isSelected ? 'checked' : '' }}
                       onchange="selectKategori(this.value)"
                       class="w-3.5 h-3.5 text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
                <span class="kategori-label font-medium transition {{ $isSelected ? 'text-blue-700 font-semibold' : 'text-slate-500 group-hover:text-blue-600' }}">
                    {{ $kategori->nama_kategori }}
                </span>
            </label>
        @endforeach
    </div>
    <p id="dashboard-filter-error" class="hidden text-xs text-red-600" role="alert"></p>

    {{-- ================= BARIS 3: 3 KARTU KPI GRADASI ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Kartu 1 — Biru (Ketersediaan Alat) --}}
        <div class="relative overflow-hidden rounded-lg shadow-sm px-6 py-5 text-white bg-gradient-to-r from-[#1d4ed8] via-[#2563eb] to-[#3b82f6]">
            {{-- Pola Garis Lengkung Dekoratif --}}
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none" viewBox="0 0 400 140" fill="none" preserveAspectRatio="none">
                <circle cx="340" cy="20" r="110" stroke="white" stroke-width="18"/>
                <circle cx="340" cy="20" r="75" stroke="white" stroke-width="12"/>
                <path d="M-20 130 Q 120 40 280 140" stroke="white" stroke-width="20" fill="none"/>
            </svg>
            <p class="text-xs font-bold tracking-wide text-white/95 mb-4 relative">Ketersediaan Alat</p>
            <div class="flex items-center justify-between relative">
                <p id="kpi-pct-1" class="text-3xl lg:text-[34px] font-bold tracking-tight leading-none">
                    {{ number_format($pctTersedia, 2) }}%
                </p>
                <div class="text-xs space-y-1.5 text-right">
                    <div class="flex items-center justify-end gap-4">
                        <span class="text-blue-100">Total Alat:</span>
                        <span id="kpi-val-1a" class="font-bold text-white min-w-[36px]">{{ $totalAlat }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        <span class="text-blue-100">Alat Tersedia:</span>
                        <span id="kpi-val-1b" class="font-bold text-white min-w-[36px]">{{ $alatTersedia }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu 2 — Oranye Terakota (Peminjaman Aktif) --}}
        <div class="relative overflow-hidden rounded-lg shadow-sm px-6 py-5 text-white bg-gradient-to-r from-[#c2410c] via-[#dd6b20] to-[#ed8936]">
            <svg class="absolute inset-0 w-full h-full opacity-15 pointer-events-none" viewBox="0 0 400 140" fill="none" preserveAspectRatio="none">
                <path d="M0 20 L160 150 M40 0 L200 150 M80 0 L240 150 M120 0 L280 150" stroke="white" stroke-width="6"/>
                <circle cx="360" cy="110" r="95" stroke="white" stroke-width="16"/>
            </svg>
            <p class="text-xs font-bold tracking-wide text-white/95 mb-4 relative">Rasio Peminjaman Aktif</p>
            <div class="flex items-center justify-between relative">
                <p id="kpi-pct-2" class="text-3xl lg:text-[34px] font-bold tracking-tight leading-none">
                    {{ number_format($pctAktif, 2) }}%
                </p>
                <div class="text-xs space-y-1.5 text-right">
                    <div class="flex items-center justify-end gap-4">
                        <span class="text-orange-100">Total Transaksi:</span>
                        <span id="kpi-val-2a" class="font-bold text-white min-w-[36px]">{{ $totalPeminjaman }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        <span class="text-orange-100">Sedang Dipinjam:</span>
                        <span id="kpi-val-2b" class="font-bold text-white min-w-[36px]">{{ $peminjamanAktif }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu 3 — Kuning Emas (Keaktifan User) --}}
        <div class="relative overflow-hidden rounded-lg shadow-sm px-6 py-5 text-white bg-gradient-to-r from-[#d97706] via-[#eab308] to-[#facc15]">
            <svg class="absolute inset-0 w-full h-full opacity-20 pointer-events-none" viewBox="0 0 400 140" fill="none" preserveAspectRatio="none">
                <circle cx="60" cy="130" r="100" stroke="white" stroke-width="18"/>
                <circle cx="350" cy="20" r="85" stroke="white" stroke-width="14"/>
            </svg>
            <p class="text-xs font-bold tracking-wide text-white/95 mb-4 relative">Indeks Keaktifan Pengguna</p>
            <div class="flex items-center justify-between relative">
                <p id="kpi-pct-3" class="text-3xl lg:text-[34px] font-bold tracking-tight leading-none">
                    {{ number_format($pctUser, 2) }}%
                </p>
                <div class="text-xs space-y-1.5 text-right">
                    <div class="flex items-center justify-end gap-4">
                        <span class="text-yellow-50">Total User:</span>
                        <span id="kpi-val-3a" class="font-bold text-white min-w-[36px]">{{ $totalUser }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        <span class="text-yellow-50">Notif Belum Dibaca:</span>
                        <span id="kpi-val-3b" class="font-bold text-white min-w-[36px]">{{ $unreadNotifications }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= BARIS 4: GRID UTAMA (2/3 Kiri : 1/3 Kanan) ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">

        {{-- ===== KOLOM KIRI (8 dari 12 kolom) ===== --}}
        <div class="lg:col-span-8 flex flex-col gap-4">

            {{-- Baris Grafik Atas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Grafik 1: Tren Peminjaman Bulanan --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800">
                            <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                            <span>Tren Peminjaman di <span class="label-wilayah text-blue-700">{{ $selectedCategoryName }}</span></span>
                        </h3>
                    </div>
                    <div class="relative h-56">
                        <canvas id="chartTrenPeminjaman"></canvas>
                    </div>
                </div>

                {{-- Grafik 2: Peminjaman Baru & Pengembalian --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800">
                            <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                            <span>Tren Sirkulasi di <span class="label-wilayah text-blue-700">{{ $selectedCategoryName }}</span></span>
                        </h3>
                    </div>
                    <div class="relative h-56">
                        <canvas id="chartKunjungan"></canvas>
                    </div>
                </div>
            </div>

            {{-- Baris Grafik Bawah --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                {{-- Grafik 3: Top 10 Alat --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 p-4 flex flex-col">
                    <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800 mb-3">
                        <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                        <span>Top 10 Alat Dipinjam (<span class="label-wilayah text-blue-700">{{ $selectedCategoryName }}</span>)</span>
                    </h3>
                    <div class="relative flex-1 min-h-[250px]">
                        <canvas id="chartTopAlat"></canvas>
                    </div>
                </div>

                {{-- Grafik 4: Analisis Status per Bulan --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 p-4 flex flex-col">
                    <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800 mb-3">
                        <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                        <span>Analisis Status Peminjaman (<span class="label-wilayah text-blue-700">{{ $selectedCategoryName }}</span>)</span>
                    </h3>
                    <div class="relative flex-1 min-h-[250px]">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== KOLOM KANAN (4 dari 12 kolom) ===== --}}
        <div class="lg:col-span-4 flex flex-col gap-4">

            {{-- Widget 5: Donut Distribusi Kategori (dengan Label Luar) --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 p-4">
                <h3 class="flex items-center gap-2 text-[13px] font-bold text-slate-800 mb-2">
                    <span class="w-1 h-4 bg-[#1d4ed8] rounded-xs"></span>
                    <span>Distribusi Alat per Kategori</span>
                </h3>
                <div class="relative h-60">
                    <canvas id="chartKategori"></canvas>
                </div>
            </div>

            {{-- Widget 6: Tabel Detail Peminjaman & Tab Notifikasi --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200/70 overflow-hidden flex-1 flex flex-col">
                <div class="flex border-b border-slate-200/80 bg-slate-50/60">
                    <button type="button" id="btn-tab-detail" onclick="switchRightTab('detail')"
                            class="flex-1 px-4 py-3 text-xs font-bold text-blue-700 border-b-2 border-blue-600 bg-white transition flex items-center justify-center gap-1.5">
                        <span class="w-1 h-3.5 bg-[#1d4ed8] rounded-xs"></span>
                        <span>Detail Peminjaman</span>
                    </button>
                    <button type="button" id="btn-tab-notif" onclick="switchRightTab('notif')"
                            class="flex-1 px-4 py-3 text-xs font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-700 transition flex items-center justify-center gap-1.5">
                        <span class="w-1 h-3.5 bg-slate-300 rounded-xs" id="bar-tab-notif"></span>
                        <span>Notifikasi</span>
                        @if($unreadNotifications > 0)
                            <span class="px-1.5 py-0.2 bg-red-500 text-white text-[10px] font-bold rounded-full">{{ $unreadNotifications }}</span>
                        @endif
                    </button>
                </div>

                {{-- TAB 1: Tabel Detail Peminjaman --}}
                <div id="panel-tab-detail" class="overflow-x-auto overflow-y-auto max-h-[340px] flex-1">
                    <table class="w-full text-[11px] text-left border-collapse">
                        <thead class="bg-[#e2e8f0]/80 text-slate-700 font-bold sticky top-0 z-10">
                            <tr>
                                <th class="px-2.5 py-2.5 text-center">No.</th>
                                <th class="px-2.5 py-2.5">Kode</th>
                                <th class="px-2.5 py-2.5">Peminjam</th>
                                <th class="px-2.5 py-2.5 text-center">Status</th>
                                <th class="px-2.5 py-2.5 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="recent-peminjaman-body" class="divide-y divide-slate-100">
                            @forelse($recentPeminjaman as $i => $p)
                                <tr class="hover:bg-blue-50/40 transition">
                                    <td class="px-2.5 py-2 text-center text-slate-500">{{ $i + 1 }}</td>
                                    <td class="px-2.5 py-2 font-mono font-semibold text-slate-800 whitespace-nowrap">{{ $p->kode_peminjaman }}</td>
                                    <td class="px-2.5 py-2 text-slate-700 truncate max-w-[95px]" title="{{ $p->user->name ?? '-' }}">
                                        {{ $p->user->name ?? '-' }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold
                                            {{ match($p->status) {
                                                'menunggu' => 'bg-amber-100 text-amber-700',
                                                'dipinjam' => 'bg-blue-100 text-blue-700',
                                                'dikembalikan' => 'bg-emerald-100 text-emerald-700',
                                                'terlambat' => 'bg-red-100 text-red-700',
                                                default => 'bg-slate-100 text-slate-600'
                                            } }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td class="px-2.5 py-2 text-right text-slate-400 whitespace-nowrap">
                                        {{ $p->created_at->format('d/m H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                                        Belum ada data peminjaman terbaru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- TAB 2: Notifikasi Sistem --}}
                <div id="panel-tab-notif" class="hidden overflow-y-auto max-h-[340px] p-3 space-y-2 flex-1">
                    @forelse($notifications as $n)
                        <div class="flex items-start gap-2.5 p-2.5 rounded border {{ !$n->is_read ? 'bg-blue-50/60 border-blue-200/70' : 'bg-white border-slate-100' }}">
                            <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ !$n->is_read ? 'bg-blue-600' : 'bg-slate-300' }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $n->title }}</p>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{{ $n->message }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-xs text-slate-400">
                            Tidak ada notifikasi sistem.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// ================= TAB SWITCHER (Vanilla JS agar 100% jalan tanpa ketergantungan) =================
function switchRightTab(tab) {
    const isDetail = tab === 'detail';
    document.getElementById('panel-tab-detail').classList.toggle('hidden', !isDetail);
    document.getElementById('panel-tab-notif').classList.toggle('hidden', isDetail);

    const btnDetail = document.getElementById('btn-tab-detail');
    const btnNotif  = document.getElementById('btn-tab-notif');
    const barNotif  = document.getElementById('bar-tab-notif');

    if (isDetail) {
        btnDetail.className = 'flex-1 px-4 py-3 text-xs font-bold text-blue-700 border-b-2 border-blue-600 bg-white transition flex items-center justify-center gap-1.5';
        btnNotif.className  = 'flex-1 px-4 py-3 text-xs font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-700 transition flex items-center justify-center gap-1.5';
        barNotif.className  = 'w-1 h-3.5 bg-slate-300 rounded-xs';
    } else {
        btnNotif.className  = 'flex-1 px-4 py-3 text-xs font-bold text-blue-700 border-b-2 border-blue-600 bg-white transition flex items-center justify-center gap-1.5';
        btnDetail.className = 'flex-1 px-4 py-3 text-xs font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-700 transition flex items-center justify-center gap-1.5';
        barNotif.className  = 'w-1 h-3.5 bg-[#1d4ed8] rounded-xs';
    }
}

// ================= CUSTOM PLUGIN: LABEL LUAR DONUT CHART =================
const outsideLabelsPlugin = {
    id: 'outsideLabels',
    afterDatasetsDraw(chart) {
        if (chart.config.type !== 'doughnut') return;
        const { ctx } = chart;
        const meta = chart.getDatasetMeta(0);
        const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);

        ctx.save();
        ctx.font = '600 10px Inter, system-ui, sans-serif';

        meta.data.forEach((arc, index) => {
            const val = chart.data.datasets[0].data[index];
            if (!val || val / total < 0.05) return; // Sembunyikan label jika irisan terlalu kecil

            const angle = (arc.startAngle + arc.endAngle) / 2;
            const rOuter = arc.outerRadius;
            const x1 = arc.x + Math.cos(angle) * rOuter;
            const y1 = arc.y + Math.sin(angle) * rOuter;
            const x2 = arc.x + Math.cos(angle) * (rOuter + 12);
            const y2 = arc.y + Math.sin(angle) * (rOuter + 12);
            const isRight = Math.cos(angle) >= 0;
            const x3 = x2 + (isRight ? 10 : -10);

            // Garis penunjuk
            ctx.strokeStyle = chart.data.datasets[0].backgroundColor[index];
            ctx.lineWidth = 1.2;
            ctx.beginPath();
            ctx.moveTo(x1, y1);
            ctx.lineTo(x2, y2);
            ctx.lineTo(x3, y2);
            ctx.stroke();

            // Teks Kategori & Jumlah
            ctx.fillStyle = '#475569';
            ctx.textAlign = isRight ? 'left' : 'right';
            ctx.textBaseline = 'middle';
            const label = chart.data.labels[index];
            ctx.fillText(`${label}`, x3 + (isRight ? 4 : -4), y2 - 5);
            ctx.fillStyle = '#0f172a';
            ctx.fillText(`${val}`, x3 + (isRight ? 4 : -4), y2 + 6);
        });
        ctx.restore();
    }
};

const dashboardUrl = @json(route('admin.dashboard'));
const selectedCategoryName = @json($selectedCategoryName);
let charts = {};
let dashboardFetchController;

document.addEventListener('DOMContentLoaded', function () {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: { boxWidth: 10, boxHeight: 10, usePointStyle: false, font: { size: 10 }, color: '#475569' }
            }
        },
        scales: {
            x: { ticks: { font: { size: 10 }, color: '#64748b' }, grid: { display: false } },
            y: { ticks: { font: { size: 10 }, color: '#64748b' }, grid: { color: '#f1f5f9' }, beginAtZero: true }
        }
    };

    charts.tren = new Chart(document.getElementById('chartTrenPeminjaman'), {
        type: 'bar',
        data: @json($chartTrenPeminjaman),
        options: commonOptions
    });
    charts.sirkulasi = new Chart(document.getElementById('chartKunjungan'), {
        type: 'line',
        data: @json($chartKunjungan),
        options: commonOptions
    });
    charts.topAlat = new Chart(document.getElementById('chartTopAlat'), {
        type: 'bar',
        data: @json($chartTopAlat),
        options: {
            ...commonOptions,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { font: { size: 9 }, color: '#64748b' }, grid: { color: '#f1f5f9' } },
                y: { ticks: { font: { size: 10 }, color: '#475569' }, grid: { display: false } }
            }
        }
    });
    charts.status = new Chart(document.getElementById('chartStatus'), {
        type: 'bar',
        data: @json($chartStatus),
        options: {
            ...commonOptions,
            scales: {
                x: { stacked: true, ticks: { font: { size: 10 } }, grid: { display: false } },
                y: { stacked: true, ticks: { font: { size: 10 } }, grid: { color: '#f1f5f9' } }
            }
        }
    });
    charts.kategori = new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',
        plugins: [outsideLabelsPlugin],
        data: @json($chartKategori),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '58%',
            layout: { padding: { top: 22, bottom: 22, left: 48, right: 48 } },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => ` ${context.label}: ${context.parsed} unit alat`
                    }
                }
            }
        }
    });
});

function selectKategori() {
    document.querySelectorAll('.kategori-label').forEach((label) => {
        const selected = label.previousElementSibling.checked;
        label.classList.toggle('text-blue-700', selected);
        label.classList.toggle('font-semibold', selected);
        label.classList.toggle('text-slate-500', !selected);
    });
    updateDashboardFilter();
}

async function updateDashboardFilter() {
    const filters = {
        bulan: document.getElementById('filterBulan').value,
        tahun: document.getElementById('filterTahun').value,
        compare: document.getElementById('filterCompare').value,
        kategori: document.querySelector('input[name="filter_kategori"]:checked').value
    };
    const url = new URL(dashboardUrl, window.location.origin);
    Object.entries(filters).forEach(([key, value]) => url.searchParams.set(key, value));

    const error = document.getElementById('dashboard-filter-error');
    const dashboard = document.getElementById('apic-dashboard');
    dashboardFetchController?.abort();
    const requestController = new AbortController();
    dashboardFetchController = requestController;
    error.classList.add('hidden');
    dashboard.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            signal: requestController.signal
        });
        if (!response.ok) {
            throw new Error(`Permintaan filter gagal (${response.status}).`);
        }
        const data = await response.json();

        updateKpis(data.kpis);
        charts.tren.data = data.charts.tren;
        charts.sirkulasi.data = data.charts.sirkulasi;
        charts.topAlat.data = data.charts.topAlat;
        charts.status.data = data.charts.status;
        charts.kategori.data = data.charts.kategori;
        Object.values(charts).forEach((chart) => chart.update());
        updateRecentPeminjaman(data.recentPeminjaman);
        document.querySelectorAll('.label-wilayah').forEach((label) => {
            label.textContent = selectedCategoryLabel(filters.kategori);
        });
        window.history.replaceState({}, '', url);
    } catch (requestError) {
        if (requestError.name === 'AbortError') return;
        console.error(requestError);
        error.textContent = 'Data dashboard gagal diperbarui. Silakan coba lagi.';
        error.classList.remove('hidden');
    } finally {
        if (dashboardFetchController === requestController) {
            dashboard.removeAttribute('aria-busy');
        }
    }
}

function selectedCategoryLabel(categoryId) {
    if (categoryId === 'all') return 'Semua Kategori';
    const selected = document.querySelector(`input[name="filter_kategori"][value="${CSS.escape(categoryId)}"]`);
    return selected?.nextElementSibling.textContent.trim() || selectedCategoryName;
}

function updateKpis(kpis) {
    document.getElementById('kpi-pct-1').textContent = `${Number(kpis.pctTersedia).toFixed(2)}%`;
    document.getElementById('kpi-val-1a').textContent = kpis.totalAlat;
    document.getElementById('kpi-val-1b').textContent = kpis.alatTersedia;
    document.getElementById('kpi-pct-2').textContent = `${Number(kpis.pctAktif).toFixed(2)}%`;
    document.getElementById('kpi-val-2a').textContent = kpis.totalPeminjaman;
    document.getElementById('kpi-val-2b').textContent = kpis.peminjamanAktif;
    document.getElementById('kpi-pct-3').textContent = `${Number(kpis.pctUser).toFixed(2)}%`;
    document.getElementById('kpi-val-3a').textContent = kpis.totalUser;
    document.getElementById('kpi-val-3b').textContent = kpis.unreadNotifications;
}

function updateRecentPeminjaman(peminjaman) {
    const tbody = document.getElementById('recent-peminjaman-body');
    tbody.replaceChildren();

    if (peminjaman.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 5;
        cell.className = 'px-4 py-10 text-center text-slate-400';
        cell.textContent = 'Belum ada data peminjaman terbaru.';
        row.append(cell);
        tbody.append(row);
        return;
    }

    const statusStyles = {
        menunggu: 'bg-amber-100 text-amber-700',
        dipinjam: 'bg-blue-100 text-blue-700',
        dikembalikan: 'bg-emerald-100 text-emerald-700',
        terlambat: 'bg-red-100 text-red-700'
    };
    peminjaman.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-blue-50/40 transition';
        [
            { text: index + 1, className: 'px-2.5 py-2 text-center text-slate-500' },
            { text: item.kode, className: 'px-2.5 py-2 font-mono font-semibold text-slate-800 whitespace-nowrap' },
            { text: item.user, className: 'px-2.5 py-2 text-slate-700 truncate max-w-[95px]' },
        ].forEach((value) => {
            const cell = document.createElement('td');
            cell.className = value.className;
            cell.textContent = value.text;
            row.append(cell);
        });
        const statusCell = document.createElement('td');
        statusCell.className = 'px-2.5 py-2 text-center';
        const status = document.createElement('span');
        status.className = `inline-block px-2 py-0.5 rounded text-[10px] font-bold ${statusStyles[item.status] || 'bg-slate-100 text-slate-600'}`;
        status.textContent = item.status.charAt(0).toUpperCase() + item.status.slice(1);
        statusCell.append(status);
        row.append(statusCell);

        const timeCell = document.createElement('td');
        timeCell.className = 'px-2.5 py-2 text-right text-slate-400 whitespace-nowrap';
        timeCell.textContent = item.waktu;
        row.append(timeCell);
        tbody.append(row);
    });
}
</script>
@endsection