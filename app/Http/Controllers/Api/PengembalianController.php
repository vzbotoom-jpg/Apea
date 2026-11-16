<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'denda' => 'nullable|numeric|min:0',
            'bukti_foto' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        $pengembalian = DB::transaction(function () use ($validated, $request, $peminjaman) {
            $peminjaman = Peminjaman::query()
                ->whereKey($peminjaman->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($peminjaman->status, ['dipinjam', 'terlambat'], true)
                || $peminjaman->pengembalian()->exists()) {
                return null;
            }

            $peminjaman->load('detailPeminjamans.alat');
            $denda = $validated['denda'] ?? $peminjaman->calculateDenda();
            $hariTerlambat = $peminjaman->getLateDays();

            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'petugas_id' => $request->user()->id,
                'tanggal_kembali' => now(),
                'denda' => $denda,
                'hari_terlambat' => $hariTerlambat,
                'kondisi_alat' => $validated['kondisi_alat'],
                'catatan' => $validated['catatan'] ?? null,
                'bukti_foto' => $request->file('bukti_foto')?->store('pengembalian', 'public'),
            ]);

            $peminjaman->update(['status' => 'dikembalikan']);

            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = $detail->alat;
                $alat->addStock($detail->jumlah);

                if ($validated['kondisi_alat'] === 'hilang') {
                    $alat->status = 'tidak_tersedia';
                    $alat->stok_tersedia = 0;
                } elseif ($validated['kondisi_alat'] === 'rusak_berat') {
                    $alat->status = 'perbaikan';
                    $alat->kondisi = 'rusak_berat';
                } elseif ($validated['kondisi_alat'] === 'rusak_ringan') {
                    $alat->kondisi = 'rusak_ringan';
                }

                if ($validated['kondisi_alat'] !== 'baik') {
                    $alat->save();
                }
            }

            if ($denda > 0) {
                Denda::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jumlah_denda' => $denda,
                    'tanggal_denda' => now(),
                    'hari_terlambat' => $hariTerlambat,
                    'status' => 'belum_bayar',
                ]);
            }

            Helper::sendNotification(
                $peminjaman->user_id,
                'Pengembalian selesai',
                "Peminjaman {$peminjaman->kode_peminjaman} telah dikembalikan.",
                $denda > 0 ? 'warning' : 'success',
                route('user.peminjaman.show', $peminjaman),
            );

            return $pengembalian;
        });

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
