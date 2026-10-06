<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'LIKE', "%{$search}%")
                    ->orWhere('kode_alat', 'LIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->has('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'tersedia') {
                $query->where('status', 'tersedia')->where('stok_tersedia', '>', 0);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Sort
        $sort = $request->get('sort', 'nama_alat');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);

        $alats = $query->paginate(12);
        $kategoris = Kategori::where('status', 'active')->get();

        return view('user.alat.index', compact('alats', 'kategoris'));
    }

    public function show(Alat $alat)
    {
        $alat->load(['kategori', 'detailPeminjamans' => function ($query) {
            $query->whereHas('peminjaman', function ($q) {
                $q->whereIn('status', ['dipinjam', 'terlambat']);
            });
        }]);

        // Get related alat (same kategori)
        $relatedAlat = Alat::where('kategori_id', $alat->kategori_id)
            ->where('id', '!=', $alat->id)
            ->where('status', 'tersedia')
            ->limit(4)
            ->get();

        return view('user.alat.show', compact('alat', 'relatedAlat'));
    }

    public function cari(Request $request)
    {
        $search = $request->get('q');
        
        $alats = Alat::where('nama_alat', 'LIKE', "%{$search}%")
            ->where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->limit(10)
            ->get(['id', 'nama_alat', 'kode_alat', 'harga_sewa_per_hari']);

        return response()->json($alats);
    }
}