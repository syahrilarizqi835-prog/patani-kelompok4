<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPanen;
use App\Models\Sawah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // =========================================================================
    // INDEX — Halaman laporan dengan filter tahun & bulan
    // =========================================================================

    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', '');

        // ── Stats ringkasan ──────────────────────────────────────────────────
        $queryProduksi = RiwayatPanen::whereYear('tanggal_panen', $tahun);
        if ($bulan) {
            $queryProduksi->whereMonth('tanggal_panen', $bulan);
        }

        $totalProduksiKg = (clone $queryProduksi)->sum('hasil_panen');
        $totalPendapatan = (clone $queryProduksi)->sum('total_pendapatan');
        $totalPanen      = (clone $queryProduksi)->count();
        $rataHasilPerHa  = (clone $queryProduksi)->whereNotNull('hasil_per_hektar')->avg('hasil_per_hektar');

        $totalPetani = User::petani()->count();
        $totalLuas   = Sawah::sum('luas');

        $stats = [
            'totalProduksiKg'  => number_format($totalProduksiKg, 0, ',', '.'),
            'totalProduksiTon' => number_format($totalProduksiKg / 1000, 2, ',', '.'),
            'totalPendapatan'  => number_format($totalPendapatan, 0, ',', '.'),
            'totalPanen'       => $totalPanen,
            'rataHasilPerHa'   => $rataHasilPerHa ? number_format($rataHasilPerHa, 2) . ' ton/ha' : '-',
            'totalPetani'      => $totalPetani,
            'totalLuas'        => number_format($totalLuas, 2, ',', '.'),
        ];

        // ── Grafik produksi per bulan ────────────────────────────────────────
        $produksiBulanan = [];
        for ($m = 1; $m <= 12; $m++) {
            $produksi   = RiwayatPanen::whereYear('tanggal_panen', $tahun)->whereMonth('tanggal_panen', $m)->sum('hasil_panen');
            $pendapatan = RiwayatPanen::whereYear('tanggal_panen', $tahun)->whereMonth('tanggal_panen', $m)->sum('total_pendapatan');

            $produksiBulanan[] = [
                'bulan'      => Carbon::create($tahun, $m)->locale('id')->isoFormat('MMM'),
                'produksi'   => round($produksi / 1000, 2),
                'pendapatan' => round($pendapatan / 1000000, 2),
            ];
        }

        // ── Distribusi kualitas panen ─────────────────────────────────────────
        $kualitasData = (clone $queryProduksi)
            ->select('kualitas', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(hasil_panen) as total_kg'))
            ->groupBy('kualitas')
            ->get()
            ->map(fn($k) => [
                'kualitas'  => ucfirst(str_replace('_', ' ', $k->kualitas)),
                'jumlah'    => $k->jumlah,
                'total_ton' => round($k->total_kg / 1000, 2),
            ]);

        // ── Top 10 petani berdasarkan produksi ───────────────────────────────
        $topPetani = RiwayatPanen::with('sawah.user')
            ->whereYear('tanggal_panen', $tahun)
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_panen', $bulan))
            ->select('sawah_id',
                DB::raw('SUM(hasil_panen) as total_kg'),
                DB::raw('SUM(total_pendapatan) as total_pendapatan'),
                DB::raw('COUNT(*) as jumlah_panen'))
            ->groupBy('sawah_id')
            ->orderByDesc('total_kg')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'nama_petani'      => optional(optional($r->sawah)->user)->name ?? '-',
                'nama_sawah'       => optional($r->sawah)->nama_sawah ?? '-',
                'luas'             => optional($r->sawah)->luas ?? 0,
                'jumlah_panen'     => $r->jumlah_panen,
                'total_ton'        => round($r->total_kg / 1000, 2),
                'total_pendapatan' => number_format($r->total_pendapatan, 0, ',', '.'),
            ]);

        // ── Daftar tahun tersedia untuk dropdown filter ───────────────────────
        $tahunTersedia = RiwayatPanen::selectRaw('YEAR(tanggal_panen) as tahun')
            ->distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();

        if (!in_array(now()->year, $tahunTersedia)) {
            array_unshift($tahunTersedia, now()->year);
        }

        return view('admin.laporan', compact(
            'stats', 'produksiBulanan', 'kualitasData',
            'topPetani', 'tahunTersedia', 'tahun', 'bulan',
        ));
    }

    // =========================================================================
    // EXPORT CSV — bisa dibuka langsung di Excel, TANPA package eksternal
    // =========================================================================

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', '');

        $periode = $bulan
            ? Carbon::create($tahun, $bulan)->locale('id')->isoFormat('MMMM Y')
            : 'Tahun ' . $tahun;

        $dataPanen = RiwayatPanen::with('sawah.user')
            ->whereYear('tanggal_panen', $tahun)
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_panen', $bulan))
            ->orderByDesc('tanggal_panen')
            ->get();

        $namaFile = 'laporan-panen-' . $tahun . ($bulan ? '-bulan' . str_pad($bulan, 2, '0', STR_PAD_LEFT) : '') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($dataPanen, $periode) {
            $handle = fopen('php://output', 'w');

            // BOM agar Excel baca UTF-8 dengan benar (huruf Indonesia tidak rusak)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header laporan
            fputcsv($handle, ['LAPORAN PRODUKSI PANEN - PATANI']);
            fputcsv($handle, ['Periode: ' . $periode]);
            fputcsv($handle, ['Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB']);
            fputcsv($handle, []); // baris kosong pemisah

            // Header kolom tabel
            fputcsv($handle, [
                'No', 'Tanggal Panen', 'Nama Petani', 'Desa', 'Nama Sawah',
                'Luas (Ha)', 'Hasil Panen (Kg)', 'Hasil Panen (Ton)',
                'Hasil/Ha (Ton)', 'Kualitas', 'Harga Jual (Rp/Kg)', 'Total Pendapatan (Rp)',
            ]);

            // Baris data per record panen
            $totalKg         = 0;
            $totalPendapatan = 0;

            foreach ($dataPanen as $i => $panen) {
                $kg          = $panen->hasil_panen ?? 0;
                $pendapatan  = $panen->total_pendapatan ?? 0;
                $totalKg    += $kg;
                $totalPendapatan += $pendapatan;

                fputcsv($handle, [
                    $i + 1,
                    Carbon::parse($panen->tanggal_panen)->format('d/m/Y'),
                    optional(optional($panen->sawah)->user)->name ?? '-',
                    optional(optional($panen->sawah)->user)->desa ?? '-',
                    optional($panen->sawah)->nama_sawah           ?? '-',
                    optional($panen->sawah)->luas                 ?? 0,
                    $kg,
                    round($kg / 1000, 2),
                    $panen->hasil_per_hektar ? round($panen->hasil_per_hektar, 2) : '-',
                    ucfirst(str_replace('_', ' ', $panen->kualitas ?? '')),
                    $panen->harga_jual ?? '-',
                    $pendapatan,
                ]);
            }

            // Baris total di bawah tabel
            fputcsv($handle, []);
            fputcsv($handle, [
                '', '', '', '', 'TOTAL', '',
                $totalKg,
                round($totalKg / 1000, 2),
                '', '', '',
                $totalPendapatan,
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =========================================================================
    // EXPORT PDF — render halaman HTML khusus lalu user Ctrl+P / Print to PDF
    // TANPA package dompdf, bekerja di semua browser modern
    // =========================================================================

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', '');

        $periode = $bulan
            ? Carbon::create($tahun, $bulan)->locale('id')->isoFormat('MMMM Y')
            : 'Tahun ' . $tahun;

        $queryProduksi = RiwayatPanen::whereYear('tanggal_panen', $tahun);
        if ($bulan) {
            $queryProduksi->whereMonth('tanggal_panen', $bulan);
        }

        $totalProduksiKg = (clone $queryProduksi)->sum('hasil_panen');
        $totalPendapatan = (clone $queryProduksi)->sum('total_pendapatan');
        $totalPetani     = User::petani()->count();
        $totalLuas       = Sawah::sum('luas');

        $dataPanen = RiwayatPanen::with('sawah.user')
            ->whereYear('tanggal_panen', $tahun)
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_panen', $bulan))
            ->orderByDesc('tanggal_panen')
            ->get()
            ->map(fn($p) => [
                'tanggal'          => Carbon::parse($p->tanggal_panen)->format('d/m/Y'),
                'nama_petani'      => optional(optional($p->sawah)->user)->name ?? '-',
                'desa'             => optional(optional($p->sawah)->user)->desa ?? '-',
                'nama_sawah'       => optional($p->sawah)->nama_sawah ?? '-',
                'luas'             => optional($p->sawah)->luas ?? 0,
                'hasil_kg'         => number_format($p->hasil_panen, 0, ',', '.'),
                'hasil_ton'        => number_format($p->hasil_panen / 1000, 2, ',', '.'),
                'kualitas'         => ucfirst(str_replace('_', ' ', $p->kualitas ?? '')),
                'total_pendapatan' => $p->total_pendapatan
                    ? 'Rp ' . number_format($p->total_pendapatan, 0, ',', '.')
                    : '-',
            ]);

        // Render view khusus print — browser otomatis buka dialog print
        return view('admin.laporan-pdf', [
            'dataPanen'        => $dataPanen,
            'totalProduksiKg'  => $totalProduksiKg,
            'totalPendapatan'  => $totalPendapatan,
            'totalPetani'      => $totalPetani,
            'totalLuas'        => $totalLuas,
            'periodeLabel'     => $periode,
            'tahun'            => $tahun,
            'bulan'            => $bulan,
        ]);
    }
}