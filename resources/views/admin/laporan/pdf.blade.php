<!-- resources/views/admin/laporan/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0d9488; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #0f766e; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0; color: #64748b; font-size: 9pt; }
        .info { margin-bottom: 15px; font-size: 9pt; color: #475569; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; color: #475569; font-weight: bold; text-align: left; padding: 8px; border-bottom: 2px solid #cbd5e1; font-size: 8pt; text-transform: uppercase; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 8pt; font-weight: bold; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #075985; }
        .footer { margin-top: 30px; text-align: center; font-size: 8pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Peminjaman Alat</h1>
        <p>SMK Muhammadiyah 1 Bantul</p>
    </div>

    <div class="info">
        <span>Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : 'Awal' }} - {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : 'Sekarang' }}</span>
        <span>Total Data: {{ $peminjamans->count() }} peminjaman</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Peminjam</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th class="text-center">Status</th>
                <th class="text-center">Item</th>
                <th class="text-right">Sewa</th>
                <th class="text-right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $peminjaman)
                <tr>
                    <td>{{ $peminjaman->kode_peminjaman }}</td>
                    <td>{{ $peminjaman->user->name }}</td>
                    <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td>{{ $peminjaman->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = 'badge-info';
                            if($peminjaman->status == 'dikembalikan') $statusClass = 'badge-success';
                            if($peminjaman->status == 'menunggu') $statusClass = 'badge-warning';
                            if($peminjaman->status == 'terlambat') $statusClass = 'badge-danger';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($peminjaman->status) }}</span>
                    </td>
                    <td class="text-center">{{ $peminjaman->total_item }}</td>
                    <td class="text-right">Rp {{ number_format($peminjaman->detailPeminjamans->sum('subtotal'), 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($peminjaman->pengembalian?->denda > 0)
                            Rp {{ number_format($peminjaman->pengembalian->denda, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f1f5f9;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-center">{{ $peminjamans->sum('total_item') }}</td>
                <td class="text-right">Rp {{ number_format($peminjamans->sum(function($p) { return $p->detailPeminjamans->sum('subtotal'); }), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($peminjamans->sum(function($p) { return $p->pengembalian?->denda ?? 0; }), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} &bull; &copy; {{ date('Y') }} APIC - SMK Muhammadiyah 1 Bantul
    </div>
</body>
</html>