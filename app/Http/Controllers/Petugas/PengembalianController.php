<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Denda;
use App\Models\Notifikasi;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tanggal jatuh tempo
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'overdue':
                    $query->whereDate('tanggal_jatuh_tempo', '<', Carbon::today());
                    break;
                case 'today':
                    $query->whereDate('tanggal_jatuh_tempo', Carbon::today());
                    break;
                case 'tomorrow':
                    $query->whereDate('tanggal_jatuh_tempo', Carbon::tomorrow());
                    break;
            }
        }

        $peminjamans = $query->orderBy('tanggal_jatuh_tempo', 'asc')->paginate(10);

        // Statistics
        $stats = [
            'total' => Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])->count(),
            'overdue' => Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])
                ->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
                ->count(),
            'today' => Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])
                ->whereDate('tanggal_jatuh_tempo', Carbon::today())
                ->count(),
            'terlambat' => Peminjaman::where('status', 'terlambat')->count(),
        ];

        return view('petugas.pengembalian.index', compact('peminjamans', 'stats'));
    }

    public function proses(Peminjaman $peminjaman)
    {
        // Validasi status
        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Peminjaman ini tidak sedang dipinjam!');
        }

        $peminjaman->load(['detailPeminjamans.alat', 'user']);

        // Calculate denda
        $denda = $peminjaman->calculateDenda();
        $hariTerlambat = $peminjaman->getLateDays();
        
        // Check if already returned
        if ($peminjaman->pengembalian) {
            return redirect()->route('petugas.pengembalian.index')
                ->with('info', 'Peminjaman ini sudah dikembalikan.');
        }

        return view('petugas.pengembalian.proses', compact('peminjaman', 'denda', 'hariTerlambat'));
    }

    public function save(Request $request, Peminjaman $peminjaman)
    {
        // Validasi
        $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan' => 'nullable|string|max:500',
            'denda' => 'nullable|numeric|min:0',
            'bukti_foto' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        // Validasi status
        if (!in_array($peminjaman->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Peminjaman ini tidak sedang dipinjam!');
        }

        // Check if already returned
        if ($peminjaman->pengembalian) {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan!');
        }

        DB::beginTransaction();

        try {
            // Handle file upload
            $buktiPath = null;
            if ($request->hasFile('bukti_foto')) {
                $file = $request->file('bukti_foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $buktiPath = $file->storeAs('pengembalian', $filename, 'public');
            }

            // Calculate denda
            $denda = $request->denda ?? $peminjaman->calculateDenda();
            $hariTerlambat = $peminjaman->getLateDays();

            // Create pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'petugas_id' => auth()->id(),
                'tanggal_kembali' => now(),
                'denda' => $denda,
                'hari_terlambat' => $hariTerlambat,
                'kondisi_alat' => $request->kondisi_alat,
                'catatan' => $request->catatan,
                'bukti_foto' => $buktiPath,
            ]);

            // Update peminjaman status
            $peminjaman->status = 'dikembalikan';
            $peminjaman->save();

            // Update stok alat (kembalikan stok)
            foreach ($peminjaman->detailPeminjamans as $detail) {
                $detail->alat->addStock($detail->jumlah);
            }

            // Jika ada kondisi alat rusak/hilang, update status alat
            if (in_array($request->kondisi_alat, ['rusak_ringan', 'rusak_berat', 'hilang'])) {
                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $alat = $detail->alat;
                    if ($request->kondisi_alat === 'hilang') {
                        $alat->status = 'tidak_tersedia';
                        $alat->stok_tersedia = 0;
                    } elseif ($request->kondisi_alat === 'rusak_berat') {
                        $alat->status = 'perbaikan';
                        $alat->kondisi = 'rusak_berat';
                    } elseif ($request->kondisi_alat === 'rusak_ringan') {
                        $alat->kondisi = 'rusak_ringan';
                    }
                    $alat->save();
                }
            }

            // Create denda record if has denda
            if ($denda > 0) {
                Denda::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jumlah_denda' => $denda,
                    'tanggal_denda' => now(),
                    'hari_terlambat' => $hariTerlambat,
                    'status' => 'belum_bayar',
                ]);
            }

            // Send notification to user
            $message = "Peminjaman {$peminjaman->kode_peminjaman} telah dikembalikan.";
            if ($denda > 0) {
                $message .= " Anda memiliki denda sebesar Rp " . number_format($denda, 0, ',', '.') . 
                           ". Silakan bayar denda ke petugas.";
            }
            if ($request->kondisi_alat !== 'baik') {
                $message .= " Kondisi alat: " . ucfirst(str_replace('_', ' ', $request->kondisi_alat));
            }

            Helper::sendNotification(
                $peminjaman->user_id,
                '🔄 Pengembalian Selesai',
                $message,
                $denda > 0 ? 'warning' : 'success',
                route('user.peminjaman.show', $peminjaman)
            );

            DB::commit();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($peminjaman)
                ->log("Memproses pengembalian {$peminjaman->kode_peminjaman}");

            $statusMessage = '🔄 Pengembalian berhasil diproses!';
            if ($denda > 0) {
                $statusMessage .= " Denda: Rp " . number_format($denda, 0, ',', '.');
            }

            return redirect()->route('petugas.pengembalian.index')
                ->with('success', $statusMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $query = Pengembalian::with(['peminjaman.user', 'petugas']);

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_kembali', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_kembali', '<=', $request->end_date);
        }

        // Filter by kondisi
        if ($request->has('kondisi_alat')) {
            $query->where('kondisi_alat', $request->kondisi_alat);
        }

        $pengembalians = $query->orderBy('tanggal_kembali', 'desc')->paginate(10);

        // Statistics
        $stats = [
            'total' => Pengembalian::count(),
            'total_denda' => Pengembalian::sum('denda'),
            'baik' => Pengembalian::where('kondisi_alat', 'baik')->count(),
            'rusak' => Pengembalian::whereIn('kondisi_alat', ['rusak_ringan', 'rusak_berat'])->count(),
            'hilang' => Pengembalian::where('kondisi_alat', 'hilang')->count(),
        ];

        return view('petugas.pengembalian.report', compact('pengembalians', 'stats'));
    }

    public function printReceipt(Pengembalian $pengembalian)
    {
        $pengembalian->load(['peminjaman.user', 'petugas', 'peminjaman.detailPeminjamans.alat']);
        return view('petugas.pengembalian.receipt', compact('pengembalian'));
    }
}