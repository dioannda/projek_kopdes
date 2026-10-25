<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Form ubah password sendiri (tambahan): berlaku untuk semua peran.
     * Berbeda dengan UC-08 (manajer mengelola pengguna lain), di sini
     * pengguna mengubah passwordnya sendiri.
     */
    public function edit(): View
    {
        return view('profil.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $pengguna = Auth::user();
        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (! Hash::check($data['password_lama'], $pengguna->password)) {
            return back()->withErrors(['password_lama' => 'Password lama salah.']);
        }

        // Jangan Hash::make di sini: cast 'hashed' pada model Pengguna
        // yang meng-hash otomatis (aturan yang sama seperti UC-08).
        $pengguna->password = $data['password_baru'];
        $pengguna->save();

        return back()->with('sukses', 'Password berhasil diubah.');
    }
}
