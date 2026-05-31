<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Sawah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SawahController extends Controller
{
    public function index()
    {
        $sawahList = Sawah::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.sawah', compact('sawahList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sawah'    => 'required|string|max:255',
            'lokasi'        => 'required|string|max:255',
            'desa'          => 'required|string|max:255',
            'kecamatan'     => 'required|string|max:255',
            'luas'          => 'required|numeric|min:0',
            'jenis_padi'    => 'required|string|max:255',
            'tanggal_tanam' => 'nullable|date',
            'kondisi_tanah' => 'required|in:subur,sedang,kurang',
            'kondisi_air'   => 'required|in:baik,cukup,kurang',
            'catatan'       => 'nullable|string',
            'foto_lahan'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['user_id']    = Auth::id();
        $validated['status']     = 'aktif';
        $validated['fase_tanam'] = 'vegetatif';

        if (!empty($validated['tanggal_tanam'])) {
            $validated['estimasi_panen'] = Carbon::parse($validated['tanggal_tanam'])->addMonths(4);
        }

        // Upload foto
        if ($request->hasFile('foto_lahan')) {
            $validated['foto_lahan'] = $request->file('foto_lahan')
                ->store('foto_sawah', 'public');
        }

        Sawah::create($validated);

        return redirect()->back()->with('success', 'Data sawah berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $sawah = Sawah::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'nama_sawah'    => 'required|string|max:255',
            'lokasi'        => 'required|string|max:255',
            'desa'          => 'required|string|max:255',
            'kecamatan'     => 'required|string|max:255',
            'luas'          => 'required|numeric|min:0',
            'jenis_padi'    => 'required|string|max:255',
            'tanggal_tanam' => 'nullable|date',
            'kondisi_tanah' => 'required|in:subur,sedang,kurang',
            'kondisi_air'   => 'required|in:baik,cukup,kurang',
            'fase_tanam'    => 'required|in:persiapan,vegetatif,generatif,pematangan,panen',
            'status'        => 'required|in:aktif,panen,istirahat',
            'catatan'       => 'nullable|string',
            'foto_lahan'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if (!empty($validated['tanggal_tanam']) && !$sawah->estimasi_panen) {
            $validated['estimasi_panen'] = Carbon::parse($validated['tanggal_tanam'])->addMonths(4);
        }

        // Upload foto baru → hapus foto lama
        if ($request->hasFile('foto_lahan')) {
            if ($sawah->foto_lahan) {
                Storage::disk('public')->delete($sawah->foto_lahan);
            }
            $validated['foto_lahan'] = $request->file('foto_lahan')
                ->store('foto_sawah', 'public');
        } else {
            unset($validated['foto_lahan']); // jangan overwrite jika tidak ada foto baru
        }

        $sawah->update($validated);

        return redirect()->back()->with('success', 'Data sawah berhasil diupdate!');
    }

    public function destroy($id)
    {
        $sawah = Sawah::where('user_id', Auth::id())->findOrFail($id);

        // Hapus foto jika ada
        if ($sawah->foto_lahan) {
            Storage::disk('public')->delete($sawah->foto_lahan);
        }

        $sawah->delete();

        return redirect()->back()->with('success', 'Data sawah berhasil dihapus!');
    }
}