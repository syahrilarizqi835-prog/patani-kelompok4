@extends('layouts.dashboard')

@section('page-title', 'Riwayat Panen')

@section('content')
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

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start gap-2">
        <i class="fas fa-check-circle mt-0.5"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- ── STATUS ML ─────────────────────────────────────────────── --}}
    <div class="rounded-lg border-2 p-5 {{ $mlReady ? 'border-green-400 bg-green-50' : 'border-yellow-400 bg-yellow-50' }}">
        <div class="flex items-center gap-3 mb-3">
            <i class="fas fa-robot text-2xl {{ $mlReady ? 'text-green-600' : 'text-yellow-600' }}"></i>
            <div>
                <h3 class="font-bold text-gray-800">
                    Status Machine Learning:
                    @if($mlReady)
                        <span class="text-green-700"> AKTIF</span>
                    @else
                        <span class="text-yellow-700"> Belum Aktif</span>
                    @endif
                </h3>
                <p class="text-sm text-gray-600">
                    @if($mlReady)
                        Model Linear Regression siap digunakan dengan <strong>{{ $jumlahDataML }} data training</strong>.
                        Semakin banyak data, semakin akurat prediksi.
                    @else
                        Butuh minimal <strong>5 data panen</strong> untuk mengaktifkan ML.
                        Saat ini: <strong>{{ $jumlahDataML }}/5 data</strong>.
                    @endif
                </p>
            </div>
        </div>
        {{-- Progress bar --}}
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500 {{ $mlReady ? 'bg-green-500' : 'bg-yellow-500' }}"
                style="width: {{ $mlProgress }}%"></div>
        </div>
        @if(!$mlReady)
        <p class="text-xs text-yellow-700 mt-1">{{ 5 - $jumlahDataML }} data lagi untuk mengaktifkan Machine Learning</p>
        @else
        <p class="text-xs text-green-700 mt-1">Confidence prediksi meningkat seiring bertambahnya data historis</p>
        @endif
    </div>

    {{-- ── STATISTIK CARDS ─────────────────────────────────────────── --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Panen (Tahun Ini)</span>
                <i class="fas fa-warehouse text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">
                {{ $totalPanenKg > 0 ? number_format($totalPanenKg, 0, ',', '.') . ' Kg' : '—' }}
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ date('Y') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Rata-rata per Hektar</span>
                <i class="fas fa-chart-bar text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">
                {{ $avgPerHa ? number_format($avgPerHa, 2) . ' Ton/Ha' : '—' }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Rata-rata semua periode</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-600">Total Pendapatan</span>
                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-800">
                {{ $totalPendapatan > 0 ? 'Rp ' . number_format($totalPendapatan, 0, ',', '.') : '—' }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Tahun {{ date('Y') }}</p>
        </div>
    </div>

    {{-- ── TABEL RIWAYAT ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold">Data Riwayat Panen</h3>
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                <i class="fas fa-brain mr-1"></i>{{ $jumlahDataML }} data training ML
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
                    @forelse($riwayatList as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ \Carbon\Carbon::parse($r->tanggal_panen)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">{{ $r->sawah->nama_sawah }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-800">
                                {{ number_format($r->hasil_panen, 0, ',', '.') }} Kg
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($r->hasil_per_hektar)
                                <span class="font-semibold text-green-700">{{ number_format($r->hasil_per_hektar, 2) }} Ton/Ha</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
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
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $kualitasColor }}">
                                {{ $kualitasLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $r->harga_jual ? 'Rp ' . number_format($r->harga_jual, 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($r->total_pendapatan)
                                <span class="font-semibold text-green-600">
                                    Rp {{ number_format($r->total_pendapatan, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('dashboard.riwayat.destroy', $r->id) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus data ini? Data yang dihapus akan mengurangi akurasi ML.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
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
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── GRAFIK ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-1">Grafik Hasil Panen (12 Bulan Terakhir)</h3>
        <p class="text-xs text-gray-500 mb-4">Data ini digunakan sebagai input training model Machine Learning</p>
        <canvas id="harvestChart" height="100"></canvas>
    </div>

    {{-- ── MODAL TAMBAH DATA ────────────────────────────────────────── --}}
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

                {{-- Info ML --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                    <i class="fas fa-brain mr-1"></i>
                    Data yang Anda masukkan akan digunakan untuk <strong>melatih model Machine Learning</strong>
                    pada fitur Prediksi Panen.
                </div>

                <form action="{{ route('dashboard.riwayat.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Sawah <span class="text-red-500">*</span></label>
                        <select name="sawah_id" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">-- Pilih Sawah --</option>
                            @foreach($sawahList as $sawah)
                            <option value="{{ $sawah->id }}">
                                {{ $sawah->nama_sawah }} ({{ $sawah->luas }} Ha — {{ $sawah->kondisi_tanah }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Panen <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_panen" required max="{{ date('Y-m-d') }}"
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($chartLabels);
    const data   = @json($chartData);

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
@endpush