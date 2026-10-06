<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    // ================= USER =================
    public function form(Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        // ✅ PERBAIKAN: Block payment untuk status yang tidak diizinkan
        $allowedStatus = ['dipinjam', 'terlambat', 'dikembalikan'];
        abort_unless(in_array($peminjaman->status, $allowedStatus), 403, 
            "Pembayaran hanya boleh untuk peminjaman yang sedang berlangsung atau telah dikembalikan.");

        $metodeAktif = json_decode(Setting::get('metode_pembayaran_aktif',
            json_encode(['transfer_bank', 'dana', 'shopeepay', 'gopay', 'tunai'])), true);
        $tujuan = json_decode(Setting::get('tujuan_pembayaran', json_encode([])), true) ?: [];

        return view('user.pembayaran.bayar', compact('peminjaman', 'metodeAktif', 'tujuan'));
    }

    public function store(Request $request, Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        // ✅ PERBAIKAN 1: Block payment untuk status yang tidak diizinkan
        $allowedStatus = ['dipinjam', 'terlambat', 'dikembalikan'];
        abort_unless(in_array($peminjaman->status, $allowedStatus), 403, 
            "Pembayaran hanya boleh untuk peminjaman yang sedang berlangsung atau telah dikembalikan.");

        // ✅ PERBAIKAN 2: Guard against duplicate pending payment
        $existingPending = Pembayaran::where('peminjaman_id', $peminjaman->id)
            ->where('status', 'menunggu')
            ->exists();
        
        abort_if($existingPending, 422, 
            'Sudah ada pembayaran dalam proses verifikasi. Tunggu hasilnya atau hubungi petugas.');

        $metodeAktif = json_decode(Setting::get('metode_pembayaran_aktif',
            json_encode(['transfer_bank', 'dana', 'shopeepay', 'gopay', 'tunai'])), true);

        $request->validate([
            'metode'  => 'required|in:' . implode(',', $metodeAktif),
            'nominal' => 'required|integer|min:' . $peminjaman->total_bayar, // ✅ PERBAIKAN 3: nominal >= total_bayar
            'bukti'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nominal.min' => 'Nominal pembayaran minimal Rp ' . number_format($peminjaman->total_bayar, 0, ',', '.')
        ]);

        $pembayaran = Pembayaran::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id'       => auth()->id(),
            'metode'        => $request->metode,
            'nominal'       => $request->nominal,
            'bukti_path'    => $request->file('bukti')->store(
                'bukti-pembayaran/' . auth()->id(), 
                'private'  // ✅ PERBAIKAN 4: Store on private disk
            ),
            'status'        => 'menunggu',
        ]);

        // ✅ NOTIFIKASI KHUSUS ke petugas & admin (berisi nominal)
        User::whereIn('role', ['petugas', 'admin'])->get()->each(
            fn ($u) => Helper::sendNotification(
                $u->id,
                '💰 Pembayaran Baru — Verifikasi Sekarang',
                "{$peminjaman->user->name} membayar Rp " . number_format($pembayaran->nominal, 0, ',', '.') .
                " via {$pembayaran->metode_label} untuk {$peminjaman->kode_peminjaman}.",
                'info',
                route('pembayaran.show', $pembayaran)
            )
        );

        return redirect()->route('user.peminjaman.show', $peminjaman)
            ->with('success', '✅ Bukti pembayaran terkirim! Menunggu verifikasi petugas.');
    }

    // ================= PETUGAS / ADMIN =================
    private function guardStaff(): void
    {
        abort_unless(in_array(auth()->user()->role, ['petugas', 'admin']), 403);
    }

    public function index()
    {
        $this->guardStaff();

        $pembayarans    = Pembayaran::with(['user', 'peminjaman'])->latest()->paginate(10);
        $menunggu       = Pembayaran::where('status', 'menunggu')->count();
        $totalTerkumpul = Pembayaran::where('status', 'disetujui')->sum('nominal');

        return view('pembayaran.index', compact('pembayarans', 'menunggu', 'totalTerkumpul'));
    }

    public function show(Pembayaran $pembayaran)
    {
        $this->guardStaff();
        $pembayaran->load(['user', 'peminjaman', 'verifier']);

        return view('pembayaran.show', compact('pembayaran'));
    }

    public function setujui(Pembayaran $pembayaran)
    {
        $this->guardStaff();
        abort_unless($pembayaran->status === 'menunggu', 422);

        DB::transaction(function () use ($pembayaran) {
            // ✅ PERBAIKAN 5: Re-check status inside transaction to prevent double approval
            $pembayaran->refresh();
            abort_unless($pembayaran->status === 'menunggu', 422, 
                'Pembayaran ini sudah diproses oleh petugas lain.');

            $pembayaran->update([
                'status'      => 'disetujui',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
            $pembayaran->peminjaman->update([
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => $pembayaran->metode,
                'tanggal_bayar'     => now(),
                'dibayar_oleh'      => auth()->id(),
            ]);
        });

        Helper::sendNotification(
            $pembayaran->user_id,
            '✅ Pembayaran Terverifikasi — LUNAS',
            "Pembayaran Rp " . number_format($pembayaran->nominal, 0, ',', '.') .
            " untuk {$pembayaran->peminjaman->kode_peminjaman} telah diverifikasi.",
            'success',
            route('user.peminjaman.show', $pembayaran->peminjaman)
        );

        activity()->causedBy(auth()->user())->performedOn($pembayaran)
            ->log("Verifikasi pembayaran {$pembayaran->peminjaman->kode_peminjaman}");

        return redirect()->route('pembayaran.index')->with('success', '✅ Pembayaran diverifikasi — status LUNAS.');
    }

    public function tolak(Request $request, Pembayaran $pembayaran)
    {
        $this->guardStaff();
        abort_unless($pembayaran->status === 'menunggu', 422);

        $request->validate(['catatan' => 'required|string|max:500']);

        $pembayaran->update([
            'status'      => 'ditolak',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan'     => $request->catatan,
        ]);

        Helper::sendNotification(
            $pembayaran->user_id,
            '❌ Pembayaran Ditolak',
            "Pembayaran {$pembayaran->peminjaman->kode_peminjaman} ditolak: {$request->catatan}",
            'error',
            route('user.peminjaman.show', $pembayaran->peminjaman)
        );

        return redirect()->route('pembayaran.index')->with('success', '❌ Pembayaran ditolak.');
    }
}