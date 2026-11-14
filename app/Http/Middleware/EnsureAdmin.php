<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }
        if (! $user->is_active || ! $user->role) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('admin.login')->withErrors(['email' => 'Your account is inactive. Contact the administrator.']);
        }

        return $next($request);
    }
}
