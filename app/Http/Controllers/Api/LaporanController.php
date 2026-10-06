<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\User;
use App\Models\Denda;
use App\Models\LogActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PeminjamanExport;

class LaporanController extends Controller
{
    /**
     * Dashboard stats
     */
    public function dashboard(Request $request)
    {
        $totalPeminjaman = Peminjaman::count();
        $totalUser = User::where('role', 'user')->count();
        $totalAlat = Alat::count();
        $totalDenda = Pengembalian::sum('denda');
        
        $peminjamanBulanIni = Peminjaman::whereMonth('created_at', Carbon::now()->month)->count();
        $peminjamanAktif = Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])->count();
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();

        // Peminjaman per bulan
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

        // Top 5 alat
        $topAlat = Alat::withCount('detailPeminjamans')
            ->orderBy('detail_peminjamans_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'stats' => [
                    'total_peminjaman' => $totalPeminjaman,
                    'total_user' => $totalUser,
                    'total_alat' => $totalAlat,
                    'total_denda' => $totalDenda,
                    'peminjaman_bulan_ini' => $peminjamanBulanIni,
                    'peminjaman_aktif' => $peminjamanAktif,
                    'peminjaman_menunggu' => $peminjamanMenunggu,
                ],
                'chart' => $chartData,
                'top_alat' => $topAlat,
            ],
        ]);
    }

    /**
     * Laporan peminjaman
     */
    public function peminjaman(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        if ($request->has('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $peminjamans = $query->orderBy('tanggal_pinjam', 'desc')->get();

        // Summary
        $summary = [
            'total' => $peminjamans->count(),
            'total_item' => $peminjamans->sum('total_item'),
            'total_sewa' => $peminjamans->sum(function ($p) {
                return $p->detailPeminjamans->sum('subtotal');
            }),
            'total_denda' => $peminjamans->sum(function ($p) {
                return $p->pengembalian?->denda ?? 0;
            }),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => $summary,
                'peminjamans' => $peminjamans,
            ],
        ]);
    }

    /**
     * Laporan pengembalian
     */
    public function pengembalian(Request $request)
    {
        $query = Pengembalian::with(['peminjaman.user', 'petugas']);

        if ($request->has('start_date')) {
            $query->whereDate('tanggal_kembali', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('tanggal_kembali', '<=', $request->end_date);
        }
        if ($request->has('kondisi_alat')) {
            $query->where('kondisi_alat', $request->kondisi_alat);
        }

        $pengembalians = $query->orderBy('tanggal_kembali', 'desc')->get();

        $summary = [
            'total' => $pengembalians->count(),
            'total_denda' => $pengembalians->sum('denda'),
            'baik' => $pengembalians->where('kondisi_alat', 'baik')->count(),
            'rusak' => $pengembalians->whereIn('kondisi_alat', ['rusak_ringan', 'rusak_berat'])->count(),
            'hilang' => $pengembalians->where('kondisi_alat', 'hilang')->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => $summary,
                'pengembalians' => $pengembalians,
            ],
        ]);
    }

    /**
     * Alat terpopuler
     */
    public function alatTerpopuler(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        $topAlat = Alat::withCount(['detailPeminjamans' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('peminjaman', function ($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal_pinjam', '>=', $startDate)
                    ->whereDate('tanggal_pinjam', '<=', $endDate);
            });
        }])
        ->having('detail_peminjamans_count', '>', 0)
        ->orderBy('detail_peminjamans_count', 'desc')
        ->limit(10)
        ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'top_alat' => $topAlat,
            ],
        ]);
    }

    /**
     * Laporan denda (Admin only)
     */
    public function denda(Request $request)
    {
        $query = Denda::with(['peminjaman.user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->whereDate('tanggal_denda', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('tanggal_denda', '<=', $request->end_date);
        }

        $dendas = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total' => $dendas->count(),
            'total_denda' => $dendas->sum('jumlah_denda'),
            'belum_bayar' => $dendas->where('status', 'belum_bayar')->sum('jumlah_denda'),
            'lunas' => $dendas->where('status', 'lunas')->sum('jumlah_denda'),
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => $summary,
                'dendas' => $dendas,
            ],
        ]);
    }

    /**
     * Bayar denda (Admin only)
     */
    public function bayarDenda(Denda $denda)
    {
        if ($denda->status === 'lunas') {
            return response()->json([
                'status' => false,
                'message' => 'Denda sudah lunas!',
            ], 400);
        }

        $denda->markAsPaid();

        LogActivity::log(auth()->id(), "Membayar denda peminjaman {$denda->peminjaman->kode_peminjaman}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Denda berhasil dibayar!',
            'data' => $denda,
        ]);
    }

    /**
     * Laporan user (Admin only)
     */
    public function users(Request $request)
    {
        $query = User::withCount('peminjamans');

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('peminjamans_count', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $users,
        ]);
    }

    /**
     * Laporan log aktivitas (Admin only)
     */
    public function logs(Request $request)
    {
        $query = LogActivity::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('activity')) {
            $query->where('activity', 'LIKE', "%{$request->activity}%");
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->limit($request->get('limit', 50))
            ->get();

        return response()->json([
            'status' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        if ($request->has('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        $peminjamans = $query->orderBy('tanggal_pinjam', 'desc')->get();

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('peminjamans'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PeminjamanExport($request),
            'laporan-peminjaman-' . date('Y-m-d') . '.xlsx'
        );
    }
}