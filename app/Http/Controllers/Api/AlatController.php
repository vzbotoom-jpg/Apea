<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AlatController extends Controller
{
    /**
     * Get all alat with filters
     */
    public function index(Request $request)
    {
        $query = Alat::with('kategori');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'LIKE', "%{$search}%")
                    ->orWhere('kode_alat', 'LIKE', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kondisi
        if ($request->has('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Sort
        $sort = $request->get('sort', 'nama_alat');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);

        $perPage = $request->get('per_page', 15);
        $alats = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $alats,
        ]);
    }

    /**
     * Get available alat (stok > 0)
     */
    public function available(Request $request)
    {
        $query = Alat::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->with('kategori');

        if ($request->has('search')) {
            $query->where('nama_alat', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $alats = $query->limit(20)->get();

        return response()->json([
            'status' => true,
            'data' => $alats,
        ]);
    }

    /**
     * Get single alat
     */
    public function show(Alat $alat)
    {
        $alat->load(['kategori', 'detailPeminjamans.peminjaman.user']);

        return response()->json([
            'status' => true,
            'data' => $alat,
        ]);
    }

    /**
     * Get alat history (peminjaman)
     */
    public function history(Alat $alat)
    {
        $history = $alat->detailPeminjamans()
            ->with('peminjaman.user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $history,
        ]);
    }

    /**
     * Store new alat (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kondisi' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'perbaikan'])],
            'status' => ['required', Rule::in(['tersedia', 'dipinjam', 'perbaikan', 'tidak_tersedia'])],
            'gambar' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
            'harga_sewa_per_hari' => 'required|integer|min:0',
            'denda_per_hari' => 'required|integer|min:0',
        ]);

        // Generate kode
        $lastAlat = Alat::latest()->first();
        $lastNumber = $lastAlat ? (int) substr($lastAlat->kode_alat, -3) : 0;
        $validated['kode_alat'] = 'ALT-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $validated['slug'] = Str::slug($validated['nama_alat']) . '-' . uniqid();
        $validated['stok_tersedia'] = $validated['stok'];

        // Handle image
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('alat', $filename, 'public');
            $validated['gambar'] = $path;
        }

        $alat = Alat::create($validated);

        LogActivity::log(auth()->id(), "Menambah alat baru: {$alat->nama_alat}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Alat berhasil ditambahkan!',
            'data' => $alat,
        ], 201);
    }

    /**
     * Update alat (Admin only)
     */
    public function update(Request $request, Alat $alat)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kondisi' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'perbaikan'])],
            'status' => ['required', Rule::in(['tersedia', 'dipinjam', 'perbaikan', 'tidak_tersedia'])],
            'gambar' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
            'harga_sewa_per_hari' => 'required|integer|min:0',
            'denda_per_hari' => 'required|integer|min:0',
        ]);

        // Handle image
        if ($request->hasFile('gambar')) {
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('alat', $filename, 'public');
            $validated['gambar'] = $path;
        }

        // Update slug if name changed
        if ($alat->nama_alat !== $validated['nama_alat']) {
            $validated['slug'] = Str::slug($validated['nama_alat']) . '-' . uniqid();
        }

        // Update stok_tersedia if stok changed
        if ($alat->stok !== $validated['stok']) {
            $difference = $validated['stok'] - $alat->stok;
            $validated['stok_tersedia'] = max(0, $alat->stok_tersedia + $difference);
        }

        $alat->update($validated);

        LogActivity::log(auth()->id(), "Memperbarui alat: {$alat->nama_alat}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Alat berhasil diperbarui!',
            'data' => $alat,
        ]);
    }

    /**
     * Delete alat (Admin only)
     */
    public function destroy(Alat $alat)
    {
        if ($alat->detailPeminjamans()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Alat tidak dapat dihapus karena sudah digunakan dalam peminjaman!',
            ], 400);
        }

        if ($alat->gambar) {
            Storage::disk('public')->delete($alat->gambar);
        }

        $namaAlat = $alat->nama_alat;
        $alat->delete();

        LogActivity::log(auth()->id(), "Menghapus alat: {$namaAlat}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Alat berhasil dihapus!',
        ]);
    }

    /**
     * Toggle status alat (Admin only)
     */
    public function toggleStatus(Alat $alat)
    {
        $statuses = ['tersedia', 'dipinjam', 'perbaikan', 'tidak_tersedia'];
        $currentIndex = array_search($alat->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);
        $alat->status = $statuses[$nextIndex];
        $alat->save();

        LogActivity::log(auth()->id(), "Mengubah status alat {$alat->nama_alat} menjadi {$alat->status}", 'API');

        return response()->json([
            'status' => true,
            'message' => "Status alat berhasil diubah menjadi {$alat->status}!",
            'data' => $alat,
        ]);
    }
}