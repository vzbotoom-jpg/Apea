<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        // Featured alat (terbaru)
        $alatTerbaru = Alat::with('kategori')
            ->where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Alat terpopuler
        $alatTerpopuler = Alat::withCount('detailPeminjamans')
            ->where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->orderBy('detail_peminjamans_count', 'desc')
            ->limit(4)
            ->get();

        // Kategori
        $kategoris = Kategori::where('status', 'active')
            ->withCount('alats')
            ->orderBy('alats_count', 'desc')
            ->limit(8)
            ->get();

        // Stats
        $totalAlat = Alat::count();
        $totalTersedia = Alat::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->count();
        $totalKategori = Kategori::where('status', 'active')->count();

        // Hitung peminjaman aktif (dipinjam atau terlambat)
        $totalPeminjaman = Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])->count();

        // Testimonials (static atau dari database)
        $testimonials = [
            [
                'name' => 'Ahmad Rizki',
                'role' => 'Mahasiswa',
                'avatar' => 'A',
                'message' => 'Aplikasi ini sangat membantu saya dalam meminjam alat untuk praktikum. Prosesnya cepat dan mudah!',
                'rating' => 5,
            ],
            [
                'name' => 'Nurhaliza',
                'role' => 'Guru',
                'avatar' => 'S',
                'message' => 'Saya sangat terbantu dengan sistem peminjaman ini. Stok alat selalu terupdate dengan baik.',
                'rating' => 5,
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Petugas Lab',
                'avatar' => 'B',
                'message' => 'Sistem ini memudahkan kami dalam mengelola inventaris alat dan memonitor peminjaman.',
                'rating' => 4,
            ],
        ];

        return view('home', compact(
            'alatTerbaru',
            'alatTerpopuler',
            'kategoris',
            'totalAlat',
            'totalTersedia',
            'totalKategori',
            'totalPeminjaman',
            'testimonials'
        ));
    }

    /**
     * ✅ BARU: Katalog Alat publik (menu "Alat" di navbar)
     * Bisa diakses tamu (tanpa login) untuk melihat stok tersedia.
     */
    public function alat(Request $request)
    {
        $query = Alat::with('kategori')->where('status', 'tersedia');

        // Filter pencarian (nama / kode)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'LIKE', "%{$search}%")
                  ->orWhere('kode_alat', 'LIKE', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $alats     = $query->latest()->paginate(12)->withQueryString();
        $kategoris = Kategori::where('status', 'active')->orderBy('nama_kategori')->get();

        return view('alat', compact('alats', 'kategoris'));
    }

    /**
     * Display the about page
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Search alat via AJAX
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $alats = Alat::where(function ($q) use ($query) {
            $q->where('nama_alat', 'LIKE', "%{$query}%")
              ->orWhere('kode_alat', 'LIKE', "%{$query}%")
              ->orWhere('deskripsi', 'LIKE', "%{$query}%");
        })
            ->where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->with('kategori')
            ->limit(10)
            ->get(['id', 'nama_alat', 'kode_alat', 'harga_sewa_per_hari']);

        return response()->json($alats);
    }

    // ============================================
    // PUBLIC INFORMATION PAGES
    // ============================================

    public function guide()
    {
        return view('guide');
    }

    public function faq()
    {
        return view('faq');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }

    public function model()
    {
        return view('model');
    }

    public function usage()
    {
        return view('usage');
    }

    public function privacyChoices()
    {
        return view('privacy-choices');
    }

    public function features()
    {
        return view('features');
    }

    public function overview()
{
    $stats = [
        'alat'       => \App\Models\Alat::count(),
        'kategori'   => \App\Models\Kategori::where('status', 'active')->count(),
        'peminjaman' => \App\Models\Peminjaman::count(),
        'user'       => \App\Models\User::count(),
    ];

    return view('overview', compact('stats'));
}
}