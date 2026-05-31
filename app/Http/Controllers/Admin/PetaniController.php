<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetaniController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'petani')->with('sawah');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('desa', 'like', "%{$search}%");
            });
        }
        
        $petaniList = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.petani', compact('petaniList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nik' => 'nullable|string|unique:users|max:16',
            'phone' => 'nullable|string|max:20',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'password' => 'required|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'petani';
        $validated['status'] = 'aktif';

        User::create($validated);

        return redirect()->back()->with('success', 'Petani berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $petani = User::where('role', 'petani')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nik' => 'nullable|string|unique:users,nik,' . $id . '|max:16',
            'phone' => 'nullable|string|max:20',
            'desa' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $petani->update($validated);

        return redirect()->back()->with('success', 'Data petani berhasil diupdate!');
    }

    public function destroy($id)
    {
        $petani = User::where('role', 'petani')->findOrFail($id);
        $petani->delete();

        return redirect()->back()->with('success', 'Petani berhasil dihapus!');
    }

    public function show($id)
    {
        $petani = User::where('role', 'petani')
            ->with(['sawah', 'sawah.riwayatPanen'])
            ->findOrFail($id);
        
        return view('admin.petani-detail', compact('petani'));
    }
}
