<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\Alat;
use App\Models\Kategori;
use App\Http\Requests\AlatRequest;
use App\Models\MutasiAlat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $gambarPath = null;
        try {
            $alat = DB::transaction(function () use ($data, $request, &$gambarPath) {
                $data['kode_alat'] = Helper::generateKode('ALT-', Alat::class, 'kode_alat');
                $data['slug'] = Str::slug($data['nama_alat']).'-'.uniqid();
                $data['stok_tersedia'] = $data['stok'];

                if ($request->hasFile('gambar')) {
                    $gambarPath = $request->file('gambar')->store('alat', 'public');
                    $data['gambar'] = $gambarPath;
                }

                $alat = Alat::create($data);
                MutasiAlat::catat($alat, 'masuk', $alat->stok, 'Stok awal alat');

                return $alat;
            });
        } catch (\Throwable $exception) {
            if ($gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }

            throw $exception;
        }

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

        // Update slug if name changed
        if ($alat->nama_alat !== $data['nama_alat']) {
            $data['slug'] = Str::slug($data['nama_alat']) . '-' . uniqid();
        }

        $oldImage = $alat->gambar;
        $updated = DB::transaction(function () use ($alat, $data, $request) {
            $lockedAlat = Alat::whereKey($alat->id)->lockForUpdate()->firstOrFail();
            $oldStok = $lockedAlat->stok;
            $stokTersedia = $lockedAlat->availableStockAfterAdjustment((int) $data['stok']);

            if ($stokTersedia === null) {
                throw ValidationException::withMessages([
                    'stok' => 'Stok total tidak boleh lebih kecil dari unit yang sedang tidak tersedia.',
                ]);
            }

            $data['stok_tersedia'] = $stokTersedia;

            if ($request->hasFile('gambar')) {
                $data['gambar'] = $request->file('gambar')->store('alat', 'public');
            }

            $lockedAlat->update($data);
            $difference = $lockedAlat->stok - $oldStok;

            if ($difference > 0) {
                MutasiAlat::catat($lockedAlat, 'masuk', $difference, 'Penyesuaian stok manual');
            } elseif ($difference < 0) {
                MutasiAlat::catat($lockedAlat, 'keluar', abs($difference), 'Penyesuaian stok manual');
            }

            return $lockedAlat;
        });

        if ($request->hasFile('gambar') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        // Log activity
        activity()->log("Memperbarui alat: {$updated->nama_alat}");

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