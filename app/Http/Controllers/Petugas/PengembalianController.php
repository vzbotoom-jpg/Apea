<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Notifikasi;
use App\Helpers\Helper;
use App\Services\PengembalianService;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->whereIn('status', ['dipinjam', 'terlambat']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tanggal jatuh tempo
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'overdue':
                    $query->whereDate('tanggal_jatuh_tempo', '<', Carbon::today());
                    break;
                case 'today':
                    $query->whereDate('tanggal_jatuh_tempo', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('tanggal_jatuh_tempo', Carbon::tomorrow());
                    break;
            }
        }

        $peminjamans = $query->orderBy('tanggal_jatuh_tempo', 'asc')->paginate(10);

        // Statistics
        $stats = [
            'total' => Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])->count(),
            'overdue' => Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])
                ->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
                ->count(),
            'today' => Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])
                ->whereDate('tanggal_jatuh_tempo', Carbon::today())
                ->count(),
            'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
        ];

        return view('petugas.pengembalian.index', compact('peminjamans', 'stats'));
    }

    public function proses(Peminjaman $peminjaman)
    {
        // Validasi status
        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Peminjaman ini tidak sedang dipinjam!');
        }

        $peminjaman->load(['detailPeminjamans.alat', 'user']);

        // Calculate denda
        $denda = $peminjaman->calculateDenda();
        $hariTerlambat = $peminjaman->getLateDays();
        
        // Check if already returned
        if ($peminjaman->pengembalian) {
            return redirect()->route('petugas.pengembalian.index')
                ->with('info', 'Peminjaman ini sudah dikembalikan.');
        }

        $masaTenggangHari = (int) Setting::get('masa_tenggang_hari', 2);

        return view('petugas.pengembalian.proses', compact('peminjaman', 'denda', 'hariTerlambat', 'masaTenggangHari'));
    }

    public function save(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan' => 'nullable|string|max:500',
            'bukti_foto' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        $pengembalian = app(PengembalianService::class)->process(
            $peminjaman,
            $request->user(),
            $validated,
            $request->file('bukti_foto')
        );

        if (! $pengembalian) {
            return back()->with('error', 'Peminjaman ini sudah tidak dapat diproses sebagai pengembalian.');
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($peminjaman)
            ->log("Memproses pengembalian {$peminjaman->kode_peminjaman}");

        return redirect()->route('petugas.pengembalian.index')
            ->with('success', 'Pengembalian berhasil diproses!');
    }

    public function report(Request $request)
    {
        $query = Pengembalian::with(['peminjaman.user', 'petugas']);

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_kembali', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_kembali', '<=', $request->end_date);
        }

        // Filter by kondisi
        if ($request->has('kondisi_alat')) {
            $query->where('kondisi_alat', $request->kondisi_alat);
        }

        $pengembalians = $query->orderBy('tanggal_kembali', 'desc')->paginate(10);

        // Statistics
        $stats = [
            'total' => Pengembalian::count(),
            'total_denda' => Pengembalian::sum('denda'),
            'baik' => Pengembalian::where('kondisi_alat', 'baik')->count(),
            'rusak' => Pengembalian::whereIn('kondisi_alat', ['rusak_ringan', 'rusak_berat'])->count(),
            'hilang' => Pengembalian::where('kondisi_alat', 'hilang')->count(),
        ];

        return view('petugas.pengembalian.report', compact('pengembalians', 'stats'));
    }

    public function printReceipt(Pengembalian $pengembalian)
    {
        $pengembalian->load(['peminjaman.user', 'petugas', 'peminjaman.detailPeminjamans.alat']);
        return view('petugas.pengembalian.receipt', compact('pengembalian'));
    }
}