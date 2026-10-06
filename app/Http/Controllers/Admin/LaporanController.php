<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\User;
use App\Models\Pengembalian;
use App\Exports\PeminjamanExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        $totalPeminjaman = Peminjaman::count();
        $totalUser = User::where('role', 'user')->count();
        $totalAlat = Alat::count();
        $peminjamanBulanIni = Peminjaman::whereMonth('created_at', Carbon::now()->month)->count();
        $totalDenda = Pengembalian::sum('denda');
        $totalPemasukan = \App\Models\Pembayaran::where('status', 'disetujui')->sum('nominal');

        // Peminjaman per bulan (chart)
        $peminjamanPerBulan = Peminjaman::selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pinjam', Carbon::now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $peminjamanPerBulan[$i] ?? 0;
        }

        // Top 5 alat terpopuler
        $topAlat = Alat::withCount('detailPeminjamans')
            ->orderBy('detail_peminjamans_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.laporan.index', compact(
            'totalPeminjaman',
            'totalUser',
            'totalAlat',
            'peminjamanBulanIni',
            'totalDenda',
            'totalPemasukan',
            'chartData',
            'topAlat'
        ));
    }

    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $peminjamans = $query->orderBy('tanggal_pinjam', 'desc')->get();

        return view('admin.laporan.peminjaman', compact('peminjamans'));
    }

    public function exportPdf(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        $peminjamans = $query->orderBy('tanggal_pinjam', 'desc')->get();
        $total = $peminjamans->sum('detailPeminjamans.sum(subtotal)');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('peminjamans', 'total'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PeminjamanExport($request), 
            'laporan-peminjaman-' . date('Y-m-d') . '.xlsx'
        );
    }
}