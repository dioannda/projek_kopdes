<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan form login (UC-01). Pengguna yang sudah login
     * diarahkan ke dashboard.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses login (UC-01): verifikasi lewat Pengguna::login(),
     * membuka sesi, lalu mengarahkan ke dashboard.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $pengguna = Pengguna::login($request->username, $request->password);

        if ($pengguna === null) {
            return back()
                ->withErrors(['login' => 'Username atau password salah.'])
                ->onlyInput('username');
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Mengakhiri sesi (UC-01).
     */
    public function logout(Request $request): RedirectResponse
    {
        $pengguna = $request->user();

        if ($pengguna instanceof Pengguna) {
            $pengguna->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
