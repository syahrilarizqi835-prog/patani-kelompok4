<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Sawah;
use App\Models\PrediksiPanen;
use App\Models\RiwayatPanen;
use App\Models\Cuaca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    // =========================================================
    //  ENCODING
    // =========================================================
    private function encodeTanah(?string $k): float
    {
        return match($k) { 'subur' => 2.0, 'sedang' => 1.0, 'kurang' => 0.0, default => 1.0 };
    }

    private function encodeAir(?string $k): float
    {
        return match($k) { 'baik' => 2.0, 'cukup' => 1.0, 'kurang' => 0.0, default => 1.0 };
    }

    // =========================================================
    //  AMBIL DATA TRAINING
    // =========================================================
    private function getTrainingData(): array
{
    $userId = Auth::id();

    $rows = DB::table('riwayat_panen as rp')
        ->join('sawah as s', 's.id', '=', 'rp.sawah_id')
        ->select([
            's.luas', 's.kondisi_tanah', 's.kondisi_air',
            'rp.tanggal_panen', 'rp.hasil_panen', 'rp.hasil_per_hektar',
        ])
        ->where('s.user_id', $userId) // ← hanya sawah milik petani ini
        ->whereNotNull('rp.hasil_panen')
        ->whereNotNull('s.luas')
        ->where('s.luas', '>', 0)
        ->latest('rp.tanggal_panen')
        ->limit(50)
        ->get();

    $dataset = [];
    foreach ($rows as $row) {
        if (!empty($row->hasil_per_hektar) && $row->hasil_per_hektar > 0) {
            $target = (float) $row->hasil_per_hektar;
        } else {
            $target = ((float) $row->hasil_panen / 1000) / (float) $row->luas;
        }

        if ($target < 0.5 || $target > 15) continue;

        $tgl   = Carbon::parse($row->tanggal_panen);
        $cuaca = DB::table('cuaca')
            ->where('lokasi', 'like', '%Indramayu%')
            ->whereYear('tanggal', $tgl->year)
            ->whereMonth('tanggal', $tgl->month)
            ->selectRaw('AVG(suhu) as s, AVG(curah_hujan) as ch')
            ->first();

        $dataset[] = [
            'luas'        => (float) $row->luas,
            'tanah'       => $this->encodeTanah($row->kondisi_tanah),
            'air'         => $this->encodeAir($row->kondisi_air),
            'suhu'        => $cuaca && $cuaca->s  ? (float) $cuaca->s  : null,
            'curah_hujan' => $cuaca && $cuaca->ch ? (float) $cuaca->ch : null,
            'target'      => $target,
            'tanggal'     => $tgl,
        ];
    }
    return $dataset;
}

    // =========================================================
    //  GAUSSIAN ELIMINATION
    // =========================================================
    private function gaussianElimination(array $A, array $b): array
    {
        $n = count($b);
        $M = [];
        for ($i = 0; $i < $n; $i++) { $M[$i] = $A[$i]; $M[$i][$n] = $b[$i]; }

        for ($col = 0; $col < $n; $col++) {
            $maxVal = abs($M[$col][$col]); $maxRow = $col;
            for ($row = $col + 1; $row < $n; $row++) {
                if (abs($M[$row][$col]) > $maxVal) { $maxVal = abs($M[$row][$col]); $maxRow = $row; }
            }
            [$M[$col], $M[$maxRow]] = [$M[$maxRow], $M[$col]];
            if (abs($M[$col][$col]) < 1e-12) continue;
            for ($row = $col + 1; $row < $n; $row++) {
                $f = $M[$row][$col] / $M[$col][$col];
                for ($k = $col; $k <= $n; $k++) $M[$row][$k] -= $f * $M[$col][$k];
            }
        }

        $x = array_fill(0, $n, 0.0);
        for ($i = $n - 1; $i >= 0; $i--) {
            if (abs($M[$i][$i]) < 1e-12) { $x[$i] = 0.0; continue; }
            $sum = $M[$i][$n];
            for ($j = $i + 1; $j < $n; $j++) $sum -= $M[$i][$j] * $x[$j];
            $x[$i] = $sum / $M[$i][$i];
        }
        return $x;
    }

    // =========================================================
    //  TRAINING — MULTI-LINEAR REGRESSION + WEIGHTED FALLBACK
    // =========================================================
    private function trainModel(array $dataset): array
    {
        $n = count($dataset);

        // ── Cek variasi fitur ────────────────────────────────────
        // Hitung std dev tiap fitur — jika semua nilai sama, fitur tidak informatif
        $featureKeys  = ['luas', 'tanah', 'air'];
        $usableKeys   = [];

        foreach ($featureKeys as $key) {
            $vals = array_column($dataset, $key);
            $mean = array_sum($vals) / count($vals);
            $variance = array_sum(array_map(fn($v) => ($v - $mean) ** 2, $vals)) / count($vals);
            if ($variance > 1e-6) $usableKeys[] = $key; // hanya pakai fitur yang punya variasi
        }

        // Tambah cuaca jika tersedia dan punya variasi
        foreach (['suhu', 'curah_hujan'] as $key) {
            $vals = array_filter(array_column($dataset, $key), fn($v) => !is_null($v));
            if (count($vals) >= 3) {
                $mean = array_sum($vals) / count($vals);
                $variance = array_sum(array_map(fn($v) => ($v - $mean) ** 2, $vals)) / count($vals);
                if ($variance > 1e-6) $usableKeys[] = $key;
            }
        }

        // ── Jika ada fitur usable & data cukup → Linear Regression ─
        if (count($usableKeys) > 0 && $n >= count($usableKeys) + 2) {
            // Filter baris lengkap
            $clean = array_values(array_filter($dataset, function($row) use ($usableKeys) {
                foreach ($usableKeys as $k) { if (is_null($row[$k])) return false; }
                return true;
            }));

            if (count($clean) >= count($usableKeys) + 2) {
                return $this->runOLS($clean, $usableKeys);
            }
        }

        // ── Fallback: Weighted Moving Average (bobot terbaru lebih berat) ─
        return $this->weightedAverageModel($dataset);
    }

    /**
     * Ordinary Least Squares Regression
     */
    private function runOLS(array $data, array $featureKeys): array
    {
        $m = count($data);
        $p = count($featureKeys) + 1; // +1 intercept

        $X = []; $y = [];
        foreach ($data as $i => $row) {
            $X[$i] = [1.0];
            foreach ($featureKeys as $k) $X[$i][] = (float)$row[$k];
            $y[$i] = (float)$row['target'];
        }

        // XᵀX
        $XtX = [];
        for ($j = 0; $j < $p; $j++) {
            $XtX[$j] = array_fill(0, $p, 0.0);
            for ($k = 0; $k < $p; $k++) {
                $s = 0.0;
                for ($i = 0; $i < $m; $i++) $s += $X[$i][$j] * $X[$i][$k];
                $XtX[$j][$k] = $s;
            }
        }

        // Xᵀy
        $Xty = array_fill(0, $p, 0.0);
        for ($j = 0; $j < $p; $j++) {
            $s = 0.0;
            for ($i = 0; $i < $m; $i++) $s += $X[$i][$j] * $y[$i];
            $Xty[$j] = $s;
        }

        $beta = $this->gaussianElimination($XtX, $Xty);

        // R²
        $yMean = array_sum($y) / $m;
        $ssTot = 0.0; $ssRes = 0.0;
        for ($i = 0; $i < $m; $i++) {
            $pred = 0.0;
            for ($j = 0; $j < $p; $j++) $pred += $beta[$j] * $X[$i][$j];
            $ssRes += ($y[$i] - $pred) ** 2;
            $ssTot += ($y[$i] - $yMean) ** 2;
        }
        $r2 = $ssTot > 1e-12 ? max(0.0, 1.0 - ($ssRes / $ssTot)) : 0.0;

        // Named coef
        $featureLabels = ['luas' => 'luas', 'tanah' => 'kondisi_tanah',
                          'air' => 'kondisi_air', 'suhu' => 'suhu', 'curah_hujan' => 'curah_hujan'];
        $coef = ['intercept' => $beta[0]];
        foreach ($featureKeys as $idx => $k) $coef[$featureLabels[$k]] = $beta[$idx + 1];

        return [
            'metode'   => 'Multiple Linear Regression (OLS)',
            'beta'     => $beta,
            'features' => $featureKeys,
            'coef'     => $coef,
            'r2'       => $r2,
            'n'        => $m,
        ];
    }

    /**
     * Weighted Moving Average — digunakan saat semua fitur tidak punya variasi
     * (misal: semua data dari 1 sawah dengan luas sama).
     * Bobot eksponensial: data terbaru dapat bobot lebih besar.
     */
    private function weightedAverageModel(array $dataset): array
    {
        $n      = count($dataset);
        $total  = 0.0;
        $wSum   = 0.0;

        foreach ($dataset as $i => $row) {
            // Bobot eksponensial: data ke-i (0=terlama) → bobot 2^i
            $w      = pow(2, $i);
            $total += $w * $row['target'];
            $wSum  += $w;
        }

        $wavg = $wSum > 0 ? $total / $wSum : 5.5;

        // Hitung MAE sebagai proxy akurasi
        $mae = 0.0;
        foreach ($dataset as $row) $mae += abs($row['target'] - $wavg);
        $mae /= $n;

        // Konversi MAE ke pseudo-R² (semakin kecil MAE relatif terhadap range, semakin baik)
        $targets = array_column($dataset, 'target');
        $range   = max($targets) - min($targets);
        $pseudoR2 = $range > 0 ? max(0.0, 1.0 - ($mae / ($range + 1e-9))) : 0.5;

        return [
            'metode'      => 'Weighted Moving Average (Exponential)',
            'beta'        => [$wavg],
            'features'    => [],
            'coef'        => ['intercept' => $wavg],
            'r2'          => $pseudoR2,
            'weighted_avg'=> $wavg,
            'n'           => $n,
        ];
    }

    /**
     * Prediksi menggunakan model
     */
    private function predictFromModel(array $model, array $input): float
    {
        // Weighted average model
        if (empty($model['features'])) {
            return $model['weighted_avg'] ?? $model['beta'][0];
        }

        // OLS model
        $pred = $model['beta'][0]; // intercept
        foreach ($model['features'] as $idx => $key) {
            $val = $input[$key] ?? 0.0;
            // Mean imputation jika null
            $pred += $model['beta'][$idx + 1] * (float)$val;
        }
        return $pred;
    }

    // =========================================================
    //  CONFIDENCE
    // =========================================================
    private function hitungConfidence(float $r2, bool $hasCuaca, bool $hasTanah, bool $hasAir): int
    {
        $base = match(true) {
            $r2 > 0.8 => 90,
            $r2 > 0.6 => 80,
            $r2 > 0.4 => 70,
            default   => 60,
        };
        if (!$hasCuaca)             $base = min($base, 75);
        if (!$hasTanah || !$hasAir) $base = min($base, 70);
        return $base;
    }

    // =========================================================
    //  CONTROLLER ACTIONS
    // =========================================================
    public function index()
{
    $sawahList = Sawah::where('user_id', Auth::id())->get();

    $prediksiList = PrediksiPanen::whereHas('sawah', fn($q) => $q->where('user_id', Auth::id()))
        ->latest()->get();

    $jumlahDataML = RiwayatPanen::whereHas('sawah', fn($q) => $q->where('user_id', Auth::id()))
        ->whereNotNull('hasil_panen')
        ->count();

    return view('dashboard.prediksi', compact('sawahList', 'prediksiList', 'jumlahDataML'));
}

    public function predict(Request $request)
    {
        $validated = $request->validate(['sawah_id' => 'required|exists:sawah,id']);
        $sawah     = Sawah::where('user_id', Auth::id())->findOrFail($validated['sawah_id']);

        // ── 1. Data training ───────────────────────────────────────
        $dataset = $this->getTrainingData();

        if (count($dataset) < 5) {
            return $this->predictFallback($sawah);
        }

        // ── 2. Cuaca hari ini ──────────────────────────────────────
        $cuaca = Cuaca::where('lokasi', 'like', '%Indramayu%')
            ->whereDate('tanggal', Carbon::today())->latest()->first()
            ?? Cuaca::where('lokasi', 'like', '%Indramayu%')->latest('tanggal')->first();

        $hasCuaca   = $cuaca !== null;
        $hasTanah   = !empty($sawah->kondisi_tanah);
        $hasAir     = !empty($sawah->kondisi_air);
        $suhu       = $hasCuaca ? (float)$cuaca->suhu        : null;
        $curahHujan = $hasCuaca ? (float)$cuaca->curah_hujan : null;
        $kelembaban = $hasCuaca ? (float)$cuaca->kelembaban  : null;

        // ── 3. Training ────────────────────────────────────────────
        $model = $this->trainModel($dataset);

        // ── 4. Input prediksi ──────────────────────────────────────
        $input = [
            'luas'        => (float)$sawah->luas,
            'tanah'       => $hasTanah ? $this->encodeTanah($sawah->kondisi_tanah) : 1.0,
            'air'         => $hasAir   ? $this->encodeAir($sawah->kondisi_air)     : 1.0,
            'suhu'        => $suhu,
            'curah_hujan' => $curahHujan,
        ];

        // Mean imputation untuk null
        foreach (['suhu', 'curah_hujan'] as $key) {
            if (is_null($input[$key])) {
                $vals = array_filter(array_column($dataset, $key), fn($v) => !is_null($v));
                $input[$key] = count($vals) > 0 ? array_sum($vals) / count($vals) : 0.0;
            }
        }

        // ── 5. Prediksi ────────────────────────────────────────────
        $prediksiPerHa = $this->predictFromModel($model, $input);
        $prediksiPerHa = max(0.5, min(15.0, $prediksiPerHa)); // sanity clamp
        $prediksiTotal = round($prediksiPerHa * $sawah->luas, 2);

        // ── 6. Confidence ──────────────────────────────────────────
        $confidence = $this->hitungConfidence($model['r2'], $hasCuaca, $hasTanah, $hasAir);

        // ── 7. Faktor prediksi ─────────────────────────────────────
        $faktorPrediksi = [
            'luas'              => $sawah->luas,
            'kondisi_tanah'     => $sawah->kondisi_tanah ?? '-',
            'kondisi_air'       => $sawah->kondisi_air   ?? '-',
            'suhu'              => $suhu        ?? '-',
            'curah_hujan'       => $curahHujan  ?? '-',
            'kelembaban'        => $kelembaban  ?? '-',
            'metode'            => $model['metode'],
            'jumlah_data_training' => $model['n'],
            'r_squared'         => round($model['r2'], 4),
            'prediksi_per_ha'   => round($prediksiPerHa, 4),
            'koefisien'         => array_map(fn($v) => round($v, 6), $model['coef']),
        ];

        // ── 8. Rekomendasi ─────────────────────────────────────────
        $rekomendasi = $this->generateRekomendasi(
            $sawah, $suhu, $curahHujan, $kelembaban, $hasCuaca, $model['r2'], $model['n'], $model['metode']
        );

        PrediksiPanen::create([
            'sawah_id'         => $sawah->id,
            'tanggal_prediksi' => now(),
            'prediksi_hasil'   => $prediksiTotal,
            'confidence_level' => $confidence,
            'faktor_prediksi'  => json_encode($faktorPrediksi),
            'rekomendasi'      => $rekomendasi,
        ]);

        return redirect()->back()
            ->with('success', 'Prediksi berhasil! Metode: ' . $model['metode'])
            ->with('prediksi', $prediksiTotal);
    }

    // =========================================================
    //  FALLBACK
    // =========================================================
    private function predictFallback(Sawah $sawah)
    {
        $base = 5.5;
        $adj  = match($sawah->kondisi_tanah) { 'subur' => 10, 'kurang' => -15, default => 0 }
              + match($sawah->kondisi_air)    { 'baik'  =>  8, 'kurang' => -12, default => 0 };

        $perHa = $base * (1 + $adj / 100);
        $total = round($perHa * $sawah->luas, 2);

        PrediksiPanen::create([
            'sawah_id'         => $sawah->id,
            'tanggal_prediksi' => now(),
            'prediksi_hasil'   => $total,
            'confidence_level' => 60,
            'faktor_prediksi'  => json_encode([
                'luas' => $sawah->luas, 'kondisi_tanah' => $sawah->kondisi_tanah,
                'kondisi_air' => $sawah->kondisi_air, 'metode' => 'Fallback Agronomi',
                'prediksi_per_ha' => round($perHa, 4),
            ]),
            'rekomendasi' => 'Data historis panen belum cukup (minimal 5 data). Prediksi menggunakan baseline agronomi nasional 5.5 ton/ha. Tambah data riwayat panen untuk mengaktifkan Machine Learning.',
        ]);

        return redirect()->back()
            ->with('success', 'Prediksi dibuat (mode dasar — tambah data riwayat panen untuk ML).')
            ->with('prediksi', $total);
    }

    // =========================================================
    //  REKOMENDASI
    // =========================================================
    private function generateRekomendasi(
        Sawah $sawah, ?float $suhu, ?float $curahHujan, ?float $kelembaban,
        bool $hasCuaca, float $r2, int $nData, string $metode
    ): string {
        $r = [];

        // Tanah
        $r[] = match($sawah->kondisi_tanah) {
            'kurang' => 'Tanah kurang subur: tambahkan pupuk organik minimal 2 ton/ha sebelum tanam',
            'sedang' => 'Kondisi tanah sedang: lakukan pemupukan berimbang NPK sesuai uji tanah',
            default  => 'Tanah subur: pertahankan dengan pemupukan organik rutin',
        };

        // Air
        if ($sawah->kondisi_air === 'kurang')
            $r[] = 'Kondisi air kurang: optimalkan irigasi dan lakukan pengairan bergilir';
        elseif ($sawah->kondisi_air === 'cukup')
            $r[] = 'Kondisi air cukup: pertahankan jadwal pengairan dan cek drainase';

        // Cuaca
        if ($hasCuaca && $suhu !== null) {
            if ($suhu > 32)      $r[] = 'Suhu terlalu tinggi (' . $suhu . '°C): tambah pengairan pagi & sore untuk cegah stres panas';
            elseif ($suhu < 25)  $r[] = 'Suhu rendah (' . $suhu . '°C): hindari genangan agar suhu tanah tetap hangat';
            else                 $r[] = 'Suhu optimal (' . $suhu . '°C): kondisi mendukung pertumbuhan padi';
        }

        if ($hasCuaca && $curahHujan !== null) {
            $bln = round($curahHujan * 30);
            if ($bln > 300)      $r[] = 'Curah hujan tinggi (est. ' . $bln . ' mm/bulan): pastikan drainase lancar, waspadai blast';
            elseif ($bln < 100)  $r[] = 'Curah hujan rendah (est. ' . $bln . ' mm/bulan): optimalkan irigasi teknis';
            else                 $r[] = 'Curah hujan ideal (est. ' . $bln . ' mm/bulan): manfaatkan air hujan optimal';
        }

        if ($hasCuaca && $kelembaban !== null) {
            if ($kelembaban > 85) $r[] = 'Kelembaban tinggi (' . $kelembaban . '%): waspadai serangan jamur, gunakan fungisida preventif';
            elseif ($kelembaban < 60) $r[] = 'Kelembaban rendah (' . $kelembaban . '%): tingkatkan frekuensi pengairan';
        }

        if (!$hasCuaca) $r[] = 'Data cuaca tidak tersedia: perbarui data cuaca Indramayu untuk prediksi lebih akurat';

        $r[] = 'Lakukan pemupukan dasar (Urea, SP-36, KCl) sesuai rekomendasi dosis setempat';
        $r[] = 'Monitor hama penggerek batang, wereng coklat, dan tikus setiap minggu';

        // Info metode ML
        $akurasiLabel = match(true) {
            $r2 > 0.8 => 'tinggi', $r2 > 0.6 => 'baik', $r2 > 0.4 => 'cukup', default => 'sedang'
        };
        $r[] = 'Metode: ' . $metode . ' | Dilatih dari ' . $nData . ' data historis | Akurasi ' . $akurasiLabel . ' (R² = ' . round($r2, 3) . '). Tambah data riwayat panen untuk meningkatkan akurasi';
        $r[] = 'Prediksi dihitung menggunakan model Machine Learning yang dilatih dari data historis panen petani, disesuaikan dengan kondisi lahan dan cuaca wilayah Indramayu';

        return implode('. ', $r) . '.';
    }
}