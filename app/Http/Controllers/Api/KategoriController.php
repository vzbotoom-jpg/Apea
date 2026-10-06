<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /**
     * Get all kategori
     */
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('nama_kategori', 'LIKE', "%{$request->search}%");
        }

        $kategoris = $query->orderBy('nama_kategori')->get();

        return response()->json([
            'status' => true,
            'data' => $kategoris,
        ]);
    }

    /**
     * Get alat by kategori
     */
    public function alatByKategori(Kategori $kategori)
    {
        $alats = $kategori->alats()
            ->where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'kategori' => $kategori,
                'alats' => $alats,
            ],
        ]);
    }

    /**
     * Store new kategori (Admin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategoris',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['slug'] = Str::slug($validated['nama_kategori']) . '-' . uniqid();

        $kategori = Kategori::create($validated);

        LogActivity::log(auth()->id(), "Menambahkan kategori: {$kategori->nama_kategori}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'data' => $kategori,
        ], 201);
    }

    /**
     * Update kategori (Admin only)
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kategoris')->ignore($kategori->id),
            ],
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($kategori->nama_kategori !== $validated['nama_kategori']) {
            $validated['slug'] = Str::slug($validated['nama_kategori']) . '-' . uniqid();
        }

        $kategori->update($validated);

        LogActivity::log(auth()->id(), "Memperbarui kategori: {$kategori->nama_kategori}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil diperbarui!',
            'data' => $kategori,
        ]);
    }

    /**
     * Delete kategori (Admin only)
     */
    public function destroy(Kategori $kategori)
    {
        if ($kategori->alats()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh alat!',
            ], 400);
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        LogActivity::log(auth()->id(), "Menghapus kategori: {$namaKategori}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil dihapus!',
        ]);
    }

    /**
     * Toggle kategori status (Admin only)
     */
    public function toggleStatus(Kategori $kategori)
    {
        $kategori->status = $kategori->status === 'active' ? 'inactive' : 'active';
        $kategori->save();

        LogActivity::log(auth()->id(), "Mengubah status kategori {$kategori->nama_kategori} menjadi {$kategori->status}", 'API');

        return response()->json([
            'status' => true,
            'message' => 'Status kategori berhasil diubah!',
            'data' => $kategori,
        ]);
    }
}