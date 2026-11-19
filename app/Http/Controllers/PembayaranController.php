<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $pendingPayment = $peminjaman->pembayarans()
            ->where('status', 'menunggu')
            ->latest()
            ->first();
        if ($pendingPayment) {
            return redirect()->route('user.pembayaran.instruksi', $pendingPayment);
        }

        $metodeAktif = Pembayaran::enabledMethods();
        $sisaTagihan = $peminjaman->getSisaTagihan();

        return view('user.pembayaran.bayar', compact('peminjaman', 'metodeAktif', 'sisaTagihan'));
    }

    public function store(Request $request, Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);

        $metodeAktif = Pembayaran::enabledMethods();
        abort_if(empty($metodeAktif), 503, 'Belum ada metode pembayaran yang siap digunakan. Hubungi admin.');

        $validated = $request->validate([
            'metode' => ['required', Rule::in($metodeAktif)],
        ]);

        $pembayaran = DB::transaction(function () use ($peminjaman, $validated) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            abort_unless(
                in_array($validated['metode'], Pembayaran::enabledMethods(), true),
                422,
                'Metode pembayaran ini sudah tidak aktif.'
            );
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

            return Pembayaran::create([
                'peminjaman_id' => $loan->id,
                'user_id' => auth()->id(),
                'metode' => $validated['metode'],
                'nominal' => $amountDue,
                'qr_path' => Pembayaran::qrCodes()[$validated['metode']] ?? null,
                'status' => 'menunggu',
            ]);
        });

        User::whereIn('role', ['petugas', 'admin'])->get()->each(
            fn ($u) => Helper::sendNotification(
                $u->id,
                '💳 Pembayaran Menunggu Verifikasi',
                "{$peminjaman->user->name} memilih {$pembayaran->metode_label} untuk tagihan Rp ".
                number_format($pembayaran->nominal, 0, ',', '.')." ({$peminjaman->kode_peminjaman}).",
                'info',
                route('pembayaran.show', $pembayaran)
            )
        );

        return redirect()->route('user.pembayaran.instruksi', $pembayaran);
    }

    public function instructions(Pembayaran $pembayaran)
    {
        abort_unless($pembayaran->user_id === auth()->id(), 403);
        $pembayaran->load('peminjaman');

        return view('user.pembayaran.instruksi', compact('pembayaran'));
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