<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Log aktivitas login
            // activity()->log('User logged in');

            // Redirect berdasarkan role
            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard')->with('success', 'Selamat datang Admin!'),
                'petugas' => redirect()->route('petugas.dashboard')->with('success', 'Selamat datang Petugas!'),
                default => redirect()->route('user.dashboard')->with('success', 'Selamat datang!'),
            };
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout.');
    }
}