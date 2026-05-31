<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPanen;
use App\Models\Sawah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $riwayatList = RiwayatPanen::whereHas('sawah', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('sawah')->latest('tanggal_panen')->get();

        $sawahList = Sawah::where('user_id', $user->id)->get();

        // ── Statistik real ──────────────────────────────────────────
        $tahunIni = Carbon::now()->year;

        $totalPanenKg = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
            ->whereYear('tanggal_panen', $tahunIni)
            ->sum('hasil_panen');

        $avgPerHa = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
            ->whereNotNull('hasil_per_hektar')
            ->where('hasil_per_hektar', '>', 0)
            ->avg('hasil_per_hektar');

        $totalPendapatan = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
            ->whereYear('tanggal_panen', $tahunIni)
            ->sum('total_pendapatan');

        // ── Status data ML ───────────────────────────────────────────
        $jumlahDataML = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
            ->whereNotNull('hasil_panen')
            ->count();

        $mlReady    = $jumlahDataML >= 5;
        $mlProgress = min(100, ($jumlahDataML / 5) * 100);

        // ── Data grafik (12 bulan terakhir) ─────────────────────────
        $chartLabels = [];
        $chartData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $chartLabels[] = $m->locale('id')->isoFormat('MMM YY');
            $chartData[]   = (float) RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
                ->whereYear('tanggal_panen', $m->year)
                ->whereMonth('tanggal_panen', $m->month)
                ->sum('hasil_panen');
        }

        return view('dashboard.riwayat', compact(
            'riwayatList',
            'sawahList',
            'totalPanenKg',
            'avgPerHa',
            'totalPendapatan',
            'jumlahDataML',
            'mlReady',
            'mlProgress',
            'chartLabels',
            'chartData'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sawah_id'       => 'required|exists:sawah,id',
            'tanggal_panen'  => 'required|date',
            'hasil_panen'    => 'required|numeric|min:1',
            'kualitas' => 'required|in:gabah_basah,gabah_kering,beras',
            'harga_jual'     => 'nullable|numeric|min:0',
            'catatan'        => 'nullable|string|max:500',
        ]);

        // Pastikan sawah milik user ini
        $sawah = Sawah::where('user_id', Auth::id())->findOrFail($validated['sawah_id']);

        // Hitung hasil per hektar (ton/ha)
        $hasilPerHektar = ($validated['hasil_panen'] / 1000) / $sawah->luas;

        // Hitung total pendapatan
        $totalPendapatan = null;
        if (!empty($validated['harga_jual'])) {
            $totalPendapatan = $validated['hasil_panen'] * $validated['harga_jual'];
        }

        RiwayatPanen::create([
            'sawah_id'        => $sawah->id,
            'tanggal_panen'   => $validated['tanggal_panen'],
            'hasil_panen'     => $validated['hasil_panen'],
            'hasil_per_hektar'=> round($hasilPerHektar, 4),
            'kualitas'        => $validated['kualitas'],
            'harga_jual'      => $validated['harga_jual'] ?? null,
            'total_pendapatan'=> $totalPendapatan,
            'catatan'         => $validated['catatan'] ?? null,
        ]);

        // Cek apakah ML sudah aktif setelah tambah data ini
        $jumlah = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', Auth::id()))->count();
        $mlPesanTambahan = '';
        if ($jumlah >= 5) {
            $mlPesanTambahan = ' 🤖 Model Machine Learning sekarang aktif dengan ' . $jumlah . ' data training!';
        } else {
            $sisa = 5 - $jumlah;
            $mlPesanTambahan = ' Tambah ' . $sisa . ' data lagi untuk mengaktifkan Machine Learning.';
        }

        return redirect()->back()->with('success', 'Data riwayat panen berhasil disimpan!' . $mlPesanTambahan);
    }

    public function destroy($id)
    {
        $riwayat = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        $riwayat->delete();

        return redirect()->back()->with('success', 'Data riwayat panen berhasil dihapus.');
    }
}