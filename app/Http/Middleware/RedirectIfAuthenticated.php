<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole(['admin'])) {
                    return redirect()->route('admin.dashboard');
                }
                if ($user->hasRole(['petugas'])) {
                    return redirect()->route('petugas.dashboard');
                }
                if ($user->role === 'user') {
                    return redirect()->route('user.dashboard');
                }
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
