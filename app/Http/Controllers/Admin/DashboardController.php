<?php

namespace App\Http\Controllers\Admin;

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
        $totalUser = User::where('role', 'user')->count();
        $totalPeminjaman = Peminjaman::count();
        $peminjamanAktif = Peminjaman::whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam'])->count();

        // Notifications
        $notifications = Notifikasi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $unreadNotifications = Notifikasi::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        // Recent Peminjaman
        $recentPeminjaman = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Chart Data - Peminjaman per bulan
        $peminjamanPerBulan = Peminjaman::selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pinjam', Carbon::now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $peminjamanPerBulan[$i] ?? 0;
        }

        // Top 5 Alat Terpopuler
        $topAlat = Alat::withCount('detailPeminjamans')
            ->orderBy('detail_peminjamans_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAlat',
            'totalUser',
            'totalPeminjaman',
            'peminjamanAktif',
            'notifications',
            'unreadNotifications',
            'recentPeminjaman',
            'chartData',
            'topAlat'
        ));
    }
}