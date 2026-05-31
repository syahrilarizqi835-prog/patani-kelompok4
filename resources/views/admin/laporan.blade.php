@extends('layouts.dashboard')

@section('page-title', 'Laporan & Statistik')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan & Statistik</h1>
            <p class="text-gray-500 text-sm mt-0.5">Data produksi panen petani berdasarkan periode</p>
        </div>

        {{-- Tombol Export --}}
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.export.excel', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.laporan.export.pdf', ['tahun' => $tahun, 'bulan' => $bulan]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- =====================================================================
         FILTER TAHUN & BULAN
         ===================================================================== --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.laporan') }}" class="flex flex-wrap gap-3 items-end">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @foreach($tahunTersedia as $t)
                        <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-filter mr-1"></i> Terapkan Filter
            </button>

            @if($bulan)
            <a href="{{ route('admin.laporan', ['tahun' => $tahun]) }}"
               class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </form>
    </div>

    {{-- =====================================================================
         STATS CARDS — 4 kartu ringkasan
         ===================================================================== --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Produksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['totalProduksiTon'] }} <span class="text-sm font-normal text-gray-500">ton</span></p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $stats['totalProduksiKg'] }} Kg</p>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Pendapatan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ $stats['totalPendapatan'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Akumulasi seluruh petani</p>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Rata-rata / Ha</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['rataHasilPerHa'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $stats['totalPanen'] }} kali panen tercatat</p>
        </div>

        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Lahan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['totalLuas'] }} <span class="text-sm font-normal text-gray-500">Ha</span></p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $stats['totalPetani'] }} petani terdaftar</p>
        </div>
    </div>

    {{-- =====================================================================
         CHARTS — Produksi bulanan & Kualitas
         ===================================================================== --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Chart produksi & pendapatan bulanan --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                Produksi & Pendapatan Bulanan
                <span class="text-xs text-gray-400 font-normal ml-1">(Tahun {{ $tahun }})</span>
            </h3>
            @if(collect($produksiBulanan)->sum('produksi') == 0)
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <i class="fas fa-chart-bar text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data panen untuk tahun {{ $tahun }}.</p>
                </div>
            @else
                <canvas id="chartProduksi" height="200"></canvas>
            @endif
        </div>

        {{-- Chart distribusi kualitas --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Distribusi Kualitas</h3>
            @if($kualitasData->isEmpty())
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <i class="fas fa-chart-pie text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data.</p>
                </div>
            @else
                <canvas id="chartKualitas" height="200"></canvas>
                <div class="mt-4 space-y-2">
                    @php $kualitasColors = ['#22c55e','#eab308','#3b82f6','#ef4444','#a855f7']; @endphp
                    @foreach($kualitasData as $i => $k)
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full inline-block"
                                  style="background: {{ $kualitasColors[$i % 5] }}"></span>
                            {{ $k['kualitas'] }}
                        </div>
                        <span class="font-medium">{{ $k['total_ton'] }} ton ({{ $k['jumlah'] }}x)</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- =====================================================================
         TOP PETANI — tabel 10 petani dengan produksi terbanyak
         ===================================================================== --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">
            Top 10 Petani Berdasarkan Produksi
            <span class="text-xs text-gray-400 font-normal ml-1">
                ({{ $bulan ? \Carbon\Carbon::create($tahun, $bulan)->locale('id')->isoFormat('MMMM Y') : 'Tahun ' . $tahun }})
            </span>
        </h3>

        @if($topPetani->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <i class="fas fa-users text-4xl mb-2"></i>
                <p class="text-sm">Belum ada data panen tercatat untuk periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-green-50 border-b border-green-100">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Rank</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama Petani</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Sawah</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Luas (Ha)</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Jml Panen</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Total (Ton)</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($topPetani as $i => $p)
                        @php
                            $rowBg = match(true) {
                                $i === 0 => 'bg-yellow-50 border-l-4 border-yellow-400',
                                $i === 1 => 'bg-gray-50 border-l-4 border-gray-400',
                                $i === 2 => 'bg-amber-50 border-l-4 border-amber-600',
                                default  => 'hover:bg-gray-50',
                            };
                        @endphp
                        <tr class="transition-colors {{ $rowBg }}">
                            <td class="py-3 px-4">
                                <span class="font-bold text-sm {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-500' : ($i === 2 ? 'text-amber-600' : 'text-gray-400')) }}">
                                    {{ $i + 1 }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ $p['nama_petani'] }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $p['nama_sawah'] }}</td>
                            <td class="py-3 px-4 text-right text-gray-600">{{ $p['luas'] }}</td>
                            <td class="py-3 px-4 text-right text-gray-600">{{ $p['jumlah_panen'] }}x</td>
                            <td class="py-3 px-4 text-right font-semibold text-green-700">{{ $p['total_ton'] }} ton</td>
                            <td class="py-3 px-4 text-right text-gray-700">Rp {{ $p['total_pendapatan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 11;

// ── Chart Produksi & Pendapatan Bulanan ──────────────────────────────────────
@if(collect($produksiBulanan)->sum('produksi') > 0)
const produksiBulanan = @json($produksiBulanan);

new Chart(document.getElementById('chartProduksi'), {
    type: 'bar',
    data: {
        labels: produksiBulanan.map(d => d.bulan),
        datasets: [
            {
                label: 'Produksi (Ton)',
                data: produksiBulanan.map(d => d.produksi),
                backgroundColor: 'rgba(34, 197, 94, 0.85)',
                borderRadius: 4,
                yAxisID: 'yTon',
                order: 2,
            },
            {
                label: 'Pendapatan (Juta Rp)',
                data: produksiBulanan.map(d => d.pendapatan),
                type: 'line',
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointRadius: 4,
                tension: 0.4,
                fill: false,
                yAxisID: 'yRp',
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
                    label: ctx => ctx.dataset.label.includes('Ton')
                        ? ` Produksi: ${ctx.parsed.y.toFixed(2)} ton`
                        : ` Pendapatan: Rp ${ctx.parsed.y.toFixed(1)} juta`,
                },
            },
        },
        scales: {
            yTon: {
                type: 'linear',
                position: 'left',
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: v => v + ' ton' },
            },
            yRp: {
                type: 'linear',
                position: 'right',
                beginAtZero: true,
                grid: { drawOnChartArea: false },
                ticks: { callback: v => 'Rp ' + v + ' jt' },
            },
            x: { grid: { display: false } },
        },
    },
});
@endif

// ── Chart Distribusi Kualitas ────────────────────────────────────────────────
@if(!$kualitasData->isEmpty())
const kualitasData = @json($kualitasData);
const kualitasColors = ['#22c55e','#eab308','#3b82f6','#ef4444','#a855f7'];

new Chart(document.getElementById('chartKualitas'), {
    type: 'doughnut',
    data: {
        labels: kualitasData.map(d => d.kualitas),
        datasets: [{
            data: kualitasData.map(d => d.total_ton),
            backgroundColor: kualitasColors.slice(0, kualitasData.length),
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 6,
        }],
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed.toFixed(2)} ton`,
                },
            },
        },
    },
});
@endif
</script>
@endpush