<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::all()->pluck('value', 'key')->toArray();
        $admin      = Auth::user();
        return view('admin.pengaturan', compact('pengaturan', 'admin'));
    }

    /**
     * Update pengaturan sistem (nama app, email, telepon).
     */
    public function update(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            Pengaturan::set($key, $value);
        }

        return redirect()->back()
            ->with('success', 'Pengaturan sistem berhasil disimpan!')
            ->with('active_tab', 'sistem');
    }

    /**
     * Update data profil admin.
     */
    public function updateProfil(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone'     => ['nullable', 'string', 'max:20'],
            'nik'       => ['nullable', 'string', 'max:20'],
            'desa'      => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'alamat'    => ['nullable', 'string', 'max:255'],
        ]);

        $admin->update($request->only(['name', 'email', 'phone', 'nik', 'desa', 'kecamatan', 'alamat']));

        return redirect()->back()
            ->with('success', 'Data profil berhasil diperbarui!')
            ->with('active_tab', 'profil');
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

        if ($admin->foto_profil && Storage::disk('public')->exists($admin->foto_profil)) {
            Storage::disk('public')->delete($admin->foto_profil);
        }

        $path = $request->file('foto_profil')->store('foto-profil', 'public');
        $admin->update(['foto_profil' => $path]);

        return redirect()->back()
            ->with('success', 'Foto profil berhasil diperbarui!')
            ->with('active_tab', 'profil');
    }

    /**
     * Update password admin.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama'              => ['required'],
            'password_baru'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_baru_confirmation' => ['required'],
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->password_lama, $admin->password)) {
            return redirect()->back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->with('active_tab', 'profil');
        }

        $admin->update(['password' => bcrypt($request->password_baru)]);

        return redirect()->back()
            ->with('success', 'Password berhasil diperbarui!')
            ->with('active_tab', 'profil');
    }
}