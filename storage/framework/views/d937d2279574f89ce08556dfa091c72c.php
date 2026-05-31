<?php $__env->startSection('page-title', 'Dashboard Petani'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Dashboard Petani</h1>
        <p class="text-gray-600">Selamat datang kembali! Berikut ringkasan lahan Anda.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Luas Lahan</span>
                <i class="fas fa-map text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800"><?php echo e($stats['totalLuasLahan']); ?></div>
            <p class="text-xs text-gray-600"><?php echo e($stats['jumlahPetak']); ?></p>
            <p class="text-xs text-green-600 mt-1">+2 Ha dari tahun lalu</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Status Tanaman</span>
                <i class="fas fa-seedling text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800"><?php echo e($stats['statusTanaman']); ?></div>
            <p class="text-xs text-gray-600"><?php echo e($stats['umurTanaman']); ?></p>
            <p class="text-xs text-green-600 mt-1">Kondisi sehat</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Estimasi Panen</span>
                <i class="fas fa-calendar text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800"><?php echo e($stats['estimasiPanen']); ?></div>
            <p class="text-xs text-gray-600"><?php echo e($stats['hariSampaiPanen']); ?></p>
            <p class="text-xs text-green-600 mt-1">Tepat waktu</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Prediksi Hasil</span>
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800"><?php echo e($stats['prediksiHasil']); ?></div>
            <p class="text-xs text-gray-600"><?php echo e($stats['prediksiPerHa']); ?></p>
            <p class="text-xs text-green-600 mt-1">+15% dari musim lalu</p>
        </div>
    </div>

    <!-- Weather Card -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-cloud-sun text-green-600 text-xl"></i>
            <h3 class="text-lg font-semibold text-gray-800">Informasi Cuaca Hari Ini</h3>
        </div>
        <p class="text-sm text-gray-600 mb-4">Lokasi: <?php echo e(auth()->user()->desa ?? 'Indramayu'); ?>, Jawa Barat</p>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex items-center gap-4 rounded-lg bg-gray-50 p-4">
                <i class="fas fa-thermometer-half text-green-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-gray-600">Suhu</p>
                    <p class="text-xl font-bold text-gray-800"><?php echo e($weatherData->suhu); ?>°C</p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg bg-gray-50 p-4">
                <i class="fas fa-tint text-green-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-gray-600">Kelembaban</p>
                    <p class="text-xl font-bold text-gray-800"><?php echo e($weatherData->kelembaban); ?>%</p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg bg-gray-50 p-4">
                <i class="fas fa-cloud-rain text-green-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-gray-600">Curah Hujan</p>
                    <p class="text-xl font-bold text-gray-800"><?php echo e($weatherData->curah_hujan); ?> mm</p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-lg bg-gray-50 p-4">
                <i class="fas fa-wind text-green-600 text-2xl"></i>
                <div>
                    <p class="text-sm text-gray-600">Kec. Angin</p>
                    <p class="text-xl font-bold text-gray-800"><?php echo e($weatherData->kecepatan_angin); ?> km/h</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Perkembangan Produksi</h3>
        <p class="text-sm text-gray-600 mb-4">Data produksi dalam kg per bulan tahun 2025-2026</p>
        <canvas id="productionChart" height="100"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Riwayat Panen</h3>
        <p class="text-sm text-gray-600 mb-4">Data hasil panen dalam kg per periode</p>
        <canvas id="harvestChart" height="100"></canvas>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Production Chart
const productionData = <?php echo json_encode($productionData, 15, 512) ?>;
const productionCtx = document.getElementById('productionChart').getContext('2d');
new Chart(productionCtx, {
    type: 'line',
    data: {
        labels: productionData.map(d => d.month),
        datasets: [{
            label: 'Produksi (kg)',
            data: productionData.map(d => d.produksi),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Harvest Chart
const harvestData = <?php echo json_encode($harvestHistory, 15, 512) ?>;
const harvestCtx = document.getElementById('harvestChart').getContext('2d');
new Chart(harvestCtx, {
    type: 'bar',
    data: {
        labels: harvestData.map(d => d.periode),
        datasets: [{
            label: 'Hasil Panen (kg)',
            data: harvestData.map(d => d.hasil),
            backgroundColor: 'rgb(34, 197, 94)',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const productionData = <?php echo json_encode($productionData ?? [], 15, 512) ?>;
    const harvestData = <?php echo json_encode($harvestHistory ?? [], 15, 512) ?>;

    // ===== PRODUKSI =====
    const productionCanvas = document.getElementById('productionChart');
    if (productionCanvas && productionData.length > 0) {

        new Chart(productionCanvas, {
            type: 'line',
            data: {
                labels: productionData.map(d => d.month),
                datasets: [{
                    label: 'Produksi (kg)',
                    data: productionData.map(d => d.produksi),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

    }

    // ===== PANEN =====
    const harvestCanvas = document.getElementById('harvestChart');
    if (harvestCanvas && harvestData.length > 0) {

        new Chart(harvestCanvas, {
            type: 'bar',
            data: {
                labels: harvestData.map(d => d.periode),
                datasets: [{
                    label: 'Hasil Panen (kg)',
                    data: harvestData.map(d => d.hasil),
                    backgroundColor: '#16a34a'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

    }

});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Semester 6\Proyek 3\laravel-patani\resources\views/dashboard/index.blade.php ENDPATH**/ ?>