<?php $__env->startSection('page-title', 'Riwayat Panen'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Panen</h1>
            <p class="text-gray-600">Data hasil panen & sumber training Machine Learning</p>
        </div>
        <button @click="showModal = true"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Data Panen
        </button>
    </div>

    <?php if(session('success')): ?>
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start gap-2">
        <i class="fas fa-check-circle mt-0.5"></i>
        <span><?php echo e(session('success')); ?></span>
    </div>
    <?php endif; ?>

    
    <div class="rounded-lg border-2 p-5 <?php echo e($mlReady ? 'border-green-400 bg-green-50' : 'border-yellow-400 bg-yellow-50'); ?>">
        <div class="flex items-center gap-3 mb-3">
            <i class="fas fa-robot text-2xl <?php echo e($mlReady ? 'text-green-600' : 'text-yellow-600'); ?>"></i>
            <div>
                <h3 class="font-bold text-gray-800">
                    Status Machine Learning:
                    <?php if($mlReady): ?>
                        <span class="text-green-700"> AKTIF</span>
                    <?php else: ?>
                        <span class="text-yellow-700"> Belum Aktif</span>
                    <?php endif; ?>
                </h3>
                <p class="text-sm text-gray-600">
                    <?php if($mlReady): ?>
                        Model Linear Regression siap digunakan dengan <strong><?php echo e($jumlahDataML); ?> data training</strong>.
                        Semakin banyak data, semakin akurat prediksi.
                    <?php else: ?>
                        Butuh minimal <strong>5 data panen</strong> untuk mengaktifkan ML.
                        Saat ini: <strong><?php echo e($jumlahDataML); ?>/5 data</strong>.
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500 <?php echo e($mlReady ? 'bg-green-500' : 'bg-yellow-500'); ?>"
                style="width: <?php echo e($mlProgress); ?>%"></div>
        </div>
        <?php if(!$mlReady): ?>
        <p class="text-xs text-yellow-700 mt-1"><?php echo e(5 - $jumlahDataML); ?> data lagi untuk mengaktifkan Machine Learning</p>
        <?php else: ?>
        <p class="text-xs text-green-700 mt-1">Confidence prediksi meningkat seiring bertambahnya data historis</p>
        <?php endif; ?>
    </div>

    
    <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Panen (Tahun Ini)</span>
                <i class="fas fa-warehouse text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">
                <?php echo e($totalPanenKg > 0 ? number_format($totalPanenKg, 0, ',', '.') . ' Kg' : '—'); ?>

            </div>
            <p class="text-xs text-gray-500 mt-1"><?php echo e(date('Y')); ?></p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Rata-rata per Hektar</span>
                <i class="fas fa-chart-bar text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">
                <?php echo e($avgPerHa ? number_format($avgPerHa, 2) . ' Ton/Ha' : '—'); ?>

            </div>
            <p class="text-xs text-gray-500 mt-1">Rata-rata semua periode</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Pendapatan</span>
                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">
                <?php echo e($totalPendapatan > 0 ? 'Rp ' . number_format($totalPendapatan, 0, ',', '.') : '—'); ?>

            </div>
            <p class="text-xs text-gray-500 mt-1">Tahun <?php echo e(date('Y')); ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold">Data Riwayat Panen</h3>
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                <i class="fas fa-brain mr-1"></i><?php echo e($jumlahDataML); ?> data training ML
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sawah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil Panen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hasil/Ha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kualitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga/Kg</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $riwayatList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php echo e(\Carbon\Carbon::parse($r->tanggal_panen)->format('d M Y')); ?>

                        </td>
                        <td class="px-6 py-4 text-sm font-medium"><?php echo e($r->sawah->nama_sawah); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-800">
                                <?php echo e(number_format($r->hasil_panen, 0, ',', '.')); ?> Kg
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if($r->hasil_per_hektar): ?>
                                <span class="font-semibold text-green-700"><?php echo e(number_format($r->hasil_per_hektar, 2)); ?> Ton/Ha</span>
                            <?php else: ?>
                                <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $kualitasColor = match($r->kualitas) {
                                'gabah_basah'  => 'bg-blue-100 text-blue-800',
                                'gabah_kering' => 'bg-yellow-100 text-yellow-800',
                                'beras'        => 'bg-green-100 text-green-800',
                                default        => 'bg-gray-100 text-gray-800',
                            };
                                $kualitasLabel = match($r->kualitas) {
                                'gabah_basah'  => 'Gabah Basah',
                                'gabah_kering' => 'Gabah Kering',
                                'beras'        => 'Beras',
                                default        => ucfirst($r->kualitas),
                            };
                            ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($kualitasColor); ?>">
                                <?php echo e($kualitasLabel); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php echo e($r->harga_jual ? 'Rp ' . number_format($r->harga_jual, 0, ',', '.') : '—'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if($r->total_pendapatan): ?>
                                <span class="font-semibold text-green-600">
                                    Rp <?php echo e(number_format($r->total_pendapatan, 0, ',', '.')); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="<?php echo e(route('dashboard.riwayat.destroy', $r->id)); ?>" method="POST"
                                onsubmit="return confirm('Yakin hapus data ini? Data yang dihapus akan mengurangi akurasi ML.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <i class="fas fa-database text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-gray-500 font-medium">Belum ada data riwayat panen</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan data panen untuk mengaktifkan Machine Learning</p>
                            <button @click="showModal = true"
                                class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                                <i class="fas fa-plus mr-1"></i> Tambah Data Pertama
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-1">Grafik Hasil Panen (12 Bulan Terakhir)</h3>
        <p class="text-xs text-gray-500 mb-4">Data ini digunakan sebagai input training model Machine Learning</p>
        <canvas id="harvestChart" height="100"></canvas>
    </div>

    
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black opacity-50"></div>
            <div class="relative bg-white rounded-xl max-w-lg w-full p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-plus-circle text-green-600 mr-2"></i>Tambah Data Panen
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                    <i class="fas fa-brain mr-1"></i>
                    Data yang Anda masukkan akan digunakan untuk <strong>melatih model Machine Learning</strong>
                    pada fitur Prediksi Panen.
                </div>

                <form action="<?php echo e(route('dashboard.riwayat.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Sawah <span class="text-red-500">*</span></label>
                        <select name="sawah_id" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">-- Pilih Sawah --</option>
                            <?php $__currentLoopData = $sawahList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sawah): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sawah->id); ?>">
                                <?php echo e($sawah->nama_sawah); ?> (<?php echo e($sawah->luas); ?> Ha — <?php echo e($sawah->kondisi_tanah); ?>)
                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Panen <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_panen" required max="<?php echo e(date('Y-m-d')); ?>"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Hasil Panen (Kg) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="hasil_panen" required min="1" step="0.01"
                            placeholder="Contoh: 6500"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-400 mt-1">Sistem otomatis menghitung ton/ha dari luas sawah yang dipilih</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kualitas Panen <span class="text-red-500">*</span></label>
                    <select name="kualitas" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="gabah_basah">Gabah Basah</option>
                        <option value="gabah_kering" selected>Gabah Kering</option>
                        <option value="beras">Beras</option>
                    </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp/Kg)</label>
                            <input type="number" name="harga_jual" min="0" step="50"
                                placeholder="Contoh: 5500"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <input type="text" name="catatan" placeholder="Opsional"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium">
                            <i class="fas fa-save mr-1"></i> Simpan & Training ML
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = <?php echo json_encode($chartLabels, 15, 512) ?>;
    const data   = <?php echo json_encode($chartData, 15, 512) ?>;

    new Chart(document.getElementById('harvestChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hasil Panen (Kg)',
                data: data,
                backgroundColor: data.map(v => v > 0 ? 'rgba(22, 163, 74, 0.75)' : 'rgba(229, 231, 235, 0.8)'),
                borderColor: data.map(v => v > 0 ? 'rgb(22, 163, 74)' : 'rgb(209, 213, 219)'),
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y > 0
                            ? ctx.parsed.y.toLocaleString('id-ID') + ' Kg'
                            : 'Tidak ada data'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => v.toLocaleString('id-ID') + ' Kg'
                    }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Semester 6\Proyek 3\laravel-patani\resources\views/dashboard/riwayat.blade.php ENDPATH**/ ?>