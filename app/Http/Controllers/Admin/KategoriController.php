<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Http\Requests\KategoriRequest;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_kategori', 'LIKE', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $kategoris = $query->orderBy('nama_kategori')->paginate(10);
        
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(KategoriRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['nama_kategori']) . '-' . uniqid();

        $kategori = Kategori::create($data);

        Helper::logActivity(auth()->id(), "Menambahkan kategori: {$kategori->nama_kategori}");

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(KategoriRequest $request, Kategori $kategori)
    {
        $data = $request->validated();

        if ($kategori->nama_kategori !== $data['nama_kategori']) {
            $data['slug'] = Str::slug($data['nama_kategori']) . '-' . uniqid();
        }

        $kategori->update($data);

        Helper::logActivity(auth()->id(), "Memperbarui kategori: {$kategori->nama_kategori}");

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Kategori $kategori)
    {
        // Check if kategori has alat
        if ($kategori->alats()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh alat!');
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        Helper::logActivity(auth()->id(), "Menghapus kategori: {$namaKategori}");

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }

    public function toggleStatus(Kategori $kategori)
    {
        $kategori->status = $kategori->status === 'active' ? 'inactive' : 'active';
        $kategori->save();

        Helper::logActivity(auth()->id(), "Mengubah status kategori {$kategori->nama_kategori} menjadi {$kategori->status}");

        return back()->with('success', "Status kategori berhasil diubah!");
    }
}