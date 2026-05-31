@extends('layouts.dashboard')

@section('page-title', 'Prediksi Panen')

@section('content')
<div class="space-y-6" x-data="{
    sawahId: '',
    sawahList: {{ $sawahList->toJson() }},
    get selected() {
        return this.sawahList.find(s => s.id == this.sawahId) || null
    },
    kondisiLabel(val, map) {
        return map[val] || val
    }
}">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">Prediksi Panen</h1>
        <p class="text-gray-600">Prediksi hasil panen berbasis Machine Learning dari data historis</p>
    </div>

    {{-- Notifikasi hasil --}}
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start gap-2">
        <i class="fas fa-check-circle mt-0.5 text-green-600"></i>
        <div>
            <p>{{ session('success') }}</p>
            @if(session('prediksi'))
            <p class="font-bold text-lg mt-1 text-green-700">
                Prediksi Hasil: {{ number_format(session('prediksi'), 2) }} Ton
            </p>
            @endif
        </div>
    </div>
    @endif

    {{-- Form Prediksi --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Form Prediksi Hasil Panen</h3>

        <form action="{{ route('dashboard.prediksi.predict') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Pilih Sawah --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Pilih Sawah <span class="text-red-500">*</span>
                </label>
                <select name="sawah_id" x-model="sawahId" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">-- Pilih Sawah --</option>
                    @foreach($sawahList as $sawah)
                    <option value="{{ $sawah->id }}">
                        {{ $sawah->nama_sawah }} — {{ $sawah->luas }} Ha ({{ $sawah->jenis_padi }})
                    </option>
                    @endforeach
                </select>
                @error('sawah_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Detail Sawah Terpilih --}}
            <div x-show="selected" x-cloak>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
                    <h4 class="font-semibold text-gray-700 flex items-center gap-2 mb-3">
                        <i class="fas fa-map text-green-600"></i>
                        Detail Sawah yang Diprediksi
                    </h4>

                    {{-- Foto Lahan --}}
                    <template x-if="selected && selected.foto_lahan">
                        <div class="mb-3">
                            <img :src="'/storage/' + selected.foto_lahan"
                                :alt="'Foto ' + selected.nama_sawah"
                                class="w-full object-contain rounded-lg border border-gray-200 max-h-64 bg-gray-50">
                        </div>
                    </template>
                    <template x-if="selected && !selected.foto_lahan">
                        <div class="mb-3 w-full h-32 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-image text-3xl mb-1"></i>
                            <p class="text-xs">Belum ada foto lahan</p>
                        </div>
                    </template>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        {{-- Luas --}}
                        <div class="bg-white rounded-lg p-3 border text-center">
                            <i class="fas fa-ruler-combined text-green-600 text-lg mb-1"></i>
                            <p class="text-xs text-gray-500">Luas Lahan</p>
                            <p class="font-bold text-gray-800" x-text="selected ? selected.luas + ' Ha' : '-'"></p>
                        </div>

                        {{-- Kondisi Tanah --}}
                        <div class="bg-white rounded-lg p-3 border text-center">
                            <i class="fas fa-mountain text-yellow-600 text-lg mb-1"></i>
                            <p class="text-xs text-gray-500">Kondisi Tanah</p>
                            <p class="font-bold text-gray-800 capitalize"
                                x-text="selected ? selected.kondisi_tanah : '-'"></p>
                        </div>

                        {{-- Kondisi Air --}}
                        <div class="bg-white rounded-lg p-3 border text-center">
                            <i class="fas fa-tint text-blue-600 text-lg mb-1"></i>
                            <p class="text-xs text-gray-500">Kondisi Air</p>
                            <p class="font-bold text-gray-800 capitalize"
                                x-text="selected ? selected.kondisi_air : '-'"></p>
                        </div>

                        {{-- Jenis Padi --}}
                        <div class="bg-white rounded-lg p-3 border text-center">
                            <i class="fas fa-seedling text-green-600 text-lg mb-1"></i>
                            <p class="text-xs text-gray-500">Jenis Padi</p>
                            <p class="font-bold text-gray-800"
                                x-text="selected ? selected.jenis_padi : '-'"></p>
                        </div>
                    </div>

                    {{-- Fase & Lokasi --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white rounded-lg p-3 border">
                            <p class="text-xs text-gray-500 mb-1"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i>Lokasi</p>
                            <p class="font-semibold text-gray-800 text-sm"
                                x-text="selected ? (selected.desa + ', ' + selected.kecamatan) : '-'"></p>
                        </div>
                        <div class="bg-white rounded-lg p-3 border">
                            <p class="text-xs text-gray-500 mb-1"><i class="fas fa-leaf text-green-400 mr-1"></i>Fase Tanam</p>
                            <p class="font-semibold text-gray-800 text-sm capitalize"
                                x-text="selected ? selected.fase_tanam : '-'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info ML --}}
            <div class="rounded-lg p-4 border {{ $jumlahDataML >= 5 ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                <h4 class="font-semibold mb-1 {{ $jumlahDataML >= 5 ? 'text-green-800' : 'text-yellow-800' }}">
                    <i class="fas fa-robot mr-1"></i>
                    Status Model ML:
                    @if($jumlahDataML >= 5)
                        Aktif ({{ $jumlahDataML }} data training)
                    @else
                        Belum aktif — butuh {{ 5 - $jumlahDataML }} data lagi
                    @endif
                </h4>
                <p class="text-sm {{ $jumlahDataML >= 5 ? 'text-green-700' : 'text-yellow-700' }}">
                    @if($jumlahDataML >= 5)
                        Prediksi menggunakan Machine Learning berbasis data historis panen Anda.
                        Tambah lebih banyak data untuk meningkatkan akurasi.
                    @else
                        Saat ini menggunakan baseline agronomi nasional (5.5 ton/ha).
                        Masukkan minimal 5 data di menu <a href="{{ route('dashboard.riwayat') }}"
                        class="underline font-medium">Riwayat Panen</a> untuk mengaktifkan ML.
                    @endif
                </p>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition flex items-center justify-center gap-2">
                
                Hitung Prediksi dengan ML
            </button>
        </form>
    </div>

    {{-- Riwayat Prediksi --}}
    @if($prediksiList->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold">Riwayat Prediksi</h3>
            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                {{ $prediksiList->count() }} prediksi
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sawah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prediksi Hasil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode ML</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Confidence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($prediksiList as $p)
                    @php
                        $faktor = json_decode($p->faktor_prediksi, true);
                        $metode = $faktor['metode'] ?? 'ML';
                        $r2     = $faktor['r_squared'] ?? null;
                        $nData  = $faktor['jumlah_data_training'] ?? null;

                        $metodeColor = match(true) {
                            str_contains($metode, 'Linear Regression') => 'bg-blue-100 text-blue-800',
                            str_contains($metode, 'Weighted')          => 'bg-purple-100 text-purple-800',
                            default                                    => 'bg-gray-100 text-gray-800',
                        };
                        $confidenceColor = match(true) {
                            $p->confidence_level >= 85 => 'bg-green-100 text-green-800',
                            $p->confidence_level >= 70 => 'bg-blue-100 text-blue-800',
                            default                    => 'bg-yellow-100 text-yellow-800',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $p->tanggal_prediksi->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800 text-sm">{{ $p->sawah->nama_sawah }}</p>
                            <p class="text-xs text-gray-500">{{ $p->sawah->luas }} Ha</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xl font-bold text-green-600">
                                {{ number_format($p->prediksi_hasil, 2) }} Ton
                            </span>
                            @if($p->sawah->luas > 0)
                            <p class="text-xs text-gray-500">
                                {{ number_format($p->prediksi_hasil / $p->sawah->luas, 2) }} Ton/Ha
                            </p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $metodeColor }}">
                                {{ str_contains($metode, 'Linear') ? 'OLS Regression' :
                                   (str_contains($metode, 'Weighted') ? 'Weighted Avg' : 'Agronomi') }}
                            </span>
                            @if($r2 !== null)
                            <p class="text-xs text-gray-400 mt-1">R² = {{ $r2 }} | n = {{ $nData }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $confidenceColor }}">
                                {{ $p->confidence_level }}% Akurat
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                            <p class="line-clamp-3">{{ $p->rekomendasi }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection