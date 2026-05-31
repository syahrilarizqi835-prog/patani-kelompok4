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
            'sawah_id' => 'required|exists:sawah,id',
            'tanggal' => 'required|date',
            'jenis_perawatan' => 'required|in:pemupukan,penyemprotan,pengairan,penyiangan,lainnya',
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'bahan_digunakan' => 'nullable|string|max:255',
            'jumlah' => 'nullable|numeric',
            'satuan' => 'nullable|string|max:50',
            'biaya' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);
        
        Perawatan::create($validated);
        
        return redirect()->back()->with('success', 'Data perawatan berhasil ditambahkan!');
    }
}
