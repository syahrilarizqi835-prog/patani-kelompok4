<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminProfilController extends Controller
{
    /**
     * Tampilkan halaman profil admin.
     */
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profil', compact('admin'));
    }

    /**
     * Update data profil admin (nama, email, telepon, dll).
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone'      => ['nullable', 'string', 'max:20'],
            'nik'        => ['nullable', 'string', 'max:20'],
            'desa'       => ['nullable', 'string', 'max:100'],
            'kecamatan'  => ['nullable', 'string', 'max:100'],
            'alamat'     => ['nullable', 'string', 'max:255'],
        ]);

        $admin->update($request->only(['name', 'email', 'phone', 'nik', 'desa', 'kecamatan', 'alamat']));

        return redirect()->route('admin.profil')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update foto profil admin.
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $admin = Auth::user();

        // Hapus foto lama jika ada
        if ($admin->foto_profil && Storage::disk('public')->exists($admin->foto_profil)) {
            Storage::disk('public')->delete($admin->foto_profil);
        }

        // Simpan foto baru
        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $admin->update(['foto_profil' => $path]);

        return redirect()->route('admin.profil')->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Update password admin.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama'          => ['required'],
            'password_baru'          => ['required', 'string', 'min:8', 'confirmed'],
            'password_baru_confirmation' => ['required'],
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->password_lama, $admin->password)) {
            return redirect()->route('admin.profil')
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->with('tab', 'keamanan');
        }

        $admin->update(['password' => bcrypt($request->password_baru)]);

        return redirect()->route('admin.profil')->with('success', 'Password berhasil diperbarui!');
    }
}