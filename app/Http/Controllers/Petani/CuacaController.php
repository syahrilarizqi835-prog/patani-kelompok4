<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Cuaca;
use App\Models\Sawah;

class CuacaController extends Controller
{
    /**
     * Ambil data cuaca dari OpenWeatherMap API
     */
    private function fetchWeatherData()
    {
        try {
            $city   = config('services.openweathermap.city', 'Indramayu');
            $apiKey = config('services.openweathermap.api_key');

            if (empty($apiKey)) {
                throw new \Exception('API Key OpenWeatherMap tidak ditemukan.');
            }

            // ===============================
            // CUACA HARI INI (REAL-TIME)
            // ===============================
            $response = Http::timeout(15)->get("https://api.openweathermap.org/data/2.5/weather", [
                'q'     => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang'  => 'id'
            ]);

            $current = $response->json();

            // Simpan ke database jika berhasil
            if ($response->successful()) {
                Cuaca::updateOrCreate(
                    [
                        'lokasi'  => $city,
                        'tanggal' => now()->toDateString()
                    ],
                    [
                        'suhu'             => $current['main']['temp'] ?? null,
                        'kelembaban'       => $current['main']['humidity'] ?? null,
                        'curah_hujan'      => $current['rain']['1h'] ?? 0,
                        'kecepatan_angin'  => $current['wind']['speed'] ?? null,
                        'kondisi'          => $current['weather'][0]['description'] ?? null,
                    ]
                );
            }

            // ===============================
            // FORECAST 5 HARI
            // ===============================
            $forecastResponse = Http::timeout(15)->get("https://api.openweathermap.org/data/2.5/forecast", [
                'q'     => $city,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang'  => 'id'
            ]);
            
            $forecast = $forecastResponse->json();

            return compact('current', 'forecast');
        } catch (\Exception $e) {
            \Log::error("Gagal mengambil data cuaca: " . $e->getMessage());
            return [
                'current'  => null,
                'forecast' => null,
                'error'    => 'Gagal mengambil data cuaca dari server OpenWeatherMap.'
            ];
        }
    }

    public function index()
    {
        $weatherData = $this->fetchWeatherData();
        $current  = $weatherData['current'];
        $forecast = $weatherData['forecast'];

        // ===============================
        // DATA SAWAH PETANI
        // ===============================
        $sawah = Sawah::where('user_id', auth()->id())->get();

        return view('dashboard.cuaca', compact('current', 'forecast', 'sawah'));
    }

    /**
     * AJAX endpoint untuk auto-refresh cuaca realtime
     */
    public function refresh()
    {
        $weatherData = $this->fetchWeatherData();

        return response()->json([
            'current'  => $weatherData['current'],
            'forecast' => $weatherData['forecast'],
            'updated_at' => now()->format('H:i:s'),
        ]);
    }
}
