<?php

namespace App\Http\Controllers\User;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\MutasiAlat;
use App\Models\Notifikasi;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class PeminjamanController extends Controller
{
    public function index()
    {
        $peminjamans = Peminjaman::with(['detailPeminjamans.alat', 'petugas'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.peminjaman.index', compact('peminjamans'));
    }

    public function create()
    {
        // Get available alat
        $alats = Alat::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->get();

        $maxAlat = (int) Setting::get('max_alat_per_hari', 2);

        // Check if user has active peminjaman today
        $todayPeminjaman = Peminjaman::where('user_id', auth()->id())
            ->whereDate('tanggal_pinjam', Carbon::today())
            ->whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam'])
            ->withCount('detailPeminjamans')
            ->get();

        $totalDipinjamHariIni = $todayPeminjaman->sum('detail_peminjamans_count');

        return view('user.peminjaman.create', compact('alats', 'totalDipinjamHariIni', 'maxAlat'));
    }

    public function store(Request $request)
    {
        $maxAlat = (int) Setting::get('max_alat_per_hari', 2);

        $request->validate([
            'alat_ids' => 'required|array|min:1|max:' . $maxAlat,
            'alat_ids.*' => 'required|distinct|exists:alats,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_jatuh_tempo' => 'required|date|after:tanggal_pinjam',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            User::whereKey(auth()->id())->lockForUpdate()->firstOrFail();

            $todayPeminjaman = Peminjaman::where('user_id', auth()->id())
                ->whereDate('tanggal_pinjam', $request->input('tanggal_pinjam'))
                ->whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam'])
                ->withCount('detailPeminjamans')
                ->get();
            $totalDipinjamHariIni = $todayPeminjaman->sum('detail_peminjamans_count');

            if (($totalDipinjamHariIni + count($request->alat_ids)) > $maxAlat) {
                DB::rollBack();

                return back()->with('error', "Maksimal peminjaman {$maxAlat} alat per hari!");
            }

            // Generate kode peminjaman
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

            $rentalDays = $peminjaman->getRentalDays();

            // Create detail peminjaman
            foreach ($request->alat_ids as $alatId) {
                $alat = Alat::whereKey($alatId)->lockForUpdate()->firstOrFail();
                
                // Check availability
                if (!$alat->isAvailable()) {
                    DB::rollBack();
                    return back()->with('error', "Alat {$alat->nama_alat} tidak tersedia!");
                }

                // Reserve stock when the request is submitted.
                if (!$alat->reduceStock(1)) {
                    DB::rollBack();
                    return back()->with('error', "Stok alat {$alat->nama_alat} tidak mencukupi!");
                }
                MutasiAlat::catat($alat, 'keluar', 1, "Reservasi {$kodePeminjaman}", $peminjaman);

                // Create detail
                $detail = DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => 1,
                    'harga_sewa_saat_pinjam' => $alat->harga_sewa_per_hari,
                    'denda_per_hari_saat_pinjam' => $alat->denda_per_hari,
                    'subtotal' => $alat->harga_sewa_per_hari * $rentalDays,
                ]);
            }

            // Notify both admins and staff who can verify the request.
            $this->sendNotificationToStaff($peminjaman);

            DB::commit();

            return redirect()->route('user.peminjaman.show', $peminjaman)
                ->with('success', 'Peminjaman berhasil diajukan! Tunggu verifikasi dari petugas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Peminjaman $peminjaman)
    {
        // Check ownership
        if ($peminjaman->user_id !== auth()->id()) {
            abort(403);
        }

        $peminjaman->load(['detailPeminjamans.alat.kategori', 'petugas']);
        
        // Calculate denda if late
        $denda = $peminjaman->calculateDenda();
        $maxPerpanjanganHari = (int) Setting::get('maks_perpanjangan_hari', 7);
        $pembayaranMenunggu = $peminjaman->pembayarans()
            ->where('status', 'menunggu')
            ->latest()
            ->first();

        return view('user.peminjaman.show', compact('peminjaman', 'denda', 'maxPerpanjanganHari', 'pembayaranMenunggu'));
    }

    public function batalkan(Peminjaman $peminjaman)
    {
        // Check ownership
        if ($peminjaman->user_id !== auth()->id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($peminjaman->status !== 'menunggu') {
                DB::rollBack();
                return back()->with('error', 'Peminjaman tidak dapat dibatalkan karena sudah diverifikasi!');
            }

            foreach ($peminjaman->detailPeminjamans as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                $alat->addStock($detail->jumlah);
                MutasiAlat::catat($alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            $peminjaman->status = 'dibatalkan';
            $peminjaman->save();

            DB::commit();

            // Send notification
            $this->sendNotificationToStaff($peminjaman, 'dibatalkan');

            return redirect()->route('user.peminjaman.index')
                ->with('success', 'Peminjaman berhasil dibatalkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function sendNotificationToStaff($peminjaman, $type = 'baru')
    {
        $staff = User::whereIn('role', ['admin', 'petugas'])->get();

        $title = $type === 'baru' ? 'Peminjaman Baru' : 'Peminjaman Dibatalkan';
        $message = $type === 'baru' 
            ? "{$peminjaman->user->name} mengajukan peminjaman {$peminjaman->kode_peminjaman}"
            : "{$peminjaman->user->name} membatalkan peminjaman {$peminjaman->kode_peminjaman}";

        foreach ($staff as $recipient) {
            Helper::sendNotification(
                $recipient->id,
                $title,
                $message,
                $type === 'baru' ? 'info' : 'warning',
                route(
                    $recipient->role === 'admin' ? 'admin.peminjaman.show' : 'petugas.peminjaman.show',
                    $peminjaman
                )
            );
        }
    }

    public function requestPerpanjangan(Request $request, Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        $maxPerpanjanganHari = (int) Setting::get('maks_perpanjangan_hari', 7);
        $validated = $request->validate([
            'tambahan_hari' => "required|integer|min:1|max:{$maxPerpanjanganHari}",
            'alasan' => 'required|string|max:500',
        ]);

        $updated = DB::transaction(function () use ($peminjaman, $validated) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            if ($loan->user_id !== auth()->id() || ! $loan->bisaPerpanjangan()) {
                return null;
            }

            $loan->update([
                'perpanjangan_requested' => true,
                'tanggal_perpanjangan' => $loan->tanggal_jatuh_tempo->copy()->addDays((int) $validated['tambahan_hari']),
                'status_perpanjangan' => 'menunggu',
                'alasan_perpanjangan' => $validated['alasan'],
            ]);

            $staff = User::whereIn('role', ['admin', 'petugas'])->get();
            foreach ($staff as $recipient) {
                $route = $recipient->role === 'admin'
                    ? route('admin.peminjaman.show', $loan)
                    : route('petugas.peminjaman.show', $loan);

                Helper::sendNotification(
                    $recipient->id,
                    '⏳ Pengajuan Perpanjangan',
                    "{$loan->user->name} mengajukan perpanjangan {$loan->kode_peminjaman} hingga {$loan->tanggal_perpanjangan->format('d/m/Y')}.",
                    'warning',
                    $route
                );
            }

            return $loan;
        });

        if (! $updated) {
            return back()->with('error', 'Peminjaman ini tidak dapat diperpanjang.');
        }

        return back()->with('success', 'Pengajuan perpanjangan terkirim. Menunggu persetujuan petugas.');
    }
}