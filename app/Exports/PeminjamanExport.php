<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        if ($this->request->has('start_date') && $this->request->start_date) {
            $query->whereDate('tanggal_pinjam', '>=', $this->request->start_date);
        }
        if ($this->request->has('end_date') && $this->request->end_date) {
            $query->whereDate('tanggal_pinjam', '<=', $this->request->end_date);
        }

        return $query->orderBy('tanggal_pinjam', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Kode Peminjaman',
            'Peminjam',
            'Tanggal Pinjam',
            'Jatuh Tempo',
            'Tanggal Kembali',
            'Status',
            'Total Item',
            'Total Sewa',
            'Denda',
        ];
    }

    public function map($peminjaman): array
    {
        return [
            $peminjaman->kode_peminjaman,
            $peminjaman->user->name,
            $peminjaman->tanggal_pinjam->format('d/m/Y'),
            $peminjaman->tanggal_jatuh_tempo->format('d/m/Y'),
            $peminjaman->pengembalian?->tanggal_kembali?->format('d/m/Y') ?? '-',
            ucfirst($peminjaman->status),
            $peminjaman->total_item,
            'Rp ' . number_format($peminjaman->total_sewa, 0, ',', '.'),
            'Rp ' . number_format($peminjaman->pengembalian?->denda ?? 0, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}