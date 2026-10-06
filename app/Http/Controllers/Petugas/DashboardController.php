<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Stats
        $totalAlat = Alat::count();
        $alatTersedia = Alat::where('status', 'tersedia')->count();
        $totalPeminjaman = Peminjaman::count();
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $peminjamanAktif = Peminjaman::whereIn('status', ['diverifikasi', 'dipinjam'])->count();
        $peminjamanTerlambat = Peminjaman::where('status', 'terlambat')->count();

        // Peminjaman hari ini
        $peminjamanHariIni = Peminjaman::whereDate('tanggal_pinjam', Carbon::today())->count();
        $pengembalianHariIni = Peminjaman::whereDate('tanggal_jatuh_tempo', Carbon::today())
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count();

        // Notifications
        $notifications = Notifikasi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $unreadNotifications = Notifikasi::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        // Recent Peminjaman yang perlu diverifikasi
        $needVerification = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // Recent Peminjaman yang perlu dikembalikan
        $needReturn = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDate('tanggal_jatuh_tempo', '<=', Carbon::today())
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->limit(5)
            ->get();

        return view('petugas.dashboard', compact(
            'totalAlat',
            'alatTersedia',
            'totalPeminjaman',
            'peminjamanMenunggu',
            'peminjamanAktif',
            'peminjamanTerlambat',
            'peminjamanHariIni',
            'pengembalianHariIni',
            'notifications',
            'unreadNotifications',
            'needVerification',
            'needReturn'
        ));
    }
}