@extends('layouts.dashboard')

@section('page-title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
        <p class="text-gray-600 mt-1">Selamat datang di panel administrasi PATANI — data diperbarui secara real-time.</p>
    </div>

    {{-- =====================================================================
         STATS CARDS — 4 kartu utama
         ===================================================================== --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

        {{-- Total Petani --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Petani</span>
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['totalPetani'] }}</div>
            <p class="text-xs mt-1 flex items-center gap-1">
                @if($stats['trendPetani'] === 'naik')
                    <i class="fas fa-arrow-up text-green-500"></i>
                    <span class="text-green-600">{{ $stats['perubahanPetani'] }}</span>
                @else
                    <i class="fas fa-arrow-down text-red-500"></i>
                    <span class="text-red-500">{{ $stats['perubahanPetani'] }}</span>
                @endif
                <span class="text-gray-500">vs bulan lalu</span>
            </p>
        </div>

        {{-- Total Luas Sawah --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Luas Sawah</span>
                <i class="fas fa-leaf text-emerald-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['totalSawah'] }} ha</div>
            <p class="text-xs mt-1 flex items-center gap-1">
                @if($stats['trendSawah'] === 'naik')
                    <i class="fas fa-arrow-up text-green-500"></i>
                    <span class="text-green-600">{{ $stats['perubahanSawah'] }}</span>
                @else
                    <i class="fas fa-arrow-down text-red-500"></i>
                    <span class="text-red-500">{{ $stats['perubahanSawah'] }}</span>
                @endif
                <span class="text-gray-500">dari bulan lalu</span>
            </p>
        </div>

        {{-- Total Produksi --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Produksi Tahun Ini</span>
                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['totalProduksi'] }} ton</div>
            <p class="text-xs mt-1 flex items-center gap-1">
                @if($stats['trendProduksi'] === 'naik')
                    <i class="fas fa-arrow-up text-green-500"></i>
                    <span class="text-green-600">{{ $stats['perubahanProduksi'] }}</span>
                @else
                    <i class="fas fa-arrow-down text-red-500"></i>
                    <span class="text-red-500">{{ $stats['perubahanProduksi'] }}</span>
                @endif
                <span class="text-gray-500">vs bulan lalu</span>
            </p>
        </div>

        {{-- Diskusi Aktif --}}
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Diskusi Aktif</span>
                <i class="fas fa-comments text-purple-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['diskusiAktif'] }}</div>
            <p class="text-xs mt-1 flex items-center gap-1">
                @if($stats['trendDiskusi'] === 'naik')
                    <i class="fas fa-arrow-up text-green-500"></i>
                    <span class="text-green-600">{{ $stats['perubahanDiskusi'] }}</span>
                @else
                    <i class="fas fa-arrow-down text-red-500"></i>
                    <span class="text-red-500">{{ $stats['perubahanDiskusi'] }}</span>
                @endif
                <span class="text-gray-500">vs minggu lalu</span>
            </p>
        </div>
    </div>

    {{-- =====================================================================
         STATISTIK TAMBAHAN — status sawah, petani aktif, pendapatan
         ===================================================================== --}}
    <div class="grid gap-4 md:grid-cols-3">

        {{-- Status Sawah --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-th-large text-green-500"></i> Status Sawah
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Aktif</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        {{ $extraStats['sawahAktif'] }} petak
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Siap Panen</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ $extraStats['sawahPanen'] }} petak
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Istirahat</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $extraStats['sawahIstirahat'] }} petak
                    </span>
                </div>
            </div>
        </div>

        {{-- Status Petani --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-user-check text-blue-500"></i> Status Petani
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Aktif</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                        {{ $extraStats['petaniAktif'] }} orang
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Nonaktif</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                        {{ $extraStats['petaniNonaktif'] }} orang
                    </span>
                </div>
            </div>
        </div>

        {{-- Produksi & Pendapatan --}}
        <div class="bg-white rounded-lg shadow p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <i class="fas fa-coins text-yellow-500"></i> Kinerja Tahun Ini
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Pendapatan</span>
                    <span class="text-sm font-semibold text-gray-800">
                        Rp {{ $extraStats['totalPendapatan'] }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Rata-rata/ha</span>
                    <span class="text-sm font-semibold text-gray-800">
                        {{ $extraStats['rataHasilPerHa'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         CHARTS — Produksi vs Target & Pertumbuhan Petani
         ===================================================================== --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Produksi vs Target (6 Bulan)</h3>
                <span class="text-xs text-gray-400 italic">dalam ton</span>
            </div>
            @if(collect($productionData)->sum('produksi') == 0)
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <i class="fas fa-seedling text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data panen tercatat.</p>
                </div>
            @else
                <canvas id="productionChart" height="250"></canvas>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pertumbuhan Petani (6 Bulan)</h3>
                <span class="text-xs text-gray-400 italic">kumulatif</span>
            </div>
            @if(collect($farmerGrowthData)->sum('petani') == 0)
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <i class="fas fa-users text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data petani.</p>
                </div>
            @else
                <canvas id="farmerChart" height="250"></canvas>
            @endif
        </div>
    </div>

    {{-- =====================================================================
         BARIS BAWAH — Distribusi Varietas & Aktivitas Terbaru
         ===================================================================== --}}
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Distribusi Varietas Padi --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Varietas Padi</h3>
            @if(count($varietyData) == 1 && $varietyData[0]['name'] === 'Belum ada data')
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <i class="fas fa-chart-pie text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data varietas tercatat.</p>
                </div>
            @else
                <canvas id="varietyChart" height="220"></canvas>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach($varietyData as $i => $v)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <span class="inline-block w-3 h-3 rounded-full" style="background-color: {{ ['#22c55e','#16a34a','#86efac','#4ade80','#bbf7d0'][$i] ?? '#9ca3af' }}"></span>
                        {{ $v['name'] }}: <strong>{{ $v['value'] }}%</strong>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Aktivitas Terbaru — data nyata dari DB --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>

            @if(empty($recentActivities))
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <i class="fas fa-bell-slash text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada aktivitas tercatat.</p>
                </div>
            @else
                <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                    @foreach($recentActivities as $activity)
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas {{ $activity['icon'] }} {{ $activity['color'] }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 leading-snug">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $activity['time_label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Warna tema hijau PATANI ──────────────────────────────────────────────────
const COLORS = {
    green:       'rgb(34, 197, 94)',
    greenAlpha:  'rgba(34, 197, 94, 0.15)',
    blue:        'rgb(59, 130, 246)',
    blueAlpha:   'rgba(59, 130, 246, 0.15)',
    gray:        'rgb(156, 163, 175)',
    variety:     ['#22c55e','#16a34a','#86efac','#4ade80','#bbf7d0'],
};

// ── Chart.js global defaults ─────────────────────────────────────────────────
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size   = 12;

// ── 1. Chart Produksi vs Target ──────────────────────────────────────────────
@if(collect($productionData)->sum('produksi') > 0)
const productionData = @json($productionData);

new Chart(document.getElementById('productionChart'), {
    type: 'bar',
    data: {
        labels: productionData.map(d => d.bulan),
        datasets: [
            {
                label: 'Produksi Aktual (ton)',
                data: productionData.map(d => d.produksi),
                backgroundColor: COLORS.green,
                borderRadius: 4,
                order: 2,
            },
            {
                label: 'Target (ton)',
                data: productionData.map(d => d.target),
                type: 'line',
                borderColor: COLORS.blue,
                backgroundColor: COLORS.blueAlpha,
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: COLORS.blue,
                tension: 0.4,
                fill: false,
                order: 1,
            },
        ],
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toFixed(2)} ton`,
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: v => v + ' ton' },
            },
            x: { grid: { display: false } },
        },
    },
});
@endif

// ── 2. Chart Pertumbuhan Petani ──────────────────────────────────────────────
@if(collect($farmerGrowthData)->sum('petani') > 0)
const farmerData = @json($farmerGrowthData);

new Chart(document.getElementById('farmerChart'), {
    type: 'line',
    data: {
        labels: farmerData.map(d => d.bulan),
        datasets: [{
            label: 'Jumlah Petani',
            data: farmerData.map(d => d.petani),
            borderColor: COLORS.green,
            backgroundColor: COLORS.greenAlpha,
            borderWidth: 2.5,
            pointBackgroundColor: COLORS.green,
            pointRadius: 4,
            tension: 0.4,
            fill: true,
        }],
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ` Total Petani: ${ctx.parsed.y} orang`,
                },
            },
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { stepSize: 1, callback: v => v + ' org' },
            },
            x: { grid: { display: false } },
        },
    },
});
@endif

// ── 3. Chart Distribusi Varietas ─────────────────────────────────────────────
@if(count($varietyData) > 0 && $varietyData[0]['name'] !== 'Belum ada data')
const varietyData = @json($varietyData);

new Chart(document.getElementById('varietyChart'), {
    type: 'doughnut',
    data: {
        labels: varietyData.map(d => d.name),
        datasets: [{
            data: varietyData.map(d => d.value),
            backgroundColor: ['#22c55e','#16a34a','#86efac','#4ade80','#bbf7d0'],
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 6,
        }],
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed}%`,
                },
            },
        },
    },
});
@endif
</script>
@endpush