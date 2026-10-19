<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\MutasiAlat;
use App\Models\Peminjaman;
use App\Models\Notifikasi;
use App\Helpers\Helper;
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

        // Search by kode or user name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->has('perpanjangan')) {
            $query->where('status_perpanjangan', 'menunggu');
        }

        $peminjamans = $query->orderBy('created_at', 'desc')->paginate(10);
        $statusCount = [
            'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
            'diverifikasi' => Peminjaman::where('status', 'diverifikasi')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'dibatalkan' => Peminjaman::where('status', 'dibatalkan')->count(),
            'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
        ];

        return view('admin.peminjaman.index', compact('peminjamans', 'statusCount'));
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'petugas', 'detailPeminjamans.alat.kategori']);
        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    public function verifikasi(Peminjaman $peminjaman)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($peminjaman->status !== 'menunggu') {
                DB::rollBack();
                return back()->with('error', 'Peminjaman ini sudah diverifikasi!');
            }

            $peminjaman->status = 'diverifikasi';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->tanggal_verifikasi = now();
            $peminjaman->save();

            // Send notification to user
            Helper::sendNotification(
                $peminjaman->user_id,
                'Peminjaman Diverifikasi',
                "Peminjaman {$peminjaman->kode_peminjaman} telah diverifikasi oleh petugas. Silakan ambil alat.",
                'success',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            // Log activity
            activity()->log("Memverifikasi peminjaman {$peminjaman->kode_peminjaman}");

            return back()->with('success', 'Peminjaman berhasil diverifikasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function batalkan(Peminjaman $peminjaman)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if (!in_array($peminjaman->status, ['menunggu', 'diverifikasi'], true)) {
                DB::rollBack();
                return back()->with('error', 'Peminjaman tidak dapat dibatalkan!');
            }

            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->addStock($detail->jumlah);
                MutasiAlat::catat($alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            $peminjaman->status = 'dibatalkan';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->save();

            // Send notification to user
            Helper::sendNotification(
                $peminjaman->user_id,
                'Peminjaman Dibatalkan',
                "Peminjaman {$peminjaman->kode_peminjaman} telah dibatalkan oleh petugas.",
                'error',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            // Log activity
            activity()->log("Membatalkan peminjaman {$peminjaman->kode_peminjaman}");

            return back()->with('success', 'Peminjaman berhasil dibatalkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}