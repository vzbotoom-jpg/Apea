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
            'alat_ids.*' => 'exists:alats,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_jatuh_tempo' => 'required|date|after:tanggal_pinjam',
            'catatan' => 'nullable|string|max:500',
        ]);

        // Check max alat per hari
        $todayPeminjaman = Peminjaman::where('user_id', auth()->id())
            ->whereDate('tanggal_pinjam', Carbon::today())
            ->whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam'])
            ->withCount('detailPeminjamans')
            ->get();

        $totalDipinjamHariIni = $todayPeminjaman->sum('detail_peminjamans_count');
        
        if (($totalDipinjamHariIni + count($request->alat_ids)) > $maxAlat) {
            return back()->with('error', "Maksimal peminjaman {$maxAlat} alat per hari!");
        }

        DB::beginTransaction();

        try {
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

            // Create detail peminjaman
            $totalSewa = 0;
            foreach ($request->alat_ids as $alatId) {
                $alat = Alat::find($alatId);
                
                // Check availability
                if (!$alat->isAvailable()) {
                    DB::rollBack();
                    return back()->with('error', "Alat {$alat->nama_alat} tidak tersedia!");
                }

                // Reduce stock
                $alat->reduceStock(1);

                // Create detail
                $detail = DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => 1,
                    'harga_sewa_saat_pinjam' => $alat->harga_sewa_per_hari,
                    'subtotal' => $alat->harga_sewa_per_hari,
                ]);

                $totalSewa += $detail->subtotal;
            }

            // Send notification to admin/petugas
            $this->sendNotificationToAdmin($peminjaman);

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

        return view('user.peminjaman.show', compact('peminjaman', 'denda'));
    }

    public function batalkan(Peminjaman $peminjaman)
    {
        // Check ownership
        if ($peminjaman->user_id !== auth()->id()) {
            abort(403);
        }

        // Only can cancel if status is 'menunggu'
        if ($peminjaman->status !== 'menunggu') {
            return back()->with('error', 'Peminjaman tidak dapat dibatalkan karena sudah diverifikasi!');
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($peminjaman->detailPeminjamans as $detail) {
                $detail->alat->addStock($detail->jumlah);
                MutasiAlat::catat($detail->alat, 'masuk', $detail->jumlah, "Pembatalan {$peminjaman->kode_peminjaman}", $peminjaman);
            }

            $peminjaman->status = 'dibatalkan';
            $peminjaman->save();

            DB::commit();

            // Send notification
            $this->sendNotificationToAdmin($peminjaman, 'dibatalkan');

            return redirect()->route('user.peminjaman.index')
                ->with('success', 'Peminjaman berhasil dibatalkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function sendNotificationToAdmin($peminjaman, $type = 'baru')
    {
        // Get all admins
        $admins = User::where('role', 'admin')->get();

        $title = $type === 'baru' ? 'Peminjaman Baru' : 'Peminjaman Dibatalkan';
        $message = $type === 'baru' 
            ? "{$peminjaman->user->name} mengajukan peminjaman {$peminjaman->kode_peminjaman}"
            : "{$peminjaman->user->name} membatalkan peminjaman {$peminjaman->kode_peminjaman}";

        foreach ($admins as $admin) {
            Helper::sendNotification(
                $admin->id,
                $title,
                $message,
                $type === 'baru' ? 'info' : 'warning',
                route('admin.peminjaman.show', $peminjaman)
            );
        }
    }

    public function requestPerpanjangan(Request $request, Peminjaman $peminjaman)
{
    // Check ownership
    if ($peminjaman->user_id !== auth()->id()) {
        abort(403);
    }

    if (!$peminjaman->bisaPerpanjangan()) {
        return back()->with('error', 'Peminjaman ini tidak dapat diperpanjang.');
    }

    $request->validate([
        'tambahan_hari' => 'required|integer|min:1|max:7',
        'alasan'        => 'required|string|max:500',
    ]);

    $peminjaman->update([
        'perpanjangan_requested' => true,
        'tanggal_perpanjangan'   => $peminjaman->tanggal_jatuh_tempo->copy()->addDays((int) $request->tambahan_hari),
        'status_perpanjangan'    => 'menunggu',
        'alasan_perpanjangan'    => $request->alasan,
    ]);

    // Notifikasi ke admin & petugas (link sesuai role)
    $staff = User::whereIn('role', ['admin', 'petugas'])->get();
    foreach ($staff as $u) {
        $route = $u->role === 'admin'
            ? route('admin.peminjaman.show', $peminjaman)
            : route('petugas.peminjaman.show', $peminjaman);

        Helper::sendNotification(
            $u->id,
            '⏳ Pengajuan Perpanjangan',
            "{$peminjaman->user->name} mengajukan perpanjangan {$peminjaman->kode_peminjaman} hingga {$peminjaman->tanggal_perpanjangan->format('d/m/Y')}.",
            'warning',
            $route
        );
    }

    return back()->with('success', 'Pengajuan perpanjangan terkirim. Menunggu persetujuan petugas.');
}
}