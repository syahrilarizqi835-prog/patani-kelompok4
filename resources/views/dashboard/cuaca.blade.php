@extends('layouts.dashboard')

@section('page-title', 'Informasi Cuaca')

@section('content')
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Informasi Cuaca</h1>
            <p class="text-gray-600">Prakiraan cuaca untuk perencanaan pertanian</p>
        </div>
        <div class="flex items-center gap-3 mt-3 md:mt-0">
            <span id="weather-status" class="flex items-center gap-2 text-sm text-green-600">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Live — Realtime
            </span>
            <span id="last-updated" class="text-xs text-gray-400"></span>
            <button onclick="refreshWeather()" id="refresh-btn" class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition flex items-center gap-1">
                <i class="fas fa-sync-alt" id="refresh-icon"></i>
                Refresh
            </button>
        </div>
    </div>


    {{-- ================= CUACA HARI INI ================= --}}
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            {{-- KIRI --}}
            <div>
                <p class="text-lg opacity-90">
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </p>

                <h2 class="text-3xl md:text-4xl font-bold mt-2">
                    {{ auth()->user()->desa ?? 'Indramayu' }}, Jawa Barat
                </h2>

                <div class="flex items-center gap-4 mt-6">
                    <i class="fas fa-cloud-sun text-5xl md:text-6xl" id="weather-icon"></i>
                    <div>
                        <div class="text-5xl md:text-6xl font-bold" id="current-temp">
                            {{ round($current['main']['temp'] ?? 0) }}°C
                        </div>
                        <p class="text-xl opacity-90 capitalize" id="current-desc">
                            {{ $current['weather'][0]['description'] ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- KANAN --}}
            <div class="text-right space-y-3">
                <div class="flex items-center gap-3 justify-end">
                    <span>Kelembaban</span>
                    <span class="font-bold text-xl" id="current-humidity">
                        {{ $current['main']['humidity'] ?? '-' }}%
                    </span>
                </div>

                <div class="flex items-center gap-3 justify-end">
                    <span>Curah Hujan</span>
                    <span class="font-bold text-xl" id="current-rain">
                        {{ $current['rain']['1h'] ?? 0 }} mm
                    </span>
                </div>

                <div class="flex items-center gap-3 justify-end">
                    <span>Kec. Angin</span>
                    <span class="font-bold text-xl" id="current-wind">
                        {{ $current['wind']['speed'] ?? 0 }} km/h
                    </span>
                </div>
            </div>

        </div>
    </div>


    {{-- ================= PRAKIRAAN 7 HARI ================= --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Prakiraan Cuaca</h3>

        @php
            $daily = collect($forecast['list'] ?? [])
                ->groupBy(function ($item) {
                    return \Carbon\Carbon::parse($item['dt_txt'])->format('Y-m-d');
                })
                ->take(7);
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach($daily as $date => $items)
                @php
                    $day       = \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd');
                    $temp      = round($items[0]['main']['temp'] ?? 0);
                    $condition = $items[0]['weather'][0]['description'] ?? '-';
                    $humidity  = $items[0]['main']['humidity'] ?? 0;
                @endphp

                <div class="bg-gray-50 rounded-lg p-4 text-center hover:bg-blue-50 transition">
                    <p class="font-semibold text-gray-700 mb-2">{{ $day }}</p>
                    <p class="text-sm text-gray-600 capitalize mb-2">{{ $condition }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $temp }}°C</p>
                    <p class="text-sm text-gray-500 mt-1">💧 {{ $humidity }}%</p>
                </div>
            @endforeach
        </div>
    </div>


    {{-- ================= REKOMENDASI ================= --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">
            Rekomendasi Berdasarkan Cuaca
        </h3>

        <div class="space-y-3">

            {{-- Rekomendasi Umum --}}
            @if(($current['wind']['speed'] ?? 0) < 10)
                <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-green-800">Cuaca Baik untuk Penyemprotan</p>
                        <p class="text-sm text-green-700">
                            Angin rendah, aman untuk aktivitas pertanian.
                        </p>
                    </div>
                </div>
            @endif

            @if(($current['main']['humidity'] ?? 0) > 80)
                <div class="flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                    <div>
                        <p class="font-semibold text-yellow-800">Kelembaban Tinggi</p>
                        <p class="text-sm text-yellow-700">
                            Perhatikan potensi jamur atau penyakit tanaman.
                        </p>
                    </div>
                </div>
            @endif

            {{-- ================= REKOMENDASI ================= --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">
            
            Rekomendasi Berdasarkan Cuaca
        </h3>

        @php
            $suhu       = $current['main']['temp']     ?? 0;
            $kelembaban = $current['main']['humidity'] ?? 0;
            $angin      = $current['wind']['speed']    ?? 0;
            $hujan      = $current['rain']['1h']       ?? 0;
            $kondisi    = strtolower($current['weather'][0]['description'] ?? '');
            $rekomList  = [];

            // Suhu
            if ($suhu >= 25 && $suhu <= 32) {
                $rekomList[] = ['icon' => 'fas fa-thermometer-half', 'warna' => 'green',
                    'judul' => 'Suhu Optimal untuk Padi (' . round($suhu) . '°C)',
                    'isi'   => 'Suhu berada dalam rentang ideal 25–32°C. Kondisi sangat mendukung pertumbuhan dan pembungaan padi secara optimal.'];
            } elseif ($suhu > 32) {
                $rekomList[] = ['icon' => 'fas fa-temperature-high', 'warna' => 'red',
                    'judul' => 'Suhu Terlalu Tinggi (' . round($suhu) . '°C)',
                    'isi'   => 'Suhu melebihi 32°C dapat menyebabkan stres panas pada tanaman padi. Lakukan pengairan tambahan pada pagi dan sore hari untuk menurunkan suhu tanah.'];
            } else {
                $rekomList[] = ['icon' => 'fas fa-thermometer-quarter', 'warna' => 'blue',
                    'judul' => 'Suhu Di Bawah Optimal (' . round($suhu) . '°C)',
                    'isi'   => 'Suhu di bawah 25°C dapat memperlambat pertumbuhan padi. Hindari genangan air terlalu dalam agar suhu tanah tetap hangat.'];
            }

            // Kelembaban
            if ($kelembaban > 85) {
                $rekomList[] = ['icon' => 'fas fa-exclamation-triangle', 'warna' => 'yellow',
                    'judul' => 'Kelembaban Sangat Tinggi (' . $kelembaban . '%)',
                    'isi'   => 'Kelembaban di atas 85% meningkatkan risiko serangan jamur blast dan hawar daun bakteri. Pastikan drainase sawah lancar dan pertimbangkan penyemprotan fungisida preventif.'];
            } elseif ($kelembaban >= 60) {
                $rekomList[] = ['icon' => 'fas fa-tint', 'warna' => 'green',
                    'judul' => 'Kelembaban Normal (' . $kelembaban . '%)',
                    'isi'   => 'Tingkat kelembaban dalam rentang ideal 60–85% untuk padi. Pertahankan sistem drainase agar tidak berlebihan.'];
            } else {
                $rekomList[] = ['icon' => 'fas fa-tint-slash', 'warna' => 'orange',
                    'judul' => 'Kelembaban Rendah (' . $kelembaban . '%)',
                    'isi'   => 'Kelembaban di bawah 60% mengindikasikan kondisi kering. Tingkatkan frekuensi pengairan dan pertimbangkan mulsa jerami untuk menjaga kelembaban tanah.'];
            }

            // Angin
            if ($angin < 10) {
                $rekomList[] = ['icon' => 'fas fa-wind', 'warna' => 'green',
                    'judul' => 'Kondisi Angin Aman (' . round($angin) . ' km/h)',
                    'isi'   => 'Kecepatan angin rendah — kondisi ideal untuk penyemprotan pestisida, pupuk daun, atau fungisida tanpa risiko drift ke lahan lain.'];
            } elseif ($angin < 20) {
                $rekomList[] = ['icon' => 'fas fa-wind', 'warna' => 'yellow',
                    'judul' => 'Angin Sedang — Waspada Penyemprotan (' . round($angin) . ' km/h)',
                    'isi'   => 'Kecepatan angin sedang. Hindari penyemprotan pestisida karena berisiko terbawa angin. Lakukan di pagi hari saat angin lebih tenang.'];
            } else {
                $rekomList[] = ['icon' => 'fas fa-wind', 'warna' => 'red',
                    'judul' => 'Angin Kencang — Tunda Penyemprotan (' . round($angin) . ' km/h)',
                    'isi'   => 'Angin kencang di atas 20 km/h. Tunda semua kegiatan penyemprotan. Waspadai kerusakan fisik tanaman padi terutama pada fase generatif.'];
            }

            // Hujan
            if ($hujan > 10) {
                $rekomList[] = ['icon' => 'fas fa-cloud-showers-heavy', 'warna' => 'blue',
                    'judul' => 'Hujan Lebat (' . $hujan . ' mm/jam)',
                    'isi'   => 'Curah hujan tinggi saat ini. Pastikan saluran drainase tidak tersumbat untuk mencegah banjir di lahan. Tunda pemupukan karena pupuk akan tercuci oleh air hujan.'];
            } elseif ($hujan > 0) {
                $rekomList[] = ['icon' => 'fas fa-cloud-rain', 'warna' => 'blue',
                    'judul' => 'Hujan Ringan (' . $hujan . ' mm/jam)',
                    'isi'   => 'Hujan ringan membantu kebutuhan air tanaman. Kurangi frekuensi irigasi hari ini. Tunda penyemprotan hingga hujan berhenti minimal 2 jam.'];
            } else {
                $rekomList[] = ['icon' => 'fas fa-sun', 'warna' => 'yellow',
                    'judul' => 'Tidak Ada Hujan Saat Ini',
                    'isi'   => 'Tidak ada curah hujan. Pastikan jadwal irigasi berjalan sesuai kebutuhan tanaman, terutama pada fase vegetatif dan generatif.'];
            }

            // Per Sawah
            foreach ($sawah as $item) {
                if ($suhu > 32 && $item->kondisi_air === 'kurang') {
                    $rekomList[] = ['icon' => 'fas fa-water', 'warna' => 'red',
                        'judul' => '⚠️ ' . $item->nama_sawah . ' — Krisis Air + Panas',
                        'isi'   => 'Kombinasi suhu ' . round($suhu) . '°C dan kondisi air kurang sangat berbahaya. Segera lakukan irigasi tambahan untuk mencegah kegagalan panen.'];
                }
                if ($hujan > 5 && $item->fase_tanam === 'generatif') {
                    $rekomList[] = ['icon' => 'fas fa-seedling', 'warna' => 'yellow',
                        'judul' => $item->nama_sawah . ' — Fase Generatif saat Hujan',
                        'isi'   => 'Hujan saat fase generatif dapat mengganggu proses penyerbukan dan menurunkan kualitas bulir. Monitor kondisi tanaman dan pastikan drainase lancar.'];
                }
                if ($kelembaban > 85 && $item->kondisi_tanah === 'subur') {
                    $rekomList[] = ['icon' => 'fas fa-bug', 'warna' => 'yellow',
                        'judul' => $item->nama_sawah . ' — Waspadai Jamur Blast',
                        'isi'   => 'Tanah subur dengan kelembaban ' . $kelembaban . '% sangat kondusif untuk pertumbuhan jamur blast. Pertimbangkan aplikasi fungisida preventif.'];
                }
                if ($hujan > 0 && $item->fase_tanam === 'panen') {
                    $rekomList[] = ['icon' => 'fas fa-exclamation-circle', 'warna' => 'red',
                        'judul' => $item->nama_sawah . ' — Tunda Panen saat Hujan',
                        'isi'   => 'Sawah dalam fase panen namun ada hujan. Tunda pemanenan karena gabah basah menurunkan kualitas dan harga jual. Tunggu cuaca kering minimal 1–2 hari.'];
                }
            }
        @endphp

        <div class="space-y-3">
            @foreach($rekomList as $r)
            @php
                $colorMap = [
                    'green'  => ['bg'=>'bg-green-50',  'border'=>'border-green-200',  'icon'=>'text-green-600',  'title'=>'text-green-800',  'text'=>'text-green-700'],
                    'red'    => ['bg'=>'bg-red-50',    'border'=>'border-red-200',    'icon'=>'text-red-600',    'title'=>'text-red-800',    'text'=>'text-red-700'],
                    'yellow' => ['bg'=>'bg-yellow-50', 'border'=>'border-yellow-200', 'icon'=>'text-yellow-600', 'title'=>'text-yellow-800', 'text'=>'text-yellow-700'],
                    'blue'   => ['bg'=>'bg-blue-50',   'border'=>'border-blue-200',   'icon'=>'text-blue-600',   'title'=>'text-blue-800',   'text'=>'text-blue-700'],
                    'orange' => ['bg'=>'bg-orange-50', 'border'=>'border-orange-200', 'icon'=>'text-orange-600', 'title'=>'text-orange-800', 'text'=>'text-orange-700'],
                ];
                $c = $colorMap[$r['warna']] ?? $colorMap['blue'];
            @endphp
            <div class="flex items-start gap-3 p-4 {{ $c['bg'] }} border {{ $c['border'] }} rounded-lg">
                <i class="{{ $r['icon'] }} {{ $c['icon'] }} mt-1 text-lg"></i>
                <div>
                    <p class="font-semibold {{ $c['title'] }}">{{ $r['judul'] }}</p>
                    <p class="text-sm {{ $c['text'] }} mt-0.5">{{ $r['isi'] }}</p>
                </div>
            </div>
            @endforeach

            <div class="flex items-start gap-3 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <i class="fas fa-satellite-dish text-gray-500 mt-1"></i>
                <div>
                    <p class="font-semibold text-gray-700">Sumber Data Cuaca</p>
                    <p class="text-sm text-gray-500">Data cuaca real-time dari OpenWeather API — Wilayah Indramayu, Jawa Barat. Diperbarui otomatis setiap kali halaman dibuka dan disimpan ke database untuk keperluan prediksi ML.</p>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Auto-refresh cuaca setiap 5 menit
    const REFRESH_INTERVAL = 5 * 60 * 1000; // 5 menit
    let refreshTimer;

    function refreshWeather() {
        const btn = document.getElementById('refresh-btn');
        const icon = document.getElementById('refresh-icon');
        const status = document.getElementById('weather-status');

        // Animasi loading
        btn.disabled = true;
        icon.classList.add('fa-spin');
        status.innerHTML = '<span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span> Memperbarui...';
        status.className = 'flex items-center gap-2 text-sm text-yellow-600';

        fetch('{{ route("dashboard.cuaca.refresh") }}')
            .then(response => response.json())
            .then(data => {
                const current = data.current;

                // Update suhu
                document.getElementById('current-temp').textContent = Math.round(current.main?.temp ?? 0) + '°C';
                document.getElementById('current-desc').textContent = current.weather?.[0]?.description ?? '-';
                document.getElementById('current-humidity').textContent = (current.main?.humidity ?? '-') + '%';
                document.getElementById('current-rain').textContent = (current.rain?.['1h'] ?? 0) + ' mm';
                document.getElementById('current-wind').textContent = (current.wind?.speed ?? 0) + ' km/h';

                // Update status
                document.getElementById('last-updated').textContent = 'Terakhir: ' + data.updated_at;
                status.innerHTML = '<span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Live — Realtime';
                status.className = 'flex items-center gap-2 text-sm text-green-600';
            })
            .catch(err => {
                console.error('Weather refresh error:', err);
                status.innerHTML = '<span class="w-2 h-2 bg-red-500 rounded-full"></span> Gagal memperbarui';
                status.className = 'flex items-center gap-2 text-sm text-red-600';
            })
            .finally(() => {
                btn.disabled = false;
                icon.classList.remove('fa-spin');
            });
    }

    // Set waktu terakhir update saat halaman dibuka
    document.getElementById('last-updated').textContent = 'Terakhir: {{ now()->format("H:i:s") }}';

    // Auto-refresh setiap 5 menit
    refreshTimer = setInterval(refreshWeather, REFRESH_INTERVAL);

    // Hentikan timer saat tab tidak aktif, jalankan lagi saat aktif
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(refreshTimer);
        } else {
            refreshWeather(); // Refresh langsung saat tab aktif lagi
            refreshTimer = setInterval(refreshWeather, REFRESH_INTERVAL);
        }
    });
</script>
@endpush
@endsection