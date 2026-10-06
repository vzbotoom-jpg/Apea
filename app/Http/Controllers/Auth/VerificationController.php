<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function notice(Request $request)
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request)
    {
        $user = $request->user();

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            abort(403, 'Tautan verifikasi tidak valid.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('user.dashboard')
            ->with('success', '✅ Email berhasil diverifikasi. Selamat datang di APIC!');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('user.dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', '📧 Tautan verifikasi baru telah dikirim.');
    }
}