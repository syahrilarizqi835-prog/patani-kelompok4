<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman pengaturan profil petani.
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.pengaturan', compact('user'));
    }

    /**
     * Update data profil petani.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'      => ['nullable', 'string', 'max:20'],
            'nik'        => ['nullable', 'string', 'max:20'],
            'desa'       => ['nullable', 'string', 'max:100'],
            'kecamatan'  => ['nullable', 'string', 'max:100'],
            'alamat'     => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'nik', 'desa', 'kecamatan', 'alamat']));

        return redirect()->route('dashboard.pengaturan')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update foto profil petani.
     */
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Simpan foto baru
        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $user->update(['foto_profil' => $path]);

        return redirect()->route('dashboard.pengaturan')->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Update password petani.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama'              => ['required'],
            'password_baru'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_baru_confirmation' => ['required'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return redirect()->route('dashboard.pengaturan')
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->with('tab', 'keamanan');
        }

        $user->update(['password' => bcrypt($request->password_baru)]);

        return redirect()->route('dashboard.pengaturan')->with('success', 'Password berhasil diperbarui!');
    }
}