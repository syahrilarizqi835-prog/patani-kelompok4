<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'      => ['required', 'string', 'max:20'],
            'nik'        => ['nullable', 'string', 'max:20', 'unique:users,nik'],
            'desa'       => ['nullable', 'string', 'max:100'],
            'kecamatan'  => ['nullable', 'string', 'max:100'],
            'alamat'     => ['nullable', 'string', 'max:255'],
            'foto_profil'=> ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'role'       => ['required', 'in:admin,petani'],
        ]);

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_profil')) {
            $fotoPath = $request->file('foto_profil')->store('foto-profil', 'public');
        }

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'nik'         => $validated['nik'] ?? null,
            'desa'        => $validated['desa'] ?? null,
            'kecamatan'   => $validated['kecamatan'] ?? null,
            'alamat'      => $validated['alamat'] ?? null,
            'foto_profil' => $fotoPath,
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'status'      => 'aktif',
        ]);

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/dashboard');
    }
}