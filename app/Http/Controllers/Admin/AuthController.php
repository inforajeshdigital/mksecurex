<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        if (! Auth::user()->role) {
            Auth::logout();

            return back()->withErrors(['email' => 'Your account has no role assigned.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        Auth::user()->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
        ActivityLog::record('login', 'Logged in');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        ActivityLog::record('logout', 'Logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
