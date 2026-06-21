<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'murid.dashboard');
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $identifier = $credentials['login_identifier'];
        $password = $credentials['password'];

        // 1. Coba cari di User dengan username (untuk Admin)
        $user = User::where('username', $identifier)->first();

        // 2. Jika tidak ketemu, coba cari di Murid berdasarkan NIS/NISN
        if (!$user) {
            $murid = Murid::where('nis', $identifier)
                ->orWhere('nisn', $identifier)
                ->first();

            if ($murid) {
                $user = $murid->user;
            }
        }

        // 3. Verifikasi password dan coba login
        if ($user && Auth::attempt(['username' => $user->username, 'password' => $password], $request->filled('remember'))) {
            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('murid.dashboard');
            }
        }

        return back()->withErrors([
            'login_identifier' => 'NIS/NISN/Username atau Password tidak cocok dengan data kami.',
        ])->onlyInput('login_identifier');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
