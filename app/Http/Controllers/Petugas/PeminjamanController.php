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
use App\Models\Pembayaran;
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

    public function scanTransaction(string $token)
    {
        $peminjaman = Peminjaman::where('qr_token', $token)
            ->with(['user', 'detailPeminjamans.alat'])
            ->firstOrFail();

        return view('petugas.scan-transaction', compact('peminjaman'));
    }

    public function verifikasi(Peminjaman $peminjaman)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($peminjaman->status !== 'menunggu') {
                DB::rollBack();
                return back()->with('error', 'Peminjaman ini sudah diverifikasi atau dibatalkan!');
            }

            // Update status
            $peminjaman->status = 'diverifikasi';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->tanggal_verifikasi = now();
            $peminjaman->save();

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
        $updated = DB::transaction(function () use ($peminjaman) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($loan->status !== 'diverifikasi') {
                return false;
            }

            $loan->status = 'dipinjam';
            $loan->tanggal_ambil = now();
            $loan->save();

            Helper::sendNotification(
                $loan->user_id,
                '📦 Alat Telah Diambil',
                "Anda telah mengambil alat untuk peminjaman {$loan->kode_peminjaman}. Jangan lupa mengembalikan tepat waktu!",
                'info',
                route('user.peminjaman.show', $loan)
            );

            return $loan;
        });

        if (! $updated) {
            return back()->with('error', 'Peminjaman belum diverifikasi atau sudah diserahkan.');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($updated)
            ->log("Menyerahkan alat untuk peminjaman {$updated->kode_peminjaman}");

        return redirect()->route('petugas.peminjaman.show', $updated)
            ->with('success', '📦 Alat berhasil diserahkan kepada peminjam!');
    }

    public function batalkan(Peminjaman $peminjaman, Request $request)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if (!in_array($peminjaman->status, ['menunggu', 'diverifikasi'], true)) {
                DB::rollBack();
                return back()->with('error', 'Peminjaman tidak dapat dibatalkan!');
            }

            // The reservation is created at submission, so every pre-handover cancellation restores it.
            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->addStock($detail->jumlah);
                MutasiAlat::catat($alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
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

        $pembayaran = DB::transaction(function () use ($request, $peminjaman) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            $amountDue = $loan->getSisaTagihan();

            if ($amountDue === 0) {
                return null;
            }

            if ($loan->pembayarans()->where('status', 'menunggu')->exists()) {
                return false;
            }

            $method = $request->metode === 'transfer' ? 'transfer_bank' : 'tunai';
            $payment = Pembayaran::create([
                'peminjaman_id' => $loan->id,
                'user_id' => $loan->user_id,
                'metode' => $method,
                'nominal' => $amountDue,
                'status' => 'disetujui',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $loan->denda?->markAsPaid();
            $loan->update([
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => $request->metode,
                'tanggal_bayar' => now(),
                'dibayar_oleh' => auth()->id(),
            ]);

            return $payment;
        });

        if ($pembayaran === null) {
            return back()->with('error', 'Tagihan peminjaman ini sudah lunas.');
        }

        if ($pembayaran === false) {
            return back()->with('error', 'Pembayaran online masih menunggu verifikasi.');
        }

        Helper::sendNotification(
            $peminjaman->user_id,
            '💰 Pembayaran Diterima',
            "Pembayaran {$peminjaman->kode_peminjaman} sebesar Rp " . number_format($pembayaran->nominal, 0, ',', '.') . " telah dikonfirmasi ({$request->metode}).",
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
        $approved = DB::transaction(function () use ($peminjaman) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($loan->status !== 'dipinjam' || $loan->status_perpanjangan !== 'menunggu') {
                return null;
            }

            $loan->update([
                'tanggal_jatuh_tempo' => $loan->tanggal_perpanjangan,
                'status_perpanjangan' => 'disetujui',
                'approved_by' => auth()->id(),
            ]);

            Helper::sendNotification(
                $loan->user_id,
                '✅ Perpanjangan Disetujui',
                "Perpanjangan {$loan->kode_peminjaman} disetujui. Jatuh tempo baru: {$loan->tanggal_jatuh_tempo->format('d/m/Y')}.",
                'success',
                route('user.peminjaman.show', $loan)
            );

            return $loan;
        });

        if (! $approved) {
            return back()->with('error', 'Perpanjangan hanya dapat disetujui saat peminjaman masih aktif dan permintaan menunggu.');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($approved)
            ->log("Menyetujui perpanjangan {$approved->kode_peminjaman}");

        return back()->with('success', '✅ Perpanjangan berhasil disetujui!');
    }

    public function tolakPerpanjangan(Peminjaman $peminjaman)
    {
        $rejected = DB::transaction(function () use ($peminjaman) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($loan->status !== 'dipinjam' || $loan->status_perpanjangan !== 'menunggu') {
                return null;
            }

            $loan->update([
                'status_perpanjangan' => 'ditolak',
                'approved_by' => auth()->id(),
            ]);

            Helper::sendNotification(
                $loan->user_id,
                '❌ Perpanjangan Ditolak',
                "Pengajuan perpanjangan {$loan->kode_peminjaman} ditolak. Harap kembalikan sesuai jatuh tempo.",
                'error',
                route('user.peminjaman.show', $loan)
            );

            return $loan;
        });

        if (! $rejected) {
            return back()->with('error', 'Perpanjangan hanya dapat ditolak saat peminjaman masih aktif dan permintaan menunggu.');
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($rejected)
            ->log("Menolak perpanjangan {$rejected->kode_peminjaman}");

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