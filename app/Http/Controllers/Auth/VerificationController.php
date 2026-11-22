<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function notice(Request $request)
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        if (Auth::id() === $user->id) {
            $destination = match ($user->role) {
                'admin' => 'admin.dashboard',
                'petugas' => 'petugas.dashboard',
                default => 'user.dashboard',
            };

            return redirect()->route($destination)
                ->with('success', 'Email berhasil diverifikasi.');
        }

        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi. Silakan login.');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            $destination = match ($request->user()->role) {
                'admin' => 'admin.dashboard',
                'petugas' => 'petugas.dashboard',
                default => 'user.dashboard',
            };

            return redirect()->route($destination);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', '📧 Tautan verifikasi baru telah dikirim.');
    }
}