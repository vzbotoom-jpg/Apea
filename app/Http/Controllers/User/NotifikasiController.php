<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    // Halaman daftar semua notifikasi
    public function index()
    {
        $userId = auth()->id();

        $notifications = Notifikasi::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unread = Notifikasi::where('user_id', $userId)
            ->where('is_read', false)->count();

        return view('user.notifikasi.index', compact('notifications', 'unread'));
    }

    // Buka notifikasi → tandai dibaca
    public function show(Notifikasi $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        $notification->markAsRead();

        return view('user.notifikasi.show', compact('notification'));
    }

    // Tandai semua sudah dibaca
    public function readAll()
    {
        Notifikasi::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('user.notifikasi.index')
            ->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}