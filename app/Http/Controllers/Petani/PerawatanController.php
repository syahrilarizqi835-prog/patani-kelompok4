<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Sawah;
use App\Models\Perawatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerawatanController extends Controller
{
    public function index()
    {
        $sawahList = Sawah::where('user_id', Auth::id())->get();
        $perawatanList = Perawatan::whereHas('sawah', function($query) {
            $query->where('user_id', Auth::id());
        })->with('sawah')->latest('tanggal')->get();
        
        return view('dashboard.perawatan', compact('sawahList', 'perawatanList'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sawah_id'        => 'required|exists:sawah,id',
            'tanggal'         => 'required|date',
            'jenis_perawatan' => 'required|in:pemupukan,penyemprotan,pengairan,penyiangan,lainnya',
            'nama_kegiatan'   => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'bahan_digunakan' => 'nullable|string|max:255',
            'jumlah'          => 'required|numeric|min:0',
            'satuan'          => 'nullable|string|max:50',
            'biaya'           => 'required|numeric|min:0',
            'catatan'         => 'nullable|string',
        ], [
            'sawah_id.required'        => 'Sawah wajib dipilih.',
            'sawah_id.exists'          => 'Sawah tidak valid.',
            'tanggal.required'         => 'Tanggal wajib diisi.',
            'jenis_perawatan.required' => 'Jenis perawatan wajib dipilih.',
            'nama_kegiatan.required'   => 'Nama kegiatan wajib diisi.',
            'jumlah.required'          => 'Jumlah wajib diisi.',
            'jumlah.numeric'           => 'Jumlah harus berupa angka.',
            'biaya.required'           => 'Biaya wajib diisi.',
            'biaya.numeric'            => 'Biaya harus berupa angka.',
        ]);

        // Proteksi IDOR: Pastikan sawah milik user yang sedang login
        $sawah = Sawah::where('user_id', Auth::id())->findOrFail($validated['sawah_id']);
        
        Perawatan::create($validated);
        
        return redirect()->back()->with('success', 'Data perawatan berhasil ditambahkan!');
    }
}
