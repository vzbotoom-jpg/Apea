<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\MutasiAlat;
use App\Models\Notifikasi;
use App\Helpers\Helper;
use App\Services\PengembalianService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->whereIn('status', ['dipinjam', 'terlambat']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $peminjamans = $query->orderBy('tanggal_jatuh_tempo', 'asc')->paginate(10);

        return view('admin.pengembalian.index', compact('peminjamans'));
    }

    public function proses(Peminjaman $peminjaman)
    {
        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Peminjaman ini tidak sedang dipinjam!');
        }

        $peminjaman->load(['detailPeminjamans.alat', 'user']);
        
        // Calculate denda
        $denda = $peminjaman->calculateDenda();
        $hariTerlambat = $peminjaman->getLateDays();

        return view('admin.pengembalian.proses', compact('peminjaman', 'denda', 'hariTerlambat'));
    }

    public function save(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan' => 'nullable|string|max:500',
            'denda' => 'nullable|numeric|min:0',
            'bukti_foto' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        $pengembalian = app(PengembalianService::class)->process(
            $peminjaman,
            $request->user(),
            $validated,
            $request->file('bukti_foto')
        );

        if (! $pengembalian) {
            return back()->with('error', 'Peminjaman ini sudah tidak dapat diproses sebagai pengembalian.');
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($peminjaman)
            ->log("Mengembalikan peminjaman {$peminjaman->kode_peminjaman}");

        return redirect()->route('admin.pengembalian.index')
            ->with('success', 'Pengembalian berhasil diproses!');
    }
}