<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Notifikasi;
use App\Models\Alat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // ================= STATS =================
        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $menunggu  = Peminjaman::where('user_id', $userId)->where('status', 'menunggu')->count();
        $dipinjam  = Peminjaman::where('user_id', $userId)->whereIn('status', ['diverifikasi', 'dipinjam'])->count();
        $terlambat = Peminjaman::where('user_id', $userId)->where('status', 'terlambat')->count();
        $selesai   = Peminjaman::where('user_id', $userId)->whereIn('status', ['dikembalikan', 'dibatalkan'])->count();

        // ============ KUOTA HARIAN (maks 2 pengajuan/hari) ============
        $kuotaSisa = max(0, 2 - Peminjaman::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('status', ['dikembalikan', 'dibatalkan'])
            ->count());

        // ============ PEMINJAMAN AKTIF (kartu di dashboard) ============
       $aktif = Peminjaman::with(['detailPeminjamans.alat', 'pembayarans'])
            ->where('user_id', $userId)
            ->whereNotIn('status', ['dikembalikan', 'dibatalkan'])
            ->orderBy('created_at', 'desc')
            ->get();

        // ============ REMINDER JATUH TEMPO (≤ 2 hari) ============
        $reminder = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDate('tanggal_jatuh_tempo', '<=', Carbon::today()->addDays(2))
            ->orderBy('tanggal_jatuh_tempo')
            ->first();

        $daysLeft = $reminder
            ? abs((int) Carbon::today()->diffInDays($reminder->tanggal_jatuh_tempo, false))
            : 0;

        // ============ NOTIFIKASI (widget + badge topbar/sidebar) ============
        try {
            $notifications = Notifikasi::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $unread = Notifikasi::where('user_id', $userId)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            $notifications = collect([]);
            $unread = 0;
        }

        // Alias untuk layout (topbar) & sidebar
        $unreadNotifications = $unread;

        // ============ DATA PENDUKUNG (dipakai view lain bila perlu) ============
        $recentPeminjaman = Peminjaman::with(['detailPeminjamans.alat'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $alatTersedia = Alat::where('status', 'tersedia')->where('stok_tersedia', '>', 0)->count();

        $needReturn = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDate('tanggal_jatuh_tempo', '<=', Carbon::today()->addDays(3))
            ->count();

        return view('user.dashboard', compact(
            'totalPeminjaman', 'menunggu', 'dipinjam', 'terlambat', 'selesai',
            'kuotaSisa', 'aktif', 'reminder', 'daysLeft',
            'notifications', 'unread', 'unreadNotifications',
            'recentPeminjaman', 'alatTersedia', 'needReturn'
        ));
    }
}