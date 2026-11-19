<?php

namespace App\Http\Controllers;

use App\Models\Denda;
use App\Services\DendaService;
use Illuminate\Http\Request;

class DendaController extends Controller
{
    public function index(Request $request, DendaService $dendaService)
    {
        $dendaService->synchronizeOverdue();

        $status = $request->input('status');
        $query = Denda::with(['peminjaman.user']);
        if (in_array($status, ['belum_bayar', 'lunas'], true)) {
            $query->where('status', $status);
        }

        $dendas = $query->latest('tanggal_denda')->paginate(15)->withQueryString();
        $summary = [
            'belum_bayar' => Denda::where('status', 'belum_bayar')->sum('jumlah_denda'),
            'lunas' => Denda::where('status', 'lunas')->sum('jumlah_denda'),
            'jumlah_belum_bayar' => Denda::where('status', 'belum_bayar')->count(),
        ];

        return view('denda.index', compact('dendas', 'summary', 'status'));
    }
}
