<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\MutasiAlat;
use App\Models\Notifikasi;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $peminjamans = $query->orderBy('tanggal_jatuh_tempo', 'asc')->paginate(10);

        return view('admin.pengembalian.index', compact('peminjamans'));
    }

    public function proses(Peminjaman $peminjaman)
    {
        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Peminjaman ini tidak sedang dipinjam!');
        }

        $peminjaman->load(['detailPeminjamans.alat', 'user']);
        
        // Calculate denda
        $denda = $peminjaman->calculateDenda();
        $hariTerlambat = $peminjaman->getLateDays();

        return view('admin.pengembalian.proses', compact('peminjaman', 'denda', 'hariTerlambat'));
    }

    public function save(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan' => 'nullable|string|max:500',
            'denda' => 'nullable|numeric|min:0',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Peminjaman ini tidak sedang dipinjam!');
        }

        DB::beginTransaction();

        try {
            // Create pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'petugas_id' => auth()->id(),
                'tanggal_kembali' => now(),
                'denda' => $request->denda ?? 0,
                'hari_terlambat' => $peminjaman->getLateDays(),
                'kondisi_alat' => $request->kondisi_alat,
                'catatan' => $request->catatan,
            ]);

            // Update peminjaman status
            $peminjaman->status = 'dikembalikan';
            $peminjaman->save();

            // Update stock alat (kembalikan stok)
            foreach ($peminjaman->detailPeminjamans as $detail) {
                $detail->alat->addStock($detail->jumlah);
                MutasiAlat::catat($detail->alat, 'masuk', $detail->jumlah, "Pengembalian {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            // Create denda record if has denda
            if ($request->denda > 0) {
                Denda::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jumlah_denda' => $request->denda,
                    'tanggal_denda' => now(),
                    'hari_terlambat' => $peminjaman->getLateDays(),
                    'status' => 'belum_bayar',
                ]);
            }

            // Send notification to user
            $message = "Peminjaman {$peminjaman->kode_peminjaman} telah dikembalikan.";
            if ($request->denda > 0) {
                $message .= " Anda memiliki denda sebesar Rp " . number_format($request->denda, 0, ',', '.');
            }

            Helper::sendNotification(
                $peminjaman->user_id,
                'Pengembalian Selesai',
                $message,
                'success',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            // Log activity
            activity()->log("Mengembalikan peminjaman {$peminjaman->kode_peminjaman}");

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diproses!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}