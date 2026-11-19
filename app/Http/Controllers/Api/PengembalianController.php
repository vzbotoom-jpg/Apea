<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Services\PengembalianService;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengembalian::with([
            'peminjaman.user',
            'peminjaman.detailPeminjamans.alat',
            'petugas',
        ]);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_kembali', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_kembali', '<=', $request->input('end_date'));
        }

        if ($request->filled('kondisi_alat')) {
            $query->where('kondisi_alat', $request->input('kondisi_alat'));
        }

        return response()->json([
            'status' => true,
            'data' => $query->latest('tanggal_kembali')->paginate(15),
        ]);
    }

    public function proses(Peminjaman $peminjaman)
    {
        if (! in_array($peminjaman->status, ['dipinjam', 'terlambat'], true)
            || $peminjaman->pengembalian()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Peminjaman ini tidak dapat diproses sebagai pengembalian.',
            ], 409);
        }

        $peminjaman->load(['user', 'detailPeminjamans.alat']);

        return response()->json([
            'status' => true,
            'data' => [
                'peminjaman' => $peminjaman,
                'denda' => $peminjaman->calculateDenda(),
                'hari_terlambat' => $peminjaman->getLateDays(),
            ],
        ]);
    }

    public function store(Request $request, Peminjaman $peminjaman)
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
            return response()->json([
                'status' => false,
                'message' => 'Peminjaman ini tidak dapat diproses sebagai pengembalian.',
            ], 409);
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengembalian berhasil diproses.',
            'data' => $pengembalian->load([
                'peminjaman.user',
                'peminjaman.detailPeminjamans.alat',
                'petugas',
            ]),
        ], 201);
    }

    public function show(Pengembalian $pengembalian)
    {
        return response()->json([
            'status' => true,
            'data' => $pengembalian->load([
                'peminjaman.user',
                'peminjaman.detailPeminjamans.alat',
                'petugas',
            ]),
        ]);
    }

    public function receipt(Pengembalian $pengembalian)
    {
        return $this->show($pengembalian);
    }
}
