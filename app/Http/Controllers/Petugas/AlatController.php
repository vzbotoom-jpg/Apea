<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_alat', 'LIKE', "%{$request->search}%")
                  ->orWhere('nama_alat', 'LIKE', "%{$request->search}%");
            });
        }

        // Filter by kategori
        if ($request->has('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kondisi
        if ($request->has('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $alats = $query->latest()->paginate(10);
        $kategoris = Kategori::where('status', 'active')->get();

        return view('petugas.alat.index', compact('alats', 'kategoris'));
    }

    public function scan()
    {
        return view('petugas.scan');
    }

    public function show(Alat $alat)
    {
        $alat->load(['kategori', 'detailPeminjamans' => function ($query) {
            $query->whereHas('peminjaman', function ($q) {
                $q->whereIn('status', ['dipinjam', 'terlambat']);
            })->with('peminjaman.user');
        }]);

        return view('petugas.alat.show', compact('alat'));
    }

    public function getAvailableAlats(Request $request)
    {
        $query = Alat::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_alat', 'LIKE', "%{$search}%");
        }

        if ($request->has('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $alats = $query->limit(10)->get();

        return response()->json($alats);
    }
}