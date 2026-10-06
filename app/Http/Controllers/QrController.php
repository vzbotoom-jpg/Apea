<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrController extends Controller
{
    /** Gambar QR (PNG) */
    public function alat(Alat $alat)
    {
        $data = "APIC|{$alat->kode_alat}|" . route('petugas.alat.show', $alat);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->size(400)
            ->margin(8)
            ->data($data)
            ->build();

        return response($result->getString(), 200, [
            'Content-Type'  => $result->getMimeType(),
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /** Gambar QR peminjaman */
    public function peminjaman(Peminjaman $peminjaman)
    {
        $data = "APIC-PMJ|{$peminjaman->kode_peminjaman}|" . route('petugas.peminjaman.show', $peminjaman);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->size(400)
            ->margin(8)
            ->data($data)
            ->build();

        return response($result->getString(), 200, [
            'Content-Type'  => $result->getMimeType(),
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /** Halaman cetak QR (auto-print) */
    public function print(Alat $alat)
    {
        return view('qr.print', compact('alat'));
    }
}