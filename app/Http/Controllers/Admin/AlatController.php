<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Http\Requests\AlatRequest;
use App\Models\MutasiAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            $query->where('status', $request->status);
        }

        $alats = $query->orderBy('created_at', 'desc')->paginate(10);
        $kategoris = Kategori::where('status', 'active')->get();

        return view('admin.alat.index', compact('alats', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::where('status', 'active')->get();
        return view('admin.alat.create', compact('kategoris'));
    }

    public function store(AlatRequest $request)
    {
        $data = $request->validated();
        
        // Generate kode alat
        $lastAlat = Alat::latest()->first();
        $lastNumber = $lastAlat ? (int) substr($lastAlat->kode_alat, -3) : 0;
        $data['kode_alat'] = 'ALT-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        // Generate slug
        $data['slug'] = Str::slug($data['nama_alat']) . '-' . uniqid();
        
        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('alat', $filename, 'public');
            $data['gambar'] = $path;
        }

        // Set stok_tersedia sama dengan stok
        $data['stok_tersedia'] = $data['stok'];

        $alat = Alat::create($data);

        // Catat mutasi stok awal alat
        MutasiAlat::catat($alat, 'masuk', $alat->stok, 'Stok awal alat');

        // Log activity
        activity()->log("Menambahkan alat baru: {$alat->nama_alat}");

        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil ditambahkan!');
    }

    public function show(Alat $alat)
    {
        $alat->load('kategori', 'detailPeminjamans.peminjaman.user');
        return view('admin.alat.show', compact('alat'));
    }

    public function edit(Alat $alat)
    {
        $kategoris = Kategori::where('status', 'active')->get();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    public function update(AlatRequest $request, Alat $alat)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }
            
            $image = $request->file('gambar');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('alat', $filename, 'public');
            $data['gambar'] = $path;
        }

        // Update slug if name changed
        if ($alat->nama_alat !== $data['nama_alat']) {
            $data['slug'] = Str::slug($data['nama_alat']) . '-' . uniqid();
        }

        // Update stok_tersedia if stok changed
        $oldStok = $alat->stok;
        if ($alat->stok !== $data['stok']) {
            $difference = $data['stok'] - $alat->stok;
            $data['stok_tersedia'] = $alat->stok_tersedia + $difference;
            if ($data['stok_tersedia'] < 0) {
                $data['stok_tersedia'] = 0;
            }
        }

        $alat->update($data);

        // Catat mutasi stok manual jika ada perubahan stok
        $diff = $alat->stok - $oldStok;
        if ($diff > 0) {
            MutasiAlat::catat($alat, 'masuk', $diff, 'Penyesuaian stok manual');
        }
        if ($diff < 0) {
            MutasiAlat::catat($alat, 'keluar', abs($diff), 'Penyesuaian stok manual');
        }

        // Log activity
        activity()->log("Memperbarui alat: {$alat->nama_alat}");

        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil diperbarui!');
    }

    public function destroy(Alat $alat)
    {
        // Check if alat is being used
        if ($alat->detailPeminjamans()->exists()) {
            return back()->with('error', 'Alat tidak dapat dihapus karena sudah digunakan dalam peminjaman!');
        }

        // Delete image
        if ($alat->gambar) {
            Storage::disk('public')->delete($alat->gambar);
        }

        $namaAlat = $alat->nama_alat;
        $alat->delete();

        // Log activity
        activity()->log("Menghapus alat: {$namaAlat}");

        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil dihapus!');
    }

    public function toggleStatus(Alat $alat)
    {
        $statuses = ['tersedia', 'dipinjam', 'perbaikan', 'tidak_tersedia'];
        $currentIndex = array_search($alat->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);
        $alat->status = $statuses[$nextIndex];
        $alat->save();

        // Log activity
        activity()->log("Mengubah status alat {$alat->nama_alat} menjadi {$alat->status}");

        return back()->with('success', "Status alat berhasil diubah menjadi {$alat->status}!");
    }
}