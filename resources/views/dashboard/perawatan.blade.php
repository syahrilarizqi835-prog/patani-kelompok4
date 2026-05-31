@extends('layouts.dashboard')

@section('page-title', 'Perawatan Sawah')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Perawatan Sawah</h1>
            <p class="text-gray-600">Kelola jadwal perawatan dan monitoring kegiatan</p>
        </div>
        <button @click="showModal = true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i> Tambah Perawatan
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Riwayat Perawatan -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Riwayat Perawatan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sawah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bahan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($perawatanList as $perawatan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $perawatan->tanggal->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $perawatan->sawah->nama_sawah }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $perawatan->jenis_perawatan === 'pemupukan' ? 'bg-green-100 text-green-800' : 
                                   ($perawatan->jenis_perawatan === 'penyemprotan' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($perawatan->jenis_perawatan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $perawatan->nama_kegiatan }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $perawatan->bahan_digunakan }} 
                            @if($perawatan->jumlah)
                                ({{ $perawatan->jumlah }} {{ $perawatan->satuan }})
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                            Rp {{ number_format($perawatan->biaya, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data perawatan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Perawatan -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showModal = false" class="fixed inset-0 bg-black opacity-50"></div>
            
            <div class="relative bg-white rounded-lg max-w-2xl w-full p-6">
                <h3 class="text-xl font-bold mb-4">Tambah Data Perawatan</h3>
                
                <form action="{{ route('dashboard.perawatan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Pilih Sawah</label>
                            <select name="sawah_id" required class="w-full px-3 py-2 border rounded-lg">
                                <option value="">-- Pilih Sawah --</option>
                                @foreach($sawahList as $sawah)
                                    <option value="{{ $sawah->id }}">{{ $sawah->nama_sawah }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Tanggal</label>
                            <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Jenis Perawatan</label>
                            <select name="jenis_perawatan" required class="w-full px-3 py-2 border rounded-lg">
                                <option value="pemupukan">Pemupukan</option>
                                <option value="penyemprotan">Penyemprotan</option>
                                <option value="pengairan">Pengairan</option>
                                <option value="penyiangan">Penyiangan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" required class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Bahan Digunakan</label>
                            <input type="text" name="bahan_digunakan" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Jumlah</label>
                            <input type="number" step="0.01" name="jumlah" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Satuan</label>
                            <input type="text" name="satuan" placeholder="kg, liter, dll" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Biaya (Rp)</label>
                        <input type="number" name="biaya" class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div class="flex gap-2 justify-end pt-4">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
