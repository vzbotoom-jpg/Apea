<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Alat;
use App\Models\MutasiAlat;
use App\Models\Notifikasi;
use App\Models\LogActivity;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PeminjamanController extends Controller
{
    /**
     * Get all peminjaman (Petugas/Admin)
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date_from')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->date_to);
        }

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

        $perPage = $request->get('per_page', 15);
        $peminjamans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $peminjamans,
        ]);
    }

    /**
     * Get user's peminjaman
     */
    public function userPeminjaman(Request $request)
    {
        $query = Peminjaman::with(['detailPeminjamans.alat', 'petugas'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $peminjamans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $peminjamans,
        ]);
    }

    /**
     * Get single peminjaman
     */
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'petugas', 'detailPeminjamans.alat.kategori']);

        // Check access
        $user = auth()->user();
        if ($peminjaman->user_id !== $user->id && !$user->hasRole(['admin', 'petugas'])) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki akses ke peminjaman ini.',
            ], 403);
        }

        $data = $peminjaman->toArray();
        $data['denda'] = $peminjaman->calculateDenda();
        $data['hari_terlambat'] = $peminjaman->getLateDays();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * Check availability before borrowing
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'alat_ids' => 'required|array',
            'alat_ids.*' => 'exists:alats,id',
        ]);

        $results = [];
        foreach ($request->alat_ids as $alatId) {
            $alat = Alat::find($alatId);
            $results[] = [
                'alat_id' => $alatId,
                'nama_alat' => $alat->nama_alat,
                'available' => $alat->isAvailable(),
                'stok_tersedia' => $alat->stok_tersedia,
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $results,
        ]);
    }

    /**
     * Store new peminjaman (User)
     */
    public function store(Request $request)
    {
        $request->validate([
            'alat_ids' => 'required|array|min:1|max:2',
            'alat_ids.*' => 'exists:alats,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_jatuh_tempo' => 'required|date|after:tanggal_pinjam',
            'catatan' => 'nullable|string|max:500',
        ]);

        // Check max 2 alat per hari
        $todayPeminjaman = Peminjaman::where('user_id', auth()->id())
            ->whereDate('tanggal_pinjam', Carbon::today())
            ->whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam'])
            ->withCount('detailPeminjamans')
            ->get();

        $totalDipinjamHariIni = $todayPeminjaman->sum('detail_peminjamans_count');
        
        if (($totalDipinjamHariIni + count($request->alat_ids)) > 2) {
            return response()->json([
                'status' => false,
                'message' => 'Maksimal peminjaman 2 alat per hari!',
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Generate kode
            $kodePeminjaman = Helper::generateKode('PMJ-', new Peminjaman(), 'kode_peminjaman');

            // Create peminjaman
            $peminjaman = Peminjaman::create([
                'kode_peminjaman' => $kodePeminjaman,
                'user_id' => auth()->id(),
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status' => 'menunggu',
                'catatan' => $request->catatan,
            ]);

            // Create details
            foreach ($request->alat_ids as $alatId) {
                $alat = Alat::whereKey($alatId)->lockForUpdate()->firstOrFail();
                
                if (!$alat->isAvailable()) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Alat {$alat->nama_alat} tidak tersedia!",
                    ], 400);
                }

                if (!$alat->reduceStock(1)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Stok alat {$alat->nama_alat} tidak mencukupi!",
                    ], 400);
                }
                MutasiAlat::catat($alat, 'keluar', 1, "Reservasi {$kodePeminjaman}", $peminjaman);

                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => 1,
                    'harga_sewa_saat_pinjam' => $alat->harga_sewa_per_hari,
                    'subtotal' => $alat->harga_sewa_per_hari,
                ]);
            }

            // Notify admins and staff who can verify the request.
            $this->sendNotificationToStaff($peminjaman);

            DB::commit();

            LogActivity::log(auth()->id(), "Mengajukan peminjaman {$peminjaman->kode_peminjaman}", 'API');

            return response()->json([
                'status' => true,
                'message' => 'Peminjaman berhasil diajukan! Tunggu verifikasi petugas.',
                'data' => $peminjaman->load('detailPeminjamans.alat'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel peminjaman (User)
     */
    public function batalkan(Peminjaman $peminjaman)
    {
        if ($peminjaman->user_id !== auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak memiliki akses.',
            ], 403);
        }

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($peminjaman->user_id !== auth()->id()) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Anda tidak memiliki akses.',
                ], 403);
            }
            if ($peminjaman->status !== 'menunggu') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Peminjaman tidak dapat dibatalkan karena sudah diverifikasi!',
                ], 400);
            }

            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->addStock($detail->jumlah);
                MutasiAlat::catat($alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            $peminjaman->status = 'dibatalkan';
            $peminjaman->save();

            DB::commit();
            $this->sendNotificationToStaff($peminjaman, 'dibatalkan');
            LogActivity::log(auth()->id(), "Membatalkan peminjaman {$peminjaman->kode_peminjaman}", 'API');

            return response()->json([
                'status' => true,
                'message' => 'Peminjaman berhasil dibatalkan!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verifikasi peminjaman (Petugas/Admin)
     */
    public function verifikasi(Peminjaman $peminjaman)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($peminjaman->status !== 'menunggu') {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Peminjaman sudah diverifikasi atau dibatalkan!',
                ], 400);
            }

            $peminjaman->status = 'diverifikasi';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->tanggal_verifikasi = now();
            $peminjaman->save();

            // Send notification
            Helper::sendNotification(
                $peminjaman->user_id,
                '✅ Peminjaman Diverifikasi',
                "Peminjaman {$peminjaman->kode_peminjaman} telah diverifikasi.",
                'success'
            );

            DB::commit();

            LogActivity::log(auth()->id(), "Memverifikasi peminjaman {$peminjaman->kode_peminjaman}", 'API');

            return response()->json([
                'status' => true,
                'message' => 'Peminjaman berhasil diverifikasi!',
                'data' => $peminjaman,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Serahkan alat (Petugas/Admin)
     */
    public function ambil(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'diverifikasi') {
            return response()->json([
                'status' => false,
                'message' => 'Peminjaman belum diverifikasi atau sudah diambil!',
            ], 400);
        }

        DB::beginTransaction();

        try {
            $peminjaman->status = 'dipinjam';
            $peminjaman->tanggal_ambil = now();
            $peminjaman->save();

            Helper::sendNotification(
                $peminjaman->user_id,
                '📦 Alat Telah Diambil',
                "Anda telah mengambil alat untuk peminjaman {$peminjaman->kode_peminjaman}.",
                'info'
            );

            DB::commit();

            LogActivity::log(auth()->id(), "Menyerahkan alat untuk peminjaman {$peminjaman->kode_peminjaman}", 'API');

            return response()->json([
                'status' => true,
                'message' => 'Alat berhasil diserahkan!',
                'data' => $peminjaman,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Batalkan peminjaman (Petugas/Admin)
     */
    public function batalkanPetugas(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if (!in_array($peminjaman->status, ['menunggu', 'diverifikasi'], true)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Peminjaman tidak dapat dibatalkan!',
                ], 400);
            }

            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->addStock($detail->jumlah);
                MutasiAlat::catat($alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            $peminjaman->status = 'dibatalkan';
            $peminjaman->petugas_id = auth()->id();
            $peminjaman->catatan = ($peminjaman->catatan ? $peminjaman->catatan . "\n" : '') . "Dibatalkan oleh petugas: " . $request->alasan;
            $peminjaman->save();

            Helper::sendNotification(
                $peminjaman->user_id,
                '❌ Peminjaman Dibatalkan',
                "Peminjaman {$peminjaman->kode_peminjaman} telah dibatalkan oleh petugas.",
                'error'
            );

            DB::commit();

            LogActivity::log(auth()->id(), "Membatalkan peminjaman {$peminjaman->kode_peminjaman}", 'API');

            return response()->json([
                'status' => true,
                'message' => 'Peminjaman berhasil dibatalkan!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get status count for dashboard
     */
    public function statusCount()
    {
        $counts = [
            'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
            'diverifikasi' => Peminjaman::where('status', 'diverifikasi')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'dibatalkan' => Peminjaman::where('status', 'dibatalkan')->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => $counts,
        ]);
    }

    /**
     * Delete peminjaman (Admin only - hard delete)
     */
    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'dibatalkan') {
            return response()->json([
                'status' => false,
                'message' => 'Hanya peminjaman yang dibatalkan yang dapat dihapus!',
            ], 400);
        }

        $kode = $peminjaman->kode_peminjaman;
        $peminjaman->delete();

        LogActivity::log(auth()->id(), "Menghapus peminjaman {$kode}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman berhasil dihapus!',
        ]);
    }

    /**
     * Notify staff who can verify new borrowing requests.
     */
    private function sendNotificationToStaff($peminjaman, $type = 'baru')
    {
        $staff = \App\Models\User::whereIn('role', ['admin', 'petugas'])->get();
        $cancelled = $type === 'dibatalkan';

        foreach ($staff as $recipient) {
            Helper::sendNotification(
                $recipient->id,
                $cancelled ? 'Peminjaman Dibatalkan' : '📋 Peminjaman Baru',
                $cancelled
                    ? "{$peminjaman->user->name} membatalkan peminjaman {$peminjaman->kode_peminjaman}"
                    : "{$peminjaman->user->name} mengajukan peminjaman {$peminjaman->kode_peminjaman}",
                $cancelled ? 'warning' : 'info',
                route(
                    $recipient->role === 'admin' ? 'admin.peminjaman.show' : 'petugas.peminjaman.show',
                    $peminjaman
                )
            );
        }
    }
}