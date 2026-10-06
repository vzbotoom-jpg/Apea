<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Alat;
use App\Models\MutasiAlat;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Setting;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            switch ($request->date) {
                case 'today':
                    $query->whereDate('tanggal_pinjam', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('tanggal_pinjam', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('tanggal_pinjam', Carbon::now()->month);
                    break;
            }
        }

        // Search by kode or user name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->has('perpanjangan')) {
            $query->where('status_perpanjangan', 'menunggu');
        }

        $peminjamans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Status counts
        $statusCount = [
            'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
            'diverifikasi' => Peminjaman::where('status', 'diverifikasi')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'dibatalkan' => Peminjaman::where('status', 'dibatalkan')->count(),
        ];

        return view('petugas.peminjaman.index', compact('peminjamans', 'statusCount'));
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'detailPeminjamans.alat.kategori']);
        
        // Calculate denda if late
        $denda = $peminjaman->calculateDenda();
        $hariTerlambat = $peminjaman->getLateDays();

        return view('petugas.peminjaman.show', compact('peminjaman', 'denda', 'hariTerlambat'));
    }

    public function verifikasi(Peminjaman $peminjaman)
    {
        // Validasi status
        if ($peminjaman->status !== 'menunggu') {
            return back()->with('error', 'Peminjaman ini sudah diverifikasi atau dibatalkan!');
        }

        // Validasi ketersediaan alat
        foreach ($peminjaman->detailPeminjamans as $detail) {
            $alat = Alat::find($detail->alat_id);
            if (!$alat->isAvailable()) {
                return back()->with('error', "Alat {$alat->nama_alat} tidak tersedia untuk dipinjam!");
            }
        }

        DB::beginTransaction();

        try {
            // Update status
            $peminjaman->status = 'diverifikasi';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->tanggal_verifikasi = now();
            $peminjaman->save();

            // Kurangi stok alat
            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = Alat::find($detail->alat_id);
                $alat->reduceStock($detail->jumlah);
                MutasiAlat::catat($alat, 'keluar', $detail->jumlah, "Serah terima {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            // Send notification to user
            Helper::sendNotification(
                $peminjaman->user_id,
                '✅ Peminjaman Diverifikasi',
                "Peminjaman {$peminjaman->kode_peminjaman} telah diverifikasi. Silakan ambil alat ke petugas.",
                'success',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($peminjaman)
                ->log("Memverifikasi peminjaman {$peminjaman->kode_peminjaman}");

            return redirect()->route('petugas.peminjaman.show', $peminjaman)
                ->with('success', '✅ Peminjaman berhasil diverifikasi! Alat siap diambil.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function ambil(Peminjaman $peminjaman)
    {
        // Validasi status
        if ($peminjaman->status !== 'diverifikasi') {
            return back()->with('error', 'Peminjaman belum diverifikasi atau sudah diambil!');
        }

        DB::beginTransaction();

        try {
            // Update status
            $peminjaman->status = 'dipinjam';
            $peminjaman->tanggal_ambil = now();
            $peminjaman->save();

            // Update stok alat (sudah dikurangi saat verifikasi)

            // Send notification to user
            Helper::sendNotification(
                $peminjaman->user_id,
                '📦 Alat Telah Diambil',
                "Anda telah mengambil alat untuk peminjaman {$peminjaman->kode_peminjaman}. Jangan lupa mengembalikan tepat waktu!",
                'info',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($peminjaman)
                ->log("Menyerahkan alat untuk peminjaman {$peminjaman->kode_peminjaman}");

            return redirect()->route('petugas.peminjaman.show', $peminjaman)
                ->with('success', '📦 Alat berhasil diserahkan kepada peminjam!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function batalkan(Peminjaman $peminjaman, Request $request)
    {
        // Validasi status
        if (!in_array($peminjaman->status, ['menunggu', 'diverifikasi'])) {
            return back()->with('error', 'Peminjaman tidak dapat dibatalkan!');
        }

        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Kembalikan stok jika sudah diverifikasi
            if ($peminjaman->status === 'diverifikasi') {
                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $detail->alat->addStock($detail->jumlah);
                    MutasiAlat::catat($detail->alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
                }
            }

            // Update status
            $peminjaman->status = 'dibatalkan';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->catatan = ($peminjaman->catatan ? $peminjaman->catatan . "\n" : '') . "Dibatalkan oleh petugas: " . $request->alasan;
            $peminjaman->save();

            // Send notification to user
            Helper::sendNotification(
                $peminjaman->user_id,
                '❌ Peminjaman Dibatalkan',
                "Peminjaman {$peminjaman->kode_peminjaman} telah dibatalkan oleh petugas. Alasan: {$request->alasan}",
                'error',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($peminjaman)
                ->log("Membatalkan peminjaman {$peminjaman->kode_peminjaman}");

            return redirect()->route('petugas.peminjaman.index')
                ->with('success', '❌ Peminjaman berhasil dibatalkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function konfirmasiBayar(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'metode' => 'required|in:tunai,transfer',
        ]);

        $peminjaman->update([
            'status_pembayaran' => 'lunas',
            'metode_pembayaran' => $request->metode,
            'tanggal_bayar' => now(),
            'dibayar_oleh' => auth()->id(),
        ]);

        Helper::sendNotification(
            $peminjaman->user_id,
            '💰 Pembayaran Diterima',
            "Pembayaran {$peminjaman->kode_peminjaman} sebesar Rp " . number_format($peminjaman->total_bayar, 0, ',', '.') . " telah dikonfirmasi ({$request->metode}).",
            'success',
            route('user.peminjaman.show', $peminjaman)
        );

        activity()
            ->causedBy(auth()->user())
            ->performedOn($peminjaman)
            ->log("Mengkonfirmasi pembayaran {$peminjaman->kode_peminjaman}");

        return back()->with('success', '💰 Pembayaran berhasil dikonfirmasi!');
    }

    public function setujuiPerpanjangan(Peminjaman $peminjaman)
{
    if ($peminjaman->status_perpanjangan !== 'menunggu') {
        return back()->with('error', 'Tidak ada pengajuan perpanjangan yang menunggu.');
    }

    DB::beginTransaction();
    try {
        $peminjaman->update([
            'tanggal_jatuh_tempo' => $peminjaman->tanggal_perpanjangan,
            'status_perpanjangan' => 'disetujui',
            'approved_by'         => auth()->id(),
        ]);

        Helper::sendNotification(
            $peminjaman->user_id,
            '✅ Perpanjangan Disetujui',
            "Perpanjangan {$peminjaman->kode_peminjaman} disetujui. Jatuh tempo baru: {$peminjaman->tanggal_jatuh_tempo->format('d/m/Y')}.",
            'success',
            route('user.peminjaman.show', $peminjaman)
        );

        DB::commit();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($peminjaman)
            ->log("Menyetujui perpanjangan {$peminjaman->kode_peminjaman}");

        return back()->with('success', '✅ Perpanjangan berhasil disetujui!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function tolakPerpanjangan(Peminjaman $peminjaman)
{
    if ($peminjaman->status_perpanjangan !== 'menunggu') {
        return back()->with('error', 'Tidak ada pengajuan perpanjangan yang menunggu.');
    }

    $peminjaman->update([
        'status_perpanjangan' => 'ditolak',
        'approved_by'         => auth()->id(),
    ]);

    Helper::sendNotification(
        $peminjaman->user_id,
        '❌ Perpanjangan Ditolak',
        "Pengajuan perpanjangan {$peminjaman->kode_peminjaman} ditolak. Harap kembalikan sesuai jatuh tempo.",
        'error',
        route('user.peminjaman.show', $peminjaman)
    );

    activity()
        ->causedBy(auth()->user())
        ->performedOn($peminjaman)
        ->log("Menolak perpanjangan {$peminjaman->kode_peminjaman}");

    return back()->with('success', '❌ Perpanjangan ditolak.');
}

    public function getDetail(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'detailPeminjamans.alat']);
        return response()->json($peminjaman);
    }

    public function getPeminjamanHariIni()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->whereDate('tanggal_pinjam', Carbon::today())
            ->whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam'])
            ->get();

        return response()->json($peminjamans);
    }
}