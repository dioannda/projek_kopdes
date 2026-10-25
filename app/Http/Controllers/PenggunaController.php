<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function create(): View
    {
        return view('pengguna.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Password dikirim mentah: cast 'hashed' di model meng-hash otomatis.
        Pengguna::create($this->validasi($request));

        return redirect()->route('pengaturan.index')->with('sukses', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Pengguna $pengguna): View
    {
        return view('pengguna.edit', ['pengguna' => $pengguna]);
    }

    public function update(Request $request, Pengguna $pengguna): RedirectResponse
    {
        $data = $this->validasi($request, $pengguna);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $pengguna->update($data);

        return redirect()->route('pengaturan.index')->with('sukses', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna (UC-08): ditolak jika akun sendiri atau masih
     * terkait riwayat transaksi/penerimaan (FK restrict).
     */
    public function destroy(Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->id === Auth::id()) {
            return redirect()->route('pengaturan.index')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        if ($pengguna->transaksi()->exists() || $pengguna->barangMasuk()->exists()) {
            return redirect()->route('pengaturan.index')
                ->with('error', 'Pengguna tidak dapat dihapus karena masih terkait dengan riwayat transaksi atau penerimaan.');
        }
        $pengguna->delete();

        return redirect()->route('pengaturan.index')->with('sukses', 'Pengguna berhasil dihapus.');
    }

    private function validasi(Request $request, ?Pengguna $pengguna = null): array
    {
        $passwordRule = $pengguna
            ? ['nullable', 'string', 'min:8']
            : ['required', 'string', 'min:8'];

        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('pengguna', 'username')->ignore($pengguna?->id)],
            'peran' => ['required', 'in:kasir,gudang,manajer'],
            'password' => $passwordRule,
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'peran.required' => 'Peran wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);
    }
}
