<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\Perawatan;
use App\Models\RiwayatPanen;
use App\Models\Sawah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // =====================================================================
        // STATS CARDS — semua dari database, tidak ada data palsu
        // =====================================================================

        $bulanIni   = Carbon::now();
        $bulanLalu  = Carbon::now()->subMonth();

        // Total petani aktif
        $totalPetani      = User::petani()->count();
        $petaniBulanIni   = User::petani()->whereMonth('created_at', $bulanIni->month)
                                          ->whereYear('created_at', $bulanIni->year)
                                          ->count();
        $petaniBulanLalu  = User::petani()->whereMonth('created_at', $bulanLalu->month)
                                          ->whereYear('created_at', $bulanLalu->year)
                                          ->count();

        // Total luas sawah (hektar) — semua sawah yang terdaftar
        $totalLuasSawah     = Sawah::sum('luas');
        $luasSawahBulanIni  = Sawah::whereMonth('created_at', $bulanIni->month)
                                    ->whereYear('created_at', $bulanIni->year)
                                    ->sum('luas');
        $luasSawahBulanLalu = Sawah::whereMonth('created_at', $bulanLalu->month)
                                    ->whereYear('created_at', $bulanLalu->year)
                                    ->sum('luas');

        // Total produksi tahun berjalan (kg → dikonversi ke ton di view)
        $totalProduksiTahunIni  = RiwayatPanen::whereYear('tanggal_panen', $bulanIni->year)->sum('hasil_panen');
        $totalProduksiBulanIni  = RiwayatPanen::whereYear('tanggal_panen', $bulanIni->year)
                                               ->whereMonth('tanggal_panen', $bulanIni->month)
                                               ->sum('hasil_panen');
        $totalProduksiBulanLalu = RiwayatPanen::whereYear('tanggal_panen', $bulanLalu->year)
                                               ->whereMonth('tanggal_panen', $bulanLalu->month)
                                               ->sum('hasil_panen');

        // Diskusi aktif: topik yang dibuat dalam 7 hari terakhir
        $diskusiAktif        = ForumTopic::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $diskusiMingguLalu   = ForumTopic::whereBetween('created_at', [
                                    Carbon::now()->subWeeks(2),
                                    Carbon::now()->subWeek(),
                               ])->count();

        $stats = [
            // Petani
            'totalPetani'       => number_format($totalPetani),
            'petaniBulanIni'    => $petaniBulanIni,
            'perubahanPetani'   => $this->hitungPerubahan($petaniBulanIni, $petaniBulanLalu),
            'trendPetani'       => $petaniBulanIni >= $petaniBulanLalu ? 'naik' : 'turun',

            // Sawah
            'totalSawah'        => number_format($totalLuasSawah, 1),
            'perubahanSawah'    => $this->hitungPerubahan($luasSawahBulanIni, $luasSawahBulanLalu),
            'trendSawah'        => $luasSawahBulanIni >= $luasSawahBulanLalu ? 'naik' : 'turun',

            // Produksi (tampilkan dalam ton, 2 desimal)
            'totalProduksi'     => number_format($totalProduksiTahunIni / 1000, 2),
            'perubahanProduksi' => $this->hitungPerubahan($totalProduksiBulanIni, $totalProduksiBulanLalu),
            'trendProduksi'     => $totalProduksiBulanIni >= $totalProduksiBulanLalu ? 'naik' : 'turun',

            // Diskusi
            'diskusiAktif'      => number_format($diskusiAktif),
            'perubahanDiskusi'  => $this->hitungPerubahan($diskusiAktif, $diskusiMingguLalu),
            'trendDiskusi'      => $diskusiAktif >= $diskusiMingguLalu ? 'naik' : 'turun',
        ];

        // =====================================================================
        // CHART PRODUKSI VS TARGET — 6 bulan terakhir, data asli dari DB
        // Target dihitung dari rata-rata produksi per hektar × luas sawah aktif
        // =====================================================================

        $productionData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);

            // Produksi nyata bulan tersebut (kg → ton)
            $produksiKg = RiwayatPanen::whereYear('tanggal_panen', $bulan->year)
                ->whereMonth('tanggal_panen', $bulan->month)
                ->sum('hasil_panen');

            // Target sederhana: luas sawah aktif × 5 ton/ha (standar nasional)
            $luasSawahAktif = Sawah::where('status', 'aktif')
                ->where('created_at', '<=', $bulan->endOfMonth())
                ->sum('luas');
            $targetTon = $luasSawahAktif * 5; // 5 ton/ha = target nasional rata-rata

            $productionData[] = [
                'bulan'   => $bulan->locale('id')->isoFormat('MMM YY'),
                'produksi' => round($produksiKg / 1000, 2),  // kg → ton
                'target'   => round($targetTon, 2),
            ];
        }

        // =====================================================================
        // CHART PERTUMBUHAN PETANI — 6 bulan terakhir, kumulatif dari DB
        // =====================================================================

        $farmerGrowthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);

            // Total petani yang sudah terdaftar sampai akhir bulan tersebut
            $jumlahPetani = User::petani()
                ->where('created_at', '<=', $bulan->copy()->endOfMonth())
                ->count();

            $farmerGrowthData[] = [
                'bulan'  => $bulan->locale('id')->isoFormat('MMM YY'),
                'petani' => $jumlahPetani,
            ];
        }

        // =====================================================================
        // DISTRIBUSI VARIETAS PADI — dari kolom jenis_padi di tabel sawah
        // =====================================================================

        $rawVariety = Sawah::select('jenis_padi', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('jenis_padi')
            ->groupBy('jenis_padi')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        $totalSawahCount = $rawVariety->sum('jumlah') ?: 1; // hindari division by zero

        $varietyData = $rawVariety->map(function ($item) use ($totalSawahCount) {
            return [
                'name'  => $item->jenis_padi,
                'value' => round(($item->jumlah / $totalSawahCount) * 100, 1),
            ];
        })->toArray();

        // Jika belum ada data sawah sama sekali, tampilkan placeholder kosong
        if (empty($varietyData)) {
            $varietyData = [['name' => 'Belum ada data', 'value' => 100]];
        }

        // =====================================================================
        // RECENT ACTIVITIES — dari data nyata, bukan teks hardcoded
        // Gabungan dari: petani baru, panen terbaru, perawatan terbaru, forum baru
        // =====================================================================

        $recentActivities = collect();

        // 1. Petani baru (5 terbaru)
        $petaniBaru = User::petani()
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn($u) => [
                'type'    => 'registration',
                'icon'    => 'fa-user-plus',
                'color'   => 'text-blue-500',
                'message' => "Petani baru mendaftar: {$u->name}" . ($u->desa ? " dari Desa {$u->desa}" : ''),
                'time'    => $u->created_at,
            ]);

        // 2. Panen terbaru (5 terbaru)
        $panenTerbaru = RiwayatPanen::with('sawah.user')
            ->latest('tanggal_panen')
            ->limit(3)
            ->get()
            ->map(function ($p) {
                $namaPetani = optional(optional($p->sawah)->user)->name ?? 'Petani';
                $namaSawah  = optional($p->sawah)->nama_sawah ?? 'sawah';
                $hasilTon   = number_format($p->hasil_panen / 1000, 1);
                return [
                    'type'    => 'harvest',
                    'icon'    => 'fa-seedling',
                    'color'   => 'text-green-500',
                    'message' => "Laporan panen masuk: {$namaPetani} — {$namaSawah} ({$hasilTon} ton)",
                    'time'    => $p->created_at,
                ];
            });

        // 3. Perawatan terbaru (3 terbaru)
        $perawatanTerbaru = Perawatan::with('sawah.user')
            ->latest()
            ->limit(2)
            ->get()
            ->map(function ($r) {
                $namaPetani = optional(optional($r->sawah)->user)->name ?? 'Petani';
                $jenis      = ucfirst(str_replace('_', ' ', $r->jenis_perawatan));
                return [
                    'type'    => 'maintenance',
                    'icon'    => 'fa-tools',
                    'color'   => 'text-yellow-500',
                    'message' => "Perawatan dicatat: {$namaPetani} — {$jenis} ({$r->nama_kegiatan})",
                    'time'    => $r->created_at,
                ];
            });

        // 4. Forum terbaru (3 terbaru)
        $forumTerbaru = ForumTopic::with('user')
            ->latest()
            ->limit(2)
            ->get()
            ->map(fn($f) => [
                'type'    => 'forum',
                'icon'    => 'fa-comments',
                'color'   => 'text-purple-500',
                'message' => "Diskusi baru: " . \Str::limit($f->title, 60),
                'time'    => $f->created_at,
            ]);

        // Gabungkan semua aktivitas, urutkan dari yang paling baru
        $recentActivities = $petaniBaru
            ->merge($panenTerbaru)
            ->merge($perawatanTerbaru)
            ->merge($forumTerbaru)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->map(function ($item) {
                // Format waktu relatif
                $item['time_label'] = Carbon::parse($item['time'])->diffForHumans();
                return $item;
            })
            ->toArray();

        // =====================================================================
        // STATISTIK TAMBAHAN — untuk section bawah dashboard
        // =====================================================================

        // Sawah per status
        $sawahPerStatus = Sawah::select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        // Petani aktif vs nonaktif
        $petaniAktif    = User::petani()->where('status', 'aktif')->count();
        $petaniNonaktif = User::petani()->where('status', 'nonaktif')->count();

        // Total pendapatan petani tahun ini
        $totalPendapatan = RiwayatPanen::whereYear('tanggal_panen', now()->year)
            ->sum('total_pendapatan');

        // Rata-rata hasil panen per hektar tahun ini
        $rataHasilPerHa = RiwayatPanen::whereYear('tanggal_panen', now()->year)
            ->whereNotNull('hasil_per_hektar')
            ->avg('hasil_per_hektar');

        $extraStats = [
            'sawahAktif'      => $sawahPerStatus['aktif']    ?? 0,
            'sawahPanen'      => $sawahPerStatus['panen']    ?? 0,
            'sawahIstirahat'  => $sawahPerStatus['istirahat'] ?? 0,
            'petaniAktif'     => $petaniAktif,
            'petaniNonaktif'  => $petaniNonaktif,
            'totalPendapatan' => number_format($totalPendapatan, 0, ',', '.'),
            'rataHasilPerHa'  => $rataHasilPerHa ? number_format($rataHasilPerHa, 2) . ' ton/ha' : 'Belum ada data',
        ];

        return view('admin.index', compact(
            'stats',
            'productionData',
            'farmerGrowthData',
            'varietyData',
            'recentActivities',
            'extraStats',
        ));
    }

    // =========================================================================
    // HELPER PRIVATE — hitung persentase perubahan dengan aman
    // =========================================================================

    /**
     * Hitung perubahan persen antara nilai baru dan nilai lama.
     * Mengembalikan string seperti "+12%" atau "-5%" atau "0%".
     */
    private function hitungPerubahan(float|int $baru, float|int $lama): string
    {
        if ($lama == 0) {
            return $baru > 0 ? '+' . $baru . ' baru' : '0';
        }

        $persen = round((($baru - $lama) / $lama) * 100, 1);

        return ($persen >= 0 ? '+' : '') . $persen . '%';
    }
}