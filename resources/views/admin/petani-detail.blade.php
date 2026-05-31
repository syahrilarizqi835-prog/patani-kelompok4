@extends('layouts.dashboard')

@section('page-title', 'Detail Petani')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.petani') }}" 
           class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Petani</h1>
            <p class="text-gray-500 text-sm">Informasi lengkap data petani</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profil Card --}}
        <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center text-center">
            {{-- Foto profil --}}
            @if($petani->foto_profil)
                <img src="{{ $petani->foto_profil_url }}"
                     alt="{{ $petani->name }}"
                     class="h-20 w-20 rounded-full object-cover mb-4 border-2 border-green-200">
            @else
                <div class="h-20 w-20 bg-green-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mb-4">
                    {{ substr($petani->name, 0, 1) }}
                </div>
            @endif
            <h2 class="text-xl font-bold text-gray-800">{{ $petani->name }}</h2>
            <p class="text-gray-500 text-sm mb-3">{{ $petani->email }}</p>
            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                {{ $petani->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                <i class="fas fa-circle text-xs mr-1"></i>{{ ucfirst($petani->status) }}
            </span>

            <div class="mt-6 w-full border-t pt-4 space-y-2 text-sm text-left">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Sawah</span>
                    <span class="font-semibold text-gray-800">{{ $petani->sawah->count() }} lahan</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Bergabung</span>
                    <span class="font-semibold text-gray-800">{{ $petani->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Premium</span>
                    <span class="font-semibold {{ $petani->is_premium ? 'text-yellow-600' : 'text-gray-500' }}">
                        {{ $petani->is_premium ? '✓ Aktif' : 'Tidak' }}
                    </span>
                </div>
            </div>

            {{-- Tombol Hapus --}}
            <div class="mt-6 w-full">
                <form action="{{ route('admin.petani.destroy', $petani->id) }}" 
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus petani {{ addslashes($petani->name) }}?\nSemua data terkait akan ikut terhapus dan tidak bisa dikembalikan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition font-medium text-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus Petani Ini
                    </button>
                </form>
            </div>
        </div>

        {{-- Info Detail --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Data Pribadi --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card text-green-600"></i> Data Pribadi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">NIK</p>
                        <p class="font-medium text-gray-800">{{ $petani->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Telepon</p>
                        <p class="font-medium text-gray-800">{{ $petani->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Desa</p>
                        <p class="font-medium text-gray-800">{{ $petani->desa ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Kecamatan</p>
                        <p class="font-medium text-gray-800">{{ $petani->kecamatan ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-500 mb-1">Alamat Lengkap</p>
                        <p class="font-medium text-gray-800">{{ $petani->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Sawah --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-seedling text-green-600"></i> Data Sawah 
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-normal">
                        {{ $petani->sawah->count() }} lahan
                    </span>
                </h3>

                @forelse($petani->sawah as $sawah)
                <div class="border border-gray-100 rounded-lg p-4 mb-3 hover:border-green-200 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $sawah->nama_sawah ?? 'Sawah #'.$sawah->id }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $sawah->lokasi ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right text-sm">
                            <p class="font-semibold text-green-700">{{ $sawah->luas_sawah ?? '-' }} Ha</p>
                            @if(isset($sawah->status_verifikasi))
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                {{ $sawah->status_verifikasi === 'lulus' ? 'bg-green-100 text-green-700' : 
                                   ($sawah->status_verifikasi === 'tolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($sawah->status_verifikasi ?? 'pending') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($sawah->riwayatPanen && $sawah->riwayatPanen->count() > 0)
                    <div class="mt-3 pt-3 border-t border-gray-50 text-xs text-gray-500">
                        <i class="fas fa-history mr-1"></i>
                        {{ $sawah->riwayatPanen->count() }} riwayat panen tercatat
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-seedling text-3xl mb-3 block opacity-30"></i>
                    <p>Belum ada data sawah terdaftar</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection