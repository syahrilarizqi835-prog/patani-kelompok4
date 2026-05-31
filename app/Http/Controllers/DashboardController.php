<?php

namespace App\Http\Controllers;

use App\Models\Sawah;
use App\Models\RiwayatPanen;
use App\Models\PrediksiPanen;
use App\Models\Cuaca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $sawahList = Sawah::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->get();

        $totalLuasLahan = $sawahList->sum('luas');
        $latestSawah    = $sawahList->first();

        // ── Grafik produksi 8 bulan (data real, tanpa rand) ─────────
        $productionData = [];
        for ($i = 7; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $produksi = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
                ->whereYear('tanggal_panen', $month->year)
                ->whereMonth('tanggal_panen', $month->month)
                ->sum('hasil_panen');

            $productionData[] = [
                'month'    => $month->locale('id')->isoFormat('MMM'),
                'produksi' => (float) $produksi,
            ];
        }

        // ── Grafik riwayat panen per semester ───────────────────────
        $harvestHistory = [];
        for ($i = 5; $i >= 0; $i--) {
            $period   = Carbon::now()->subMonths($i * 4);
            $semester = $period->month <= 6 ? 1 : 2;
            $periodName = $period->year . '-S' . $semester;

            $hasil = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
                ->whereBetween('tanggal_panen', [
                    $period->copy()->startOfYear()->addMonths($semester == 1 ? 0 : 6),
                    $period->copy()->startOfYear()->addMonths($semester == 1 ? 5 : 11)->endOfMonth()
                ])
                ->sum('hasil_panen');

            $harvestHistory[] = [
                'periode' => $periodName,
                'hasil'   => (float) $hasil,
            ];
        }

        // ── Cuaca ────────────────────────────────────────────────────
        $weatherData = Cuaca::where('lokasi', 'like', '%' . ($user->desa ?? 'Indramayu') . '%')
            ->whereDate('tanggal', Carbon::today())
            ->first();

        if (!$weatherData) {
            $weatherData = Cuaca::where('lokasi', 'like', '%Indramayu%')
                ->latest('tanggal')->first();
        }

        if (!$weatherData) {
            $weatherData = (object)[
                'suhu' => 28, 'kelembaban' => 75,
                'curah_hujan' => 12, 'kecepatan_angin' => 8, 'kondisi' => 'Cerah Berawan'
            ];
        }

        // ── Prediksi hasil dari riwayat prediksi terakhir ───────────
        $prediksiTerakhir = PrediksiPanen::whereHas('sawah', fn($q) => $q->where('user_id', $user->id))
            ->latest('tanggal_prediksi')->first();

        $prediksiHasil = $prediksiTerakhir
            ? number_format($prediksiTerakhir->prediksi_hasil, 1) . ' Ton'
            : '— Ton';

        $prediksiPerHa = $prediksiTerakhir && $latestSawah && $latestSawah->luas > 0
            ? number_format($prediksiTerakhir->prediksi_hasil / $latestSawah->luas, 2) . ' Ton/Ha'
            : 'Belum ada prediksi';

        // ── Stats ────────────────────────────────────────────────────
        $stats = [
            'totalLuasLahan' => number_format($totalLuasLahan, 1) . ' Ha',
            'jumlahPetak'    => $sawahList->count() . ' petak sawah aktif',
            'statusTanaman'  => $latestSawah ? ucfirst($latestSawah->fase_tanam) : 'Belum ada',
            'umurTanaman'    => $latestSawah ? ($latestSawah->umur_tanaman ?? '-') . ' hari' : '-',
            'estimasiPanen'  => $latestSawah && $latestSawah->estimasi_panen
                ? Carbon::parse($latestSawah->estimasi_panen)->locale('id')->isoFormat('D MMM YYYY')
                : '-',
            'hariSampaiPanen'=> $latestSawah ? (($latestSawah->hari_sampai_panen ?? '-') . ' hari lagi') : '-',
            'prediksiHasil'  => $prediksiHasil,
            'prediksiPerHa'  => $prediksiPerHa,
        ];

        return view('dashboard.index', compact(
            'sawahList', 'productionData', 'harvestHistory',
            'weatherData', 'stats'
        ));
    }
}