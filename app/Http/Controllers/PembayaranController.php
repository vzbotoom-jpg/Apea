<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PembayaranController extends Controller
{
    // ================= USER =================
    public function form(Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        // ✅ PERBAIKAN: Block payment untuk status yang tidak diizinkan
        $allowedStatus = ['diverifikasi', 'dipinjam', 'terlambat', 'dikembalikan'];
        abort_unless(in_array($peminjaman->status, $allowedStatus), 403, 
            "Pembayaran hanya boleh untuk peminjaman yang sedang berlangsung atau telah dikembalikan.");
        abort_if($peminjaman->getSisaTagihan() === 0, 403, 'Tagihan peminjaman ini sudah lunas.');

        $metodeAktif = json_decode(Setting::get('metode_pembayaran_aktif',
            json_encode(['transfer_bank', 'dana', 'shopeepay', 'gopay', 'tunai'])), true);
        $tujuan = json_decode(Setting::get('tujuan_pembayaran', json_encode([])), true) ?: [];
        $infoTransfer = Setting::get('info_transfer', '');
        if ($infoTransfer !== '') {
            $tujuan['transfer'] = $tujuan['transfer'] ?? $infoTransfer;
            $tujuan['transfer_bank'] = $tujuan['transfer_bank'] ?? $infoTransfer;
        }
        $sisaTagihan = $peminjaman->getSisaTagihan();

        return view('user.pembayaran.bayar', compact('peminjaman', 'metodeAktif', 'tujuan', 'sisaTagihan'));
    }

    public function store(Request $request, Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        $metodeAktif = json_decode(Setting::get('metode_pembayaran_aktif',
            json_encode(['transfer_bank', 'dana', 'shopeepay', 'gopay', 'tunai'])), true);

        $validated = $request->validate([
            'metode'  => 'required|in:' . implode(',', $metodeAktif),
            'nominal' => 'required|integer|min:1',
            'bukti'   => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pembayaran = DB::transaction(function () use ($request, $peminjaman, $validated) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            $allowedStatus = ['diverifikasi', 'dipinjam', 'terlambat', 'dikembalikan'];
            abort_unless(in_array($loan->status, $allowedStatus, true), 403,
                'Pembayaran hanya boleh untuk peminjaman yang sedang berlangsung atau telah dikembalikan.');

            abort_if($loan->getSisaTagihan() === 0, 422, 'Tagihan peminjaman ini sudah lunas.');
            abort_if(
                Pembayaran::where('peminjaman_id', $loan->id)->where('status', 'menunggu')->exists(),
                422,
                'Sudah ada pembayaran dalam proses verifikasi. Tunggu hasilnya atau hubungi petugas.'
            );

            $amountDue = $loan->getSisaTagihan();
            $request->validate([
                'nominal' => "required|integer|min:{$amountDue}",
            ], [
                'nominal.min' => 'Nominal pembayaran minimal Rp '.number_format($amountDue, 0, ',', '.'),
            ]);

            return Pembayaran::create([
                'peminjaman_id' => $loan->id,
                'user_id' => auth()->id(),
                'metode' => $validated['metode'],
                'nominal' => $validated['nominal'],
                'bukti_path' => $request->file('bukti')->store('bukti-pembayaran/'.auth()->id(), 'private'),
                'status' => 'menunggu',
            ]);
        });

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

    public function bukti(Pembayaran $pembayaran)
    {
        // Akses hanya untuk pemilik pembayaran atau staf (admin/petugas)
        $user = auth()->user();
        if ($pembayaran->user_id !== $user->id && !in_array($user->role, ['admin', 'petugas'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (!$pembayaran->bukti_path || !Storage::disk('private')->exists($pembayaran->bukti_path)) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('private')->response($pembayaran->bukti_path);
    }

    public function setujui(Pembayaran $pembayaran)
    {
        $this->guardStaff();

        $pembayaran = DB::transaction(function () use ($pembayaran) {
            $payment = Pembayaran::whereKey($pembayaran->id)->lockForUpdate()->firstOrFail();
            abort_unless($payment->status === 'menunggu', 422, 'Pembayaran ini sudah diproses.');

            $loan = Peminjaman::whereKey($payment->peminjaman_id)->lockForUpdate()->firstOrFail();
            $amountDue = $loan->getSisaTagihan();
            abort_if($amountDue === 0, 422, 'Tagihan peminjaman ini sudah lunas.');
            abort_if($payment->nominal < $amountDue, 422, 'Nominal pembayaran belum mencukupi sisa tagihan.');

            $payment->update([
                'status'      => 'disetujui',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $loan->denda?->markAsPaid();
            $loan->update([
                'status_pembayaran' => $loan->getSisaTagihan() === 0 ? 'lunas' : 'belum_bayar',
                'metode_pembayaran' => $pembayaran->metode,
                'tanggal_bayar'     => now(),
                'dibayar_oleh'      => auth()->id(),
            ]);

            return $payment->load('peminjaman');
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

        DB::transaction(function () use ($pembayaran, $request) {
            $payment = Pembayaran::whereKey($pembayaran->id)->lockForUpdate()->firstOrFail();
            abort_unless($payment->status === 'menunggu', 422, 'Pembayaran ini sudah diproses.');
            $payment->update([
                'status' => 'ditolak',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'catatan' => $request->catatan,
            ]);
        });

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