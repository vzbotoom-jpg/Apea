<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class QrController extends Controller
{
    /** Gambar QR (PNG) */
    public function alat(Alat $alat)
    {
        $data = "APIC|{$alat->kode_alat}|".route('petugas.alat.show', $alat);

        return $this->renderPng($data, 'public, max-age=86400');
    }

    /** Gambar QR peminjaman */
    public function peminjaman(Peminjaman $peminjaman)
    {
        $user = Auth::user();
        abort_unless(
            $user && ($user->id === $peminjaman->user_id || $user->hasRole(['admin', 'petugas'])),
            403
        );

        $data = "APIC-PMJ|{$peminjaman->qr_token}";

        return $this->renderPng($data, 'private, no-store');
    }

    private function renderPng(string $data, string $cacheControl): Response
    {
        $result = (new Builder(
            writer: new PngWriter,
            data: $data,
            size: 400,
            margin: 8,
        ))->build();

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Cache-Control' => $cacheControl,
        ]);
    }

    /** Halaman cetak QR (auto-print) */
    public function print(Alat $alat)
    {
        return view('qr.print', compact('alat'));
    }
}
