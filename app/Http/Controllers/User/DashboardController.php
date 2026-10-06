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

        // Stats
        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $menunggu = Peminjaman::where('user_id', $userId)->where('status', 'menunggu')->count();
        $dipinjam = Peminjaman::where('user_id', $userId)->whereIn('status', ['diverifikasi', 'dipinjam'])->count();
        $selesai = Peminjaman::where('user_id', $userId)->whereIn('status', ['dikembalikan', 'dibatalkan'])->count();

        // Notifications - TAMBAHKAN TRY CATCH
        try {
            $notifications = Notifikasi::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $unreadNotifications = Notifikasi::where('user_id', $userId)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            // Jika tabel belum ada, gunakan collection kosong
            $notifications = collect([]);
            $unreadNotifications = 0;
        }

        // Recent Peminjaman
        $recentPeminjaman = Peminjaman::with(['detailPeminjamans.alat'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Alat tersedia
        $alatTersedia = Alat::where('status', 'tersedia')->where('stok_tersedia', '>', 0)->count();

        // Peminjaman yang perlu dikembalikan
        $needReturn = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDate('tanggal_jatuh_tempo', '<=', Carbon::today()->addDays(3))
            ->count();

        return view('user.dashboard', compact(
            'totalPeminjaman',
            'menunggu',
            'dipinjam',
            'selesai',
            'notifications',
            'unreadNotifications',
            'recentPeminjaman',
            'alatTersedia',
            'needReturn'
        ));
    }
}